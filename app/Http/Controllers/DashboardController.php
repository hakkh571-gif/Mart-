<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\User;
use App\Models\Product; // ថែម Import Product Model ប្រសិនបើមាន
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    /**
     * បង្ហាញទំព័រ Dashboard មេ
     */
    public function index()
    {
        $today = Carbon::today();

        // 1. គណនាសរុបចំណូល និងចំនួនលក់ថ្ងៃនេះ (ការពារកុំឲ្យ Error បើគ្មាន DB/Record)
        $todayRevenue = Schema::hasTable('sales') ? (Sale::whereDate('created_at', $today)->sum('total_amount') ?? 0) : 0;
        $todayCupsSold = Schema::hasTable('sales') ? (Sale::whereDate('created_at', $today)->sum('total_qty') ?? 0) : 0;
        $totalStaff = Schema::hasTable('users') ? User::count() : 0;

        // 2. ទាញយកទិន្នន័យបញ្ជីភេសជ្ជៈ
        // បើមានការលក់ក្នុង sales ទាញពី sales បើគ្មានទេ ទាញចេញពី products មកបង្ហាញ
        $dailyDrinkReport = collect();
        if (Schema::hasTable('sales') && Sale::exists()) {
            $dailyDrinkReport = Sale::latest()->take(10)->get();
        } elseif (Schema::hasTable('products')) {
            $dailyDrinkReport = DB::table('products')->latest()->take(10)->get();
        }

        return view('dashboard', compact(
            'todayRevenue',
            'todayCupsSold',
            'totalStaff',
            'dailyDrinkReport'
        ));
    }

    /**
     * ទាញយកទិន្នន័យក្រាបចំណូល ៧ ថ្ងៃចុងក្រោយ (Chart 1 - Fix Gap Dates)
     */
    public function getSalesChartData()
    {
        $dates = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $dates->put($date, 0); // Default $0
        }

        if (Schema::hasTable('sales') && Sale::exists()) {
            $salesData = Sale::select(
                    DB::raw('DATE(created_at) as date_only'),
                    DB::raw('SUM(total_amount) as total')
                )
                ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
                ->groupBy(DB::raw('DATE(created_at)'))
                ->pluck('total', 'date_only');

            foreach ($dates as $date => $defaultTotal) {
                $dates[$date] = (float) ($salesData->get($date) ?? $defaultTotal);
            }
        }

        $labels = [];
        $data = [];
        foreach ($dates as $date => $total) {
            $labels[] = Carbon::parse($date)->format('d-M');
            $data[] = $total;
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data
        ]);
    }

    /**
     * ទាញយកទិន្នន័យក្រាបទំនិញលក់ដាច់ជាងគេ (Chart 2)
     */
    public function getProductsChartData()
    {
        $labels = [];
        $data = [];

        // ករណីទី ១៖ មានទិន្នន័យលក់ក្នុង sales
        if (Schema::hasTable('sales') && Sale::exists()) {
            $productData = Sale::select(
                    'product_name',
                    DB::raw('SUM(total_qty) as total_qty')
                )
                ->whereNotNull('product_name')
                ->where('product_name', '!=', '')
                ->groupBy('product_name')
                ->orderByDesc('total_qty')
                ->limit(7)
                ->get();

            foreach ($productData as $item) {
                $labels[] = $item->product_name;
                $data[] = (int) $item->total_qty;
            }
        }

        // ករណីទី ២៖ បើគ្មានទិន្នន័យលក់ទេ ឲ្យទាញឈ្មោះភេសជ្ជៈពីតារាង products មកបង្ហាញលើ Chart ជាមុន
        if (empty($labels) && Schema::hasTable('products')) {
            $products = DB::table('products')->limit(7)->get();
            foreach ($products as $prod) {
                $labels[] = $prod->name ?? $prod->product_name;
                $data[] = (int) ($prod->stock ?? 0); // បង្ហាញចំនួនស្តុកបណ្តោះអាសន្ន
            }
        }

        return response()->json([
            'labels' => $labels,
            'data'   => $data
        ]);
    }

    /**
     * មុខងារកែប្រែទិន្នន័យភេសជ្ជៈ (AJAX Update)
     */
    public function updateProduct(Request $request)
    {
        $validated = $request->validate([
            'id'     => 'required',
            'name'   => 'required|string|max:255',
            'qty'    => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        $item = Sale::find($validated['id']);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យដែលត្រូវកែប្រែទេ!'
            ], 404);
        }

        $item->product_name = $validated['name'];
        $item->total_qty    = $validated['qty'];
        $item->total_amount = $validated['amount'];
        $item->save();

        return response()->json([
            'success' => true,
            'message' => 'បានកែប្រែទិន្នន័យដោយជោគជ័យ!'
        ]);
    }

    /**
     * មុខងារលុបទិន្នន័យភេសជ្ជៈ (AJAX Delete)
     */
    public function deleteProduct($id)
    {
        $item = Sale::find($id);

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'រកមិនឃើញទិន្នន័យដែលត្រូវលុបទេ!'
            ], 404);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'បានលុបទិន្នន័យដោយជោគជ័យ!'
        ]);
    }
}