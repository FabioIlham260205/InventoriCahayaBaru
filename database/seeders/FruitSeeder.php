<?php

namespace Database\Seeders;

use App\Models\Fruit;
use Illuminate\Database\Seeder;

class FruitSeeder extends Seeder
{
    public function run(): void
    {
        $fruits = [
            [
                'name' => 'Apel Fuji',
                'sku' => 'APL-FUJI',
                'category' => 'Apel',
                'unit' => 'kg',
                'current_stock' => 50,
                'minimum_stock' => 10,
                'purchase_price' => 25000,
                'selling_price' => 35000,
            ],
            [
                'name' => 'Jeruk Medan',
                'sku' => 'JRK-MEDAN',
                'category' => 'Jeruk',
                'unit' => 'kg',
                'current_stock' => 80,
                'minimum_stock' => 15,
                'purchase_price' => 12000,
                'selling_price' => 18000,
            ],
            [
                'name' => 'Pisang Cenderawasih',
                'sku' => 'PIS-CEND',
                'category' => 'Pisang',
                'unit' => 'sisir',
                'current_stock' => 40,
                'minimum_stock' => 8,
                'purchase_price' => 8000,
                'selling_price' => 12000,
            ],
            [
                'name' => 'Mangga Harum Manis',
                'sku' => 'MGG-HM',
                'category' => 'Mangga',
                'unit' => 'kg',
                'current_stock' => 35,
                'minimum_stock' => 10,
                'purchase_price' => 18000,
                'selling_price' => 28000,
            ],
            [
                'name' => 'Semangka Merah',
                'sku' => 'SMK-MR',
                'category' => 'Semangka',
                'unit' => 'kg',
                'current_stock' => 60,
                'minimum_stock' => 10,
                'purchase_price' => 6000,
                'selling_price' => 12000,
            ],
            [
                'name' => 'Alpukat Mentega',
                'sku' => 'ALP-MNT',
                'category' => 'Alpukat',
                'unit' => 'kg',
                'current_stock' => 30,
                'minimum_stock' => 8,
                'purchase_price' => 22000,
                'selling_price' => 32000,
            ],
            [
                'name' => 'Durian Montong',
                'sku' => 'DUR-MNT',
                'category' => 'Durian',
                'unit' => 'kg',
                'current_stock' => 20,
                'minimum_stock' => 5,
                'purchase_price' => 45000,
                'selling_price' => 65000,
            ],
            [
                'name' => 'Nanas Madu',
                'sku' => 'NNS-MDU',
                'category' => 'Nanas',
                'unit' => 'buah',
                'current_stock' => 45,
                'minimum_stock' => 10,
                'purchase_price' => 8000,
                'selling_price' => 15000,
            ],
            [
                'name' => 'Anggur Hijau',
                'sku' => 'ANG-HJ',
                'category' => 'Anggur',
                'unit' => 'kg',
                'current_stock' => 25,
                'minimum_stock' => 5,
                'purchase_price' => 40000,
                'selling_price' => 58000,
            ],
            [
                'name' => 'Pepaya California',
                'sku' => 'PPY-CLN',
                'category' => 'Pepaya',
                'unit' => 'kg',
                'current_stock' => 40,
                'minimum_stock' => 10,
                'purchase_price' => 7000,
                'selling_price' => 12000,
            ],
        ];

        foreach ($fruits as $fruit) {
            Fruit::updateOrCreate(
                ['sku' => $fruit['sku']],
                $fruit
            );
        }
    }
}
