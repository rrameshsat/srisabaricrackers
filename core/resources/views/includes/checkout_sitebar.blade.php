<aside class="sidebar">
    <div class="padding-top-2x hidden-lg-up"></div>
    <!-- Items in Cart Widget-->
    @php
        $normalizeMetaText = function ($value) {
            $value = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value))));
            return $value === '' ? null : $value;
        };
        $displayMetaText = function ($value, $limit = 60) use ($normalizeMetaText) {
            $value = $normalizeMetaText($value);
            return $value ? Str::limit($value, $limit) : null;
        };
    @endphp


    <section class="card widget widget-featured-posts widget-order-summary p-4">
        <h3 class="widget-title">{{ __('Order Summary') }}</h3>
@php
            $conditional_shipping = DB::table('shipping_services')->whereStatus(1)->whereIsCondition(1)->first();
            $minimumOrderSetting = $extra_settings ?? null;
            
            $minOrderAmount = 3000;
            if ($minimumOrderSetting && $minimumOrderSetting->minimum_order_amount) {
                $minOrderAmount = $minimumOrderSetting->minimum_order_amount;
                if (Session::has('currency')) {
                    $curr = \App\Models\Currency::findOrFail(Session::get('currency'));
                    $minOrderAmount = $minOrderAmount * $curr->value;
                }
            }
        @endphp

        @if ($conditional_shipping && $cart_total < $conditional_shipping->minimum_price)
            <p class="free-shippin-aa"><em>{{ __('Shipping applies from order subtotal') }}
                    {{ PriceHelper::setCurrencyPrice($conditional_shipping->minimum_price) }}</em></p>
        @endif

        @if ($minimumOrderSetting && $minimumOrderSetting->is_min_order_message == 1 && $cart_total < (float) $minOrderAmount)
            <p class="text-danger mb-3">
                {{ $minimumOrderSetting->minimum_order_message ?: __('Minimum order amount must be above Rs.' . $minOrderAmount) }}
            </p>
        @endif

        <table class="table">
            <tr>
                <td>{{ __('Cart subtotal') }}:</td>
                <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($cart_total) }}</td>
            </tr>

            @if ($tax != 0)
                <tr>
                    <td>{{ __('Estimated tax') }}:</td>
                    <td class="text-gray-dark">{{ PriceHelper::setCurrencyPrice($tax) }}</td>
                </tr>
            @endif

            @if (!empty($state_price) && $state_price > 0)
                <tr class="set__state_price_tr">
                    <td>{{ __('State tax') }}:</td>
                    <td class="text-gray-dark set__state_price">
                        {{ PriceHelper::setCurrencyPrice($state_price) }}
                    </td>
                </tr>
            @endif

            @if ($discount)
                <tr>
                    <td>{{ __('Coupon discount') }}:</td>
                    <td class="text-danger">-
                        {{ PriceHelper::setCurrencyPrice($discount ? $discount['discount'] : 0) }}</td>
                </tr>
            @endif

            @if ($shipping)
                <tr class="d-none set__shipping_price_tr">
                    <td>{{ __('Shipping') }}:</td>
                    <td class="text-gray-dark set__shipping_price">
                        {{ PriceHelper::setCurrencyPrice($shipping ? $shipping->price : 0) }}</td>
                </tr>
            @endif
            <tr>
                <td class="text-lg text-primary">{{ __('Order total') }}</td>
                <td class="text-lg text-primary grand_total_set">{{ PriceHelper::setCurrencyPrice($grand_total) }}
                </td>
            </tr>
        </table>
    </section>


    <section class="card widget widget-featured-posts widget-featured-products p-4">
        <h3 class="widget-title">{{ __('Items In Your Cart') }}</h3>
        @foreach ($cart as $key => $item)
            @php
                $quantity = $item['qty'] ?? $item['quantity'] ?? 0;
                $productName = trim((string) ($item['name'] ?? ''));
                if ($productName === '' && \App\Models\Item::where('id', \App\Helpers\PriceHelper::GetItemId($key))->exists()) {
                    $productName = \App\Models\Item::find(\App\Helpers\PriceHelper::GetItemId($key))->name ?? '';
                }
                $price = \App\Helpers\PriceHelper::cartEntryTotalPrice($item);
            @endphp
            <div class="entry">
                <div class="entry-thumb"><a href="{{ route('front.product', $item['slug']) }}"><img
                            src="{{ url('/core/public/storage/images/' . $item['photo']) }}" alt="Product"></a>
                </div>
                <div class="entry-content">
                    <h4 class="entry-title"><a href="{{ route('front.product', $item['slug']) }}">
                            {{ Str::limit($productName, 40) }}

                        </a></h4>
                    <span class="entry-meta">{{ $quantity }} x {{ PriceHelper::setCurrencyPrice($price) }}.</span>

                    @if (!empty($item['attribute']['option_name']) && is_array($item['attribute']['option_name']))
                        @foreach ($item['attribute']['option_name'] as $optionkey => $option_name)
                            <div class="entry-meta">
                                <span class="entry-meta d-inline">{{ $item['attribute']['names'][$optionkey] }}:</span>
                                <span class="entry-meta d-inline"><b>{{ $option_name }}</b></span>
                            </div>
                        @endforeach
                    @endif

                </div>
            </div>
        @endforeach
    </section>

</aside>
