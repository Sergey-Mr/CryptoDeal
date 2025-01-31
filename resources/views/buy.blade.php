<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buy: ') }} {{ $symbol}} - {{ $name }} - {{ $price }}

        </h2>

       
    </x-slot>

    <div class = "container">
        <div class="operational-field" style="display: flex; flex-direction: column; align-items: center; margin-top: 2%;">
            <!-- Your operational field goes here -->
            @if ($userHasCurrency)
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight" style="margin-bottom: 3%;">
                    {{ __('You own: ') }} {{ $userCurrencyAmount }}
                </h2>
        
            @endif
            <label for="quantity" id="amount" style="color: white">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" value ="1" oninput="updateEstimate()">
            <p id="estimate" style="color: grey"></p>

            <div class="buy-sell">
                <!-- Buy button -->
                <form method="GET" action="{{ route('buy.purchase') }}">
                    @csrf
                    <input type="hidden" name="symbol" value="{{ $symbol }}">
                    <input type="hidden" name="name" value="{{ $name }}">
                    <input type="hidden" name="price" value="{{ $price }}">
                    <input type="hidden" id="hiddenQuantity" name="quantity" value="">

                    @if (Auth::user()->balance >= $price)
                    <x-secondary-button type="submit">
                                {{ __('Buy') }}
                    </x-secondary-button>
                    @else
                    <x-secondary-button type="submit" disabled>
                        {{ __('Buy') }}
                    </x-secondary-button>
                    @endif
                </form>

                <!-- Sell button -->
                <form method="GET" action="{{ route('buy.sell') }}">
                    @csrf
                    <input type="hidden" name="symbol" value="{{ $symbol }}">
                    <input type="hidden" name="name" value="{{ $name }}">
                    <input type="hidden" name="price" value="{{ $price }}">
                    <input type="hidden" id="hiddenQuantity2" name="quantity" value="">

                    @if ($userHasCurrency)
                    <x-secondary-button type="submit">
                        {{ __('Sell') }}
                    </x-secondary-button>
                    @else
                    <x-secondary-button type="submit" disabled>
                        {{ __('Sell') }}
                    </x-secondary-button>
                    @endif
                </form>

            </div>

            <x-secondary-button  id="set-amount" type="button" class="btn btn-primary" style="margin-top: 4%;">
                Sell all
            </x-secondary-button >

        
            <div class="transactions">
                <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <h2 class="text-lg font-semibold mb-4"><b>History: </b></h2>
                        @php
                            $currentDay = null;
                        @endphp

                        @foreach ($purchases as $purchase)
                            @php
                                $dayOfPurchase = $purchase->created_at->format('Y-m-d');
                            @endphp

                            @if ($dayOfPurchase != $currentDay)
                                @if ($currentDay != null)
                                    </div> <!-- Close the previous day's div -->
                                @endif
                                <hr>
                                <div class="day"> <!-- Start a new day's div -->
                                <h3><b><i>{{ $purchase->created_at->format('F j, Y') }}</i></b></h3>
                                @php
                                    $currentDay = $dayOfPurchase;
                                @endphp
                            @endif
                            <hr class="dashed">
                            <div class="transaction">
                                <p style="color: rgb(175, 175, 175)">Time: {{ $purchase->created_at->format('H:i:s') }}</p>
                                <p style="color: rgb(175, 175, 175)">Quantity: {{ $purchase->quantity }}</p>
                                @if ($purchase->quantity == 1)
                                    <p><b>Price: {{ number_format($purchase->price_per_unit, 3, '.', ' ') }}</b></p>
                                @else
                                    <p>Price: {{ number_format($purchase->price_per_unit, 3, '.', ' ') }}</p>
                                @endif

                                @if ($purchase->operation == 1)
                                    <p style="color: green">Operation: Buy</p>
                                @else
                                    <p style="color: red">Operation: Sell</p>
                                @endif

                                @if ($purchase->quantity > 1)
                                    <p><b>Total cost: {{ number_format($purchase->total_cost, 3, '.', ' ') }}<b></p>
                                @endif
                            </div>
                        @endforeach

                        @if ($currentDay != null)
                            </div> <!-- Close the last day's div -->
                        @endif
                    </div>
                </div>
            </div>
        </div>
            
        <div class="graph" style="width: 1480px;">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg" style="width: 100%;">
                <div class="p-6 text-gray-900 dark:text-gray-100" style="width: 100%;">
                    <h3 class="text-lg font-semibold mb-4">{{ $name }} graph:</h3>
                    <div class="p-4 flex flex-col items-center bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg" style="height:700px; width: 100%;">
                        <!-- TradingView Widget BEGIN -->
                        <div class="tradingview-widget-container" style="height:100%;width:100%">
                          <div class="tradingview-widget-container__widget" style="height:calc(100% - 32px);width:100%"></div>
                          <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text"></span></a></div>
                          @php
                            $symbol = $symbol . 'USD';
                          @endphp
                          <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
                        
                        {
                          "autosize": false,
                          "symbol": "{{ $symbol }}",
                          "interval": "D",
                          "timezone": "Etc/UTC",
                          "theme": "dark",
                          "style": "1",
                          "locale": "en",
                          "enable_publishing": false,
                          "allow_symbol_change": false,
                          "calendar": false,
                          "support_host": "https://www.tradingview.com"
                        }
                          </script>
                          
                        </div>
                        <!-- TradingView Widget END -->
                    </div>
                    
                </div>
            </div>
        </div>

    </div>
    
    
</x-app-layout>
<script>
    function updateEstimate(){
        var amount = document.getElementById('quantity').value;
        var amount2 = amount;
        var estimatedPrice = amount * {{ $price }};
        //console.log(estimatedPrice);
        //console.log('quantity:', document.querySelector('input[name="quantity"]').value);
        document.getElementById("estimate").innerHTML = "Estimated Price: " + estimatedPrice;
        document.getElementById("hiddenQuantity").value = amount;
        document.getElementById("hiddenQuantity2").value = amount;
    }

    updateEstimate();

</script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
    #timeframe {
        background-color: #111827;
        color: white; 
        border-radius: 5px; 
        font-weight: bold; 
    }
    #quantity{
        background-color: #1f2937; 
        color: white;
        border-radius: 5px; 
        font-weight: bold; 
    }
    .container {
        display: flex;
    }
    .operational-field {
        flex: 0 0 20%; /* operation field takes up 20% of the container */
        align-items : center;
    }
    .graph {
        flex: 1; /* This makes the graph take up the rest of the container */
    }
    .buy-sell {
        display: flex;
        gap: 10px; /* Adjust this to change the space between the buy and sell buttons */
    }

    .transactions {
        width: 100%; 
        padding-left: 12%; 
        padding-right: 12%; 
    }

    hr.dashed {
        border-top: 1px dashed;
        border-bottom: none;
    }

    /*hr.gradient {
      height: 3px;
      border: none;
      border-radius: 6px;
      background: linear-gradient(
        90deg,
        rgba(147, 31, 242) 0%, 
        rgba(197, 66, 245) 21%,
        rgba(237, 0, 229) 51%,
        rgba(232, 5, 190) 100%
        rgba(187, 0, 255) 0%,
        rgba(253, 193, 324) 100%
      );
    }*/

</style>

<script>
    document.getElementById('set-amount').addEventListener('click', function() {
        document.getElementById('quantity').value = {{ $userCurrencyAmount }};
        document.getElementById('hiddenQuantity2').value = {{ $userCurrencyAmount }};
    });
</script>