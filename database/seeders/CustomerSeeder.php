<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Customer::create([
            "name" => "Dummy",
            "phone_number" => "08123456789",
            "address" => "Jl.Dummy 99"
        ]);

        $suppliers = [
            [
                'name' => 'PT. Bangun Jaya',
                'address' => 'Jl. Raya Merdeka No. 123, Jakarta',
                'phone_number' => '021-12345678',
                'email' => 'info@bangunjaya.com',
                'website' => 'https://bangunjaya.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'CV. Maju Makmur',
                'address' => 'Jl. Sudirman No. 45, Bandung',
                'phone_number' => '022-98765432',
                'email' => 'contact@majumakmur.com',
                'website' => 'https://majumakmur.com',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        // Gunakan loop untuk menyimpan setiap data
        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
