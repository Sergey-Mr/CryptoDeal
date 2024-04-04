<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            <!--{{ __('Dashboard') }}-->
            <script defer src="https://www.livecoinwatch.com/static/lcw-widget.js"></script> <div class="livecoinwatch-widget-5" lcw-base="USD" lcw-color-tx="#999999" lcw-marquee-1="coins" lcw-marquee-2="coins" lcw-marquee-items="30" ></div>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="flex flex-row dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __("Current Balance") }}</h3>
                    <p class="text-xl">{{ number_format(Auth::user()->balance, 0, '', ' ') }} USD</p>
                </div>
                <div class="p-6 text-gray-900 dark:text-gray-100" id="total-assets"></div>
                <div class="p-6 text-gray-900 dark:text-gray-100" id="percent-change"></div>
            </div>
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">My Portfolio</h3>
                    <div class="flex flex-wrap overflow-hidden shadow-sm">
                        <div class="w-1/4 p-4 flex flex-col items-center bg-white dark:bg-gray-900 shadow-sm rounded-lg" style="overflow-x:auto;">
                            <table class="table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Currency</th>
                                        <th class="px-4 py-2">Purchased value (USD)</th>
                                        <th class="px-4 py-2">Current value (USD)</th>
                                        <th class="px-4 py-2">Amount</th>
                                    </tr>
                                </thead>
                                <tbody id="table-body">
                                </tbody>
                            </table>
                        </div>
                        <div class="w-3/4 p-4 lg:w-2/3 bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg" style="margin-left:0.5rem;">
                            <div class="flex justify-center">
                                <canvas id="portfolio-chart" width="400" height="400"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">History</h3>
                    <div class="p-4 flex flex-col items-center bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg">
                        <canvas id="history-chart" style="width:100%;max-width:1000px"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    @php
        // Current prices for all cryptocurrencies
        $prices = $prices ?? Cache::get('crypto_prices', []);

        //Create a return string for the current prices
        $currentreturnString = "";

        //iterate through and at coin name and coin price
        for($i=0;$i<count($prices);$i++){
            if ($i==(count($prices)-1)){ //if its the last one don't add a comma
                $currentreturnString = $currentreturnString . $prices["".$i]["coin_name"] . "|" . $prices["".$i]["coin_price"] ;
            } else {
                $currentreturnString = $currentreturnString . $prices["".$i]["coin_name"] . "|" . $prices["".$i]["coin_price"] . ",";
            }
        }
    @endphp
    
    document.addEventListener('DOMContentLoaded', function () {
        //send it to javascript using JSON then unpack and simplify repeated rows

        @php
        //Requests data from database
        //Gets current user id 
        //Probably should be done using username instead but couldn't figure it out
        $userID = Auth::id();
        $data = DB::table("users")->join("purchases", "users.id", "=", "purchases.user_id") -> where("users.id", "=", $userID)->select("purchases.name", "purchases.quantity", "purchases.operation", "purchases.price_per_unit")->get();

        //Splits the data into the currencies and how many of each currency there is
        $currencies = $data -> pluck("name");
        $values = $data -> pluck("quantity");
        $operation = $data -> pluck("operation");
        $purchased_price = $data -> pluck("price_per_unit");

        //Get user current cash balance
        $cash = DB::table("users") -> where("users.id", "=", $userID) -> select("balance") -> get();
        $cash = $cash -> pluck("balance");
        
        // create a string to format to send to javascript
        $userreturnString = "";

        for($i=0;$i<count($currencies);$i++){
            if ($i==(count($currencies)-1)){ //if its the last one don't add a comma
                $userreturnString = $userreturnString . $currencies["".$i] . "|" . $values["".$i] . "|" . $operation["".$i] . "|" . $purchased_price["".$i];
            } else {
                $userreturnString = $userreturnString . $currencies["".$i] . "|" . $values["".$i] . "|" . $operation["".$i] . "|" . $purchased_price["".$i] . ",";
            }
        }
        @endphp
        //get cash value from php
        var cash = '<?= $cash ?>';
        //Trim the brackets that come with the string
        cash = parseInt(cash.substring(1, cash.length-1));
        
        //Get the current price data from php
        var currentPriceData = '<?= $currentreturnString ?>';

        //Split the data into an array
        var currentPriceDataArray = currentPriceData.split(",");

        //Create an array for access usning currency names
        var currentPricesDict = {};

        for(let i=0; i<currentPriceDataArray.length; i++){
            var element = currentPriceDataArray[i].split("|");
            currentPricesDict[element[0]] = element[1];
        }

        //Get the data from the php for the user
        var dataSent = '<?= $userreturnString ?>';

        //Format the data into an array
        var dataSentArray = dataSent.split(",");

        //Iterate through each element and split that into currency and amount
        //Use a dictionary to simplify repeated occurrences
        var dataDict = {};
        var purchased_valueDict = {};

        for(let i=0; i<dataSentArray.length;i++){
            var element = dataSentArray[i].split("|");
            //If the value to add is zero don't add it to the dictionary or 
            //if the coin is already in the dictionary
            if (!(element[0] in dataDict) && !(element[1]==0)){ 
                dataDict[element[0]] = 0;
                purchased_valueDict[element[0]] = 0;
            }

            if (!(element[1]==0)){
                if (dataDict[element[0]] + (parseInt(element[1]) * parseInt(element[2])) == 0){
                    
                    delete dataDict[element[0]];
                    delete purchased_valueDict[element[0]];
                } else {
                    dataDict[element[0]] = dataDict[element[0]] + (parseInt(element[1]) * parseInt(element[2]));
                    purchased_valueDict[element[0]] = element[3]
                }
            }
        }

        //Split data insto currencies and amounts
        var AmountData = Object.values(dataDict);
        var CurrencyData = Object.keys(dataDict);
        var purchased_valueData = Object.values(purchased_valueDict); 
        
        var data = [];

        // Create an array with values of the currencies at correspding indexs
        // Then can easily add it to the dictionary below

        //NOTE: data must be a list of dictionary items for the pie chart to work

        for (let i=0; i<AmountData.length;i++) { 
            tempDict = {label: CurrencyData[i], value: currentPricesDict[CurrencyData[i]], amount: AmountData[i], purchased_value: purchased_valueData[i]}
            data.push(tempDict)
            //Add value to total assest
            cash += currentPricesDict[CurrencyData[i]] * AmountData[i];
        }

        //Getting the value to two decimal places at most
        cash = Math.round(cash * 100) / 100


        var totalAssetsSection = document.getElementById('total-assets');
        totalAssetsSection.innerHTML = `
            <h3 class="text-lg font-semibold mb-4">{{ __("Total Assets") }}</h3>
            <p class="text-xl">${cash} USD</p>
        `;

        // Calculate the percentage of total assets from 100
        var cashPercent = cash * 100 / 100000;
        // Calculate the difference from 100
        if (cashPercent > 100) {
            var percentDifference = cashPercent - 100;
            var percentSection = document.getElementById('percent-change');
            percentSection.innerHTML = `
                <h3 class="text-lg font-semibold mb-4">{{ __("Growth") }}</h3>
                <p class="text-xl" style="color: green;">+${percentDifference.toFixed(3)} %</p>
            `;
        } else {
            var percentDifference = 100 - cashPercent;
            var percentSection = document.getElementById('percent-change');
            percentSection.innerHTML = `
                <h3 class="text-lg font-semibold mb-4">{{ __("Growth") }}</h3>
                <p class="text-xl" style="color: red;">-${percentDifference.toFixed(3)} %</p>
            `;
        }
        

        var ctx = document.getElementById('portfolio-chart').getContext('2d');
        var portfolioChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(data => data.label),
                datasets: [{
                    data: data.map(data => data.value * data.amount),
                    //Generate colours based of number of currencies
                    //TODO: Can associate colours with a coin in the database
                    backgroundColor: [
                        '#003f5c',
                        '#2f4b7c',
                        '#665191',
                        '#a05195',
                        '#d45087',
                        '#f95d6a',
                        '#ff7c43',
                        '#ffa600',

                        // '#FF6384',
                        // '#36A2EB',
                        // '#FFCE56',
                        // '#4BC0C0',
                        // '#9966FF',
                        // '#0066FF',
                        // '#5566FF',
                        // Add more colors when needed
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                legend: {
                    display: false
                }
            }
        });
        // Define historySampleData (this should be changed to the appropriate data from the database)
        var historySampleData = [
            { year: 2024, month: 01, day: 01, totalAssets: 8500 },
            { year: 2024, month: 01, day: 10, totalAssets: 8512 },
            { year: 2024, month: 01, day: 15, totalAssets: 8505 },
            { year: 2024, month: 01, day: 17, totalAssets: 8496 },
            { year: 2024, month: 01, day: 20, totalAssets: 8530 },
            { year: 2024, month: 01, day: 28, totalAssets: 8643 },
            { year: 2024, month: 02, day: 03, totalAssets: 8697 },
            { year: 2024, month: 02, day: 04, totalAssets: 8704 }
        ];

        // Generates line chart using historySampleData
        var historyChart = new Chart("history-chart", {
            type: "line",
            data: {
                labels: historySampleData.map(data => data.day.toString() + "/" + data.month.toString() + "/" + data.year.toString()),
                datasets: [{
                    label: "Total Assets",
                    data: historySampleData.map(data => data.totalAssets),
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

        var tableBody = document.getElementById('table-body');
        data.forEach(function (data) {
            var row = document.createElement('tr');
            row.innerHTML = `
                <td class="border px-4 py-2">${data.label}</td>
                <td class="border px-4 py-2">${data.purchased_value}</td>
                <td class="border px-4 py-2">${data.value}</td>
                <td class="border px-4 py-2">${data.amount}</td>
            `;
            tableBody.appendChild(row);
        });
    });
</script>
</x-app-layout>
