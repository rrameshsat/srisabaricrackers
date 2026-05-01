<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller as BaseController;
use App\Jobs\NotifyOrderCreated;

class CheckoutController extends BaseController
{
    // Show checkout submit page / health for the endpoint
    public function showSubmit(Request $request)
    {
        $enabled = (bool) config('delivery.enabled', true);
        if (! $enabled) {
            return response()->json(['error' => 'Checkout submission is currently disabled'], 503);
        }

        // Provide a minimal payload that helps the frontend render a form/flow
        $states = array_keys(config('delivery.charges', []));
        if (empty($states)) {
            $states = [];
        }

        return response()->json([
            'status' => 'ready',
            'available_states' => $states,
        ]);
    }

    // Process checkout submit and compute state-based delivery charge
    public function submit(Request $request)
    {
        // Guard: ensure feature is enabled
        if (! (bool) config('delivery.enabled', true)) {
            return response()->json(['error' => 'Checkout submission is currently disabled'], 503);
        }

        // Validate required fields (shipping_state & subtotal are common minimums)
        $validator = \Validator::make($request->all(), [
            'shipping_state' => 'required|string|size:2',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input', 'details' => $validator->errors()], 422);
        }

        $state = strtoupper($request->input('shipping_state'));
        $subtotal = (float) $request->input('subtotal', 0.0);

        // Resolve delivery charge from config; fall back to default if missing
        $charges = (array) config('delivery.charges', []);
        $deliveryCharge = $charges[$state] ?? (float) config('delivery.default', 0.0);

        // Compute total as a simple sum; real apps should apply coupons, taxes, etc.
        $total = max(0.0, $subtotal + $deliveryCharge);

        // Optional: trigger a post-submission job (non-blocking)
        // If you later create an Order model, you can dispatch a job with the order id.
        //* Example: dispatch(new NotifyOrderCreated($orderId)); *//

        return response()->json([
            'shipping_state' => $state,
            'delivery_charge' => $deliveryCharge,
            'subtotal' => $subtotal,
            'total' => $total,
            'message' => 'Checkout submit processed',
        ]);
    }
}
