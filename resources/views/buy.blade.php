<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buy: ') }} {{ $symbol}} - {{ $name }} - {{ $price }}
        </h2>
    </x-slot>

    <div class = "container">
        <div class="operational-field">
            <!-- Your operational field goes here -->
            <label for="quantity" id="amount" style="color: white">Quantity:</label>
            <input type="number" id="quantity" name="quantity" min="1" default ="1" oninput="updateEstimate()">
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
        </div>
        <div class="graph">
            <!-- Graph -->
            @php
                $client = new \GuzzleHttp\Client();

                $response = $client->request('GET', 'https://api.coingecko.com/api/v3/coins/markets?vs_currency=usd', [
                  'headers' => [
                    'x-cg-pro-api-key' => 'CG-rUzJ3iaAmq91nbQPLEYuzyvn',
                  ],
                ]);

                $body = $response->getBody();
                $data = json_decode($body, true);

                //Get id of the current coin
                foreach ($data as $coin) {
                    if ($coin['name'] == $name) {
                        $coinId = $coin['id'];
                        break;
                    }
                }

                $response = $client->request('GET', 'https://api.coingecko.com/api/v3/coins/' . $coinId . '/market_chart?vs_currency=usd&days=30', [
                  'headers' => [
                    'x-cg-pro-api-key' => 'CG-rUzJ3iaAmq91nbQPLEYuzyvn',
                  ],
                ]);
                
                $body = $response->getBody();
                $historicalData = json_decode($body, true);
            
            @endphp

            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ $name }} graph:</h3>
                    <select id="timeframe" class="btn btn-primary">
                        <option value="today">Today</option>
                        <option value="week">Week</option>
                        <option value="month">Month</option>
                    </select>
                    <div class="p-4 flex flex-col items-center bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg">
                        <canvas id="history-chart" style="width:100%;max-width:1000px"></canvas>
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
<script>
    document.getElementById('timeframe').onchange = function() {
        var timeframe = this.value;
    };

    // Generates line chart using historySampleData
    var historicalData = @json($historicalData);
    var historyChart = new Chart("history-chart", {
            type: "line",
            data: {
                labels: historicalData['prices'].map(data => new Date(data[0]).toLocaleDateString()),
                datasets: [{
                    label: "Price change",
                    data: historicalData['prices'].map(data => data[1]),
                    backgroundColor: "rgba(255,255,255,1.0)",
                    borderColor: "rgba(255,255,255,0.1)"
                }]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            // Adds dollar sign to cash values
                            callback: function(value, index, ticks) {
                                return '$' + value;
                            }
                        }
                    }
                },
                // Removes the mouse click event, to ensure that the label is not interacative
                events: ['mousemove','mouseout','touchstart','touchmove']
            }
        });
</script>

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

</style>