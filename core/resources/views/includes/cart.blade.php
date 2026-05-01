@php
    $cart = Session::has('cart') ? Session::get('cart') : [];
    
    $minimumOrderSetting = $extra_settings ?? null;
    
    $minOrderAmount = 3000;
    if ($minimumOrderSetting && $minimumOrderSetting->minimum_order_amount) {
        $minOrderAmount = $minimumOrderSetting->minimum_order_amount;
        if (Session::has('currency')) {
            $curr = \App\Models\Currency::findOrFail(Session::get('currency'));
            $minOrderAmount = $minOrderAmount * $curr->value;
        }
    }
    
    $normalizeMetaText = function ($value) {
        $value = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value))));
        return $value === '' ? null : $value;
    };
    $displayMetaText = function ($value, $limit = 60) use ($normalizeMetaText) {
        $value = $normalizeMetaText($value);
        return $value ? Str::limit($value, $limit) : null;
    };
@endphp

<div class="card border-0">
    <div class="card-body">
        <div class="table-responsive shopping-cart">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>{{ __('Product') }}</th>
                        <th>{{ __('Price') }}</th>
                        <th>{{ __('Quantity') }}</th>
                        <th><span class="text-gray-dark">{{ __('Total') }}</span></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cart_view_load" data-target="{{ route('cart.get.load') }}">
                    @foreach ($cart as $key => $item)
                        @php
                            $quantity = $item['qty'] ?? $item['quantity'] ?? 0;
                            $itemId = \App\Helpers\PriceHelper::GetItemId($key);
                            $productName = trim((string) ($item['name'] ?? ''));
                            if ($productName === '' && \App\Models\Item::where('id', $itemId)->exists()) {
                                $productName = \App\Models\Item::find($itemId)->name ?? '';
                            }
                            $itemPrice = \App\Helpers\PriceHelper::cartEntryTotalPrice($item);
                            $itemSubtotal = $itemPrice * $quantity;
                        @endphp
                        <tr>
                            <td>
                                <div class="product-item">
                                    <a class="product-thumb" href="{{ route('front.product', $item['slug'] ?? '') }}">
                                        <img src="{{ url('/core/public/storage/images/' . ($item['photo'] ?? 'placeholder.png')) }}" alt="Product">
                                    </a>
                                    <div class="product-info">
                                        <h4 class="product-title">
                                            <a href="{{ route('front.product', $item['slug'] ?? '') }}">
                                                {{ Str::limit($productName, 45) }}
                                            </a>
                                        </h4>

                                        @if (!empty($item['attribute']['option_name']) && is_array($item['attribute']['option_name']))
                                            @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                                                <span>
                                                    <em>{{ $item['attribute']['names'][$optionkey] ?? '' }}:</em>
                                                    {{ $option_name }}
                                                </span>
                                            @endforeach
                                        @endif

                                    </div>
                                </div>
                            </td>

                            <td class="text-center text-lg">
                                {{ PriceHelper::setCurrencyPrice($itemPrice) }}
                            </td>

                            <td class="text-center">
                                @if (($item['item_type'] ?? '') === 'normal')
                                    <div class="qtySelector product-quantity">
                                        <span class="decreaseQtycart cartsubclick" data-id="{{ $key }}" data-target="{{ PriceHelper::GetItemId($key) }}">
                                            <i class="fas fa-minus"></i>
                                        </span>
                                        <input type="text" disabled class="qtyValue cartcart-amount" value="{{ $quantity }}">
                                        <span class="increaseQtycart cartaddclick" data-id="{{ $key }}" data-target="{{ PriceHelper::GetItemId($key) }}" data-item="{{ isset($item['options_id']) ? implode(',', (array) $item['options_id']) : '' }}">
                                            <i class="fas fa-plus"></i>
                                        </span>
                                        <input type="hidden" value="3333" id="current_stock">
                                    </div>
                                @else
                                    {{ $quantity }}
                                @endif
                            </td>

                            <td class="text-center text-lg">
                                {{ PriceHelper::setCurrencyPrice($itemSubtotal) }}
                            </td>

                            <td class="text-center">
                                <a class="remove-from-cart" href="{{ route('front.cart.destroy', $key) }}" data-toggle="tooltip" title="Remove item">
                                    <i class="icon-x"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card border-0 mt-4">
    <div class="card-body">
        <div class="shopping-cart-footer">
            <div class="column">
                <form class="coupon-form" method="post" id="coupon_form" action="{{ route('front.promo.submit') }}">
                    @csrf
                    <input class="form-control form-control-sm" name="code" type="text" placeholder="{{ __('Coupon code') }}" required>
                    <button class="btn btn-primary btn-sm" type="submit"><span>{{ __('Apply Coupon') }}</span></button>
                </form>
            </div>

            <div class="text-right text-lg column {{ Session::has('coupon') ? '' : 'd-none' }}">
                <span class="text-muted">{{ __('Discount') }} ({{ Session::has('coupon') ? Session::get('coupon')['code']['title'] : '' }}) : </span>
                <span class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(Session::has('coupon') ? Session::get('coupon')['discount'] : 0) }}</span>
                <a class="remove-from-cart btn btn-danger btn-sm" href="{{ route('front.promo.destroy') }}" data-toggle="tooltip" title="Remove item">
                    <i class="icon-x"></i>
                </a>
            </div>

            <div class="text-right column text-lg">
                <span class="text-muted">{{ __('Subtotal') }}: </span>
                <span class="text-gray-dark">{{ PriceHelper::setCurrencyPrice(\App\Helpers\PriceHelper::cartTotal($cart, 2)) }}</span>
            </div>
        </div>

        @if ($minimumOrderSetting && $minimumOrderSetting->is_min_order_message == 1 && \App\Helpers\PriceHelper::cartTotal($cart, 2) < (float) $minOrderAmount)
            <div class="alert alert-warning mt-3 mb-0">
                {{ $minimumOrderSetting->minimum_order_message ?: __('Minimum order amount must be above Rs.' . $minOrderAmount) }}
            </div>
        @endif

        <div class="shopping-cart-footer">
            <div class="column"><a class="btn btn-primary" href="{{ route('front.catalog') }}"><span><i class="icon-arrow-left"></i> {{ __('Back to Shopping') }}</span></a></div>
            <div class="column"><a class="btn btn-primary" href="{{ route('front.checkout.billing') }}"><span>{{ __('Checkout') }}</span></a></div>
        </div>
    </div>
</div>