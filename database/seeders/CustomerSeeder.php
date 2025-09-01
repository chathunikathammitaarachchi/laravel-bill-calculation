<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Customer;

class CustomerSeeder extends Seeder
{
    public function run()
    {
        $names = [
            'Nimal Perera',
            'Kamal Silva',
            'Sunil Fernando',
            'Dinesh Kumara',
            'Chathurika Herath',
            'Thilina Jayasinghe',
            'Saman Kumara',
            'Dilani Wickramasinghe',
            'Kasun Weerasinghe',
            'Ruwan Gunasekara',
            'Janaka Amarasinghe',
            'Ishara Madushani',
            'Upul Bandara',
            'Anoma Rajapaksha',
            'Sajith Senanayake'
        ];

        foreach ($names as $index => $name) {
            Customer::create([
                'customer_id' => $index + 6, // Starts from 6
                'customer_name' => $name,
                'phone' => '07' . rand(1, 9) . rand(1000000, 9999999),
            ]);
        }
    }
}
