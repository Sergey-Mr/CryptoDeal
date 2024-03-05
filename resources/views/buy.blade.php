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
        //document.getElementById("hiddenQuantitySell").value = amount;
    
    }
    //function logFormData() {
    //    console.log('symbol:', document.querySelector('input[name="symbol"]').value);
    //    console.log('name:', document.querySelector('input[name="name"]').value);
    //    console.log('price:', document.querySelector('input[name="price"]').value);
    //    console.log('quantity:', document.querySelector('input[name="quantity"]').value);
    //}
    updateEstimate();
    //logFormData();
</script>