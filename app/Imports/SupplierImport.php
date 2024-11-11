<?php

namespace App\Imports;

use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class SupplierImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // Menghapus spasi berlebihan dan mengonversi ke huruf kecil
        $row = array_map(function ($value) {
            return strtolower(preg_replace('/\s+/', ' ', trim($value)));
        }, $row);

        // Memeriksa duplikasi berdasarkan nama yang telah dinormalisasi
        $normalizedName = $row['name'];

        if (Supplier::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $normalizedName)])->exists()) {
            throw new \Exception("Terdapat Nama Pemasok yang sudah ada, yaitu " . ucwords($normalizedName));
        }

        return new Supplier([
            'name' => ucwords($normalizedName), // Menyimpan nama dengan format kapitalisasi kata
            'address' => $row['address'],
            'phone_number' => $row['phone_number'],
            'email' => $row['email'] ?? null,
            'website' => $row['website'] ?? null,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:500',
            'phone_number' => 'required|numeric',
            'email' => 'nullable|email',
            'website' => 'nullable|url',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama pemasok wajib diisi.',
            'phone_number.numeric' => 'Nomor telepon harus berupa angka.',
            'email.email' => 'Email tidak valid.',
            'website.url' => 'URL situs web tidak valid.',
        ];
    }
}
