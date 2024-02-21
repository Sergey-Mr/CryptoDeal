<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;

class PurchaseController extends Controller
{
    public function buyView(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        return view('buy', compact('symbol', 'price', 'name'));
    }

    public function purchase(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');
        error_log(Auth::user()->balance);
        $totalCost = $price * $quantity;

        if (Auth::user()->balance <= $totalCost) {
            //error_log('Insufficient funds');
            return redirect()->route('buy.purchase')->with('error', 'Insufficient funds');
        }
        else{
            //error_log('Sufficient funds');
            $purchase = new Purchase;
            $purchase->user_id = Auth::user()->id;
            $purchase->symbol = $symbol;
            $purchase->name = $name;
            $purchase->price_per_unit = $price;
            $purchase->quantity = $quantity;
            $purchase->total_cost = $totalCost;
            $purchase->operation = 1;
            $purchase->save();
        
            Auth::user()->balance -= $totalCost;
            Auth::user()->save();
        
            return redirect()->route('dashboard')->with('success', 'Purchase successful');
        }

        //return view('dashboard');
    }

    public function sell(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        return view('trading', compact('symbol', 'price', 'name'));

    }
}

