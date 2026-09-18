<?php

namespace Database\Seeders;

use App\Models\Courier;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CourierSeeder extends Seeder
{
    public function run(): void
    {
        $specificCouriers = [
            [
                'name' => 'Budiono Hadi Agung',
                'phone' => '081234567801',
                'email' => 'budiono.agung@example.com',
                'level' => 3,
                'is_active' => true,
                'registered_at' => Carbon::now()->subDays(5),
            ],
            [
                'name' => 'Budi Santoso',
                'phone' => '081234567802',
                'email' => 'budi.santoso@example.com',
                'level' => 2,
                'is_active' => true,
                'registered_at' => Carbon::now()->subDays(10),
            ],
            [
                'name' => 'Agung Pratama',
                'phone' => '081234567803',
                'email' => 'agung.pratama@example.com',
                'level' => 1,
                'is_active' => true,
                'registered_at' => Carbon::now()->subDays(2),
            ],
            [
                'name' => 'Siti Nurhaliza',
                'phone' => '081234567804',
                'email' => 'siti.nurhaliza@example.com',
                'level' => 4,
                'is_active' => true,
                'registered_at' => Carbon::now()->subDays(15),
            ],
            [
                'name' => 'Hendra Wijaya',
                'phone' => '081234567805',
                'email' => 'hendra.wijaya@example.com',
                'level' => 5,
                'is_active' => true,
                'registered_at' => Carbon::now()->subDays(20),
            ],
        ];

        foreach ($specificCouriers as $courier) {
            Courier::firstOrCreate(
                ['phone' => $courier['phone']],
                $courier
            );
        }

        Courier::factory(20)->create();
    }
}
