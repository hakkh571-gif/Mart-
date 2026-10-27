<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IndexController extends Controller
{
    public function index()
    {
        // កែមកកាន់ staffs.index ដើម្បីឱ្យវាទាញយក File resources/views/staffs/index.blade.php
        $staffs = DB::table('tbl_food')->get();
        return view('staffs.index', compact('staffs')); 
    }

    public function table()
    {
        $staffs = DB::table('tbl_food')->get();
        return view('staffs.index', compact('staffs')); 
    }

    public function create()
    {
        return view('staffs.create'); 
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'price' => 'required|numeric',
        ]);

        DB::table('tbl_food')->insert([
            'name'  => $request->name,
            'price' => $request->price,
        ]);

        return redirect()->route('staffs.index');
    }
}