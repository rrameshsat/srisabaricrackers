<?php

namespace App\Services;

use App\Helpers\PriceHelper;
use App\Models\ExtraSetting;
use App\Models\Item;
use App\Models\ShippingService;
use App\Models\State;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class OrderPricingService
{
    protected function resolveAttributePrice(array $entry): float
    {
        if (!empty($entry['tax_exempt'])) {
            return 0.0;
        }

        if (isset($entry['attribute_price'])) {
            return (float) $entry['attribute_price'];
        }

        if (!empty($entry['attribute']['option_price']) && is_array($entry['attribute']['option_price'])) {
            return (float) array_sum($entry['attribute']['option_price']);
        }

        return 0.0;
    }

    public function getCart(): array
    {
        $cart = Session::get('cart', []);
        $cart = PriceHelper::normalizeCartEntries($cart);
        Session::put('cart', $cart);

        return $cart;
    }

    public function extractItemId(string|int $cartKey): int
    {
        return (int) PriceHelper::GetItemId((string) $cartKey);
    }

    public function getItemByCartKey(string|int $cartKey): ?Item
    {
        $itemId = $this->extractItemId($cartKey);
        $item = Item::find($itemId);
        
        if (!$item) {
            return null;
        }
        
        return $item;
    }

    public function cartRequiresShipping(?array $cart = null): bool
    {
        $cart = $cart ?? $this->getCart();

        foreach ($cart as $item) {
            $type = $item['item_type'] ?? $item['type'] ?? null;
            if ($type === 'normal') {
                return true;
            }
        }

        return false;
    }

    public function currentDiscount(): array
    {
        return Session::get('coupon', []);
    }

    public function shippingIsEligible(ShippingService $shipping, float $cartTotal): bool
    {
        if (!$shipping->is_condition) {
            return true;
        }

        return $cartTotal >= (float) $shipping->minimum_price;
    }

    public function resolveShipping(?int $shippingId = null, float $cartTotal = 0.0): ?ShippingService
    {
        if (!$this->cartRequiresShipping()) {
            return null;
        }

        if ($shippingId) {
            $shipping = ShippingService::find($shippingId);

            if ($shipping && $this->shippingIsEligible($shipping, $cartTotal)) {
                return $shipping;
            }

            return null;
        }

        return ShippingService::whereStatus(1)
            ->orderBy('is_condition')
            ->orderBy('minimum_price')
            ->orderBy('id')
            ->get()
            ->first(function (ShippingService $shipping) use ($cartTotal) {
                return $this->shippingIsEligible($shipping, $cartTotal);
            });
    }

    public function resolveState(?int $stateId = null): ?State
    {
        if ($stateId) {
            return State::where('status', 1)->findOrFail($stateId);
        }

        return null;
    }

    public function stateCharge(?State $state, float $cartTotal): float
    {
        if (!$state) {
            return 0.0;
        }

        $extraSetting = ExtraSetting::find(1);
        if (!$extraSetting || $extraSetting->is_state_delivery_charge != 1) {
            return 0.0;
        }

        $activeStatesCount = State::where('status', 1)->count();
        if ($activeStatesCount == 0) {
            return 0.0;
        }

        if ($state->type === 'fixed') {
            return (float) $state->price;
        }

        return ($cartTotal * $state->price) / 100;
    }

    public function calculate(
        ?array $cart = null,
        ?ShippingService $shipping = null,
        ?State $state = null,
        ?array $discount = null
    ): array {
        $cart = $cart ?? $this->getCart();
        $discount = $discount ?? $this->currentDiscount();

        $totalTax = 0.0;
        $cartTotal = 0.0;

        foreach ($cart as $key => $entry) {
            $qty = (int) ($entry['qty'] ?? $entry['quantity'] ?? 0);
            $cartTotal += PriceHelper::cartEntryTotalPrice($entry) * $qty;

            $item = $this->getItemByCartKey($key);
            if ($item && empty($entry['tax_exempt']) && empty($entry['quick_shopping']) && $item->tax) {
                $totalTax += Item::taxCalculate($item) * $qty;
            }
        }

        $statePrice = $this->stateCharge($state, $cartTotal);
        $shippingPrice = $shipping ? (float) $shipping->price : 0.0;
        $discountAmount = $discount['discount'] ?? 0.0;
        $grandTotal = ($cartTotal + $shippingPrice + $totalTax) - $discountAmount + $statePrice;

        return [
            'cart' => $cart,
            'cart_total' => $cartTotal,
            'tax' => $totalTax,
            'shipping' => $shipping,
            'shipping_price' => $shippingPrice,
            'discount' => $discount,
            'state' => $state,
            'state_price' => $statePrice,
            'grand_total' => $grandTotal,
        ];
    }

    public function checkoutViewData(?int $shippingId = null, ?int $stateId = null): array
    {
        $originalCart = $this->getCart();
        $cartTotal = 0.0;
        foreach ($originalCart as $entry) {
            $qty = (int) ($entry['qty'] ?? $entry['quantity'] ?? 0);
            $cartTotal += PriceHelper::cartEntryTotalPrice($entry) * $qty;
        }
        $pricing = $this->calculate(
            $originalCart,
            $this->resolveShipping($shippingId, $cartTotal),
            $this->resolveState($stateId)
        );

        return [
            'cart' => $originalCart,
            'cart_total' => $pricing['cart_total'],
            'grand_total' => $pricing['grand_total'],
            'discount' => $pricing['discount'],
            'shipping' => $this->cartRequiresShipping($pricing['cart']) ? $pricing['shipping'] : null,
            'tax' => $pricing['tax'],
            'state' => $pricing['state'],
            'state_price' => $pricing['state_price'],
        ];
    }
}
