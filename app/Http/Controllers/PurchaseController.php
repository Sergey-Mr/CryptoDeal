<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    public function buy(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        return view('trading', compact('symbol', 'price', 'name'));
    }

    public function sell(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        return view('trading', compact('symbol', 'price', 'name'));

    }
}
