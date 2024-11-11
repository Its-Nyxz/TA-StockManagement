<?php

namespace App\Imports;

use App\Models\Unit;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class UnitImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Menghapus spasi berlebihan dan mengonversi ke huruf kecil
        $row = array_map(function ($value) {
            return strtolower(preg_replace('/\s+/', ' ', trim($value)));
        }, $row);

        // Normalisasi nama untuk cek duplikasi
        $normalizedName = $row['name'];

        if (Unit::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $normalizedName)])->exists()) {
            throw new \Exception("Terdapat Nama Satuan yang sudah ada, yaitu " . ucwords($normalizedName));
        }

        // Mengembalikan model Unit dengan data yang telah dinormalisasi
        return new Unit([
            'name' => ucwords($normalizedName), // Menyimpan nama dalam format kapitalisasi kata
            'description' => $row['description'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama satuan wajib diisi.',
            'name.unique' => 'Terdapat Nama Satuan yang sudah ada.',
        ];
    }
}

