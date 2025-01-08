<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            'Dulux',
            'Nippon Paint',
            'KIA',
            'Broco',
            'Philips',
            'Wavin',
            'HWI (Hyundai Welding Indonesia)',
            'Makita',
            'Semen Gresik'
        ];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand, 'slug' => Str::slug($brand)]);
        }
    }
}
  