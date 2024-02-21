<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buy: ') }} {{ $symbol}} - {{ $name }} - {{ $price }}
        </h2>
    </x-slot>
    <label for="quantity" id="amount" style="color: white">Quantity:</label>
    <input type="number" id="quantity" name="quantity" min="1" default ="1" oninput="updateEstimate()">
    <p id="estimate" style="color: grey"></p>

    
    <form method="GET" action="{{ route('buy.purchase') }}">
        @csrf
        <input type="hidden" name="symbol" value="{{ $symbol }}">
        <input type="hidden" name="name" value="{{ $name }}">
        <input type="hidden" name="price" value="{{ $price }}">
        <input type="hidden" id="hiddenQuantity" name="quantity" value="">

        <x-secondary-button type="submit">
                    {{ __('Buy') }}
        </x-secondary-button>
    </form>

</x-app-layout>

<script>
    function updateEstimate(){
        var amount = document.getElementById('quantity').value;
        var estimatedPrice = amount * {{ $price }};
        console.log(estimatedPrice);
        document.getElementById("estimate").innerHTML = "Estimated Price: " + estimatedPrice;
        document.getElementById("hiddenQuantity").value = amount;
    }
    updateEstimate();

</script>
