<?php

//------------ QUICK SHOPPING (BULK ORDER) ------------//
Route::get('/quickshopping', 'Front\BookingController@index')->name('front.quickshopping');

//------------ QUICK SHOPPING API (WEB ROUTES) ------------//
Route::get('/quickshopping-categories', 'Front\BookingController@getCategories');
Route::get('/quickshopping-products/{category_id}', 'Front\BookingController@getProductsByCategory');
Route::get('/quickshopping-cart', 'Front\BookingController@getCart');
Route::post('/quickshopping-cart-add', 'Front\BookingController@addToCart');
Route::post('/quickshopping-cart-update', 'Front\BookingController@updateCartItem');
Route::post('/quickshopping-cart-remove', 'Front\BookingController@removeFromCart');
Route::post('/quickshopping-cart-clear', 'Front\BookingController@clearCart');
Route::post('/quickshopping-cart-transfer', 'Front\BookingController@transferToMainCart');

//------------ CART ------------//
Route::get('/cart', 'Front\CartController@index')->name('front.cart');
Route::get('/front/cart/clear', 'Front\CartController@cartClear')->name('front.cart.clear');
Route::get('/header/cart/load', 'Front\CartController@headerCartLoad')->name('front.header.cart');
Route::get('/main/cart/load', 'Front\CartController@CartLoad')->name('cart.get.load');
Route::post('/cart/submit', 'Front\CartController@store')->name('front.cart.submit');
Route::get('product/add/cart', 'Front\CartController@addToCart')->name('product.addcart');
Route::get('/product/cart/update/{id}', 'Front\CartController@update')->name('product.update.single');
Route::post('/promo/submit', 'Front\CartController@promoStore')->name('front.promo.submit');
Route::get('/promo/destroy', 'Front\CartController@promoDelete')->name('front.promo.destroy');
Route::get('/cart/destroy/{id}', 'Front\CartController@destroy')->name('front.cart.destroy');
Route::post('/shipping/submit', 'Front\CartController@shippingStore')->name('front.shipping.submit');
Route::post('/shipping/charge/get', 'Front\CartController@shippingCharge')->name('front.shipping.charge');

//------------ CATALOG ------------//
Route::get('/catalog', 'Front\CatalogController@index')->name('front.catalog');
Route::get('/search/suggest', 'Front\CatalogController@suggestSearch')->name('front.search.suggest');
Route::get('/catalog/view/{type}', 'Front\CatalogController@viewType')->name('front.catalog.view');
