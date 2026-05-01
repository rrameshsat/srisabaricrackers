<?php

namespace App\Http\Controllers\Front;

use App\{
    Models\ExtraSetting,
    Models\Order,
    Models\PaymentSetting,
    Traits\StripeCheckout,
    Traits\MollieCheckout,
    Traits\PaypalCheckout,
    Traits\PaystackCheckout,
    Http\Controllers\Controller,
    Http\Requests\PaymentRequest,
    Traits\CashOnDeliveryCheckout,
    Traits\BankCheckout,
};
use App\Models\State;
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Services\CheckoutService;
use App\Services\OrderPricingService;
use App\Services\SettingsService;
use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;
use Mollie\Laravel\Facades\Mollie;

class CheckoutController extends Controller
{
    // Show status for checkout-submit (GET) - used to validate route availability and state charges
    public function checkoutSubmit()
    {
        // If checkout submission is disabled, return a clear error
        if (!config('delivery.enabled', true)) {
            return response()->json(['error' => 'Checkout submission is disabled'], 503);
        }

        // Expose available states for delivery charges from config
        $states = array_keys(config('delivery.charges', []));

        return response()->json([
            'status' => 'ready',
            'available_states' => $states,
        ]);
    }

    /**
     * Return states for a given country (country_id) as JSON for dependent dropdowns
     */
    public function countryStates($country_id)
    {
        $states = State::where('country_id', (int) $country_id)
            ->where('status', 1)
            ->orderBy('name')
            ->get(['id','name']);
        return response()->json($states);
    }
    private function getMinimumOrderAmount()
    {
        $setting = ExtraSetting::find(1);
        $minimumOrderAmount = $setting->minimum_order_amount ?? 3000;

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }

