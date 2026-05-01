<?php

namespace App\Helpers;

use App\Models\AttributeOption;
use App\Models\Currency;
use App\Models\ExtraSetting;
use App\Models\Item;
use App\Models\PaymentSetting;
use App\Models\Setting;
use App\Models\State;
use App\Models\Transaction;
use Illuminate\Support\Facades\Session;

class PriceHelper
{

    public static function setPrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        return round($price * $curr->value, 2);
    }

    public static function adminCurrencyPrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        $setting = Setting::first();
        $price = self::testPrice($price * $curr->value, 2);
        if ($setting->currency_direction == 1) {
            return $curr->sign . $price;
        } else {
            return $price . $curr->sign;
        }
    }

    public static function adminCurrency()
    {
        $curr = Currency::where('is_default', 1)->first();
        return $curr->sign;
    }

    public static function storePrice($price)
    {
        $curr = Currency::where('is_default', 1)->first();
        return round($price * $curr->value, 2);
    }

    public static function setCurrencyPrice($price)
    {

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }

        $setting = Setting::first();
        $price = self::testPrice(round($price * $curr->value, 2));

        if ($setting->currency_direction == 1) {
            return $curr->sign . $price;
        } else {
            return $price . $curr->sign;
        }
    }

    public static function setPreviousPrice($price)
    {

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        if ($price != 0) {
            $setting = Setting::first();
            $price = self::testPrice($price * $curr->value, 2);
            if ($setting->currency_direction == 1) {
                return $curr->sign . $price;
            } else {
                return $price . $curr->sign;
            }
        } else {
            $price = '';
        }

        return html_entity_decode($price);
    }

    public static function setConvertPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        return round($price * $curr->value, 2);
    }

    public static function convertPrice($price)
    {
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        return round($price / $curr->value, 2);
    }

    public static function setCurrencySign()
    {
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr->sign;
    }

    public static function setCurrencyValue()
    {
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr->value;
    }

    public static function setCurrencyName()
    {
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        return $curr->name;
    }

    public static function grandCurrencyPrice($item)
    {
        $option_price = 0;
        if (count($item->attributes) > 0) {
            foreach ($item->attributes as $attr) {
                if (isset($attr->options[0])) {
                    $option_price += $attr->options[0]->price;
                }
            }
        }

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        $price = $item->discount_price + $option_price;

        $setting = Setting::first();

        $price = self::testPrice(round($price * $curr->value, 2));

        if ($setting->currency_direction == 1) {
            return $curr->sign . $price;
        } else {
            return $price . $curr->sign;
        }

    }

    public static function grandPrice($item)
    {
        $option_price = 0;
        if (count($item->attributes) > 0) {
            foreach ($item->attributes as $attr) {
                if (isset($attr->options[0])) {
                    $option_price += PriceHelper::convertPrice($attr->options[0]->price);
                }

            }

        }

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
        $price = ($item->discount_price + $option_price);

        return $price;

    }

    public static function cartEntryAttributePrice(array $cart): float
    {
        if (isset($cart['attribute_price'])) {
            return (float) $cart['attribute_price'];
        }

        if (!empty($cart['attribute']['option_price']) && is_array($cart['attribute']['option_price'])) {
            return (float) array_sum($cart['attribute']['option_price']);
        }

        return 0.0;
    }

    public static function cartEntryUnitPrice(array $cart): float
    {
        $basePrice = (float) ($cart['main_price'] ?? $cart['price'] ?? 0);

        if (!empty($cart['tax_exempt']) || !empty($cart['quick_shopping'])) {
            $itemId = (int) ($cart['item_id'] ?? 0);
            if ($itemId > 0) {
                $item = Item::find($itemId);
                if ($item) {
                    return (float) $item->discount_price;
                }
            }
        }

        return $basePrice;
    }

    public static function cartEntryTotalPrice(array $cart): float
    {
        $attributePrice = (empty($cart['tax_exempt']) && empty($cart['quick_shopping'])) ? self::cartEntryAttributePrice($cart) : 0.0;

        return self::cartEntryUnitPrice($cart) + $attributePrice;
    }

    public static function Discount($discount)
    {
        if ($discount) {
            $discount = json_decode($discount, true);
        } else {
            $discount = 0;
        }
        return $discount;
    }

    public static function OrderTotal($order, $trns = null)
    {
        $cart = json_decode($order->cart, true);

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;

        foreach ($cart as $key => $items) {
            $qty = (int) ($items['qty'] ?? $items['quantity'] ?? 0);
            $total += self::cartEntryTotalPrice($items) * $qty;
            $cart_total = $total;
            $itemId = self::GetItemId($key);
            if (Item::where('id', $itemId)->exists()) {
                $item = Item::findOrFail($itemId);
                if (isset($item)) {

                    if (empty($items['tax_exempt']) && empty($items['quick_shopping']) && $item && $item->tax) {
                        $total_tax += $item::taxCalculate($item) * $qty;
                    }
                }
            }
        }

        $shipping = [];
        if (json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        }

        $discount = [];
        if (json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        }

        $grand_total = ($cart_total + ($shipping ? $shipping['price'] : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $grand_total = $grand_total + $order->state_price;

        $total_amount = round($grand_total * $order->currency_value, 2);
        if (!$trns) {
            $total_amount = self::testPrice($total_amount);
        }

        return $total_amount;
    }
    public static function OrderTotalChart($order)
    {
        $cart = json_decode($order->cart, true);

        $total_tax = 0;
        $cart_total = 0;
        $total = 0;
        $option_price = 0;

        foreach ($cart as $key => $items) {
            $qty = (int) ($items['qty'] ?? $items['quantity'] ?? 0);
            $total += self::cartEntryUnitPrice($items) * $qty;
            $option_price += empty($items['tax_exempt']) ? self::cartEntryAttributePrice($items) * $qty : 0.0;
            $cart_total = $total + $option_price;
            $itemId = self::GetItemId($key);
            if (Item::where('id', $itemId)->exists()) {
                $item = Item::findOrFail($itemId);
                if (isset($item)) {
                    if (empty($items['tax_exempt']) && empty($items['quick_shopping']) && $item && $item->tax) {
                        $total_tax += $item::taxCalculate($item) * $qty;
                    }
                }
            }
        }

        $shipping = [];
        if (json_decode($order->shipping)) {
            $shipping = json_decode($order->shipping, true);
        }
        $discount = [];
        if (json_decode($order->discount)) {
            $discount = json_decode($order->discount, true);
        }

        $grand_total = ($cart_total + ($shipping ? $shipping['price'] : 0)) + $total_tax;
        $grand_total = $grand_total - ($discount ? $discount['discount'] : 0);
        $curr = Currency::where('is_default', 1)->first();
        $total_amount = round($grand_total * $curr->value, 2);

        return $total_amount;
    }

    public static function cartTotal($cartt, $trns = null)
    {
        $total = 0;
    
        foreach ($cartt as $key => $cart) {
            $qty = (int) ($cart['qty'] ?? $cart['quantity'] ?? 0);
            $itemTotal = self::cartEntryTotalPrice($cart) * $qty;
            $total += $itemTotal;
        }
    
        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }
    
        if ($trns) {
            if ($trns == 2) {
                return $total;
            }
            return round($total / $curr->value, 2);
        }
    
        $price = self::testPrice($total / $curr->value);
        return $price;
    }

    public static function CheckDigital()
    {
        $cart = Session::get('cart') ?? [];
        $check_digital = false;
        foreach ($cart as $key => $item) {
            if ($item['item_type'] == 'normal') {
                $check_digital = true;
            }
        }

        return $check_digital;
    }

    public static function CheckDigitalPaymentGateway()
    {
        $cart = Session::get('cart') ?? [];
        $check_digital = true;
        foreach ($cart as $key => $item) {
            if ($item['item_type'] == 'normal') {
                $check_digital = false;
            }
        }
        return $check_digital;
    }

    public static function Transaction($order_id, $txn_id, $user_email, $amount)
    {

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }

        $transaction = new Transaction();
        $transaction->order_id = $order_id;
        $transaction->txn_id = $txn_id;
        $transaction->user_email = $user_email;
        $transaction->amount = $amount / $curr->value;
        $transaction->currency_sign = $curr->sign;
        $transaction->currency_value = $curr->value;
        $transaction->save();

    }

    public static function GatewayText($keyword)
    {
        return PaymentSetting::where('unique_keyword', $keyword)->first()->text;
    }

    public static function DiscountPercentage($item)
    {
        if ($item->previous_price && $item->previous_price != 0) {
            $discount_price = $item->previous_price - $item->discount_price;
            $percentage = round($discount_price / $item->previous_price * 100);
            return $percentage . '%';
        }
    }

    public static function GetItemId($cart_id)
    {
        if (is_numeric($cart_id)) {
            return (int) $cart_id;
        }

        $item_id = explode('-', (string) $cart_id);
        return (int) $item_id[0];
    }

    public static function normalizeCartEntries(array $cart): array
    {
        $normalized = [];

        foreach ($cart as $key => $item) {
            $itemId = (int) ($item['item_id'] ?? self::GetItemId($key));
            $optionIds = [];

            if (!empty($item['options_id']) && is_array($item['options_id'])) {
                $optionIds = array_map('intval', $item['options_id']);
            } elseif (!empty($item['option_id']) && is_array($item['option_id'])) {
                $optionIds = array_map('intval', $item['option_id']);
            } elseif (!empty($item['attribute_ids']) && is_array($item['attribute_ids'])) {
                $optionIds = array_map('intval', array_values($item['attribute_ids']));
            }

            $optionIds = array_values(array_filter($optionIds, static fn ($id) => $id > 0));
            sort($optionIds);

            $canonicalKey = $itemId . '-' . (!empty($optionIds) ? implode(',', $optionIds) : 'default');

            if (!isset($normalized[$canonicalKey])) {
                $normalized[$canonicalKey] = $item;
                $normalized[$canonicalKey]['item_id'] = $itemId;
                if (!empty($optionIds)) {
                    $normalized[$canonicalKey]['options_id'] = $optionIds;
                    $normalized[$canonicalKey]['option_id'] = $optionIds;
                }
                continue;
            }

            $existingQty = (int) ($normalized[$canonicalKey]['qty'] ?? $normalized[$canonicalKey]['quantity'] ?? 0);
            $incomingQty = (int) ($item['qty'] ?? $item['quantity'] ?? 0);
            $newQty = $existingQty + $incomingQty;

            if (array_key_exists('qty', $normalized[$canonicalKey])) {
                $normalized[$canonicalKey]['qty'] = $newQty;
            }

            if (array_key_exists('quantity', $normalized[$canonicalKey])) {
                $normalized[$canonicalKey]['quantity'] = $newQty;
            }

            if (!isset($normalized[$canonicalKey]['qty']) && !isset($normalized[$canonicalKey]['quantity'])) {
                $normalized[$canonicalKey]['qty'] = $newQty;
            }
        }

        return $normalized;
    }

    public static function LicenseQtyDecrese($cart)
    {
        foreach ($cart as $item_id => $item) {
            if ($item['item_type'] == 'license') {
                $item = Item::findOrFail(PriceHelper::GetItemId($item_id));
                $license_key_new = json_decode($item->license_key, true);
                $last_key = array_key_last($license_key_new);
                unset($license_key_new[$last_key]);
                $license_name_new = json_decode($item->license_key, true);
                unset($license_name_new[$last_key]);
                $item->license_name = json_encode($license_name_new, true);
                $item->license_key = json_encode($license_key_new, true);
                $item->update();
            }

        }
    }

    public static function stockDecrese()
    {
        $cart = Session::get('cart') ?? [];
        foreach ($cart as $key => $item) {
            $main_item = Item::findOrFail(self::GetItemId($key));
            if ($main_item->item_type == 'normal') {
                $current = $main_item->stock - $item['qty'];
                if ($current <= 0) {
                    $main_item->stock = 0;
                } else {
                    $main_item->stock = $current;
                }
                $main_item->update();
                if (isset($item['options_id']) && is_array($item['options_id'])) {
                    foreach ($item['options_id'] as $id) {
                        $option = AttributeOption::findOrFail($id);
                        if ($option->stock != 'unlimited') {
                            $new_stock = (int) $option->stock - $item['qty'];
                            if ($new_stock <= 0) {
                                $option->stock = '0';
                            } else {
                                $option->stock = (string) $new_stock;
                            }
                            $option->save();
                        }
                    }
                }
            }
        }
    }

    public static function testPrice($price)
    {

        $setting = Setting::first();

        if ($setting->is_decimal == 1) {
            if (is_numeric($price) || floor($price) != $price) {
                return number_format($price, 2, $setting->decimal_separator, $setting->thousand_separator);
            } else {
                return number_format($price, 2, $setting->decimal_separator, $setting->thousand_separator);
            }
        } else {

            return number_format($price);
        }
    }

    public static function Digital()
    {
        $cart = Session::get('cart') ?? [];
        $return = false;
        foreach ($cart as $item) {
            if ($item['type'] == 'normal') {
                $return = true;
            }
        }
        return $return;
    }

    public static function StatePrce($state_id, $grand_total)
    {
        $state_price = 0;
        if ($state_id) {
            $state = State::where('status', 1)->findOrFail($state_id);
            $extraSetting = ExtraSetting::find(1);
            if ($extraSetting && $extraSetting->is_state_delivery_charge == 1) {
                $activeStatesCount = State::where('status', 1)->count();
                if ($activeStatesCount > 0) {
                    if ($state->type == 'fixed') {
                        $state_price = $state->price;
                    } else {
                        $state_price = ($grand_total * $state->price) / 100;
                    }
                }
            }
        }

        return $state_price;
    }

    public static function checkCheckout($request)
    {
        $setting = Setting::first();
        if ($setting->is_single_checkout == 0) {
            return true;
        }

        Session::put('billing_address', $request->all());

        if (PriceHelper::CheckDigital()) {
            $shipping = [
                "ship_first_name" => $request->bill_first_name,
                "ship_last_name" => $request->bill_last_name,
                "ship_email" => $request->bill_email,
                "ship_phone" => $request->bill_phone,
                "ship_company" => $request->bill_company,
                "ship_address1" => $request->bill_address1,
                "ship_address2" => $request->bill_address2,
                "ship_zip" => $request->bill_zip,
                "ship_city" => $request->bill_city,
                "ship_country" => $request->bill_country,
            ];
        } else {
            $shipping = [
                "ship_first_name" => $request->bill_first_name,
                "ship_last_name" => $request->bill_last_name,
                "ship_email" => $request->bill_email,
                "ship_phone" => $request->bill_phone,
            ];
        }
        Session::put('shipping_address', $shipping);
    }

}
