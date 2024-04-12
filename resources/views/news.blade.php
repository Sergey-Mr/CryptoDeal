<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            News
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100" id="news-container">
                </div>
            </div>
        </div>
    </div>

    <script>
        const FetchNews = async (page, q ) => {
            console.log("Fetching news...");
            let currentDate = new Date();
            currentDate.setDate(currentDate.getDate() - 1); // Subtract one day
            let year = currentDate.getFullYear();
            let month = ("0" + (currentDate.getMonth() + 1)).slice(-2); // Months are zero-based in JavaScript
            let day = ("0" + currentDate.getDate()).slice(-2);
            let formattedDate = `${year}-${month}-${day}`;
            console.log(formattedDate);
            var url = 'https://newsapi.org/v2/everything?' +
                'q=' +q+
                '&from=' + formattedDate + '&' +
                'pageSize=20&'+
                'language=en&'+
                'sortBy=popularity&' +
                'apiKey=400a9021546d45b89bc34f3972b01add';

            console.log(url);
            var req = new Request(url);

            let a = await fetch(req)
            let response = await a.json()
            console.log(JSON.stringify(response))

            console.log(response)

            let str = '';

            for (let item of response.articles) {
                str += `<div class="card" style="width: 60rem; height: 8rem; margin-bottom: 20px; margin-left: 60px;">
                            <div class="card-body">
                                <h5 class="card-title">${item.title}</h5>
                                <a href="${item.url}" target="_blank" class="btn btn-primary">Read more</a>
                            </div>
                        </div>`;

            }
            
            document.getElementById('news-container').innerHTML = str;
        }

        FetchNews(1, "economy");
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <style>
         .card {
            position: relative;
            border-width: 5px;
            border-style: solid;
            border-color: #e2e8f0; 
            transition: background-color 0.3s ease;
            border-radius: 15px;    
        }

        .card:hover {
            background-color: #e2e8f0; 
        }   

        .btn-primary:hover {
            background-color: #6c757d;
            border-color: #6c757d;
        }

        .btn-primary {  
            margin-left: auto; 
            position: absolute;
            bottom: 10px; 
            right: 10px; 
            background-color: #007bff; 
            color: #fff; 
            padding: 5px 10px; 
            border: none; 
            border-radius: 15px; 
            cursor: pointer;
        }

        .card-title {
            font-size: 20px; /* adjust as needed */
        }
    </style>
</x-app-layout>