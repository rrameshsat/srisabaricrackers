<?php

namespace App\Traits;

use App\Helpers\EmailHelper;
use App\Helpers\PriceHelper;
use App\Services\CheckoutService;
use Mollie\Laravel\Facades\Mollie;
use App\Models\Setting;
use Illuminate\Support\Facades\Session;

trait MollieCheckout
{
    public function __construct()
    {
      
    }


    public function MollieSubmit($data){
        
        $notify_url = route('front.checkout.mollie.redirect');
        $setting = Setting::find(1);
        $checkoutService = app(CheckoutService::class);
        $total_amount = $checkoutService->grandTotal($data);
       
        $payment = Mollie::api()->payments()->create([
            'amount' => [
                'currency' => PriceHelper::setCurrencyName(),
                'value' => ''.sprintf('%0.2f', $total_amount).'', // You must send the correct number of decimals, thus we enforce the use of strings
            ],
            'description' => $setting->title . 'Order' ,
            'redirectUrl' => $notify_url,
            ]);

       
        Session::put('payment_id',$payment->id);
        Session::put('input_data',$data);
        $payment = Mollie::api()->payments()->get($payment->id);
      
        if ($payment->getCheckoutUrl()) {
            /** redirect to mollie **/

            return [
                'status' => true,
                'link' => $payment->getCheckoutUrl()
            ];

        }
        return [
            'status' => false,
            'message' => __('Unknown error occurred')
        ];

 }

    
    public function mollieNotify($responseData){
        $input_data = Session::get('input_data');   
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($input_data, 'Mollie', [
            'transaction_number' => \Illuminate\Support\Str::random(10),
        ]);
        $total_amount = $checkoutService->grandTotal($input_data);
        $orderData['txnid'] = $responseData['payment_id'];
        $orderData['payment_status'] = 'Paid';
        $checkoutService->finalizeOrder($orderData, $total_amount);
        return [
            'status' => true
        ];
    }
}
