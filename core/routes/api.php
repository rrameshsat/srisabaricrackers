<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('quickshopping')->group(function () {
    Route::get('/categories', 'Front\BookingController@getCategories');
    Route::get('/products/{category_id}', 'Front\BookingController@getProductsByCategory');
    Route::get('/cart', 'Front\BookingController@getCart');
    Route::post('/cart/add', 'Front\BookingController@addToCart');
    Route::post('/cart/update', 'Front\BookingController@updateCartItem');
    Route::post('/cart/remove', 'Front\BookingController@removeFromCart');
    Route::post('/cart/clear', 'Front\BookingController@clearCart');
});



