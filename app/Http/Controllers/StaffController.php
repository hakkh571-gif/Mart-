<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class StaffController extends Controller
{
    // 1. បង្ហាញបញ្ជីបុគ្គលិក
    public function index()
    {
        $tableName = Schema::hasTable('staffs') ? 'staffs' : 'table_staff';
        $staffs = DB::table($tableName)->get();

        return view('staffs.index', compact('staffs'));
    }

    // 2. បង្ហាញ Form បង្កើតបុគ្គលិក
    public function create()
    {
        return view('staffs.create');
    }

    // 3. រក្សាទុកទិន្នន័យបុគ្គលិកថ្មី
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'position' => 'nullable|string|max:255',
            'phone'    => 'nullable|string|max:50',
            'salary'   => 'nullable|numeric',
        ]);

        $tableName = Schema::hasTable('staffs') ? 'staffs' : 'table_staff';

        DB::table($tableName)->insert([
            'name'       => $request->name,
            'position'   => $request->position ?? 'បុគ្គលិក',
            'phone'      => $request->phone,
            'salary'     => $request->salary ?? 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('staffs.index')->with('success', 'បន្ថែមបុគ្គលិកជោគជ័យ!');
    }

    // 4. លុបទិន្នន័យបុគ្គលិក
    public function destroy($id)
    {
        $tableName = Schema::hasTable('staffs') ? 'staffs' : 'table_staff';
        
        DB::table($tableName)->where('id', $id)->delete();

        return redirect()->route('staffs.index')->with('success', 'លុบบុគ្គលិកជោគជ័យ!');
    }
}