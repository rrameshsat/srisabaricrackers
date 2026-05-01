<?php

namespace App\Services;

use App\Models\Item;
use Illuminate\Support\Facades\Session;

class BookingCartService
{
    private const CART_KEY = 'booking_cart';

    public function addToCart(Item $product, int $quantity): array
    {
        $cart = $this->getCart();
        $productId = $product->id;

        $offerPrice = $product->discount_price ?? $product->previous_price;
        $originalPrice = $product->previous_price;
        $savings = ($originalPrice - $offerPrice) * $quantity;

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
            $cart[$productId]['total'] = $cart[$productId]['quantity'] * $offerPrice;
            $cart[$productId]['savings'] = ($originalPrice - $offerPrice) * $cart[$productId]['quantity'];
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'name' => $product->name,
                'image' => $product->thumbnail ?? $product->photo,
                'offer_price' => $offerPrice,
                'original_price' => $originalPrice,
                'quantity' => $quantity,
                'total' => $offerPrice * $quantity,
                'savings' => $savings,
                'stock' => $product->stock,
            ];
        }

        Session::put(self::CART_KEY, $cart);

        return [
            'success' => true,
            'cart' => $cart,
            'summary' => $this->getCartSummary(),
        ];
    }

    public function updateCartItem(int $productId, int $quantity): array
    {
        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Item not found in cart.',
            ];
        }

        $item = Item::find($productId);
        if (!$item || $item->stock < $quantity) {
            return [
                'success' => false,
                'message' => 'Not enough stock available.',
            ];
        }

        $offerPrice = $cart[$productId]['offer_price'];
        $originalPrice = $cart[$productId]['original_price'];

        if ($quantity <= 0) {
            unset($cart[$productId]);
        } else {
            $cart[$productId]['quantity'] = $quantity;
            $cart[$productId]['total'] = $offerPrice * $quantity;
            $cart[$productId]['savings'] = ($originalPrice - $offerPrice) * $quantity;
        }

        Session::put(self::CART_KEY, $cart);

        return [
            'success' => true,
            'cart' => $cart,
            'summary' => $this->getCartSummary(),
        ];
    }

    public function removeFromCart(int $productId): array
    {
        $cart = $this->getCart();

        if (!isset($cart[$productId])) {
            return [
                'success' => false,
                'message' => 'Item not found in cart.',
            ];
        }

        unset($cart[$productId]);
        Session::put(self::CART_KEY, $cart);

        return [
            'success' => true,
            'cart' => $cart,
            'summary' => $this->getCartSummary(),
        ];
    }

    public function getCartItems(): array
    {
        return $this->getCart();
    }

    public function getCartSummary(): array
    {
        $cart = $this->getCart();

        $productCount = count($cart);
        $itemCount = 0;
        $totalAmount = 0;
        $totalSavings = 0;

        foreach ($cart as $item) {
            $itemCount += $item['quantity'];
            $totalAmount += $item['total'];
            $totalSavings += $item['savings'];
        }

        return [
            'product_count' => $productCount,
            'item_count' => $itemCount,
            'total_amount' => $totalAmount,
            'savings' => $totalSavings,
        ];
    }

    public function clearCart(): void
    {
        Session::forget(self::CART_KEY);
    }

    private function getCart(): array
    {
        return Session::get(self::CART_KEY, []);
    }
}
