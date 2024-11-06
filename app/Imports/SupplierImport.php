<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class SupplierImport implements ToModel, WithHeadingRow
{
    /**
     * Define the model for each row.
     *
     * @param array $row
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check for missing columns
        if (!isset($row['name']) || !isset($row['address']) || !isset($row['phone_number']) || !isset($row['email']) || !isset($row['website'])) {
            throw new \Exception("Kolom yang diperlukan tidak ada dalam file yang diimpor.");
        }

        // Check if a supplier with the same name already exists
        if (Supplier::where('name', $row['name'])->exists()) {
            throw new \Exception("Terdapat Nama Pemasok yang sudah ada.");
        }

        return new Supplier([
            'name' => $row['name'],
            'address' => $row['address'],
            'phone_number' => $row['phone_number'],
            'email' => $row['email'],
            'website' => $row['website'],
        ]);
    }
}
