<?php

namespace App\Imports;

use App\Models\Brand;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class BrandImport implements ToModel, WithHeadingRow, WithValidation
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

        if (Brand::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $normalizedName)])->exists()) {
            throw new \Exception("Terdapat Nama Brand yang sudah ada, yaitu " . ucwords($normalizedName));
        }

        // Mengembalikan model Brand dengan data yang telah dinormalisasi
        return new Brand([
            'name' => ucwords($normalizedName), // Menyimpan nama dalam format kapitalisasi kata
            'description' => $row['keterangan'] ?? null,
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
            'name.required' => 'Nama brand wajib diisi.',
        ];
    }
}
