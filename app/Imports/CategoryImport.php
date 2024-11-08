<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class CategoryImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
    // Check if a supplier with the same name already exists
    if (Category::where('name', $row['name'])->exists()) {
        throw new \Exception("Terdapat Nama Jenis yang sudah ada.");
    }
    return new Category([
        'name' => $row['name'],
        'description' => $row['description']?? null,
    ]);
    }
}
