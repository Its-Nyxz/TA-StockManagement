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
        // Memastikan semua kolom yang dibutuhkan memiliki nilai sebelum (tidak NULL)
        if (!isset($row['name']) || !isset($row['address']) || !isset($row['phone_number']) || !isset($row['email']) || !isset($row['website'])) {
            return null; // Skip row jika ada kolom penting yang kosong
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
