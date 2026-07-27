<?php

namespace Tests\Feature;

use App\Models\CustomerOrder;
use App\Models\Fruit;
use App\Models\StockMovement;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShopFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_checkout_and_stock_is_reduced(): void
    {
        $fruit = Fruit::create([
            'name' => 'Semangka Merah',
            'sku' => 'SMK-MR',
            'category' => 'Semangka',
            'unit' => 'kg',
            'current_stock' => 12,
            'minimum_stock' => 3,
            'purchase_price' => 8000,
            'selling_price' => 12000,
        ]);

        $this->get('/shop')
            ->assertOk()
            ->assertSee('Toko Buah Cahaya Baru')
            ->assertSee('Semangka Merah');

        $this->post(route('shop.cart.add', $fruit), [
            'quantity' => 2,
        ])->assertRedirect();

        $this->post('/shop/checkout', [
            'customer_name' => 'Budi Customer',
            'customer_phone' => '08123456789',
            'customer_email' => 'budi@example.test',
            'delivery_address' => 'Jl. Buah Segar No. 1',
            'notes' => 'Kirim pagi.',
        ])->assertRedirectContains('/shop/payment/');

        $order = CustomerOrder::with('items')->firstOrFail();

        $this->assertSame('Budi Customer', $order->customer_name);
        $this->assertSame('24000.00', $order->total);
        $this->assertCount(1, $order->items);
        $this->assertSame('10.00', $fruit->fresh()->current_stock);

        $this->assertDatabaseHas('stock_movements', [
            'fruit_id' => $fruit->id,
            'type' => 'out',
            'reference' => $order->order_number,
        ]);

        $this->get(route('shop.payment.show', $order, ['gateway' => 'midtrans']))
            ->assertOk()
            ->assertSee('Pembayaran pesanan')
            ->assertSee('Gagal terhubung ke payment gateway');

        $this->assertSame('waiting_payment', $order->fresh()->payment_status);
        $this->assertSame('manual', $order->fresh()->payment_provider);
    }

    public function test_customer_cannot_add_more_than_available_stock(): void
    {
        $fruit = Fruit::create([
            'name' => 'Melon Hijau',
            'unit' => 'kg',
            'current_stock' => 1,
            'minimum_stock' => 1,
            'selling_price' => 18000,
        ]);

        $this->from('/shop')
            ->post(route('shop.cart.add', $fruit), ['quantity' => 2])
            ->assertRedirect('/shop')
            ->assertSessionHasErrors();

        $this->assertSame(0, StockMovement::count());
        $this->assertSame(0, CustomerOrder::count());
    }

    public function test_midtrans_notification_marks_order_as_paid(): void
    {
        config(['services.midtrans.server_key' => 'server-key-test']);

        $order = CustomerOrder::create([
            'order_number' => 'CB-260624-TEST1',
            'customer_name' => 'Budi Customer',
            'customer_phone' => '08123456789',
            'delivery_address' => 'Jl. Buah Segar No. 1',
            'status' => 'pending',
            'payment_status' => 'waiting_payment',
            'payment_provider' => 'midtrans',
            'subtotal' => 24000,
            'delivery_fee' => 0,
            'total' => 24000,
        ]);

        $payload = [
            'order_id' => $order->order_number,
            'status_code' => '200',
            'gross_amount' => '24000.00',
            'transaction_status' => 'settlement',
            'payment_type' => 'qris',
            'transaction_id' => 'trx-123',
        ];
        $payload['signature_key'] = hash('sha512', $payload['order_id'] . $payload['status_code'] . $payload['gross_amount'] . 'server-key-test');

        $this->postJson(route('payment.midtrans.notification'), $payload)
            ->assertOk();

        $order->refresh();

        $this->assertSame('paid', $order->payment_status);
        $this->assertSame('confirmed', $order->status);
        $this->assertSame('qris', $order->payment_type);
        $this->assertNotNull($order->paid_at);
    }
}
