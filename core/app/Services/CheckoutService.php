<?php

namespace App\Services;

use App\Helpers\EmailHelper;
use App\Helpers\PriceHelper;
use App\Helpers\SmsHelper;
use App\Jobs\EmailSendJob;
use App\Models\Notification;
use App\Models\Order;
use App\Models\PromoCode;
use App\Models\TrackOrder;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        protected OrderPricingService $pricingService,
        protected SettingsService $settingsService
    ) {
    }

    public function buildOrderData(array $input, string $paymentMethod, array $overrides = []): array
    {
        $pricing = $this->pricingService->calculate(
            null,
            $this->pricingService->resolveShipping(isset($input['shipping_id']) ? (int) $input['shipping_id'] : null),
            $this->pricingService->resolveState(isset($input['state_id']) ? (int) $input['state_id'] : null),
        );

        $user = Auth::user();

        return array_merge([
            'state' => $pricing['state'] ? json_encode($pricing['state'], true) : null,
            'cart' => json_encode($pricing['cart'], true),
            'discount' => json_encode($pricing['discount'], true),
            'shipping' => json_encode($pricing['shipping'], true),
            'tax' => $pricing['tax'],
            'state_price' => $pricing['state_price'],
            'shipping_info' => json_encode(Session::get('shipping_address'), true),
            'billing_info' => json_encode(Session::get('billing_address'), true),
            'payment_method' => $paymentMethod,
            'user_id' => $user?->id ?? 0,
            'transaction_number' => Str::random(10),
            'currency_sign' => PriceHelper::setCurrencySign(),
            'currency_value' => PriceHelper::setCurrencyValue(),
            'order_status' => 'Pending',
        ], $overrides);
    }

    public function grandTotal(array $input): float
    {
        $pricing = $this->pricingService->calculate(
            null,
            $this->pricingService->resolveShipping(isset($input['shipping_id']) ? (int) $input['shipping_id'] : null),
            $this->pricingService->resolveState(isset($input['state_id']) ? (int) $input['state_id'] : null),
        );

        return PriceHelper::setConvertPrice($pricing['grand_total']);
    }

    public function finalizeOrder(array $orderData, float $orderCost, bool $clearOrderSession = false): Order
    {
        $cart = Session::get('cart', []);
        $discount = Session::get('coupon', []);
        $setting = $this->settingsService->setting();
        $user = Auth::user();

        $order = Order::create($orderData);
        $order->transaction_number = 'ORD-' . str_pad(Carbon::now()->format('Ymd'), 4, '0000', STR_PAD_LEFT) . '-' . $order->id;
        $order->save();

        TrackOrder::create([
            'title' => 'Pending',
            'order_id' => $order->id,
        ]);

        Notification::create([
            'order_id' => $order->id,
        ]);

        PriceHelper::Transaction($order->id, $order->transaction_number, EmailHelper::getEmail(), PriceHelper::OrderTotal($order, 'trns'));
        PriceHelper::LicenseQtyDecrese($cart);
        PriceHelper::stockDecrese();

        if ($discount && isset($discount['code']['id'])) {
            $coupon = PromoCode::find($discount['code']['id']);
            if ($coupon) {
                $coupon->no_of_times -= 1;
                $coupon->save();
            }
        }

        $emailData = [
            'to' => EmailHelper::getEmail(),
            'type' => 'Order',
            'user_name' => $user ? $user->displayName() : (Session::get('billing_address')['bill_first_name'] ?? 'Customer'),
            'order_cost' => $orderCost,
            'transaction_number' => $order->transaction_number,
            'site_title' => $setting?->title,
        ];

        if ($setting?->is_queue_enabled == 1) {
            dispatch(new EmailSendJob($emailData, 'template'));
        } else {
            (new EmailHelper())->sendTemplateMail($emailData, 'template');
        }

        if ($setting?->is_twilio == 1) {
            $userNumber = json_decode($order->billing_info, true)['bill_phone'] ?? null;
            if ($userNumber) {
                (new SmsHelper())->SendSms($userNumber, "'purchase'", $order->transaction_number);
            }
        }

        Session::put('order_id', $order->id);
        Session::forget('cart');
        Session::forget('booking_cart');
        Session::forget('discount');
        Session::forget('coupon');

        if ($clearOrderSession) {
            Session::forget('order_data');
            Session::forget('order_input_data');
            Session::forget('order_payment_id');
        }

        return $order;
    }
}
