<?php

namespace App\Traits;

use App\{
    Helpers\PriceHelper,
};
use App\Services\CheckoutService;
use Exception;
use Illuminate\Support\Facades\Auth;

trait PaystackCheckout
{


    public function paystackSubmit($data){
        $user = Auth::user();
        $checkoutService = app(CheckoutService::class);
        $orderData = $checkoutService->buildOrderData($data, 'Paystack', [
            'user_id' => isset($user) ? $user->id : 0,
        ]);
        $total_amount = $checkoutService->grandTotal($data);

        try{
                $orderData['txnid'] =  $data['ref_id'];
                $orderData['payment_status'] = 'Paid';
                $checkoutService->finalizeOrder($orderData, $total_amount);
                return [
                    'status' => true
                ];
            

        }catch (Exception $e){

            return [
                'status' => false,
                'message' => $e->getMessage()
            ];

        }
    }

}
