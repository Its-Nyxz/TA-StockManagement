<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            'Unit',
            'Rim',
            'Pcs',
            'Kilogram (kg)',
            'Meter (m)',
            'Meter Persegi (m²)',
            'Meter Kubik (m³)',
            'Liter (L)',
            'Sak',
            'Lembar',
            'Batang',
            'Roll'
        ];
        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit
            ]);
        }
    }
}
