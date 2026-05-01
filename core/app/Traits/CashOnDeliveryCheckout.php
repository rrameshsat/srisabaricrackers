<?php

namespace App\Traits;

use App\{
    Helpers\PriceHelper,
};
use App\Services\CheckoutService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait CashOnDeliveryCheckout
{

    public function cashOnDeliverySubmit($data)
    {
        $user = Auth::user();
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($data, 'Cash On Delivery', [
            'user_id' => isset($user) ? $user->id : 0,
            'transaction_number' => Str::random(10),
            'payment_status' => 'Unpaid',
        ]);

        $orderCost = $checkoutService->grandTotal($data);
        $checkoutService->finalizeOrder($orderData, $orderCost);

        return [
            'status' => true
        ];
    }
}
