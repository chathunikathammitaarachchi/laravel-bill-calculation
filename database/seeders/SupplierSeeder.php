<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    public function run()
    {
        // Looping from 6 to 20 (15 suppliers)
        for ($i = 6; $i < 21; $i++) {
            Supplier::create([
                'supplier_id' => $i,
                'supplier_name' => 'Supplier ' . $i,
                'address' => 'Address ' . $i,
                'phone' => '07' . rand(1, 9) . rand(1000000, 9999999),
            ]);
        }
    }
}
