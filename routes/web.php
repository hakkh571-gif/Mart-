<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use App\Http\Controllers\PosController;
use App\Models\Order;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Helper Functions
|--------------------------------------------------------------------------
*/
function getStaffTableName() {
    if (Schema::hasTable('table_staff')) return 'table_staff';
    if (Schema::hasTable('staffs')) return 'staffs';
    if (Schema::hasTable('users')) return 'users';
    return null;
}

function getOrdersRevenueColumn() {
    if (!Schema::hasTable('orders')) return null;
    foreach (['total_amount', 'total', 'grand_total', 'amount', 'total_price', 'price'] as $col) {
        if (Schema::hasColumn('orders', $col)) {
            return $col;
        }
    }
    return null;
}

// Redirect Home to POS
Route::get('/', function () {
    return redirect()->route('pos.index');
});

// ==========================================
// DASHBOARD ROUTES
// ==========================================
Route::get('/dashboard', function () {
    $today = Carbon::today();
    $todayRevenue = 0;
    $todayCupsSold = 0;
    $totalStaff = 0;
    $dailyDrinkReport = collect();
    $recentOrders = collect();

    try {
        // 1. Total revenue today
        $revenueColumn = getOrdersRevenueColumn();
        if ($revenueColumn) {
            $todayRevenue = DB::table('orders')->whereDate('created_at', $today)->sum($revenueColumn) ?? 0;
        }

        // 2. Cups sold today
        if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quantity')) {
            $todayCupsSold = DB::table('order_items')->whereDate('created_at', $today)->sum('quantity') ?? 0;
        }

        // 3. Staff count
        $staffTable = getStaffTableName();
        if ($staffTable) {
            $totalStaff = DB::table($staffTable)->count();
        }

        // 4. Daily drink report
        if (Schema::hasTable('order_items')) {
            $nameCol = Schema::hasColumn('order_items', 'product_name') ? 'product_name' : (Schema::hasColumn('order_items', 'name') ? 'name' : 'product_name');
            $qtyCol = Schema::hasColumn('order_items', 'quantity') ? 'quantity' : (Schema::hasColumn('order_items', 'qty') ? 'qty' : 'quantity');
            $priceCol = Schema::hasColumn('order_items', 'price') ? 'price' : (Schema::hasColumn('order_items', 'amount') ? 'amount' : 'price');

            $dailyDrinkReport = DB::table('order_items')
                ->select(
                    DB::raw("MIN(id) as id"),
                    DB::raw("$nameCol as product_name"),
                    DB::raw("SUM($qtyCol) as total_qty"),
                    DB::raw("SUM($priceCol * $qtyCol) as total_amount")
                )
                ->groupBy($nameCol)
                ->take(10)
                ->get();
        }

        if (class_exists(Order::class) && Schema::hasTable((new Order)->getTable())) {
            $recentOrders = Order::latest()->take(10)->get();
        } elseif (Schema::hasTable('orders')) {
            $recentOrders = DB::table('orders')->latest()->take(10)->get();
        }
    } catch (\Exception $e) {
        Log::error("Dashboard Error: " . $e->getMessage());
    }

    return view('dashboard', compact(
        'todayRevenue', 
        'todayCupsSold', 
        'totalStaff', 
        'dailyDrinkReport', 
        'recentOrders'
    ));
})->name('dashboard');

// Dashboard Actions (Update & Delete via AJAX)
Route::post('/dashboard/product/update', function (Request $request) {
    try {
        $id = $request->input('id');
        $name = $request->input('name');
        $qty = (float) $request->input('qty', 0);
        $amount = (float) $request->input('amount', 0);

        if (Schema::hasTable('order_items')) {
            $idColumn = Schema::hasColumn('order_items', 'product_id') ? 'product_id' : 'id';
            $nameCol = Schema::hasColumn('order_items', 'product_name') ? 'product_name' : 'name';
            $qtyCol = Schema::hasColumn('order_items', 'quantity') ? 'quantity' : 'qty';
            $priceCol = Schema::hasColumn('order_items', 'price') ? 'price' : 'amount';

            $pricePerUnit = $qty > 0 ? ($amount / $qty) : 0;

            $updateData = [];
            if (Schema::hasColumn('order_items', $nameCol)) $updateData[$nameCol] = $name;
            if (Schema::hasColumn('order_items', $qtyCol)) $updateData[$qtyCol] = $qty;
            if (Schema::hasColumn('order_items', $priceCol)) $updateData[$priceCol] = $pricePerUnit;
            if (Schema::hasColumn('order_items', 'updated_at')) $updateData['updated_at'] = now();

            DB::table('order_items')
                ->where($idColumn, $id)
                ->orWhere('id', $id)
                ->update($updateData);
        }

        return response()->json([
            'success' => true,
            'message' => 'កែប្រែទិន្នន័យបានជោគជ័យ!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'មិនអាចកែប្រែបានទេ៖ ' . $e->getMessage()
        ], 500);
    }
})->name('dashboard.product.update');

