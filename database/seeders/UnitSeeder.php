<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

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
            'Dos',
            'Kilogram (kg)',
            'Meter (m)',
            'Meter Persegi (m²)',
            'Meter Kubik (m³)',
            'Liter (L)',
            'Sak',
            'Lembar',
            'Batang',
            'Pasang',
            'Roll',
            'Ikat',
            'Pack'
        ];
        foreach ($units as $unit) {
            Unit::create([
                'name' => $unit,
                'slug' => Str::slug($unit)
            ]);
        }
    }
}
