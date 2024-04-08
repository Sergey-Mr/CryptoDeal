<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;
use App\Models\Purchase;

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

        return view('dashboard', compact('prices', 'purchases', 'purchases_hisotry'));
    }

    public function indexSortPriceAscending()
    {
        Artisan::call('command:fetchCryptoPrices');
        $cryptocurrencies = Cache::get('crypto_results', []);
        $prices = Cache::get('crypto_prices', []);
    
        // Debug: print the sizes of the arrays
        echo 'Size of $cryptocurrencies: ' . count($cryptocurrencies) . '<br>';
        echo 'Size of $prices: ' . count($prices) . '<br>';
    
        // Get the 'price' values from the $prices array
        $priceValues = array_column($prices, 'price');
    
        // Sort both arrays by the price values
        array_multisort($priceValues, SORT_ASC, $cryptocurrencies, $prices);
    
        return view('trading', compact('cryptocurrencies'));
    }
}
