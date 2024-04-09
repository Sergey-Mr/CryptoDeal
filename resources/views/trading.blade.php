<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <script defer src="https://www.livecoinwatch.com/static/lcw-widget.js"></script> <div class="livecoinwatch-widget-5" lcw-base="USD" lcw-color-tx="#999999" lcw-marquee-1="coins" lcw-marquee-2="coins" lcw-marquee-items="30" ></div>

            <!-- TradingView Widget END -->
        </h2>
    </x-slot>

    <div class="py-12" style="text-align: right; margin-bottom: 20px;">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
          <!-- Add select elements for the filters -->
            <x-dropdown align="right" width="48">
                  <x-slot name="trigger">
                      <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none transition ease-in-out duration-150">
                          <div>Sort by</div>
                          <div class="ms-1">
                              <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                  <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                              </svg>
                          </div>
                      </button>
                  </x-slot>
                  <x-slot name="content">
                      <x-dropdown-link :href="route('trading.price.ascending')">
                        Price Acsending
                      </x-dropdown-link>
                      <x-dropdown-link :href="route('profile.edit')">
                        Price Descending
                      </x-dropdown-link>
                      <x-dropdown-link :href="route('profile.edit')">
                        % Acesnding
                      </x-dropdown-link>
                      <x-dropdown-link :href="route('profile.edit')">
                        % Descending
                      </x-dropdown-link>
                  </x-slot>

            </x-dropdown>

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
              
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <table style="width: 100%">
                      <thead>
                          <tr class = "text-center" style="font-size: 1.2em;">
                              <th>Symbol</th>
                              <th>Name</th>
                              <th>Current Price</th>
                              <th>Percent Change 24h</th>
                              <th>Favourite</th>
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
                          <tr style="font-size: 1.15em; line-height: 2;">
                              <td>
                                <form class="text-center" method="POST" action="{{ route('buy.view') }}">
                                    @csrf
                                    <!--<td class="text-center">{{ $cryptocurrency['coin_symbol'] }}</td>-->
                                    <input type="hidden" name="symbol" value="{{ $cryptocurrency['coin_symbol'] }}">
                                    <input type="hidden" name="name" value="{{ $cryptocurrency['coin_name'] }}">
                                    <input type="hidden" name="price" value="{{ $price['coin_price'] }}">
                                    <button type="submit" class="btn btn-link" style="text-decoration: underline; ">{{ $cryptocurrency['coin_symbol'] }}</button>
                                </form>
                              </td>

                              <td class="text-center">{{ $cryptocurrency['coin_name'] }}</td>
                              <!--<td class="text-center">{{ $price['coin_price'] }}</td>-->
                              <td class="text-center">{{ number_format($price['coin_price'], 5) }}</td>
                              
                              <td class="text-center">
                                  @if ($cryptocurrency['percent_change_24h'] >= 0)
                                      <span class="text-success" style="color: green;">+{{ $cryptocurrency['percent_change_24h'] }}%</span>
                                  @else
                                      <span class="text-danger" style="color: red;">{{ $cryptocurrency['percent_change_24h'] }}%</span>
                                  @endif
                              </td>
                              <td class="text-center">
                                <form class="text-center" method="POST" action="{{ route('save') }}">
                                    @csrf
                                    <!--<td class="text-center">{{ $cryptocurrency['coin_symbol'] }}</td>-->
                                    <input type="hidden" name="symbol" value="{{ $cryptocurrency['coin_symbol'] }}">
                                    <input type="hidden" name="name" value="{{ $cryptocurrency['coin_name'] }}">
                                    <input type="hidden" name="price" value="{{ $price['coin_price'] }}">
                                    <button type="submit" class="btn btn-link" style="text-decoration: underline; ">Save</button>
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