        return $minimumOrderAmount * $curr->value;
    }

    use StripeCheckout {
        StripeCheckout::__construct as private __stripeConstruct;
    }
    use PaypalCheckout {
        PaypalCheckout::__construct as private __paypalConstruct;
    }
    use MollieCheckout {
        MollieCheckout::__construct as private __MollieConstruct;
    }


    use BankCheckout;
    use PaystackCheckout;
    use CashOnDeliveryCheckout;

    public function __construct(
        protected OrderPricingService $pricingService,
        protected CheckoutService $checkoutService,
        protected SettingsService $settingsService
    )
    {
        $setting = $this->settingsService->setting();
        if ($setting && $setting->is_guest_checkout != 1) {
            $this->middleware('auth');
        }
        $this->middleware('localize');
        $this->__stripeConstruct();
        $this->__paypalConstruct();
    }

    public function checkoutPage()
    {
        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $extra_settings = ExtraSetting::find(1);
        $cart = Session::get('cart', []);
        $cartTotal = PriceHelper::cartTotal($cart);
        $minimumOrderAmount = $this->getMinimumOrderAmount();
        if ($extra_settings->is_min_order_message == 1 && $cartTotal < (float) $minimumOrderAmount) {
            return redirect(route('front.cart'))->withError($extra_settings->minimum_order_message ?: __('Minimum order amount must be above Rs.') . $minimumOrderAmount);
        }

        return view('front.checkout.index', $this->checkoutViewData());
    }

    public function ship_address()
    {
        $setting = $this->settingsService->setting();

        if ($setting && $setting->is_single_checkout == 1) {
            return redirect(route("front.checkout"));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $extra_settings = ExtraSetting::find(1);
        $cart = Session::get('cart', []);
        $cartTotal = PriceHelper::cartTotal($cart);
        $minimumOrderAmount = $this->getMinimumOrderAmount();
        if ($extra_settings->is_min_order_message == 1 && $cartTotal < (float) $minimumOrderAmount) {
            return redirect(route('front.cart'))->withError($extra_settings->minimum_order_message ?: __('Minimum order amount must be above Rs.') . $minimumOrderAmount);
        }

        Session::forget('shipping_address');
        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }



        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $extra_settings = ExtraSetting::find(1);
        $cart = Session::get('cart', []);
        $cartTotal = PriceHelper::cartTotal($cart);
        $minimumOrderAmount = $this->getMinimumOrderAmount();
        if ($extra_settings->is_min_order_message == 1 && $cartTotal < (float) $minimumOrderAmount) {
            return redirect(route('front.cart'))->withError($extra_settings->minimum_order_message ?: __('Minimum order amount must be above Rs.') . $minimumOrderAmount);
        }

        return view('front.checkout.billing', $this->checkoutViewData());
    }



    public function billingStore(Request $request)
    {
        // lint-friendly: billing fields
        $rules = [
            'bill_first_name' => 'required',
            'bill_last_name' => 'required',
            'bill_email' => 'required|email',
            'bill_phone' => 'required',
            'bill_address1' => 'required',
            'bill_city' => 'required',
            'bill_zip' => 'required',
        ];

        // India-state conditional validation for billing bill_state_id
        try {
            $india = \DB::table('countries')->where('name', 'India')->first();
            if ($india) {
                $stateCount = \DB::table('states')->where('country_id', (int)$india->id)->count();
                if ($stateCount > 0) {
                    $rules['bill_state_id'] = 'required|exists:states,id';
                }
            }
        } catch (\Exception $e) {
            // ignore
        }

        $request->validate($rules);

        // Mirror billing state into bill_state_id for storage, to support a dedicated column
        $input = $request->all();
        if (!isset($input['bill_state_id']) || empty($input['bill_state_id'])) {
            // Fallback: if legacy state_id was used, map to bill_state_id; otherwise keep null
            $input['bill_state_id'] = $input['state_id'] ?? null;
        }

        if ($request->same_ship_address) {
            Session::put('billing_address', $input);

            if (PriceHelper::CheckDigital()) {
                $shipping = [
                    "ship_first_name" => $input['bill_first_name'],
                    "ship_last_name" => $input['bill_last_name'],
                    "ship_email" => $input['bill_email'],
                    "ship_phone" => $input['bill_phone'],
                    "ship_company" => $input['bill_company'],
                    "ship_address1" => $input['bill_address1'],
                    "ship_address2" => $input['bill_address2'],
                    "ship_zip" => $input['bill_zip'],
                    "ship_city" => $input['bill_city'],
                    "ship_country" => $input['bill_country'],
                    "ship_state_id" => $input['bill_state_id'] ?? null,
                ];
            } else {
                $shipping = [
                    "ship_first_name" => $input['bill_first_name'],
                    "ship_last_name" => $input['bill_last_name'],
                    "ship_email" => $input['bill_email'],
                    "ship_phone" => $input['bill_phone'],
                ];
            }
            Session::put('shipping_address', $shipping);
        } else {
            Session::put('billing_address', $input);
            Session::forget('shipping_address');
        }

        if (Session::has('shipping_address')) {
            return redirect()->route('front.checkout.payment');
        } else {
            return redirect()->route('front.checkout.shipping');
        }
    }


    public function shipping()
    {

        if (Session::has('shipping_address')) {
            return redirect(route('front.checkout.payment'));
        }

        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        return view('front.checkout.shipping', $this->checkoutViewData());
    }

    public function shippingStore(Request $request)
    {
        // laravel validation
        $rules = [
            'ship_first_name' => 'required',
            'ship_last_name' => 'required',
            'ship_email' => 'required|email',
            'ship_phone' => 'required',
            'ship_address1' => 'required',
            'ship_zip' => 'required',
            'ship_city' => 'required',
        ];
        // If India has states, require ship_state_id
        try {
            $india = \DB::table('countries')->where('name','India')->first();
            if ($india) {
                $stateCount = \DB::table('states')->where('country_id', (int)$india->id)->count();
                if ($stateCount > 0) {
                    $rules['ship_state_id'] = 'required|exists:states,id';
                }
            }
        } catch (\Exception $e) {
            // ignore
        }
        
        $request->validate($rules);

        Session::put('shipping_address', $request->all());
        return redirect(route('front.checkout.payment'));
    }



    public function payment()
    {
        if (!Session::has('billing_address')) {
            return redirect(route('front.checkout.billing'));
        }

        if (!Session::has('shipping_address')) {
            return redirect(route('front.checkout.shipping'));
        }


        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        return view('front.checkout.payment', $this->checkoutViewData());
    }

    public function checkout(PaymentRequest $request)
    {

        // Global toggle: disable checkout submission if configured
        if (!Config::get('delivery.enabled', true)) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Checkout submission is disabled'], 503);
            }
            return redirect()->back()->with('error', __('Checkout submission is currently disabled'));
        }



        PriceHelper::checkCheckout($request);

        $input = $request->all();

        $checkout = false;
        $payment_redirect = false;
        $payment = null;

        if (Session::has('currency')) {
            $currency = Currency::findOrFail(Session::get('currency'));
        } else {
            $currency = Currency::where('is_default', 1)->first();
        }


        $usd_supported = array(
            "USD",
            "AED",
            "AFN",
            "ALL",
            "AMD",
            "ANG",
            "AOA",
            "ARS",
            "AUD",
            "AWG",
            "AZN",
            "BAM",
            "BBD",
            "BDT",
            "BGN",
            "BIF",
            "BMD",
            "BND",
            "BOB",
            "BRL",
            "BSD",
            "BWP",
            "BYN",
            "BZD",
            "CAD",
            "CDF",
            "CHF",
            "CLP",
            "CNY",
            "COP",
            "CRC",
            "CVE",
            "CZK",
            "DJF",
            "DKK",
            "DOP",
            "DZD",
            "EGP",
            "ETB",
            "EUR",
            "FJD",
            "FKP",
            "GBP",
            "GEL",
            "GIP",
            "GMD",
            "GNF",
            "GTQ",
            "GYD",
            "HKD",
            "HNL",
            "HTG",
            "HUF",
            "IDR",
            "ILS",
            "INR",
            "ISK",
            "JMD",
            "JPY",
            "KES",
            "KGS",
            "KHR",
            "KMF",
            "KRW",
            "KYD",
            "KZT",
            "LAK",
            "LBP",
            "LKR",
            "LRD",
            "LSL",
            "MAD",
            "MDL",
            "MGA",
            "MKD",
            "MMK",
            "MNT",
            "MOP",
            "MUR",
            "MVR",
            "MWK",
            "MXN",
            "MYR",
            "MZN",
            "NAD",
            "NGN",
            "NIO",
            "NOK",
            "NPR",
            "NZD",
            "PAB",
            "PEN",
            "PGK",
            "PHP",
            "PKR",
            "PLN",
            "PYG",
            "QAR",
            "RON",
            "RSD",
            "RUB",
            "RWF",
            "SAR",
            "SBD",
            "SCR",
            "SEK",
            "SGD",
            "SHP",
            "SLE",
            "SOS",
            "SRD",
            "STD",
            "SZL",
            "THB",
            "TJS",
            "TOP",
            "TRY",
            "TTD",
            "TWD",
            "TZS",
            "UAH",
            "UGX",
            "UYU",
            "UZS",
            "VND",
            "VUV",
            "WST",
            "XAF",
            "XCD",
            "XOF",
            "XPF",
            "YER",
            "ZAR",
            "ZMW"
        );


        $paypal_supported = ['USD', 'EUR', 'AUD', 'BRL', 'CAD', 'HKD', 'JPY', 'MXN', 'NZD', 'PHP', 'GBP', 'RUB'];
        $paystack_supported = ['NGN', "GHS", "USD", "ZAR", "KES"];
        switch ($input['payment_method']) {

            case 'Stripe':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->stripeSubmit($input);
                break;

            case 'Paypal':
                if (!in_array($currency->name, $paypal_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->paypalSubmit($input);
                break;


            case 'Mollie':
                if (!in_array($currency->name, $usd_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->MollieSubmit($input);
                break;

            case 'Paystack':
                if (!in_array($currency->name, $paystack_supported)) {
                    Session::flash('error', __('Currency Not Supported'));
                    return redirect()->back();
                }
                $checkout = true;
                $payment = $this->paystackSubmit($input);

                break;

            case 'Bank':
                $checkout = true;
                $payment = $this->BankSubmit($input);
                break;

            case 'Paytabs':
                $checkout = true;
                $payment_redirect = true;
                $payment = $this->PayTabsSubmit($input);
                break;

            case 'Cash On Delivery':
                $checkout = true;
                $payment = $this->cashOnDeliverySubmit($input);
                break;
        }



        if ($checkout) {
            if ($payment_redirect) {

                if ($payment['status']) {
                    return redirect()->away($payment['link']);
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            } else {
                if ($payment['status']) {
                    return redirect()->route('front.checkout.success');
                } else {
                    Session::put('message', $payment['message']);
                    return redirect()->route('front.checkout.cancle');
                }
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentRedirect(Request $request)
    {
        $responseData = $request->all();

        if (isset($responseData['session_id'])) {
            $payment = $this->stripeNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } elseif (Session::has('order_payment_id')) {
            $payment = $this->paypalNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function mollieRedirect(Request $request)
    {

        $responseData = $request->all();

        $payment = Mollie::api()->payments()->get(Session::get('payment_id'));
        $responseData['payment_id'] = $payment->id;
        if ($payment->status == 'paid') {
            $payment = $this->mollieNotify($responseData);
            if ($payment['status']) {
                return redirect()->route('front.checkout.success');
            } else {
                Session::put('message', $payment['message']);
                return redirect()->route('front.checkout.cancle');
            }
        } else {
            return redirect()->route('front.checkout.cancle');
        }
    }

    public function paymentSuccess()
    {
        if (Session::has('order_id')) {
            $order_id = Session::get('order_id');
            $order = Order::find($order_id);
            $cart = json_decode($order->cart, true);
            $setting = $this->settingsService->setting();
            if ($setting && $setting->is_twilio == 1) {
                // message
                $sms = new SmsHelper();
                $user_number = $order->user->phone ?? null;
                if ($user_number) {
                    $sms->SendSms($user_number, "'purchase'");
                }
            }
            return view('front.checkout.success', compact('order', 'cart'));
        }
        return redirect()->route('front.index');
    }



    public function paymentCancle()
    {
        $message = Session::pull('message', __('Payment Failed!'));
        Session::flash('error', $message);
        return redirect()->route('front.checkout.billing');
    }

    public function stateSetUp(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $cartTotal = 0.0;
        foreach (Session::get('cart', []) as $entry) {
            $qty = (int) ($entry['qty'] ?? $entry['quantity'] ?? 0);
            $cartTotal += PriceHelper::cartEntryTotalPrice($entry) * $qty;
        }

        $pricing = $this->pricingService->calculate(
            null,
            $this->pricingService->resolveShipping($request->filled('shipping_id') ? (int) $request->shipping_id : null, $cartTotal),
            $this->pricingService->resolveState($request->filled('state_id') ? (int) $request->state_id : null)
        );

        $data['shipping_eligible'] = !empty($pricing['shipping']);
        $data['shipping_price'] = PriceHelper::setCurrencyPrice($pricing['shipping_price']);
        $data['state_price'] = PriceHelper::setCurrencyPrice($pricing['state_price']);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($pricing['grand_total']);

        return response()->json($data);
    }

    public function shippingSetUp(Request $request)
    {
        if (!Session::has('cart')) {
            return redirect(route('front.cart'));
        }

        $stateId = ($request->state_id && $request->state_id != 'undefined') ? (int) $request->state_id : null;
        $cartTotal = 0.0;
        foreach (Session::get('cart', []) as $entry) {
            $qty = (int) ($entry['qty'] ?? $entry['quantity'] ?? 0);
            $cartTotal += PriceHelper::cartEntryTotalPrice($entry) * $qty;
        }
        $pricing = $this->pricingService->calculate(
            null,
            $this->pricingService->resolveShipping((int) $request->shipping_id, $cartTotal),
            $this->pricingService->resolveState($stateId)
        );

        $data['shipping_eligible'] = !empty($pricing['shipping']);
        $data['state_price'] = PriceHelper::setCurrencyPrice($pricing['state_price']);
        $data['shipping_price'] = PriceHelper::setCurrencyPrice($pricing['shipping_price']);
        $data['grand_total'] = PriceHelper::setCurrencyPrice($pricing['grand_total']);

        return response()->json($data);
    }

    protected function checkoutViewData(): array
    {
        return array_merge(
            [
                'user' => Auth::user(),
                'payments' => PaymentSetting::whereStatus(1)->get(),
                'extra_settings' => ExtraSetting::find(1),
            ],
            $this->pricingService->checkoutViewData()
        );
    }
}
