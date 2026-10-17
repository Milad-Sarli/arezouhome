<?php

namespace Database\Seeders;

use App\Models\Shipment;
use Illuminate\Database\Seeder;

class ShipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Source: kralmode local database (generic methods, reusable).
     */
    public function run(): void
    {
        $rows = [
            ['title' => 'تیپاکس', 'is_default' => false, 'pay_after_delivery' => true],
            ['title' => 'پست پیشتاز', 'is_default' => true, 'pay_after_delivery' => false],
            ['title' => 'تحویل حضوری در فروشگاه', 'is_default' => false, 'pay_after_delivery' => true],
        ];

        foreach ($rows as $row) {
            Shipment::firstOrCreate(['title' => $row['title']], $row);
        }
    }
}
