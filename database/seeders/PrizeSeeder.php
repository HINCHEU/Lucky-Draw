<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PrizeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\Prize::create([
            'name' => 'OPPO A5 Smartphone',
            'description' => 'Latest OPPO A5 with amazing features',
            'photo_path' => null, // Add image later
            'quantity' => 10,
            'order' => 1
        ]);

        \App\Models\Prize::create([
            'name' => 'Gift Card $50',
            'description' => 'CE&P Gift Card worth $50',
            'photo_path' => null,
            'quantity' => 20,
            'order' => 2
        ]);

        \App\Models\Prize::create([
            'name' => 'Bluetooth Speaker',
            'description' => 'High-quality wireless speaker',
            'photo_path' => null,
            'quantity' => 15,
            'order' => 3
        ]);
    }
}
