<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buy: ') }} {{ $symbol }} - {{ $name }} - {{ $price }}
        </h2>
    </x-slot>

    <body>
        <form>
            <div class="inputB">
                <input type="number" id="quantity" name="quantity" min="1" default="1" oninput="updateEstimate()" class="input-field">
                <label for="quantity" class="input-label">Coins</label>
                <p id="estimate" class="input-estimate"></p>
            </div>
        </form>

        <br></br>

        <div class="action-buttons">
            <form method="GET" action="{{ route('buy.purchase') }}">
                @csrf
                <input type="hidden" name="symbol" value="{{ $symbol }}">
                <input type="hidden" name="name" value="{{ $name }}">
                <input type="hidden" name="price" value="{{ $price }}">
                <input type="hidden" id="hiddenQuantity" name="quantity" value="">

                <button type="submit" class="buy-button">{{ __('Buy') }}</button>
            </form>

            <form method="GET" action="{{ route('buy.sell') }}">
                @csrf
                <input type="hidden" name="symbol" value="{{ $symbol }}">
                <input type="hidden" name="name" value="{{ $name }}">
                <input type="hidden" name="price" value="{{ $price }}">
                <input type="hidden" id="sellQuantity" name="sellquantity" value="">

                @if ($userHasCurrency)
                    <button type="submit" class="sell-button">{{ __('Sell') }}</button>
                @else
                    <button type="submit" class="sell-button" disabled>{{ __('Sell') }}</button>
                @endif
            </form>
        </div>
    </body>

    <div style="text-align: center;">
       
        <div class="tradingview-widget-container" style="margin: 0 auto;">
            <div class="tradingview-widget-container__widget"></div>
            <div class="tradingview-widget-copyright"><a href="https://www.tradingview.com/" rel="noopener nofollow" target="_blank"><span class="blue-text">Track all markets on TradingView</span></a></div>
            <script type="text/javascript" src="https://s3.tradingview.com/external-embedding/embed-widget-advanced-chart.js" async>
            {
                "width": "980",
                "height": "500",
                "symbol": "BITSTAMP:BTCUSD",
                "interval": "240",
                "timezone": "Etc/UTC",
                "theme": "dark",
                "style": "2",
                "locale": "en",
                "enable_publishing": false,
                "allow_symbol_change": true,
                "calendar": false,
                "support_host": "https://www.tradingview.com"
            }
            </script>
        </div>
    
    </div>

</x-app-layout>

<script>
    function updateEstimate() {
        var amount = document.getElementById('quantity').value;
        var estimatedPrice = amount * {{ $price }};
        console.log(estimatedPrice);
        console.log('quantity:', document.querySelector('input[name="quantity"]').value);
        document.getElementById("estimate").innerHTML = "Estimated Price: " + estimatedPrice;
        document.getElementById("hiddenQuantity").value = amount;
        document.getElementById("sellQuantity").value = amount;
    }

    function logFormData() {
        console.log('symbol:', document.querySelector('input[name="symbol"]').value);
        console.log('name:', document.querySelector('input[name="name"]').value);
        console.log('price:', document.querySelector('input[name="price"]').value);
        console.log('quantity:', document.querySelector('input[name="quantity"]').value);
    }

    updateEstimate();
    logFormData();
</script>

<style>
    @import url("https://fonts.googleapis.com/css?family=Open+Sans&dispaly=swap");
    body {
        max-width: 4000px;
        margin: 0 auto;
        padding: 20px;
        background-color: white;
        border-radius: 8px;
    }

    form {
        margin-bottom: 20px;
        position: relative;
    }

    .inputB {
        position: relative;
    }

    .input-field {
        width: 100%;
        padding-top: 50px;
        border: 1px solid rgba(255, 255, 2555, 0.25);
        border-radius: 5px;
        outline: none;
        color: red;
        font-size: 2em;
    }

    .input-label {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        pointer-events: none;
        font-size: 1em;
        color: black;
        text-transform: uppercase;
        transition: color 0.3s, transform 0.3s;
    }

    .input-field:focus + .input-label,
    .input-field:valid + .input-label {
        
        transform: translateX(10px) translateY(-50%);
        font-size: 2em;
      
    }



    .input-field:focus + .input-label {
    display: none;
}

    .inputB:nth-child(1) .input-field:focus + .input-label,
    .inputB:nth-child(1) .input-field:valid + .input-label {
        border-radius: 2px;
        color: black;
            }

.input-field::-webkit-inner-spin-button,
.input-field::-webkit-outer-spin-button {
    -webkit-appearance: none;
     
    margin: 0;
}

.input-field {
   
    padding: 10px 35px 10px 10px; 
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 5px;
    outline: none;
    color: red;
    font-size: 2em;
}



    .action-buttons {
        display: flex;
        margin-top: 10px;
    }

    button {
        padding: 10px 20px;
        border: none;
        border-radius: 4px;
        font-size: 1em;
        cursor: pointer;
    }

    .buy-button {
        background-color: #28a745;
        color: white;
        margin-right: 10px;
    }

    .sell-button {
        background-color: #dc3545;
        color: white;
    }
</style>