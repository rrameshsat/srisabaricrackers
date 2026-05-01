<?php

namespace App\Traits;

use App\{
    Models\Setting,
    Helpers\PriceHelper,
    Models\PaymentSetting,
};
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Omnipay\Omnipay;


trait PaypalCheckout
{

    private $_api_context;

    public function __construct()
    {
        $data = PaymentSetting::whereUniqueKeyword('paypal')->first();
        $paydata = $data->convertJsonData();

        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId($paydata['client_id']);
        $this->gateway->setSecret($paydata['client_secret']);
        $this->gateway->setTestMode($paydata['check_sandbox'] == 1 ? true : false);
    }

    public function paypalSubmit($data)
    {
        $setting = Setting::first();
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($data, 'Paypal', [
            'user_id' => Auth::id() ?? 0,
        ]);
        $total_amount = $checkoutService->grandTotal($data);

        $paypal_item_name = 'Payment via paypal from' . ' ' . $setting->title;
        $paypal_item_amount =  $total_amount;

        $payment_cancel_url = route('front.checkout.cancle');
        $payment_notify_url = route('front.checkout.redirect');

        try {

            $response = $this->gateway->purchase(array(
                'amount' => $paypal_item_amount,
                'currency' => PriceHelper::setCurrencyName(),
                'returnUrl' => $payment_notify_url,
                'cancelUrl' => $payment_cancel_url
            ))->send();

            if ($response->isRedirect()) {

                Session::put('order_data', $orderData);
                Session::put('order_input_data', $data);
                Session::put('order_payment_id', $response->getTransactionReference());
                if ($response->redirect()) {
                    /** redirect to paypal **/

                    return [
                        'status' => true,
                        'link' => $response->redirect()
                    ];
                }
            } else {
                dd($response->getMessage());
                return $response->getMessage();
            }
        } catch (\Throwable $th) {

            return [
                'status' => false,
                'message' => $th->getMessage()
            ];
        }
    }

    public function paypalNotify($responseData)
    {
        //dd($responseData);
        $orderData = Session::get('order_data');
        /** Get the payment ID before session clear **/
        $order_input_data = Session::get('order_input_data');

        /** clear the session payment ID **/
        if (empty($responseData['PayerID']) || empty($responseData['token'])) {
            return [
                'status' => false,
                'message' => __('Unknown error occurred')
            ];
        }
        $transaction = $this->gateway->completePurchase(array(
            'payer_id' => $responseData['PayerID'],
            'transactionReference' => $responseData['paymentId'],
        ));


        $response = $transaction->send();
        if ($response->isSuccessful()) {
            $checkoutService = app(CheckoutService::class);
            $total_amount = $checkoutService->grandTotal($order_input_data);
            $orderData['txnid'] = $response->getData()['transactions'][0]['related_resources'][0]['sale']['id'];
            $orderData['payment_status'] = 'Paid';
            $orderData['transaction_number'] = Str::random(10);
            $orderData['currency_sign'] = PriceHelper::setCurrencySign();
            $orderData['currency_value'] = PriceHelper::setCurrencyValue();
            $orderData['order_status'] = 'Pending';
            $checkoutService->finalizeOrder($orderData, $total_amount, true);
            return [
                'status' => true
            ];
        } else {
            return [
                'status' => false,
                'message' => $response->getMessage()
            ];
        }
    }
}
