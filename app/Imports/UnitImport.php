<?php

namespace App\Imports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class UnitImport implements ToModel,WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        // Check if a supplier with the same name already exists
        if (Unit::where('name', $row['name'])->exists()) {
            throw new \Exception("Terdapat Nama Jenis yang sudah ada.");
        }
        return new Unit([
            'name' => $row['name'],
            'description' => $row['description']?? null,
        ]);
    }
}
