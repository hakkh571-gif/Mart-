<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class SettingController extends Controller
{
    // ១. បង្ហាញព័ត៌មានហាង
    public function index()
    {
        if (Schema::hasTable('settings')) {
            $setting = DB::table('settings')->first();
        } else {
            $setting = (object) [
                'store_name' => 'Fresh Beverage Mart',
                'address'    => 'រាជធានីភ្នំពេញ',
                'phone'      => '012 345 678'
            ];
        }

        return view('settings', compact('setting'));
    }

    // ២. រក្សាទុក ឬ អាប់ដេតព័ត៌មានហាង
    public function update(Request $request)
    {
        $request->validate([
            'store_name' => 'required|string|max:255',
            'address'    => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:50',
        ]);

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function ($table) {
                $table->id();
                $table->string('store_name')->default('Fresh Beverage Mart');
                $table->string('address')->nullable();
                $table->string('phone')->nullable();
                $table->timestamps();
            });
        }

        DB::table('settings')->updateOrInsert(
            ['id' => 1],
            [
                'store_name' => $request->store_name,
                'address'    => $request->address,
                'phone'      => $request->phone,
                'updated_at' => now(),
            ]
        );

        return redirect()->back()->with('success', 'រក្សាទុកការផ្លាស់ប្តូរជោគជ័យ!');
    }
}