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
        $response = Http::withHeaders([
            'QC-Access-Key' => 'YNES0LMM99FXSJ8N4NEO',
            'QC-Secret-Key' => 'Q9i9cbKHsD9oCJpzNFW8FLZkoQzNm2howiRF45EkuDviFKze',
        ])->get('https://quantifycrypto.com/api/v1/coins/percent-change');

        $response2 = Http::withHeaders([
            'QC-Access-Key' => 'YNES0LMM99FXSJ8N4NEO',
            'QC-Secret-Key' => 'Q9i9cbKHsD9oCJpzNFW8FLZkoQzNm2howiRF45EkuDviFKze',
        ])->get('https://quantifycrypto.com/api/v1/coins/prices');
    
        $result = json_decode($response->body(), true); // Decode the JSON string into an array

        $prices = json_decode($response2->body(), true); // Decode the JSON string into an array

        if ($response->successful()) {
            $this->info(json_encode($result));
            $this->info(json_encode($prices));
        } else {
            $this->error('Error: ' . $response->status());
        }

        Cache::put('crypto_results', $result, 10); // Cache the data for 60 minutes
        Cache::put('crypto_prices', $prices, 10); // Cache the data for 60 minutes

        //$this->info(print_r($result, true));
        // Now $result contains the data from the API.
    }
}
