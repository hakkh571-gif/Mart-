<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    // ១. បង្ហាញបញ្ជីស្តុកភេសជ្ជៈ/ទំនិញទាំងអស់
    public function index()
    {
        $products = DB::table('products')->latest()->get();

        return view('products.index', compact('products'));
    }

    // ២. បង្ហាញ Form សម្រាប់បន្ថែមភេសជ្ជៈ/ទំនិញថ្មី
    public function create()
    {
        return view('products.create');
    }

    // ៣. រក្សាទុកទិន្នន័យភេសជ្ជៈ/ទំនិញថ្មីចូល Database (ព្រមទាំង Upload រូបភាព)
    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $imageName = null;

        // ពិនិត្យមើលថាតើមាន Upload រូបភាពមកដែរឬទេ
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            // បង្កើត Folder បើមិនទាន់មាន
            $destinationPath = public_path('uploads/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            // ផ្លាស់ទីរូបភាពទៅកាន់ public/uploads/products
            $file->move($destinationPath, $imageName);
        }

        DB::table('products')->insert([
            'name'       => $request->name,
            'category'   => $request->category ?? null,
            'price'      => $request->price,
            'stock'      => $request->stock ?? 0,
            'image'      => $imageName,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', 'បន្ថែមទំនិញជោគជ័យ!');
    }

    // ៤. បង្ហាញ Form សម្រាប់កែប្រែទិន្នន័យ (Edit)
    public function edit($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'រកមិនឃើញទិន្នន័យទំនិញនេះទេ!');
        }

        return view('products.edit', compact('product'));
    }

    // ៥. បច្ចុប្បន្នភាពទិន្នន័យទំនិញ និងរូបភាព (Update)
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $product = DB::table('products')->where('id', $id)->first();

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'រកមិនឃើញទិន្នន័យទំនិញនេះទេ!');
        }

        $imageName = $product->image; // រក្សារូបភាពចាស់ទុកប្រសិនបើមិនដូរថ្មី

        // ប្រសិនបើមាន Upload រូបភាពថ្មីមក
        if ($request->hasFile('image')) {
            // លុបរូបភាពចាស់ចេញពី Folder ប្រសិនបើមាន
            if ($product->image && file_exists(public_path('uploads/products/' . $product->image))) {
                unlink(public_path('uploads/products/' . $product->image));
            }

            // រៀបចំ និង Save រូបភាពថ្មី
            $file = $request->file('image');
            $imageName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            
            $destinationPath = public_path('uploads/products');
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0777, true);
            }

            $file->move($destinationPath, $imageName);
        }

        // ធ្វើបច្ចុប្បន្នភាពទិន្នន័យក្នុង DB
        DB::table('products')->where('id', $id)->update([
            'name'       => $request->name,
            'category'   => $request->category ?? null,
            'price'      => $request->price,
            'stock'      => $request->stock ?? 0,
            'image'      => $imageName,
            'updated_at' => now(),
        ]);

        return redirect()->route('products.index')->with('success', 'កែប្រែទិន្នន័យទំនិញជោគជ័យ!');
    }

    // ៦. លុបទិន្នន័យទំនិញ (ព្រមទាំងលុបរូបភាពចេញពី Folder)
    public function destroy($id)
    {
        $product = DB::table('products')->where('id', $id)->first();

        if ($product && $product->image && file_exists(public_path('uploads/products/' . $product->image))) {
            unlink(public_path('uploads/products/' . $product->image));
        }

        DB::table('products')->where('id', $id)->delete();

        return redirect()->route('products.index')->with('success', 'លុបទំនិញជោគជ័យ!');
    }
}