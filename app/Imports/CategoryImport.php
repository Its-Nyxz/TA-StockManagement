<?php

namespace App\Imports;

use App\Models\Category;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class CategoryImport implements ToModel, WithHeadingRow, WithValidation
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        // Menghapus spasi berlebihan dan mengonversi ke huruf kecil untuk semua kolom
        $row = array_map(function ($value) {
            return strtolower(preg_replace('/\s+/', ' ', trim($value)));
        }, $row);

        // Normalisasi nama untuk cek duplikasi
        $normalizedName = $row['name'];

        if (Category::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $normalizedName)])->exists()) {
            throw new \Exception("Terdapat Nama Kategori yang sudah ada, yaitu " . ucwords($normalizedName));
        }

        // Mengembalikan model Category dengan data yang telah dinormalisasi
        return new Category([
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
            'name.required' => 'Nama kategori wajib diisi.',
        ];
    }
}

