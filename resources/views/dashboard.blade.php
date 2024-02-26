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
                                        <th class="px-4 py-2">Value (USD)</th>
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
        var sampleData = [
            { label: 'Bitcoin', value: 3000 },
            { label: 'Ethereum', value: 2000 },
            { label: 'Litecoin', value: 1500 },
            { label: 'Ripple', value: 1200 },
            { label: 'Stellar', value: 800 },
        ];

        var totalValue = sampleData.reduce((acc, cur) => acc + cur.value, 0);

        var ctx = document.getElementById('portfolio-chart').getContext('2d');
        var portfolioChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: sampleData.map(data => data.label),
                datasets: [{
                    data: sampleData.map(data => data.value),
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
                },
                plugins: {
                    datalabels: {
                        color: 'white'
                    }
                }
            }
        });

        var tableBody = document.getElementById('table-body');
        sampleData.forEach(function (data) {
            var row = document.createElement('tr');
            row.innerHTML = `
                <td class="border px-4 py-2">${data.label}</td>
                <td class="border px-4 py-2">${data.value}</td>
            `;
            tableBody.appendChild(row);
        });
    });
</script>
</x-app-layout>
