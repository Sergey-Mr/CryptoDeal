<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
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

    @endphp
    
    document.addEventListener('DOMContentLoaded', function () {
        // Define sampleData (this should be changed to the appropriate data from the database)
        var sampleData = [
            { label: 'Bitcoin', purchased_value: 3000, current_value: 3000, amount: 2 },
            { label: 'Ethereum', purchased_value: 2000, current_value: 2000, amount: 1 },
            { label: 'Litecoin', purchased_value: 1500, current_value: 1500, amount: 1 },
            { label: 'Ripple', purchased_value: 1200, current_value: 1200, amount: 5 },
            { label: 'Stellar', purchased_value: 800, current_value: 800, amount: 5 },
        ];

        // Temporary method of calculating total assets, to be replaced with actual data
        var totalAssets = sampleData.reduce((acc, cur) => acc + cur.current_value, 0);
        var totalAssetsSection = document.getElementById('total-assets');
        totalAssetsSection.innerHTML = `
            <h3 class="text-lg font-semibold mb-4">{{ __("Total Assets") }}</h3>
            <p class="text-xl">${totalAssets} USD</p>
        `;

        var portfolioChart = new Chart("portfolio-chart", {
            type: 'pie',
            data: {
                labels: sampleData.map(data => data.label),
                datasets: [{
                    data: sampleData.map(data => data.purchased_value),
                    backgroundColor: [
                        '#FF6384',
                        '#36A2EB',
                        '#FFCE56',
                        '#4BC0C0',
                        '#9966FF',
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
        sampleData.forEach(function (data) {
            var row = document.createElement('tr');
            row.innerHTML = `
                <td class="border px-4 py-2">${data.label}</td>
                <td class="border px-4 py-2">${data.purchased_value}</td>
                <td class="border px-4 py-2">${data.current_value}</td>
                <td class="border px-4 py-2">${data.amount}</td>
            `;
            tableBody.appendChild(row);
        });
    });
</script>
</x-app-layout>
