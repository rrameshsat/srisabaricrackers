<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Helpers\PriceHelper;
use App\Models\Category;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class BookingController extends Controller
{
    protected function normalizeOptionIds(array $ids): array
    {
        $ids = array_map('intval', $ids);
        $ids = array_filter($ids, static fn ($id) => $id > 0);
        sort($ids);

        return array_values($ids);
    }

    protected function cartEntryOptionIds(array $entry): array
    {
        if (!empty($entry['options_id']) && is_array($entry['options_id'])) {
            return $this->normalizeOptionIds($entry['options_id']);
        }

        if (!empty($entry['option_id']) && is_array($entry['option_id'])) {
            return $this->normalizeOptionIds($entry['option_id']);
        }

        return [];
    }

    protected function findMainCartKey(array $cart, int $itemId, array $optionIds): ?string
    {
        $normalized = $this->normalizeOptionIds($optionIds);

        foreach ($cart as $key => $entry) {
            if ((int) ($entry['item_id'] ?? 0) !== $itemId) {
                continue;
            }

            if ($this->cartEntryOptionIds($entry) === $normalized) {
                return (string) $key;
            }
        }

        return null;
    }

    protected function quickShoppingPrice(Item $item): float
    {
        return (float) $item->discount_price;
    }

    public function index()
    {
        return view('front.booking');
    }

    public function getCategories()
    {
        $categories = Category::withCount(['items' => function ($query) {
            $query->where('status', 1)->where('item_type', 'normal');
        }])
        ->where('status', 1)
        ->orderBy('serial', 'asc')
        ->get(['id', 'name', 'slug', 'photo'])
        ->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
                'photo' => $category->photo,
                'product_count' => $category->items_count,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $categories
        ]);
    }

    public function getProductsByCategory($categoryId)
    {
        try {
            $items = Item::where('category_id', $categoryId)
                ->where('status', 1)
                ->where('item_type', 'normal')
                ->orderBy('name', 'asc')
                ->with('attributes.options')
                ->get([
                    'id', 'name', 'slug', 'photo', 'thumbnail', 
                    'discount_price', 'previous_price', 'stock', 'sku'
                ])
                ->map(function ($item) {
                    $offerPrice = PriceHelper::setConvertPrice($this->quickShoppingPrice($item));
                    $previousPrice = PriceHelper::setConvertPrice($item->previous_price);
                    return [
                        'id' => $item->id,
                        'name' => $item->name,
                        'slug' => $item->slug,
                        'image' => $item->photo,
                        'thumbnail' => $item->thumbnail,
                        'offer_price' => $offerPrice,
                        'previous_price' => $previousPrice,
                        'stock' => $item->stock,
                        'sku' => $item->sku,
                        'savings' => $previousPrice > $offerPrice 
                            ? round((($previousPrice - $offerPrice) / $previousPrice) * 100) 
                            : 0,
                    'attributes' => $item->attributes->map(function ($attr) {
                        return [
                            'id' => $attr->id,
                            'name' => $attr->name,
                            'options' => $attr->options->map(function ($opt) {
                                return [
                                    'id' => $opt->id,
                                    'name' => $opt->name,
                                    'price' => 0,
                                ];
                            }),
                        ];
                    }),
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $items
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'line' => $e->getLine()
            ], 500);
        }
    }

    public function addToCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
        ]);

        $item = Item::where('id', $request->item_id)
            ->with('attributes.options')
            ->where('status', 1)
            ->where('item_type', 'normal')
            ->first();

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Product not found'
            ], 404);
        }

        if ($item->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Not enough stock available'
            ], 400);
        }

        $attributes = $request->input('attributes', []);
        $attrData = [];
        $attrPrice = 0;
        
        if (!empty($attributes)) {
            foreach ($attributes as $attrId => $optionId) {
                $option = \App\Models\AttributeOption::find($optionId);
                if ($option && $option->item_id == $item->id) {
                    $attr = \App\Models\Attribute::find($attrId);
                    $attrData[$attrId] = [
                        'name' => $attr->name ?? '',
                        'option_id' => $optionId,
                        'option_name' => $option->name,
                        'price' => 0,
                    ];
                }
            }
        }

        $cartKey = 'booking_cart';
        $cart = Session::get($cartKey, []);

        $cartItemKey = $item->id . '-' . ($attributes ? md5(json_encode($attributes)) : 'default');

        if (isset($cart[$cartItemKey])) {
            $newQty = $cart[$cartItemKey]['quantity'] + $request->quantity;
            if ($newQty > $item->stock) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available'
                ], 400);
            }
            $cart[$cartItemKey]['quantity'] = $newQty;
            $cart[$cartItemKey]['name'] = $item->name;
            $cart[$cartItemKey]['slug'] = $item->slug;
            $cart[$cartItemKey]['photo'] = $item->photo;
            $cart[$cartItemKey]['price'] = PriceHelper::setConvertPrice($this->quickShoppingPrice($item));
            $cart[$cartItemKey]['base_price'] = $cart[$cartItemKey]['price'];
            $cart[$cartItemKey]['attribute_price'] = 0;
            $cart[$cartItemKey]['previous_price'] = PriceHelper::setConvertPrice($item->previous_price);
            $cart[$cartItemKey]['attributes'] = $attrData;
            $cart[$cartItemKey]['attribute_ids'] = $attributes;
        } else {
            $basePrice = $this->quickShoppingPrice($item);
            $cart[$cartItemKey] = [
                'item_id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'photo' => $item->photo,
                'price' => $basePrice,
                'base_price' => $basePrice,
                'attribute_price' => 0,
                'previous_price' => PriceHelper::setConvertPrice($item->previous_price),
                'quantity' => $request->quantity,
                'stock' => $item->stock,
                'attributes' => $attrData,
                'attribute_ids' => $attributes,
            ];
        }

        Session::put($cartKey, $cart);

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'data' => $this->getCartSummary()
        ]);
    }

    public function updateCartItem(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
            'quantity' => 'required|integer|min:0',
        ]);

        $cartKey = 'booking_cart';
        $cart = Session::get($cartKey, []);

        $attributes = $request->input('attributes', []);
        $cartItemKey = $request->item_id . '-' . ($attributes ? md5(json_encode($attributes)) : 'default');

        if ($request->quantity == 0) {
            if (isset($cart[$cartItemKey])) {
                unset($cart[$cartItemKey]);
                Session::put($cartKey, $cart);
            }
        } else {
            $item = Item::find($request->item_id);
            
            if (!$item) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }

            if ($item->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Not enough stock available'
                ], 400);
            }

            $attrData = [];
            $attrPrice = 0;
            if (!empty($attributes)) {
                foreach ($attributes as $attrId => $optionId) {
                    $option = \App\Models\AttributeOption::find($optionId);
                    if ($option && $option->item_id == $item->id) {
                        $attr = \App\Models\Attribute::find($attrId);
                        $attrData[$attrId] = [
                            'name' => $attr->name ?? '',
                            'option_id' => $optionId,
                            'option_name' => $option->name,
                            'price' => 0,
                        ];
                    }
                }
            }

            if (isset($cart[$cartItemKey])) {
                $cart[$cartItemKey]['quantity'] = $request->quantity;
                $cart[$cartItemKey]['attributes'] = $attrData;
                $cart[$cartItemKey]['attribute_ids'] = $attributes;
                $cart[$cartItemKey]['attribute_price'] = 0;
                $basePrice = PriceHelper::setConvertPrice($this->quickShoppingPrice($item));
                $cart[$cartItemKey]['base_price'] = $basePrice;
                $cart[$cartItemKey]['price'] = $basePrice;
                $cart[$cartItemKey]['name'] = $item->name;
                $cart[$cartItemKey]['slug'] = $item->slug;
                $cart[$cartItemKey]['photo'] = $item->photo;
                $cart[$cartItemKey]['previous_price'] = PriceHelper::setConvertPrice($item->previous_price);
                Session::put($cartKey, $cart);
            } else {
                $basePrice = PriceHelper::setConvertPrice($this->quickShoppingPrice($item));
                $cart[$cartItemKey] = [
                    'item_id' => $item->id,
                    'name' => $item->name,
                    'slug' => $item->slug,
                    'photo' => $item->photo,
                    'price' => $basePrice,
                    'base_price' => $basePrice,
                    'attribute_price' => 0,
                    'previous_price' => PriceHelper::setConvertPrice($item->previous_price),
                    'quantity' => $request->quantity,
                    'stock' => $item->stock,
                    'attributes' => $attrData,
                    'attribute_ids' => $attributes,
                ];
                Session::put($cartKey, $cart);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => $this->getCartSummary()
        ]);
    }

    public function removeFromCart(Request $request)
    {
        $request->validate([
            'item_id' => 'required|integer',
        ]);

        $cartKey = 'booking_cart';
        $cart = Session::get($cartKey, []);

        $attributes = $request->input('attributes', []);
        $cartItemKey = $request->item_id . '-' . ($attributes ? md5(json_encode($attributes)) : 'default');

        if (isset($cart[$cartItemKey])) {
            unset($cart[$cartItemKey]);
            Session::put($cartKey, $cart);
        }

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart',
            'data' => $this->getCartSummary()
        ]);
    }

    public function getCartSummary()
    {
        $cartKey = 'booking_cart';
        $cart = Session::get($cartKey, []);

        $productCount = count($cart);
        $itemCount = 0;
        $totalAmount = 0;
        $previousTotal = 0;
        $normalizedCart = [];

        foreach ($cart as $key => $item) {
            $itemModel = isset($item['item_id']) ? Item::find($item['item_id']) : null;
            $price = $itemModel ? PriceHelper::setConvertPrice($this->quickShoppingPrice($itemModel)) : (float) ($item['price'] ?? 0);
            $previousPrice = $itemModel ? PriceHelper::setConvertPrice($itemModel->previous_price) : (float) ($item['previous_price'] ?? 0);
            $quantity = (int) ($item['quantity'] ?? 0);

            $item['price'] = $price;
            $item['base_price'] = $price;
            $item['attribute_price'] = 0;
            $item['previous_price'] = $previousPrice;

            $normalizedCart[$key] = $item;
            $itemCount += $quantity;
            $totalAmount += $price * $quantity;
            $previousTotal += $previousPrice * $quantity;
        }

        Session::put($cartKey, $normalizedCart);

        return [
            'items' => $normalizedCart,
            'product_count' => $productCount,
            'item_count' => $itemCount,
            'total_amount' => round($totalAmount, 2),
            'previous_total' => round($previousTotal, 2),
            'savings' => round($previousTotal - $totalAmount, 2),
        ];
    }

    public function getCart(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $this->getCartSummary()
        ]);
    }

    public function clearCart()
    {
        Session::forget('booking_cart');

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared',
            'data' => $this->getCartSummary()
        ]);
    }

    public function transferToMainCart()
    {
        $bookingCart = Session::get('booking_cart', []);
        
        if (empty($bookingCart)) {
            return response()->json([
                'success' => false,
                'message' => 'Booking cart is empty'
            ], 400);
        }

        $mainCart = Session::get('cart', []);

        foreach ($bookingCart as $cartKey => $bookingItem) {
            $itemId = $bookingItem['item_id'] ?? 0;
            $item = Item::find($itemId);
            
            if (!$item || $item->status != 1 || $item->item_type != 'normal') {
                continue;
            }

            $bookingAttrs = $bookingItem['attribute_ids'] ?? [];
            $attrNames = $bookingItem['attributes'] ?? [];
            $attrPrices = [];
            $attrPriceTotal = 0;
            $optionIds = [];
            $optionNames = [];
            $attrNameList = [];

            if (!empty($bookingAttrs)) {
                foreach ($bookingAttrs as $attrId => $optionId) {
                    $option = \App\Models\AttributeOption::find($optionId);
                    if ($option) {
                        $attr = \App\Models\Attribute::find($attrId);
                        $attrNameList[] = $attr->name ?? '';
                        $optionIds[] = $optionId;
                        $optionNames[] = $option->name;
                        $attrPrices[] = 0;
                    }
                }
            }

            $cartItemKey = $this->findMainCartKey($mainCart, $itemId, $optionIds)
                ?: $itemId . '-' . ($bookingAttrs ? implode(',', $optionIds) : '');

            $basePrice = PriceHelper::setConvertPrice($this->quickShoppingPrice($item));

            if (isset($mainCart[$cartItemKey])) {
                $mainCart[$cartItemKey]['qty'] += $bookingItem['quantity'];
            } else {
                $mainCart[$cartItemKey] = [
                    'item_id' => $item->id,
                    'qty' => $bookingItem['quantity'],
                    'item_type' => 'normal',
                    'options_id' => $optionIds,
                    'option_id' => $optionIds,
                    'price' => $basePrice,
                    'main_price' => $basePrice,
                    'attribute_price' => 0,
                    'tax_exempt' => true,
                    'quick_shopping' => true,
                    'attribute' => [
                        'names' => $attrNameList,
                        'option_name' => $optionNames,
                        'option_price' => $attrPrices,
                    ],
                    'license' => null,
                    'unit' => $item->sort_details ?? null,
                    'box_contents' => $item->details ?? null,
                    'slug' => $item->slug,
                    'photo' => $item->photo,
                ];
            }
        }

        Session::put('cart', $mainCart);
        
        return response()->json([
            'success' => true,
            'message' => 'Cart transferred successfully',
            'redirect_url' => route('front.checkout')
        ]);
    }
}
