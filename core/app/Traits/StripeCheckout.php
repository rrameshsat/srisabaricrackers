<?php

namespace App\Traits;

use App\{
    Models\Setting,
    Helpers\PriceHelper,
    Models\PaymentSetting,
};
use App\Services\CheckoutService;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

trait StripeCheckout
{

    public function __construct()
    {
        $data = PaymentSetting::whereUniqueKeyword('stripe')->first();
        $paydata = $data->convertJsonData();
        Config::set('services.stripe.key', $paydata['key']);
        Config::set('services.stripe.secret', $paydata['secret']);
    }

    public function stripeSubmit($data)
    {
        $setting = Setting::first();
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($data, 'Stripe', [
            'user_id' => Auth::id() ?? 0,
            'transaction_number' => Str::random(10),
        ]);
        $total_amount = $checkoutService->grandTotal($data);

        $stripe = new \Stripe\StripeClient(Config::get('services.stripe.secret'));
        try {

            $notify_url = route('front.checkout.redirect') . '?session_id={CHECKOUT_SESSION_ID}';
            $response = $stripe->checkout->sessions->create([
                'success_url' => $notify_url,
                'customer_email' => Session::get('shipping_address')['ship_email'],
                'payment_method_types' => ['card'],

                'line_items' => [
                    [
                        'price_data' => [
                            'product_data' => [
                                'name' => $setting->title . ' ' . __('Order'),
                            ],
                            'unit_amount' => 100 * $total_amount,
                            'currency' => PriceHelper::setCurrencyName(),
                        ],
                        'quantity' => 1
                    ],
                ],

                'mode' => 'payment',
                'allow_promotion_codes' => false,
            ]);
            Session::put('order_data', $orderData);
            Session::put('order_input_data', $data);
            return [
                'status' => true,
                'link' => $response['url']
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }


    public function stripeNotify($resData)
    {
        $stripe = new \Stripe\StripeClient(Config::get('services.stripe.secret'));
        $response = $stripe->checkout->sessions->retrieve($resData['session_id']);

        if ($response['payment_status'] == 'paid' && $response['status'] == 'complete') {
            $checkoutService = app(CheckoutService::class);
            $order_input_data = Session::get('order_input_data', []);
            $total_amount = $checkoutService->grandTotal($order_input_data);
            $orderData = Session::get('order_data');
            $orderData['txnid'] = $response['payment_intent'];
            $orderData['payment_status'] = 'Paid';
            $checkoutService->finalizeOrder($orderData, $total_amount, true);
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false,
                'message' => 'Payment Failed'
            ];
        }
    }
}
