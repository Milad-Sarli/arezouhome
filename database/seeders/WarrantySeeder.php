<?php

namespace Database\Seeders;

use App\Models\Warranty;
use Illuminate\Database\Seeder;

class WarrantySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $rows = [
            ['title' => 'گارانتی شرکتی ۲۴ ماهه', 'type' => 'official', 'months' => 24],
            ['title' => 'گارانتی شرکتی ۱۸ ماهه', 'type' => 'official', 'months' => 18],
            ['title' => 'گارانتی شرکتی ۱۲ ماهه', 'type' => 'official', 'months' => 12],
            ['title' => 'گارانتی متفرقه ۱۲ ماهه', 'type' => 'unofficial', 'months' => 12],
            ['title' => 'بدون گارانتی', 'type' => 'unofficial', 'months' => 0],
        ];

        foreach ($rows as $row) {
            Warranty::firstOrCreate(['title' => $row['title']], $row);
        }
    }
}
