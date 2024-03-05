<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;


class FetchCryptoPrices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:fetchCryptoPrices';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // API request to get the cryptocurrency data
        $response = Http::withHeaders([
            'QC-Access-Key' => 'YNES0LMM99FXSJ8N4NEO',
            'QC-Secret-Key' => 'Q9i9cbKHsD9oCJpzNFW8FLZkoQzNm2howiRF45EkuDviFKze',
        ])->get('https://quantifycrypto.com/api/v1/coins/percent-change');

        // API request for the cryptocurrency prices
        $response2 = Http::withHeaders([
            'QC-Access-Key' => 'YNES0LMM99FXSJ8N4NEO',
            'QC-Secret-Key' => 'Q9i9cbKHsD9oCJpzNFW8FLZkoQzNm2howiRF45EkuDviFKze',
        ])->get('https://quantifycrypto.com/api/v1/coins/prices');
    
        // Decode the JSON string into an array
        $result = json_decode($response->body(), true); 
        $result = $result['data'] ?? Cache::get('crypto_results', []);

        $prices = json_decode($response2->body(), true);
        $prices = $prices['data'] ?? Cache::get('crypto_prices', []);

        if ($response2->successful()) {
            $this->info(json_encode($result));
            $this->info(json_encode($prices));
        } else {
            $this->error('Error: ' . $response->status());
        }

        // Cache the data for 10 minutes
        //Cache::put('crypto_results', $result, 10); 
        //Cache::put('crypto_prices', $prices, 10); 

        // If data present, update cache
        if ($response->successful()) {
            Cache::flush();
            Cache::forever('crypto_results', $result); 
            Cache::forever('crypto_prices', $prices);
        } else {
        }

        
    }
}
