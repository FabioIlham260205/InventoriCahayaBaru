<?php

namespace App\Http\Controllers;

use App\Models\CustomerOrder;
use App\Models\Fruit;
use App\Models\InventoryAlert;
use App\Models\StockMovement;
use App\Services\DokuPaymentService;
use App\Services\PaymentGateway;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ShopController extends Controller
{
    public function index(Request $request): View
    {
        $products = Fruit::query()
            ->where('current_stock', '>', 0)
            ->whereNotNull('selling_price')
            ->orderBy('category')
            ->orderBy('name')
            ->get();

        $cart = $this->cartDetails($request);

        return view('shop.index', compact('products', 'cart'));
    }

    public function cart(Request $request): View
    {
        $cart = $this->cartDetails($request);

        return view('shop.cart', compact('cart'));
    }

    public function checkoutPage(Request $request): View|RedirectResponse
    {
        $cart = $this->cartDetails($request);

        if ($cart['items'] === []) {
            return redirect()->route('shop.cart')->withErrors('Keranjang masih kosong.');
        }

        return view('shop.checkout', compact('cart'));
    }

    public function addToCart(Request $request, Fruit $fruit): RedirectResponse
    {
        $data = $request->validate([
            'quantity' => ['required', 'numeric', 'min:0.01'],
        ]);

        if (! $fruit->selling_price || (float) $fruit->current_stock <= 0) {
            return back()->withErrors('Produk belum tersedia untuk dibeli.');
        }

        $cart = $request->session()->get('shop_cart', []);
        $quantity = (float) $data['quantity'];
        $newQuantity = ($cart[$fruit->id] ?? 0) + $quantity;

        if ($newQuantity > (float) $fruit->current_stock) {
            return back()->withErrors("Stok {$fruit->name} hanya tersedia {$fruit->current_stock} {$fruit->unit}.");
        }

        $cart[$fruit->id] = $newQuantity;
        $request->session()->put('shop_cart', $cart);

        return back()->with('status', "{$fruit->name} ditambahkan ke keranjang.");
    }

    public function updateCart(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'items' => ['nullable', 'array'],
            'items.*' => ['nullable', 'numeric', 'min:0'],
        ]);

        $cart = [];

        foreach ($data['items'] ?? [] as $fruitId => $quantity) {
            $quantity = (float) $quantity;

            if ($quantity <= 0) {
                continue;
            }

            $fruit = Fruit::find($fruitId);

            if (! $fruit || ! $fruit->selling_price || (float) $fruit->current_stock <= 0) {
                continue;
            }

            $cart[$fruit->id] = min($quantity, (float) $fruit->current_stock);
        }

        $request->session()->put('shop_cart', $cart);

        return back()->with('status', 'Keranjang diperbarui.');
    }

    public function checkout(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_name' => ['required', 'string', 'max:120'],
            'customer_phone' => ['required', 'string', 'max:40'],
            'customer_email' => ['nullable', 'email', 'max:120'],
            'delivery_address' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:500'],
            'payment_gateway' => ['nullable', 'string', 'in:midtrans,doku'],
        ]);

        $cart = $request->session()->get('shop_cart', []);

        if ($cart === []) {
            return back()->withErrors('Keranjang masih kosong.');
        }

        try {
            $order = DB::transaction(function () use ($cart, $data): CustomerOrder {
                $fruits = Fruit::query()
                    ->whereIn('id', array_keys($cart))
                    ->get()
                    ->keyBy('id');

                $subtotal = 0.0;
                $items = [];

                foreach ($cart as $fruitId => $quantity) {
                    $fruit = $fruits->get((int) $fruitId);
                    $quantity = (float) $quantity;

                    if (! $fruit || ! $fruit->selling_price || $quantity <= 0) {
                        continue;
                    }

                    if ($quantity > (float) $fruit->current_stock) {
                        throw new \RuntimeException("Stok {$fruit->name} hanya tersedia {$fruit->current_stock} {$fruit->unit}.");
                    }

                    $unitPrice = (float) $fruit->selling_price;
                    $lineTotal = $quantity * $unitPrice;
                    $subtotal += $lineTotal;

                    $items[] = [
                        'fruit' => $fruit,
                        'quantity' => $quantity,
                        'unit_price' => $unitPrice,
                        'line_total' => $lineTotal,
                    ];
                }

                if ($items === []) {
                    throw new \RuntimeException('Produk di keranjang tidak tersedia.');
                }

                $deliveryFee = 0.0;
                $order = CustomerOrder::create([
                    ...$data,
                    'order_number' => 'CB-' . now()->format('ymd') . '-' . Str::upper(Str::random(5)),
                    'status' => 'pending',
                    'subtotal' => $subtotal,
                    'delivery_fee' => $deliveryFee,
                    'total' => $subtotal + $deliveryFee,
                ]);

                foreach ($items as $item) {
                    /** @var \App\Models\Fruit $fruit */
                    $fruit = $item['fruit'];
                    $quantity = $item['quantity'];

                    $order->items()->create([
                        'fruit_id' => $fruit->id,
                        'fruit_name' => $fruit->name,
                        'unit' => $fruit->unit,
                        'quantity' => $quantity,
                        'unit_price' => $item['unit_price'],
                        'line_total' => $item['line_total'],
                    ]);

                    $stockUpdated = Fruit::query()
                        ->whereKey($fruit->id)
                        ->where('current_stock', '>=', $quantity)
                        ->decrement('current_stock', $quantity);

                    if ($stockUpdated !== 1) {
                        throw new \RuntimeException("Stok {$fruit->name} baru saja berubah. Silakan cek keranjang lagi.");
                    }

                    StockMovement::create([
                        'fruit_id' => $fruit->id,
                        'type' => 'out',
                        'quantity' => $quantity,
                        'unit_price' => $item['unit_price'],
                        'reference' => $order->order_number,
                        'handled_by' => $data['customer_name'],
                        'movement_date' => now()->toDateString(),
                        'notes' => 'Pesanan customer dari toko online.',
                    ]);

                    $this->syncCustomerOrderAlerts($fruit->fresh());
                }

                return $order;
            });
        } catch (\RuntimeException $exception) {
            return back()->withErrors($exception->getMessage());
        }

        $request->session()->forget('shop_cart');

        $gatewayParam = $data['payment_gateway'] ?? 'midtrans';

        return redirect()->route('shop.payment.show', ['order' => $order, 'gateway' => $gatewayParam])
            ->with('status', "Pesanan {$order->order_number} berhasil dibuat. Lanjutkan pembayaran.");
    }

    public function showPayment(Request $request, CustomerOrder $order, PaymentGateway $gateway): View|RedirectResponse
    {
        $order->load('items');

        if ($order->payment_status === 'paid') {
            return view('shop.payment', compact('order'));
        }

        $gatewayName = $request->input('gateway');

        if ($gatewayName) {
            $payment = $gateway->createPayment($order, $gatewayName);
            $order->update([
                'payment_status' => $payment['status'],
                'payment_provider' => $payment['provider'],
                'payment_token' => $payment['token'],
                'payment_redirect_url' => $payment['redirect_url'],
                'payment_payload' => $payment['payload'],
            ]);
        } elseif (! $order->payment_redirect_url) {
            $gatewayName = $order->payment_provider;
            if (! $gatewayName || $gatewayName === 'manual') {
                $gatewayName = config('services.doku.client_id') ? 'doku' : (config('services.midtrans.server_key') ? 'midtrans' : null);
            }
            if ($gatewayName) {
                $payment = $gateway->createPayment($order, $gatewayName);
                $order->update([
                    'payment_status' => $payment['status'],
                    'payment_provider' => $payment['provider'],
                    'payment_token' => $payment['token'],
                    'payment_redirect_url' => $payment['redirect_url'],
                    'payment_payload' => $payment['payload'],
                ]);
            }
        }

        $order->refresh()->load('items');

        return view('shop.payment', compact('order'));
    }

    public function finishPayment(CustomerOrder $order): RedirectResponse
    {
        return redirect()->route('shop.payment.show', $order)
            ->with('status', 'Terima kasih. Status pembayaran akan diperbarui setelah gateway mengirim konfirmasi.');
    }

    public function paymentNotification(Request $request, PaymentGateway $gateway): Response
    {
        $payload = $request->all();

        if (! $gateway->notificationIsValid($payload)) {
            return response('Invalid signature', 403);
        }

        $order = CustomerOrder::where('order_number', $payload['order_id'] ?? null)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        $transactionStatus = $payload['transaction_status'] ?? 'pending';
        $fraudStatus = $payload['fraud_status'] ?? null;
        $paymentStatus = match (true) {
            in_array($transactionStatus, ['capture', 'settlement'], true) && $fraudStatus !== 'deny' => 'paid',
            in_array($transactionStatus, ['deny', 'cancel', 'expire'], true) => 'failed',
            default => 'waiting_payment',
        };

        $order->update([
            'payment_status' => $paymentStatus,
            'payment_provider' => 'midtrans',
            'payment_type' => $payload['payment_type'] ?? $order->payment_type,
            'payment_transaction_id' => $payload['transaction_id'] ?? $order->payment_transaction_id,
            'paid_at' => $paymentStatus === 'paid' ? now() : $order->paid_at,
            'payment_payload' => $payload,
            'status' => $paymentStatus === 'paid' && $order->status === 'pending' ? 'confirmed' : $order->status,
        ]);

        return response('OK');
    }

    public function paymentNotificationDoku(Request $request, PaymentGateway $gateway): Response
    {
        $payload = $request->all();

        if (! $gateway->notificationIsValid($payload, 'doku')) {
            return response('Invalid signature', 403);
        }

        $orderNumber = $payload['order']['id'] ?? null;
        $order = CustomerOrder::where('order_number', $orderNumber)->first();

        if (! $order) {
            return response('Order not found', 404);
        }

        /** @var DokuPaymentService $dokuService */
        $dokuService = app(DokuPaymentService::class);
        $result = $dokuService->getNotificationResult($payload);

        $order->update([
            'payment_status' => $result['payment_status'],
            'payment_provider' => 'doku',
            'payment_type' => $result['payment_type'] ?? $order->payment_type,
            'payment_transaction_id' => $result['payment_transaction_id'] ?? $order->payment_transaction_id,
            'paid_at' => $result['paid_at'] ?? $order->paid_at,
            'payment_payload' => $result['payload'],
            'status' => $result['payment_status'] === 'paid' && $order->status === 'pending' ? 'confirmed' : $order->status,
        ]);

        return response('OK');
    }

    public function orders(): View
    {
        $orders = CustomerOrder::query()
            ->with('items')
            ->latest()
            ->paginate(12);

        return view('shop.orders', compact('orders'));
    }

    public function updateOrderStatus(Request $request, CustomerOrder $order): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,confirmed,packed,delivered,cancelled'],
        ]);

        $order->update($data);

        return back()->with('status', "Status pesanan {$order->order_number} diperbarui.");
    }

    private function cartDetails(Request $request): array
    {
        $cart = $request->session()->get('shop_cart', []);
        $products = Fruit::query()
            ->whereIn('id', array_keys($cart))
            ->get()
            ->keyBy('id');

        $items = [];
        $subtotal = 0.0;

        foreach ($cart as $fruitId => $quantity) {
            $fruit = $products->get((int) $fruitId);

            if (! $fruit) {
                continue;
            }

            $quantity = min((float) $quantity, (float) $fruit->current_stock);
            $unitPrice = (float) ($fruit->selling_price ?? 0);
            $lineTotal = $quantity * $unitPrice;
            $subtotal += $lineTotal;

            $items[] = [
                'fruit' => $fruit,
                'quantity' => $quantity,
                'line_total' => $lineTotal,
            ];
        }

        return [
            'items' => $items,
            'subtotal' => $subtotal,
            'total_quantity' => collect($items)->sum('quantity'),
        ];
    }

    private function syncCustomerOrderAlerts(Fruit $fruit): void
    {
        if ((float) $fruit->current_stock <= (float) $fruit->minimum_stock) {
            InventoryAlert::firstOrCreate(
                ['fruit_id' => $fruit->id, 'type' => 'low_stock', 'is_read' => false],
                [
                    'title' => 'Stok rendah',
                    'message' => "{$fruit->name} tersisa {$fruit->current_stock} {$fruit->unit} setelah pesanan customer.",
                ]
            );
        }
    }
}
