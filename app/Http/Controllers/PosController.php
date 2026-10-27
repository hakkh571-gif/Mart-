<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;

class PosController extends Controller
{
    // បង្ហាញអេក្រង់ POS
    public function index()
    {
        $products = collect();

        try {
            // ទាញយកបញ្ជីភេសជ្ជៈ/ទំនិញដែលមានក្នុង Database
            if (class_exists(Product::class) && Schema::hasTable((new Product)->getTable())) {
                $products = Product::all();
            } elseif (Schema::hasTable('tbl_food')) {
                $products = DB::table('tbl_food')->get();
            } elseif (Schema::hasTable('products')) {
                $products = DB::table('products')->get();
            }
        } catch (\Exception $e) {
            Log::error("POS Controller Index Error: " . $e->getMessage());
            session()->flash('error', 'មិនអាចតភ្ជាប់ទៅកាន់ Database បានទេ! សូមពិនិត្យមើល MySQL Server');
        }

        return view('pos.index', compact('products'));
    }

    // រក្សាទុកការលក់ (Checkout/Pay)
    public function store(Request $request)
    {
        // ទទួលយក Array ទំនិញ (គាំទ្រទាំង cart និង items)
        $cart = $request->input('cart', $request->input('items', []));

        if (empty($cart)) {
            return response()->json(['success' => false, 'message' => 'សូមជ្រើសរើសភេសជ្ជៈយ៉ាងហោចណាស់ ១!'], 400);
        }

        DB::beginTransaction();
        try {
            $totalAmount = 0;

            // គណនាតម្លៃសរុប និងដោះស្រាយ Key quantity / qty
            foreach ($cart as $item) {
                $qty = $item['quantity'] ?? $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
                $totalAmount += $price * $qty;
            }

            // ១. រៀបចំទិន្នន័យសម្រាប់ Insert ចូល Table orders
            $orderData = [
                'invoice_no' => 'INV-' . time(),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (Schema::hasColumn('orders', 'subtotal')) {
                $orderData['subtotal'] = $request->input('subtotal', $totalAmount);
            }

            if (Schema::hasColumn('orders', 'total_price')) {
                $orderData['total_price'] = $totalAmount;
            }
            if (Schema::hasColumn('orders', 'total_amount')) {
                $orderData['total_amount'] = $totalAmount;
            }
            if (Schema::hasColumn('orders', 'total')) {
                $orderData['total'] = $totalAmount;
            }

            if (Schema::hasColumn('orders', 'discount')) {
                $orderData['discount'] = $request->input('discount', 0);
            }
            if (Schema::hasColumn('orders', 'tax')) {
                $orderData['tax'] = $request->input('tax', 0);
            }
            if (Schema::hasColumn('orders', 'payment_method')) {
                $orderData['payment_method'] = $request->input('payment_method', 'cash');
            }
            if (Schema::hasColumn('orders', 'status')) {
                $orderData['status'] = $request->input('status', 'completed');
            }

            // Insert ចូល Table orders
            $orderId = DB::table('orders')->insertGetId($orderData);

            // ២. បញ្ចូល Order Items & កាត់ស្តុក
            foreach ($cart as $item) {
                $qty   = $item['quantity'] ?? $item['qty'] ?? 1;
                $price = $item['price'] ?? 0;
                $name  = $item['name'] ?? $item['product_name'] ?? 'Item';
                $pId   = $item['id'] ?? $item['product_id'] ?? null;
                $image = $item['image'] ?? null;

                $itemData = [
                    'order_id'     => $orderId,
                    'product_name' => $name,
                    'price'        => $price,
                    'quantity'     => $qty,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ];

                // បន្ថែម product_id ប្រសិនបើ Column មានក្នុង Table order_items
                if ($pId && Schema::hasColumn('order_items', 'product_id')) {
                    $itemData['product_id'] = $pId;
                }

                // បន្ថែម image ប្រសិនបើ Column មានក្នុង Table order_items
                if ($image && Schema::hasColumn('order_items', 'image')) {
                    $itemData['image'] = $image;
                }

                if (Schema::hasColumn('order_items', 'subtotal')) {
                    $itemData['subtotal'] = $price * $qty;
                }
                if (Schema::hasColumn('order_items', 'total')) {
                    $itemData['total'] = $price * $qty;
                }

                DB::table('order_items')->insert($itemData);

                // កាត់ស្តុក
                if ($pId) {
                    if (Schema::hasTable('tbl_food') && Schema::hasColumn('tbl_food', 'stock')) {
                        DB::table('tbl_food')->where('id', $pId)->decrement('stock', $qty);
                    } elseif (Schema::hasTable('products') && Schema::hasColumn('products', 'stock')) {
                        DB::table('products')->where('id', $pId)->decrement('stock', $qty);
                    }
                }
            }

            DB::commit();

            return response()->json([
                'success'  => true,
                'message'  => 'រក្សាទុកការលក់ជោគជ័យ!',
                'order_id' => $orderId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'មានបញ្ហា៖ ' . $e->getMessage()], 500);
        }
    }
}