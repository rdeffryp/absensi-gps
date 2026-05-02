<?php

namespace Database\Seeders;

use App\Models\Office;
use Illuminate\Database\Seeder;

class OfficeSeeder extends Seeder
{
    public function run(): void
    {
        Office::create([
            'name' => 'Kantor Pusat',
            'latitude' => -6.914744,
            'longitude' => 107.609810,
            'radius' => 100,
        ]);
    }
}