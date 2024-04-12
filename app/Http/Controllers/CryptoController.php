<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\Purchase;
use Illuminate\Support\Facades\DB;

class CryptoController extends Controller
{
    public function index()
    {
        Artisan::call('command:fetchCryptoPrices');
        $cryptocurrencies = Cache::get('crypto_results', []);

        return view('trading', compact('cryptocurrencies'));
    }

    public function dashboard()
    {
        Artisan::call('command:fetchCryptoPrices');
        $prices = Cache::get('crypto_prices', []);

        // Show all transactions for the given currency
        $purchases = Purchase::where('user_id', auth()->id())
                     ->get();

        $purchases_hisotry = Purchase::where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->paginate(10); // 10 is the number of items per page

        // Fetch user's purchases
        // Fetch user's purchases
        $purchases = DB::table('users')
        ->join('purchases', 'users.id', '=', 'purchases.user_id')
        ->where('users.id', Auth::user()->id)
        ->select('purchases.symbol as currency', 'purchases.total_cost as total_cost', 'purchases.operation as operation_type')
        ->get();

        // Initialize arrays to hold bought and sold currencies
        $boughtCurrencies = [];
        $soldCurrencies = [];

        // Iterate over purchases and categorize them
        foreach ($purchases as $purchase) {
        if ($purchase->operation_type == 1) {
            $boughtCurrencies[] = $purchase;
        } else if ($purchase->operation_type == -1) {
            $soldCurrencies[] = $purchase;
        }
        }

        return view('dashboard', compact('prices', 'purchases', 'purchases_hisotry', 'boughtCurrencies', 'soldCurrencies'));
    }

    public function watchlist(){
        $userId = Auth::user()->id;
        $watchlist = Cryptocurrency::where('user_id', $userId)->get();

        // Get the current price of the currency
        Artisan::call('command:fetchCryptoPrices');
        $prices = Cache::get('crypto_prices', []);

        // Fetch current prices and calculate percentage change
        foreach ($watchlist as $item) {
            $currentPrice = null;
            foreach ($prices as $price) {
                if ($price['coin_symbol'] == $item->symbol) {
                    $currentPrice = $price['coin_price'];
                    break;
                }
            }

            if ($currentPrice !== null) {
                $item->current_price = $currentPrice;
                $item->percentage_change = number_format((($currentPrice - $item->price_saved) / $item->price_saved) * 100, 4);
            }
        }

        return view('watchlist', compact('watchlist'));
    }

    public function indexSortPriceAscending()
    {
        Artisan::call('command:fetchCryptoPrices');
        $cryptocurrencies = Cache::get('crypto_results', []);
        $prices = Cache::get('crypto_prices', []);
    
        // Get the 'price' values from the $prices array
        $priceValues = array_column($prices, 'coin_price');
    
        // Sort both arrays by the price values
        array_multisort($priceValues, SORT_ASC, $cryptocurrencies);

        dd($cryptocurrencies);
    
        return view('trading', compact('cryptocurrencies'));
    }
}
