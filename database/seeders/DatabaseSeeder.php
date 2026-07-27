<?php

namespace Database\Seeders;

use App\Models\Fruit;
use App\Models\InventoryAlert;
use App\Models\StockMovement;
use App\Models\TeamMessage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $fruits = collect([
            ['name' => 'Apel Fuji', 'sku' => 'APL-FJ', 'category' => 'Apel', 'unit' => 'kg', 'current_stock' => 42, 'minimum_stock' => 15, 'purchase_price' => 28000, 'selling_price' => 36000, 'supplier' => 'Pasar Induk', 'storage_location' => 'Rak A1', 'expiry_date' => now()->addDays(8)],
            ['name' => 'Pisang Cavendish', 'sku' => 'PSG-CV', 'category' => 'Pisang', 'unit' => 'sisir', 'current_stock' => 9, 'minimum_stock' => 12, 'purchase_price' => 16000, 'selling_price' => 23000, 'supplier' => 'Kebun Mitra', 'storage_location' => 'Rak B2', 'expiry_date' => now()->addDays(2)],
            ['name' => 'Jeruk Medan', 'sku' => 'JRK-MD', 'category' => 'Jeruk', 'unit' => 'kg', 'current_stock' => 31, 'minimum_stock' => 10, 'purchase_price' => 18000, 'selling_price' => 26000, 'supplier' => 'CV Sinar Buah', 'storage_location' => 'Rak C1', 'expiry_date' => now()->addDays(5)],
        ])->map(fn (array $data) => Fruit::create($data));

        foreach ($fruits as $fruit) {
            StockMovement::create([
                'fruit_id' => $fruit->id,
                'type' => 'in',
                'quantity' => $fruit->current_stock,
                'unit_price' => $fruit->purchase_price,
                'reference' => 'Stok awal',
                'handled_by' => 'Admin Gudang',
                'movement_date' => now(),
                'notes' => 'Data contoh untuk demo inventory Cahaya Baru.',
            ]);
        }

        $pisang = $fruits->firstWhere('sku', 'PSG-CV');

        InventoryAlert::create([
            'fruit_id' => $pisang->id,
            'type' => 'low_stock',
            'title' => 'Stok rendah',
            'message' => 'Pisang Cavendish tersisa 9 sisir dan sudah di bawah stok minimum.',
        ]);

        InventoryAlert::create([
            'fruit_id' => $pisang->id,
            'type' => 'expiring',
            'title' => 'Segera kadaluarsa',
            'message' => 'Pisang Cavendish perlu diprioritaskan sebelum masa simpan habis.',
        ]);

        TeamMessage::create([
            'sender_name' => 'Admin Gudang',
            'channel' => 'pembelian',
            'subject' => 'Prioritaskan pisang',
            'body' => 'Pisang Cavendish perlu restock dan dipromosikan karena masa simpan tinggal beberapa hari.',
        ]);
    }
}
