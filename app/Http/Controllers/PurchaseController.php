<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Purchase;
use App\Models\Cryptocurrency;

class PurchaseController extends Controller
{
    public function buyView(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        $user = Auth::user();
        $userHasCurrency = Purchase::where('user_id', $user->id)
                                    ->where('symbol', $symbol)
                                    ->exists();

        $userCurrencyAmount = 0;
        if ($userHasCurrency) {
            $userCurrencyAmount = Purchase::where('user_id', $user->id)
                                           ->where('symbol', $symbol)
                                           ->get()
                                           ->sum(function($purchase) {
                                               return $purchase->quantity * $purchase->operation;
                                           });
        }

        // Show all transactions for the given currency
        $purchases = Purchase::where('user_id', auth()->id())
                     ->where('symbol', $symbol)
                     ->orderBy('created_at', 'desc')
                     ->get();
        
        return view('buy', [
            'symbol' => $symbol,
            'name' => $name,
            'price' => $price,
            'userHasCurrency' => $userHasCurrency,
            'userCurrencyAmount' => $userCurrencyAmount,
            'purchases' => $purchases
        ]);
    }

    public function purchase(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');

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
        $user = Auth::user();
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');
        $quantity = $request->input('quantity');

        $totalCost = $price * $quantity;
        $currencyQuantityUp = Purchase::where('user_id', $user->id)
                        ->where('symbol', $symbol)
                        ->where('operation', 1)
                        ->sum('quantity');

        $currencyQuantityDown = Purchase::where('user_id', $user->id)
                            ->where('symbol', $symbol)
                            ->where('operation', -1)
                            ->sum('quantity');

        $totalCurrency = $currencyQuantityUp - $currencyQuantityDown;

        if ($totalCurrency < $quantity) {
            return redirect()->route('buy.purchase')->with('error', 'Insufficient amount of currency');
        }

        else{
            $purchase = new Purchase;
            $purchase->user_id = Auth::user()->id;
            $purchase->symbol = $symbol;
            $purchase->name = $name;
            $purchase->price_per_unit = $price;
            $purchase->quantity = $quantity;
            $purchase->total_cost = $totalCost;
            $purchase->operation = -1;
            $purchase->save();
        
            Auth::user()->balance += $totalCost;
            Auth::user()->save();
        
            return redirect()->route('dashboard')->with('success', 'Purchase successful');
        }

        return view('trading', compact('symbol', 'price', 'name'));

    }

    public function save(Request $request)
    {
        $symbol = $request->input('symbol');
        $name = $request->input('name');
        $price = $request->input('price');

        $watchlistEntry = new Cryptocurrency;
        $watchlistEntry->user_id = Auth::user()->id;
        $watchlistEntry->symbol = $symbol;
        $watchlistEntry->name = $name;
        $watchlistEntry->price_saved = $price;

        $watchlistEntry->save();

        return redirect()->route('trading')->with('success', 'Saved to watchlist');
    }
}

