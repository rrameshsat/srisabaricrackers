<?php

namespace App\Http\Controllers\Front;

use App\Models\Item;
use App\Models\ExtraSetting;
use App\Models\Currency;
use App\Http\Controllers\Controller;
use App\Repositories\Front\CartRepository;
use App\Helpers\PriceHelper;
use App\Models\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    private function getMinimumOrderAmount()
    {
        $setting = ExtraSetting::find(1);
        $minimumOrderAmount = $setting->minimum_order_amount ?? 3000;

        if (Session::has('currency')) {
            $curr = Currency::findOrFail(Session::get('currency'));
        } else {
            $curr = Currency::where('is_default', 1)->first();
        }

        return $minimumOrderAmount * $curr->value;
    }

    /**
     * Constructor Method.
     *
     * @param  \App\Repositories\Front\CartRepository $repository
     *
     */
    public function __construct(CartRepository $repository)
    {
        $this->repository = $repository;
        $this->middleware('localize');
    }

    protected function normalizeCartSession(): array
    {
        $cart = Session::get('cart', []);
        $cart = PriceHelper::normalizeCartEntries($cart);
        Session::put('cart', $cart);

        return $cart;
    }

    public function index()
    {
        $cart = $this->normalizeCartSession();
        return view('front.catalog.cart', [
            'cart' => $cart,
            'extra_settings' => ExtraSetting::find(1),
        ]);
    }


    public function addToCart(Request $request)
    {

        $msg = $this->repository->store($request);


        if ($request->ajax()) {
            return $msg;
        }
    }

    public function store(Request $request)
    {

        $msg = $this->repository->store($request);
        if (isset($request->addtocart)) {
            Session::flash('success_message', __('Cart Added Successfully'));
            return back();
        }

        $extra_settings = ExtraSetting::find(1);
        $cart = Session::get('cart', []);
        $cartTotal = PriceHelper::cartTotal($cart);
        $minimumOrderAmount = $this->getMinimumOrderAmount();
        if ($extra_settings->is_min_order_message == 1 && $cartTotal < (float) $minimumOrderAmount) {
            return redirect()->route('front.cart')->withError($extra_settings->minimum_order_message ?: __('Minimum order amount must be above Rs.') . $minimumOrderAmount);
        }

        return redirect()->route('front.checkout.billing')->withSuccess($msg);
    }

    public function destroy($id)
    {

        $cart = Session::get('cart');
        unset($cart[$id]);
        if (count($cart) > 0) {
            Session::put('cart', $cart);
        } else {
            Session::forget('cart');
        }
        Session::flash('success', __('Cart item remove successfully.'));
        return back();
    }

    public function promoStore(Request $request)
    {
        return response()->json($this->repository->promoStore($request));
    }

    public function shippingStore(Request $request)
    {
        return redirect()->route('front.checkout');
    }


    public function update($id)
    {
        return view('front.catalog.cart_form', [
            'item' => Item::findOrFail($id),
            'attributes' => Item::findOrFail($id)->attributes,
            'cart_item' => Session::get('cart')[$id],
        ]);
    }


    public function shippingCharge(Request $request)
    {

        $charges = [];
        $items = [];
        foreach ($request->user_id as $data) {
            $check = explode('|', $data);
            $charges[] = $check[0];
            $items[] = $check[1];
        }
        $cart = Session::get('cart');
        $delivery_amount = 0;
        foreach ($charges as $index => $charge) {
            if ($charge != 0) {
                $vendor_charge = Item::findOrFail($items[$index])->user->shipping->price;
                $delivery_amount += $vendor_charge;
                $cart[$items[$index]]['delivery_charge'] = $vendor_charge;
            } else {
                $cart[$items[$index]]['delivery_charge'] = 0;
            }
        }

        Session::put('cart', $cart);

        return response()->json(['delivery' => PriceHelper::setPrice($delivery_amount), 'main' => $delivery_amount]);
    }


    public function headerCartLoad()
    {
        $this->normalizeCartSession();
        return view('includes.header_cart');
    }
    public function CartLoad()
    {
        $this->normalizeCartSession();
        return view('includes.cart');
    }

    public function cartClear()
    {
        Session::forget('cart');
        Session::flash('success', __('Cart clear successfully'));
        return back();
    }

    public function promoDelete()
    {
        Session::forget('coupon');
        Session::flash('success', __('Promo code remove successfully'));
        return back();
    }
}
