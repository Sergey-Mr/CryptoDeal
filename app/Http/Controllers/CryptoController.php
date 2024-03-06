<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Artisan;

class CryptoController extends Controller
{
    public function index()
    {
        Artisan::call('command:fetchCryptoPrices');
        $cryptocurrencies = Cache::get('crypto_results', []);
    
        $prices = Cache::get('crypto_prices', []);

        return view('trading', compact('cryptocurrencies', 'prices'));
    }

    public function dashboard()
    {
        Artisan::call('command:fetchCryptoPrices');
    
        $prices = Cache::get('crypto_prices', []);
        return view('dashboard', compact('prices'));
    }
}
