<?php

namespace App\Http\Controllers\Superadmin;

use App\Http\Controllers\Controller;
use App\Models\Cafe;
use Illuminate\Http\Request;

class CafeController extends Controller
{
    public function index()
    {
        return Cafe::all();
    }

    public function store(Request $request)
    {
        return Cafe::create($request->validate([
            'name'=>'required',
            'address'=>'nullable',
            'phone'=>'nullable'
        ]));
    }
}
