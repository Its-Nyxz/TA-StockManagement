<?php

namespace App\Imports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class BrandImport implements ToModel, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if a supplier with the same name already exists
        if (Brand::where('name', $row['name'])->exists()) {
            throw new \Exception("Terdapat Nama Merek yang sudah ada.");
        }
        return new Brand([
            'name' => $row['name'],
            'description' => $row['description']?? null,
        ]);
    }
}
