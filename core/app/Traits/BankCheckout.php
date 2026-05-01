<?php

namespace App\Traits;

use App\{
    Helpers\PriceHelper,
};
use App\Services\CheckoutService;
use App\Services\SettingsService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

trait BankCheckout
{

    public function BankSubmit($data){
        $user = Auth::user();
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($data, 'Bank Transfer', [
            'user_id' => isset($user) ? $user->id : 0,
            'transaction_number' => Str::random(10),
            'payment_status' => 'Unpaid',
            'txnid' => $data['txn_id'],
        ]);

        $orderCost = $checkoutService->grandTotal($data);
        $checkoutService->finalizeOrder($orderData, $orderCost);

        return [
            'status' => true
        ];
    }

}