Route::delete('/dashboard/product/delete/{id}', function ($id) {
    try {
        if (Schema::hasTable('order_items')) {
            $idColumn = Schema::hasColumn('order_items', 'product_id') ? 'product_id' : 'id';

            $deleted = DB::table('order_items')->where($idColumn, $id)->delete();
            if (!$deleted && $idColumn !== 'id') {
                DB::table('order_items')->where('id', $id)->delete();
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'លុបទិន្នន័យបានជោគជ័យ!'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'មិនអាចលុបបានទេ៖ ' . $e->getMessage()
        ], 500);
    }
})->name('dashboard.product.delete');

// ==========================================
// API ROUTES FOR DASHBOARD CHARTS & REALTIME
// ==========================================
Route::get('/api/dashboard-chart', function () {
    $today = Carbon::today();
    $labels = [];
    $data = [];
    $revenueColumn = getOrdersRevenueColumn();

    // Fetch Revenue for the last 7 days
    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i);
        $dateString = $date->format('Y-m-d');
        $labels[] = $date->format('D, d M');

        $total = 0;
        if ($revenueColumn) {
            $total = DB::table('orders')
                ->whereRaw("DATE(created_at) = ?", [$dateString])
                ->sum($revenueColumn) ?? 0;
        }

        $data[] = (float) $total;
    }

    // Recalculate Realtime Summary
    $todayRevenue = 0;
    if ($revenueColumn) {
        $todayRevenue = DB::table('orders')->whereDate('created_at', $today)->sum($revenueColumn) ?? 0;
    }

    $todayCupsSold = 0;
    if (Schema::hasTable('order_items') && Schema::hasColumn('order_items', 'quantity')) {
        $todayCupsSold = DB::table('order_items')->whereDate('created_at', $today)->sum('quantity') ?? 0;
    }

    $totalStaff = 0;
    $staffTable = getStaffTableName();
    if ($staffTable) {
        $totalStaff = DB::table($staffTable)->count();
    }

    return response()->json([
        'labels'  => $labels,
        'data'    => $data,
        'summary' => [
            'todayRevenue'  => $todayRevenue,
            'todayCupsSold' => $todayCupsSold,
            'totalStaff'    => $totalStaff
        ]
    ]);
})->name('dashboard.chart');

Route::get('/api/dashboard-products-chart', function () {
    $labels = [];
    $data = [];

    if (Schema::hasTable('order_items')) {
        $nameCol = Schema::hasColumn('order_items', 'product_name') ? 'product_name' : (Schema::hasColumn('order_items', 'name') ? 'name' : 'product_name');
        $qtyCol = Schema::hasColumn('order_items', 'quantity') ? 'quantity' : (Schema::hasColumn('order_items', 'qty') ? 'qty' : 'quantity');

        $topProducts = DB::table('order_items')
            ->select($nameCol . ' as product_name', DB::raw("SUM($qtyCol) as total_qty"))
            ->groupBy($nameCol)
            ->orderByDesc('total_qty')
            ->take(7)
            ->get();

        foreach ($topProducts as $item) {
            $labels[] = $item->product_name;
            $data[] = (int) $item->total_qty;
        }
    }

    return response()->json([
        'labels' => $labels,
        'data'   => $data,
    ]);
})->name('dashboard.products_chart');

// ==========================================
// POS ROUTES
// ==========================================
Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
Route::post('/pos', [PosController::class, 'store'])->name('pos.store');

// ==========================================
// PRODUCTS ROUTES
// ==========================================
Route::get('/products', function () {
    $products = collect();
    try {
        if (Schema::hasTable('products')) {
            $products = DB::table('products')->latest()->get();
        } elseif (Schema::hasTable('tbl_food')) {
            $products = DB::table('tbl_food')->latest()->get();
        }
    } catch (\Exception $e) {
        session()->flash('error', 'មិនអាចតភ្ជាប់ទៅកាន់ Database បានទេ!');
    }
    return view('products.index', compact('products'));
})->name('products.index');

Route::get('/products/create', function () {
    return view('products.create');
})->name('products.create');

