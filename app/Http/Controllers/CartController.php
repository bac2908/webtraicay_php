<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CartController extends Controller
{
    public function index()
    {
        $cartItems = $this->getCartItems();
        $summary = $this->getCartSummary($cartItems);
        $totalQuantity = $cartItems->sum('quantity');

        return view('cart', [
            'cartItems' => $cartItems,
            'totalQuantity' => $totalQuantity,
            'summary' => $summary,
            'appliedCoupon' => $summary['coupon'],
        ]);
    }

    public function add(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['nullable', 'integer', 'min:1'],
        ]);

        $product = Product::query()
            ->where('id', $validated['product_id'])
            ->where('is_active', true)
            ->firstOrFail();

        $productId = (string) $product->id;
        $quantity = (int) ($validated['quantity'] ?? 1);
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => (int) $productId,
                'quantity' => $quantity,
            ];
        }

        session(['cart' => $cart]);

        if ($request->boolean('checkout_redirect')) {
            return redirect()->route('checkout');
        }

        return redirect()->back()->with('success', 'Da them san pham vao gio hang.');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
            'quantity' => ['required', 'integer', 'min:1', 'max:99'],
        ]);

        $productId = (string) $validated['product_id'];
        $cart = session('cart', []);

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] = (int) $validated['quantity'];
            session(['cart' => $cart]);
        }

        return redirect()->route('cart')->with('success', 'Da cap nhat gio hang.');
    }

    public function remove(Request $request)
    {
        $validated = $request->validate([
            'product_id' => ['required', 'integer'],
        ]);

        $productId = (string) $validated['product_id'];
        $cart = session('cart', []);

        unset($cart[$productId]);
        session(['cart' => $cart]);

        if (empty($cart)) {
            session()->forget('cart_coupon_code');
        }

        return redirect()->route('cart')->with('success', 'Da xoa san pham khoi gio hang.');
    }

    public function applyCoupon(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'max:80'],
        ]);

        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Gio hang dang trong, khong the ap ma giam gia.');
        }

        $subtotal = (int) $cartItems->sum('line_total');
        $code = trim((string) $validated['code']);

        $coupon = Coupon::query()
            ->whereRaw('LOWER(code) = ?', [Str::lower($code)])
            ->first();

        if (!$coupon || !$coupon->isValid()) {
            return redirect()->route('cart')->with('error', 'Ma giam gia khong hop le hoac da het han.');
        }

        if ($coupon->min_order_total && $subtotal < (int) $coupon->min_order_total) {
            return redirect()->route('cart')->with('error', 'Don hang chua dat gia tri toi thieu de ap ma.');
        }

        session(['cart_coupon_code' => $coupon->code]);

        return redirect()->route('cart')->with('success', 'Ap ma giam gia thanh cong.');
    }

    public function removeCoupon()
    {
        session()->forget('cart_coupon_code');

        return redirect()->route('cart')->with('success', 'Da bo ma giam gia.');
    }

    public function checkout()
    {
        $cartItems = $this->getCartItems();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Gio hang dang trong. Vui long them san pham de tiep tuc.');
        }

        $summary = $this->getCartSummary($cartItems);

        return view('checkout', [
            'cartItems' => $cartItems,
            'summary' => $summary,
            'appliedCoupon' => $summary['coupon'],
        ]);
    }

    public function placeOrder(Request $request)
    {
        $validated = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'shipping_address' => ['required', 'string', 'max:255'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $cartItems = $this->getCartItems();
        if ($cartItems->isEmpty()) {
            return redirect()->route('cart')->with('error', 'Gio hang dang trong.');
        }

        $summary = $this->getCartSummary($cartItems);

        $order = DB::transaction(function () use ($validated, $cartItems, $summary) {
            $order = Order::query()->create([
                'code' => $this->generateOrderCode(),
                'user_id' => auth()->id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'] ?? null,
                'customer_email' => $validated['customer_email'] ?? null,
                'shipping_address' => $validated['shipping_address'],
                'subtotal' => (int) $summary['subtotal'],
                'shipping_fee' => (int) $summary['shipping_fee'],
                'discount_total' => (int) $summary['discount_total'],
                'total' => (int) $summary['total'],
                'coupon_code' => $summary['coupon'] ? $summary['coupon']->code : null,
                'status' => Order::STATUS_PENDING,
            ]);

            foreach ($cartItems as $item) {
                OrderItem::query()->create([
                    'order_id' => $order->id,
                    'product_id' => $item['product']->id,
                    'product_name' => $item['product']->name,
                    'unit' => $item['product']->unit,
                    'unit_price' => (int) $item['unit_price'],
                    'qty' => (int) $item['quantity'],
                    'line_total' => (int) $item['line_total'],
                ]);
            }

            return $order;
        });

        session()->forget('cart');
        session()->forget('cart_coupon_code');

        return redirect()->route('checkout.thankyou', ['code' => $order->code]);
    }

    public function thankYou(string $code)
    {
        $order = Order::query()
            ->with(['items', 'items.product'])
            ->where('code', $code)
            ->firstOrFail();

        return view('checkout-success', [
            'order' => $order,
        ]);
    }

    private function getCartItems(): Collection
    {
        $cart = collect(session('cart', []));
        $productIds = $cart->pluck('product_id')->filter()->values()->all();

        if (empty($productIds)) {
            return collect();
        }

        $products = Product::query()
            ->whereIn('id', $productIds)
            ->where('is_active', true)
            ->get()
            ->keyBy('id');

        return $cart->map(function (array $item) use ($products) {
            $product = $products->get($item['product_id'] ?? null);

            if (!$product) {
                return null;
            }

            $quantity = max(1, (int) ($item['quantity'] ?? 1));
            $unitPrice = ($product->sale_price && $product->sale_price < $product->price)
                ? (int) $product->sale_price
                : (int) $product->price;

            return [
                'product' => $product,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'line_total' => $unitPrice * $quantity,
            ];
        })->filter()->values();
    }

    private function getCartSummary(Collection $cartItems): array
    {
        $subtotal = (int) $cartItems->sum('line_total');
        $shippingFee = 0;
        $coupon = $this->resolveCoupon($subtotal);
        $discountTotal = $coupon ? min($coupon->calculateDiscount($subtotal), $subtotal) : 0;
        $total = max(0, $subtotal + $shippingFee - $discountTotal);

        return [
            'subtotal' => $subtotal,
            'shipping_fee' => $shippingFee,
            'discount_total' => $discountTotal,
            'total' => $total,
            'coupon' => $coupon,
        ];
    }

    private function resolveCoupon(int $subtotal): ?Coupon
    {
        $couponCode = trim((string) session('cart_coupon_code', ''));

        if ($couponCode === '') {
            return null;
        }

        $coupon = Coupon::query()
            ->whereRaw('LOWER(code) = ?', [Str::lower($couponCode)])
            ->first();

        if (!$coupon || !$coupon->isValid()) {
            session()->forget('cart_coupon_code');
            return null;
        }

        if ($coupon->min_order_total && $subtotal < (int) $coupon->min_order_total) {
            return null;
        }

        return $coupon;
    }

    private function generateOrderCode(): string
    {
        for ($i = 0; $i < 5; $i++) {
            $code = 'DH' . now()->format('ymdHis') . sprintf('%03d', random_int(0, 999));

            if (!Order::query()->where('code', $code)->exists()) {
                return $code;
            }
        }

        return 'DH' . strtoupper(Str::random(10));
    }
}
