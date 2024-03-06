<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <!--{{ __('Trading') }}-->
            <!-- TradingView Widget BEGIN -->
            <!--
            <div class="tradingview-widget-container">
              <div class="tradingview-widget-container__widget"></div>
              <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
              <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-ticker-tape.js" async>
              {
              "symbols": [
                {
                  "proName": "FOREXCOM:SPXUSD",
                  "title": "S&P 500"
                },
                {
                  "proName": "FOREXCOM:NSXUSD",
                  "title": "US 100"
                },
                {
                  "proName": "FX_IDC:EURUSD",
                  "title": "EUR to USD"
                },
                {
                  "proName": "BITSTAMP:BTCUSD",
                  "title": "Bitcoin"
                },
                {
                  "proName": "BITSTAMP:ETHUSD",
                  "title": "Ethereum"
                }
              ],
              "showSymbolLogo": true,
              "isTransparent": true,
              "displayMode": "adaptive",
              "colorTheme": "dark",
              "locale": "en"
            }
              </script>
            </div>
          -->
            <script defer src="https://www.livecoinwatch.com/static/lcw-widget.js"></script> <div class="livecoinwatch-widget-5" lcw-base="USD" lcw-color-tx="#999999" lcw-marquee-1="coins" lcw-marquee-2="coins" lcw-marquee-items="10" ></div>

            <!-- TradingView Widget END -->
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table style="width: 100%">
                      <thead>
                          <tr>
                              <th>Symbol</th>
                              <th>Name</th>
                              <th>Current Price</th>
                              <th>Percent Change</th>
                              <th>Buy/Sell</th>
                          </tr>
                      </thead>
                      <tbody>
                      @php
                          $i = 0;
                          $data = $cryptocurrencies ?? Cache::get('crypto_data', []);
                          $prices = $prices ?? Cache::get('crypto_prices', []);
                          $count = count($data);
                      @endphp

                      @while ($i < $count)
                          @php
                              $cryptocurrency = $data[$i];
                              $price = $prices[$i];
                              $index = $cryptocurrency['coin_symbol'];
                              $i++;
                          @endphp
                          <tr>
                              <td class="text-center">{{ $cryptocurrency['coin_symbol'] }}</td>
                              <td class="text-center">{{ $cryptocurrency['coin_name'] }}</td>
                              <td class="text-center">{{ $price['coin_price'] }}</td>
                              <td class="text-center">{{ $cryptocurrency['percent_change_24h'] }}%</td>
                              <td>
                                <form class="text-center" method="POST" action="{{ route('buy.view') }}">
                                    @csrf
                                    <input type="hidden" name="symbol" value="{{ $cryptocurrency['coin_symbol'] }}">
                                    <input type="hidden" name="name" value="{{ $cryptocurrency['coin_name'] }}">
                                    <input type="hidden" name="price" value="{{ $price['coin_price'] }}">
                                    <button type="submit" class="btn btn-primary">Open</button>
                                </form>
                              </td>
                            </tr>
                          @endwhile
                      </tbody>
                    </table>

                </div>
            </div>
        </div>
    </div>
    
</x-app-layout>

<script>
$(document).ready(function() {
    function fetchCryptoPrices() {
        $.get('/fetchCryptoPrices', function(data) {
            // Handle the response here
        });
    }

    // Fetch prices when the page loads
    fetchCryptoPrices();

    // Fetch prices every minute
    setInterval(fetchCryptoPrices, 60000);

});


</script>