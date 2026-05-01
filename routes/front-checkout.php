<?php

use Illuminate\Support\Facades\Route;

// Frontend checkout routes (GET helpers)
Route::get('/checkout-submit', 'Front\\CheckoutController@checkoutSubmit')->name('front.checkout.submit.show');
