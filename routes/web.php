<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CryptoController;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\PurchaseController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/trading', [CryptoController::class, 'index'])->middleware(['auth'])->name('trading');
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
//Route::get('/trading', function () {
//    return view('trading');
//})->middleware(['auth', 'verified'])->name('trading');
Route::get('/news', function () {
    return view('news');
})->middleware(['auth', 'verified'])->name('news');

Route::get('/fetchCryptoPrices', function () {
    Artisan::call('command:fetchCryptoPrices');
    return response()->json(['success' => true]);
});

Route::middleware(['auth'])->group(function () {
    Route::post('/buy', [PurchaseController::class, 'buyView'])->name('buy.view');
    Route::get('/buy/purchase', [PurchaseController::class, 'purchase'])->name('buy.purchase');
    // Add more buy routes here
});

Route::get('/sell', [PurchaseController::class, 'sell'])->name('sell');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
