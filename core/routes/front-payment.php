<?php

Route::post('/paytm/notify', 'Payment\PaytmController@notify')->name('front.paytm.notify');
Route::post('/paytm/submit', 'Payment\PaytmController@store')->name('front.paytm.submit');
Route::post('/razorpay/notify', 'Payment\RazorpayController@notify')->name('front.razorpay.notify');
Route::post('/razorpay/submit', 'Payment\RazorpayController@store')->name('front.razorpay.submit');
Route::post('/flutterwave/notify', 'Payment\FlutterwaveController@notify')->name('front.flutterwave.notify');
Route::post('/flutterwave/submit', 'Payment\FlutterwaveController@store')->name('front.flutterwave.submit');
Route::post('/mercadopago/submit', 'Payment\MercadopagoController@store')->name('front.mercadopago.submit');
Route::post('/authorize/submit', 'Payment\AuthorizeController@store')->name('front.authorize.submit');
Route::post('/sslcommerz/notify', 'Payment\SslCommerzController@notify')->name('front.sslcommerz.notify');
Route::post('/sslcommerz/submit', 'Payment\SslCommerzController@store')->name('front.sslcommerz.submit');
Route::post('/paytab/submit', 'Payment\PaytabsCheckout@store')->name('front.paytab.submit');
Route::post('/paytab/callback', 'Payment\PaytabsCheckout@paytabCallback')->name('paytab.callback');