Route::post('/products/store', function (Request $request) {
    $validated = $request->validate([
        'name'        => 'required|string|max:255',
        'code'        => 'nullable|string|max:100',
        'price'       => 'required|numeric|min:0',
        'cost'        => 'nullable|numeric|min:0',
        'stock'       => 'nullable|integer|min:0',
        'category_id' => 'nullable|integer',
        'image'       => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    try {
        $tableName = Schema::hasTable('products') ? 'products' : (Schema::hasTable('tbl_food') ? 'tbl_food' : null);
        
        $imageName = null;
        if ($request->hasFile('image')) {
            $destinationPath = public_path('uploads/products');
            if (!File::isDirectory($destinationPath)) {
                File::makeDirectory($destinationPath, 0777, true, true);
            }
            $image = $request->file('image');
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move($destinationPath, $imageName);
        }

        if ($tableName) {
            $data = [
                'name'       => $validated['name'],
                'category'   => $request->input('category', 'Drink'),
                'price'      => $validated['price'],
                'stock'      => $validated['stock'] ?? 100,
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn($tableName, 'image') && $imageName) {
                $data['image'] = $imageName;
            }

            DB::table($tableName)->insert($data);
        }
        return redirect()->route('products.index')->with('success', 'បានបន្ថែមភេសជ្ជៈជោគជ័យ!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'មានបញ្ហាមិនអាចរក្សាទុកបាន៖ ' . $e->getMessage());
    }
})->name('products.store');

Route::get('/products/{id}/edit', function ($id) {
    $product = null;
    try {
        if (Schema::hasTable('products')) {
            $product = DB::table('products')->where('id', $id)->first();
        } elseif (Schema::hasTable('tbl_food')) {
            $product = DB::table('tbl_food')->where('id', $id)->first();
        }
    } catch (\Exception $e) {}

    if (!$product) {
        return redirect()->route('products.index')->with('error', 'រកមិនឃើញទិន្នន័យនេះទេ!');
    }

    return view('products.edit', compact('product'));
})->name('products.edit');

Route::put('/products/{id}', function (Request $request, $id) {
    $validated = $request->validate([
        'name'  => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
        'stock' => 'nullable|integer|min:0',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
    ]);

    try {
        $tableName = Schema::hasTable('products') ? 'products' : (Schema::hasTable('tbl_food') ? 'tbl_food' : null);
        
        if ($tableName) {
            $product = DB::table($tableName)->where('id', $id)->first();
            
            $data = [
                'name'       => $validated['name'],
                'category'   => $request->input('category', 'Drink'),
                'price'      => $validated['price'],
                'stock'      => $validated['stock'] ?? 100,
                'updated_at' => now(),
            ];

            if ($request->hasFile('image')) {
                if ($product && isset($product->image) && File::exists(public_path('uploads/products/' . $product->image))) {
                    File::delete(public_path('uploads/products/' . $product->image));
                }

                $destinationPath = public_path('uploads/products');
                if (!File::isDirectory($destinationPath)) {
                    File::makeDirectory($destinationPath, 0777, true, true);
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move($destinationPath, $imageName);
                
                if (Schema::hasColumn($tableName, 'image')) {
                    $data['image'] = $imageName;
                }
            }

            DB::table($tableName)->where('id', $id)->update($data);
        }
        return redirect()->route('products.index')->with('success', 'បានកែប្រែទិន្នន័យភេសជ្ជៈជោគជ័យ!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'មានបញ្ហាមិនអាចកែប្រែបាន៖ ' . $e->getMessage());
    }
})->name('products.update');

Route::delete('/products/{id}', function ($id) {
    try {
        $tableName = Schema::hasTable('products') ? 'products' : (Schema::hasTable('tbl_food') ? 'tbl_food' : null);
        if ($tableName) {
            $product = DB::table($tableName)->where('id', $id)->first();

            if ($product && isset($product->image) && File::exists(public_path('uploads/products/' . $product->image))) {
                File::delete(public_path('uploads/products/' . $product->image));
            }

            DB::table($tableName)->where('id', $id)->delete();
        }
        return redirect()->route('products.index')->with('success', 'បានលុបភេសជ្ជៈរួចរាល់!');
    } catch (\Exception $e) {
        return redirect()->route('products.index')->with('error', 'មិនអាចលុបបានទេ៖ ' . $e->getMessage());
    }
})->name('products.destroy');

// ==========================================
// STAFFS ROUTES
// ==========================================
Route::get('/staffs', function () {
    $staffs = collect();
    try {
        $tableName = getStaffTableName();
        if ($tableName) {
            $staffs = DB::table($tableName)->latest()->get();
        }
    } catch (\Exception $e) {
        session()->flash('error', 'មិនអាចតភ្ជាប់ទៅកាន់ Database បានទេ!');
    }
    return view('staffs.index', compact('staffs'));
})->name('staffs.index');

Route::post('/staffs/store', function (Request $request) {
    try {
        $tableName = getStaffTableName();

        if ($tableName) {
            $data = [
                'name'       => $request->input('name'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn($tableName, 'phone')) {
                $data['phone'] = $request->input('phone');
            }
            if (Schema::hasColumn($tableName, 'role')) {
                $data['role'] = $request->input('role', 'Cashier');
            }
            if (Schema::hasColumn($tableName, 'salary')) {
                $data['salary'] = $request->input('salary', 0);
            }

            DB::table($tableName)->insert($data);
        }
        return redirect()->route('staffs.index')->with('success', 'បានបន្ថែមបុគ្គលិកថ្មីជោគជ័យ!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'មានបញ្ហាមិនអាចរក្សាទុកបាន៖ ' . $e->getMessage());
    }
})->name('staffs.store');

Route::get('/staffs/{id}/edit', function ($id) {
    $staff = null;
    try {
        $tableName = getStaffTableName();
        if ($tableName) {
            $staff = DB::table($tableName)->where('id', $id)->first();
        }
    } catch (\Exception $e) {}

    if (!$staff) {
        return redirect()->route('staffs.index')->with('error', 'រកមិនឃើញទិន្នន័យបុគ្គលិកនេះទេ!');
    }

    return view('staffs.edit', compact('staff'));
})->name('staffs.edit');

Route::put('/staffs/{id}', function (Request $request, $id) {
    try {
        $tableName = getStaffTableName();

        if ($tableName) {
            $data = [
                'name'       => $request->input('name'),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn($tableName, 'phone')) {
                $data['phone'] = $request->input('phone');
            }
            if (Schema::hasColumn($tableName, 'role')) {
                $data['role'] = $request->input('role', 'Cashier');
            }
            if (Schema::hasColumn($tableName, 'salary')) {
                $data['salary'] = $request->input('salary', 0);
            }

            DB::table($tableName)->where('id', $id)->update($data);
        }
        return redirect()->route('staffs.index')->with('success', 'បានកែប្រែទិន្នន័យបុគ្គលិកជោគជ័យ!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'មានបញ្ហាមិនអាចកែប្រែបាន៖ ' . $e->getMessage());
    }
})->name('staffs.update');

Route::delete('/staffs/{id}', function ($id) {
    try {
        $tableName = getStaffTableName();
        if ($tableName) {
            DB::table($tableName)->where('id', $id)->delete();
        }
        return redirect()->route('staffs.index')->with('success', 'បានលុបទិន្នន័យបុគ្គលិករួចរាល់!');
    } catch (\Exception $e) {
        return redirect()->route('staffs.index')->with('error', 'មិនអាចលុបបានទេ៖ ' . $e->getMessage());
    }
})->name('staffs.destroy');

// ==========================================
// ANALYTICS & CHARTS
// ==========================================
Route::get('/analytics', function () {
    $salesLast7Days = collect();
    $revenueColumn = getOrdersRevenueColumn();

    for ($i = 6; $i >= 0; $i--) {
        $date = Carbon::now()->subDays($i)->format('Y-m-d');
        $dayLabel = Carbon::now()->subDays($i)->format('D, d M');
        
        $total = 0;
        if ($revenueColumn) {
            $total = DB::table('orders')
                ->whereDate('created_at', $date)
                ->sum($revenueColumn) ?? 0;
        }
        
        $salesLast7Days->push([
            'day' => $dayLabel,
            'total' => $total
        ]);
    }

    $topProducts = collect();
    if (Schema::hasTable('order_items')) {
        $nameCol = Schema::hasColumn('order_items', 'product_name') ? 'product_name' : (Schema::hasColumn('order_items', 'name') ? 'name' : 'product_name');
        $qtyCol = Schema::hasColumn('order_items', 'quantity') ? 'quantity' : (Schema::hasColumn('order_items', 'qty') ? 'qty' : 'quantity');

        $topProducts = DB::table('order_items')
            ->select($nameCol . ' as product_name', DB::raw("SUM($qtyCol) as total_qty"))
            ->groupBy($nameCol)
            ->orderByDesc('total_qty')
            ->take(5)
            ->get();
    }

    return view('analytics', compact('salesLast7Days', 'topProducts'));
})->name('analytics.index');

// ==========================================
// SETTINGS
// ==========================================
Route::get('/settings', function () {
    $setting = null;
    try {
        $setting = Schema::hasTable('settings') ? DB::table('settings')->first() : null;
    } catch (\Exception $e) {}
    return view('settings', compact('setting'));
})->name('settings.index');

Route::post('/settings', function (Request $request) {
    try {
        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->first();
            if ($setting) {
                DB::table('settings')->where('id', $setting->id)->update([
                    'store_name' => $request->input('store_name'),
                    'address'    => $request->input('address'),
                    'updated_at' => now(),
                ]);
            } else {
                DB::table('settings')->insert([
                    'store_name' => $request->input('store_name'),
                    'address'    => $request->input('address'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
        return redirect()->back()->with('success', 'ការកំណត់ត្រូវបានរក្សាទុក!');
    } catch (\Exception $e) {
        return redirect()->back()->with('error', 'មានបញ្ហាមិនអាចរក្សាទុកបាន៖ ' . $e->getMessage());
    }
})->name('settings.update');