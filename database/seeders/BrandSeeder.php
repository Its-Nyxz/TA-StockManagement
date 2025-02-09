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

            'Aquaproof',
            'No Drop',
            'Property',
            'Indaco',
            'Rucika',
            'Altex',
            'Reno',
            'Haston',
            'Stayvic',
            'Matrix',
            'Semen Gresik',
            'Semen PCC'
        ];

        foreach ($brands as $brand) {
            Brand::create(['name' => $brand, 'slug' => Str::slug($brand)]);
        }
    }
}
