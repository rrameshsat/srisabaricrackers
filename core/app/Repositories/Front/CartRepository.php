<?php

namespace App\Repositories\Front;

use App\{
    Models\Cart,
    Models\Item,
    Models\PromoCode,
    Helpers\PriceHelper
};
use App\Models\AttributeOption;
use App\Models\Attribute;
use Illuminate\Support\Facades\Session;

class CartRepository
{
    private function normalizeIds(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn ($id) => $id > 0);
        sort($ids);

        return array_values($ids);
    }

    private function cartEntryOptionIds(array $entry): array
    {
        if (!empty($entry['options_id']) && is_array($entry['options_id'])) {
            return $this->normalizeIds($entry['options_id']);
        }

        if (!empty($entry['option_id']) && is_array($entry['option_id'])) {
            return $this->normalizeIds($entry['option_id']);
        }

        if (!empty($entry['attribute_ids']) && is_array($entry['attribute_ids'])) {
            return $this->normalizeIds(array_values($entry['attribute_ids']));
        }

        return [];
    }

    private function findCartKeyByItemAndOptions(array $cart, int $itemId, array $optionIds): ?string
    {
        $normalizedOptions = $this->normalizeIds($optionIds);

        foreach ($cart as $key => $entry) {
            if ((int) ($entry['item_id'] ?? 0) !== $itemId) {
                continue;
            }

            if ($this->cartEntryOptionIds($entry) === $normalizedOptions) {
                return (string) $key;
            }
        }

        return null;
    }

    /**
     * Store cart.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store($request)
    {

        if (empty($request->all())) {
            $parsedUrl = parse_url($request->getRequestUri(), PHP_URL_QUERY); // Extracts the query part
            parse_str($parsedUrl, $queryArray);
            $request = (object)$queryArray;
            $qty_check  = 0;
            $input = $queryArray;
        } else {
            $input = $request->all();
        }

        $qty_check  = 0;

        $input['option_name'] = [];
        $input['option_price'] = [];
        $input['attr_name'] = [];

        $isIncrement = $input['isIncrement'];

        $qty = isset($input['quantity']) ? $input['quantity'] : 1;
        $qty = is_numeric($qty) ? $qty : 1;


        if ($input['options_ids']) {
            foreach (explode(',', $input['options_ids']) as $optionId) {
                $option = AttributeOption::findOrFail($optionId);
                if ($qty > $option->stock) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }
        }

        $cart = Session::get('cart');

        $item = Item::where('id', $input['item_id'])->select('id', 'name', 'photo', 'discount_price', 'previous_price', 'slug', 'item_type', 'license_name', 'license_key', 'stock')->first();

        if ($item->item_type == 'normal') {
            if ($item->stock < $request->quantity) {
                $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                return $data;
            }
        }



        $single = isset($request->type) ? ($request->type == '1' ? 1 : 0) : 0;

        if (Session::has('cart')) {
            if ($item->item_type == 'digital' || $item->item_type == 'license') {
                $check = array_key_exists($input['item_id'], Session::get('cart'));

                if ($check) {
                    $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                    return $data;
                } else {
                    if (array_key_exists($input['item_id'] . '-', Session::get('cart'))) {

                        $data = ['message' => 'Product already added', 'status' => 'alreadyInCart'];
                        return $data;
                    }
                }
            }
        }

        $option_id = [];

        if ($single == 1) {
            $attr_name = [];
            $option_name = [];
            $option_price = [];

            if (count($item->attributes) > 0) {
                foreach ($item->attributes as $attr) {
                    if (isset($attr->options[0]->name)) {
                        $attr_name[] = $attr->name;
                        $option_name[] = $attr->options[0]->name;
                        $option_price[] = $attr->options[0]->price;
                        $option_id[] = $attr->options[0]->id;
                    }
                }
            }

            $input['attr_name'] = $attr_name;
            $input['option_price'] = $option_price;
            $input['option_name'] = $option_name;
            $input['option_id'] = $option_id;

           
            if ($request->quantity != 'NaN') {
                $qty = $request->quantity;
                $qty_check = 1;
            } else {
                $qty = 1;
                $qty_check = 0;
            }
        } else {


            if ($input['attribute_ids']) {
                foreach (explode(',', $input['attribute_ids']) as $attrId) {
                    $attr = Attribute::findOrFail($attrId);
                    $attr_name[] = $attr->name;
                }
                $input['attr_name'] = $attr_name;
            }

            if ($input['options_ids']) {
                foreach (explode(',', $input['options_ids']) as $optionId) {
                    $option = AttributeOption::findOrFail($optionId);
                    $option_name[] = $option->name;
                    $option_price[] = $option->price;
                    $option_id[] = $option->id;
                }
                $input['option_name'] = $option_name;
                $input['option_price'] = $option_price;
            }
        }




        if (!$item) {
            abort(404);
        }


        $option_price = array_sum($input['option_price']);
        $normalizedOptionIds = $this->normalizeIds($option_id);
        $attribute['names'] = $input['attr_name'];
        $attribute['option_name'] = $input['option_name'];

        if (isset($request->item_key) && $request->item_key != (int) 0) {
            $cart_item_key = explode('-', $request->item_key)[1];
        } else {
            $cart_item_key = str_replace(' ', '', implode(',', $attribute['option_name']));
        }

        $attribute['option_price'] = $input['option_price'];
        $cart = Session::get('cart');
        $existingCartKey = $this->findCartKeyByItemAndOptions($cart ?? [], (int) $item->id, $normalizedOptionIds);
        $targetKey = $existingCartKey ?: $item->id . '-' . $cart_item_key;
        // if cart is empty then this the first product
        if (!$cart || !isset($cart[$targetKey])) {
            $license_name = json_decode($item->license_name, true);
            $license_key = json_decode($item->license_name, true);
            $cart[$targetKey] = [
                'options_id' => $option_id,
                'attribute' => $attribute,
                'attribute_price' => $option_price,
                "name" => $item->name,
                "slug" => $item->slug,
                "qty" => $qty,
                "price" => PriceHelper::grandPrice($item),
                "main_price" => $item->discount_price,
                "photo" => $item->photo,
                "type" => $item->item_type,
                "item_type" => $item->item_type,
                'item_l_n' => $item->item_type == 'license' ? end($license_name) : null,
                'item_l_k' => $item->item_type == 'license' ? end($license_key) : null,
                'unit' => $item->sort_details ?? null,
                'box_contents' => $item->details ?? null,
            ];

            Session::put('cart', $cart);


            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }

            $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            return $mgs;
        }


        // if cart not empty then check if this product exist then increment quantity
        if (isset($cart[$targetKey])) {

            $cart = Session::get('cart');

            if ($qty_check == 1) {
                if($isIncrement == 'plus'){
                    $cart[$targetKey]['qty'] +=  1;
                }
                else if($isIncrement == 'minus'){
                    $nQty = $cart[$targetKey]['qty'];
                    if($nQty == 1){
                        $cart[$targetKey]['qty'] = 1;
                    }else{
                        $cart[$targetKey]['qty'] -=  1;
                    }
                }
                else{
                    $cart[$targetKey]['qty'] = $qty;
                }
            } else {
                $cart[$targetKey]['qty'] +=  $qty;
            }

            if ($item->item_type == 'normal') {

                if ($item->stock < (int)$cart[$targetKey]['qty']) {
                    $data = ['message' => 'Product Out Of Stock', 'status' => 'outStock'];
                    return $data;
                }
            }


            Session::put('cart', $cart);

            $coupon = Session::get('coupon');

            if ($coupon) {
                $promo_code = (object)$coupon['code'];

                $cart = Session::get('cart');
                $cartTotal = PriceHelper::cartTotal($cart, 2);
                $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

                $coupon = [
                    'discount' => $discount['sub'],
                    'code'  => $promo_code
                ];
                Session::put('coupon', $coupon);
            }



            if ($qty_check == 1) {
                $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            } else {
                $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
            }

            $qty_check = 0;
            return $mgs;
        }

        $mgs = ['message' => __('Product add successfully'), 'qty' => count(Session::get('cart'))];
        return $mgs;
    }

    public function promoStore($request)
    {

        $input = $request->all();
        $promo_code = PromoCode::where('status', 1)->whereCodeName($input['code'])->where('no_of_times', '>', 0)->first();

        if ($promo_code) {
            $cart = Session::get('cart');
            $cartTotal = PriceHelper::cartTotal($cart, 2);
            $discount = $this->getDiscount($promo_code->discount, $promo_code->type, $cartTotal);

            $coupon = [
                'discount' => $discount['sub'],
                'code'  => $promo_code
            ];
            Session::put('coupon', $coupon);

            return [
                'status'  => true,
                'message' => __('Promo code found!')
            ];
        } else {
            return [
                'status'  => false,
                'message' => __('No coupon code found')
            ];
        }
    }



    public function getCart()
    {
        $cart = Session::has('cart') ? Session::get('cart') : null;
        return $cart;
    }

    public function getDiscount($discount, $type, $price)
    {
        if ($type == 'amount') {
            $sub = $discount;
            $total = $price - $sub;
        } else {
            $val = $price / 100;
            $sub = $val * $discount;
            $total = $price - $sub;
        }

        return [
            'sub' => $sub,
            'total' => $total
        ];
    }
}
