<?php

//------------ CHECKOUT ------------//
Route::get('/checkout/billing/address', 'Front\CheckoutController@ship_address')->name('front.checkout.billing');
Route::post('/checkout/billing/store', 'Front\CheckoutController@billingStore')->name('front.checkout.store');
Route::get('/checkout/shpping/address', 'Front\CheckoutController@shipping')->name('front.checkout.shipping');

// Country-states endpoint for dependent dropdowns
Route::get('/country-states/{country_id}', 'Front\CheckoutController@countryStates')->name('front.country.states');
Route::post('/checkout/shpping/store', 'Front\CheckoutController@shippingStore')->name('front.checkout.shipping.store');
Route::get('/checkout/review/payment', 'Front\CheckoutController@payment')->name('front.checkout.payment');
Route::get('/checkout/state/setup', 'Front\CheckoutController@stateSetUp')->name('front.state.setup');
Route::get('/checkout/shipping/setup', 'Front\CheckoutController@shippingSetUp')->name('front.shipping.setup');
Route::post('/checkout-submit', 'Front\CheckoutController@checkout')->name('front.checkout.submit');
Route::get('/checkout-submit', 'Front\CheckoutController@checkoutSubmit')->name('front.checkout.submit.show');
Route::post('/order/submit', 'Front\CheckoutController@checkout')->name('front.order.submit');
Route::get('/checkout/success', 'Front\CheckoutController@paymentSuccess')->name('front.checkout.success');
Route::get('/checkout/cancle', 'Front\CheckoutController@paymentCancle')->name('front.checkout.cancle');
Route::get('/checkout/redirect', 'Front\CheckoutController@paymentRedirect')->name('front.checkout.redirect');
Route::get('/checkout/mollie/notify', 'Front\CheckoutController@mollieRedirect')->name('front.checkout.mollie.redirect');
Route::get('/checkout', 'Front\CheckoutController@checkoutPage')->name('front.checkout');

// Fallback GET route for checkout-submit to ensure availability even if controller path issues exist
Route::get('/checkout-submit', function() {
    if (!config('delivery.enabled', true)) {
        return response()->json(['error' => 'Checkout submission is disabled'], 503);
    }
    $states = array_keys(config('delivery.charges', []));
    return response()->json(['status' => 'ready', 'available_states' => $states]);
});
