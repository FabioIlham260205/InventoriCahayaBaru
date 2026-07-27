<?php

namespace Tests\Feature;

use App\Models\Fruit;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InventoryFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_inventory_dashboard_can_create_fruit_and_stock_movement(): void
    {
        $this->login();

        $this->get('/')->assertOk()->assertSee('Pencatatan stok buah');

        $this->post('/fruits', [
            'name' => 'Mangga Harum Manis',
            'sku' => 'MGG-HM',
            'unit' => 'kg',
            'current_stock' => 10,
            'minimum_stock' => 5,
            'purchase_price' => 12000,
            'selling_price' => 18000,
        ])->assertRedirect();

        $fruit = Fruit::where('sku', 'MGG-HM')->firstOrFail();

        $this->post('/stock-movements', [
            'fruit_id' => $fruit->id,
            'type' => 'out',
            'quantity' => 3,
            'movement_date' => now()->toDateString(),
        ])->assertRedirect();

        $this->assertSame('7.00', $fruit->fresh()->current_stock);
    }

    public function test_report_and_communication_pages_render(): void
    {
        $this->login();

        $this->get('/reports')->assertOk()->assertSee('Laporan stok buah');
        $this->get('/reports/print')->assertOk()->assertSee('Laporan Inventory Buah Cahaya Baru');
        $this->get('/communication')->assertOk()->assertSeeText('Kirim pesan tim');
    }

    private function login(): void
    {
        $this->withSession([
            'inventory_authenticated' => true,
            'inventory_user' => 'test',
        ]);
    }
}
