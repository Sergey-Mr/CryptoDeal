<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            Watchlist
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" id="news-container">
                <table class="table-auto w-full">
                    <thead>
                        <tr>
                            <th class="px-4 py-2">Symbol</th>
                            <th class="px-4 py-2">Name</th>
                            <th class="px-4 py-2">Saved Price</th>
                            <th class="px-4 py-2">Current Price</th>
                            <th class="px-4 py-2">Percentage Change</th>
                            <th class="px-4 py-2">Remove</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($watchlist as $item)
                            <tr>
                                <td class="border px-4 py-2">
                                    <form class="text-center" method="POST" action="{{ route('buy.view') }}">
                                        @csrf
                                        <input type="hidden" name="symbol" value="{{ $item->symbol }}">
                                        <input type="hidden" name="name" value="{{ $item->name }}">
                                        <input type="hidden" name="price" value="{{ $item->price_saved }}">
                                        <button type="submit" class="btn btn-link" style="text-decoration: underline; ">{{ $item->symbol }}</button>
                                    </form>

                                </td>

                                <td class="border px-4 py-2">{{ $item->name }}</td>
                                <td class="border px-4 py-2">{{ $item->price_saved }}</td>
                                <td class="border px-4 py-2">{{ $item->current_price }}</td>
                                @if ($item->percentage_change > 0)
                                    <td class="border px-4 py-2" style="color: green;">{{ $item->percentage_change }}%</td>
                                @else
                                    <td class="border px-4 py-2" style="color: red;">{{ $item->percentage_change }}%</td>
                                @endif
                                <td class="border px-4 py-2 text-center">
                                    <form class="text-center" method="POST" action="{{ route('save') }}">
                                        @csrf
                                        <input type="hidden" name="symbol" value="{{ $item->symbol }}">
                                        <input type="hidden" name="name" value="{{ $item->name }}">
                                        <input type="hidden" name="price" value="{{ $item->price_saved }}">
                                        <button type="submit" class="btn btn-link" style="text-decoration: underline; ">Remove</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>