@php
    $grandSubtotal = 0;
    $normalizeMetaText = function ($value) {
        $value = trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string) $value))));
        return $value === '' ? null : $value;
    };
    $displayMetaText = function ($value, $limit = 60) use ($normalizeMetaText) {
        $value = $normalizeMetaText($value);
        return $value ? Str::limit($value, $limit) : null;
    };
@endphp

@if (Session::has('cart'))
    @foreach (Session::get('cart') as $key => $cart)
        @php
            $quantity = $cart['qty'] ?? $cart['quantity'] ?? 0;
            $productName = trim((string) ($cart['name'] ?? ''));
            if ($productName === '' && \App\Models\Item::where('id', \App\Helpers\PriceHelper::GetItemId($key))->exists()) {
                $productName = \App\Models\Item::find(\App\Helpers\PriceHelper::GetItemId($key))->name ?? '';
            }
            $itemPrice = \App\Helpers\PriceHelper::cartEntryTotalPrice($cart);
            $itemSubtotal = $itemPrice * $quantity;
            $grandSubtotal += $itemSubtotal;
        @endphp
        <div class="entry">
            <div class="entry-thumb">
                <a href="{{ route('front.product', $cart['slug'] ?? '') }}">
                    <img src="{{ url('/core/public/storage/images/' . ($cart['photo'] ?? 'placeholder.png')) }}" alt="Product">
                </a>
            </div>
            <div class="entry-content">
                <h4 class="entry-title">
                    <a href="{{ route('front.product', $cart['slug'] ?? '') }}">
                        {{ Str::limit($productName, 29) }}
                    </a>
                </h4>
                <span class="entry-meta">{{ $quantity }} x {{ PriceHelper::setCurrencyPrice($itemPrice) }}</span>

                @if (!empty($cart['attribute']['option_name']) && is_array($cart['attribute']['option_name']))
                    @foreach ($cart['attribute']['option_name'] as $optionkey => $option_name)
                        <span class="att"><em>{{ $cart['attribute']['names'][$optionkey] ?? '' }}:</em> {{ $option_name }}</span>
                    @endforeach
                @endif

            </div>
            <div class="entry-delete">
                <a href="{{ route('front.cart.destroy', $key) }}"><i class="icon-x"></i></a>
            </div>
        </div>
    @endforeach

    <div class="text-right">
        <p class="text-gray-dark py-2 mb-0">
            <span class="text-muted">{{ __('Subtotal') }}:</span>
            {{ PriceHelper::setCurrencyPrice($grandSubtotal) }}
        </p>
    </div>

    <div class="d-flex justify-content-between">
        <div class="w-50 d-block">
            <a class="btn btn-primary btn-sm mb-0" href="{{ route('front.cart') }}"><span>{{ __('Cart') }}</span></a>
        </div>
        <div class="w-50 d-block text-end">
            <a class="btn btn-primary btn-sm mb-0" href="{{ route('front.checkout.billing') }}"><span>{{ __('Checkout') }}</span></a>
        </div>
    </div>
@else
    {{ __('Cart empty') }}
@endif
