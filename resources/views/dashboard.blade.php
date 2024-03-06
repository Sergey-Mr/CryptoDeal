<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">{{ __("Current Balance") }}</h3>
                    <p class="text-xl">{{ number_format(Auth::user()->balance, 0, '', ' ') }} USD</p>

                </div>
            </div>
            
            <div class="dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">My Portfolio</h3>
                    <div class="flex flex-wrap overflow-hidden shadow-sm">
                        <div class="w-1/4 p-4 flex flex-col items-center bg-white dark:bg-gray-900 overflow-hidden shadow-sm rounded-lg">
                            <table class="table-auto">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-2">Currency</th>
                                        <th class="px-4 py-2">Cost (USD)</th>
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
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Define sampleData (this should be changed to the appropriate data from the database)
        //Pull data from database 
        //send it to javascript using JSON then unpack and simplify repeated rows

        @php
        //Requests data from database
        //Gets current user id 
        //Probably should be done using username instead but couldn't figure it out
        $userID = Auth::id();
        $data = DB::table("users")->join("purchases", "users.id", "=", "purchases.user_id") -> where("users.id", "=", $userID)->select("purchases.name", "purchases.quantity", "purchases.operation")->get();
        
        //Splits the data into the currencies and how many of each currency there is
        $currencies = $data -> pluck("name");
        $values = $data -> pluck("quantity");
        $operation = $data -> pluck("operation");
        
        // create a string to format to send to javascript
        $returnString = "";

        for($i=0;$i<count($currencies);$i++){
            if ($i==(count($currencies)-1)){ //if its the last one don't add a comma
                $returnString = $returnString . $currencies["".$i] . "|" . $values["".$i] . "|" . $operation["".$i];
            } else {
                $returnString = $returnString . $currencies["".$i] . "|" . $values["".$i] . "|" . $operation["".$i] . ",";
            }
        }
        @endphp
        
        //Get the data from the php
        var dataSent = '<?= $returnString ?>';

        //Format the data into an array
        var dataSentArray = dataSent.split(",");

        //Iterate through each element and split that into currency and amount
        //Use a dictionary to simplify repeated occurrences
        var dataDict = {};

        for(let i=0; i<dataSentArray.length;i++){
            var element = dataSentArray[i].split("|");
            if (!(element[0] in dataDict) && !(element[1]==0)){
                dataDict[element[0]] = 0;
            }

            if (!(element[1]==0)){
                dataDict[element[0]] = dataDict[element[0]] + (parseInt(element[1]) * parseInt(element[2]));
            }
        }

        //Split data insto currencies and amounts
        var AmountData = Object.values(dataDict);
        var CurrencyData = Object.keys(dataDict);
        
        var data = [];

        //TODO: change value to one pulled from api
        // Create an array with values of the currencies at correspding indexs
        // Then can easily add it to the dictionary below

        //NOTE: data must be a list of dictionary items for the pie chart to work

        for (let i=0; i<AmountData.length;i++) { 
            tempDict = {label: CurrencyData[i], value: 1, amount: AmountData[i]}
            data.push(tempDict)
        }

        var totalValue = data.reduce((acc, cur) => acc + cur.value, 0);

        var ctx = document.getElementById('portfolio-chart').getContext('2d');
        var portfolioChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.map(data => data.label),
                datasets: [{
                    data: data.map(data => data.value),
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
                },
                plugins: {
                    datalabels: {
                        color: 'white'
                        // Doesn't make pie chart labels white, this will be fixed (not for MVP)
                    }
                }
            }
        });

        var tableBody = document.getElementById('table-body');
        data.forEach(function (data) {
            var row = document.createElement('tr');
            row.innerHTML = `
                <td class="border px-4 py-2">${data.label}</td>
                <td class="border px-4 py-2">${data.value}</td>
                <td class="border px-4 py-2">${data.amount}</td>
            `;
            tableBody.appendChild(row);
        });
    });
</script>
</x-app-layout>
