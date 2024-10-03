<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Brand;

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

        foreach($brands as $brand){
            Brand::create(['name'=>$brand]);
        }
    }
}
