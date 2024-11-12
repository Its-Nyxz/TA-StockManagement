<?php

namespace App\Imports;

use App\Models\Item;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Unit;
use App\Models\Supplier;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class ItemsImport implements ToModel, WithHeadingRow, WithValidation
{

    public function model(array $row)
    {
        // dd($row);
        // Menghapus spasi berlebihan dan mengonversi ke huruf kecil
        $row = array_map(function ($value) {
            return strtolower(preg_replace('/\s+/', ' ', trim($value)));
        }, $row);
        // Memeriksa duplikasi berdasarkan nama yang telah dinormalisasi
        $normalizedName = $row['name'];
        if (Item::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $normalizedName)])->exists()) {
            throw new \Exception("Terdapat Nama Variant Barang yang sudah ada, yaitu " . ucwords($normalizedName));
        }

        // Pencarian ID relasi dengan case-insensitive dan menghapus spasi
        $categoryId = Category::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $row['jenis'])])->value('id');
        $brandId = Brand::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $row['merk'])])->value('id');
        $unitId = Unit::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $row['satuan'])])->value('id');
        $supplierId = Supplier::whereRaw('LOWER(TRIM(REPLACE(name, " ", ""))) = ?', [str_replace(' ', '', $row['supplier'])])->value('id');

        if (!$categoryId || !$brandId || !$unitId || !$supplierId) {
            throw new \Exception("Data terkait tidak ditemukan untuk satu atau beberapa bidang dalam baris: " . json_encode($row));
        }

        return new Item([
            'name' => ucwords($normalizedName), // Menyimpan nama dengan format kapitalisasi kata
            'image' => null,
            'code' => 'BRG-' . strtotime(now()), // Otomatisasi kode
            'price' => 0,
            'quantity' => $row['jumlah'] ?? 0,
            'category_id' => $categoryId,
            'unit_id' => $unitId,
            'brand_id' => $brandId,
            'active' => false,
            'supplier_id' => $supplierId,
        ]);
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'jumlah' => 'required|integer|min:0',
            'jenis' => 'required|string|exists:categories,name',
            'satuan' => 'required|string|exists:units,name',
            'merk' => 'required|string|exists:brands,name',
            'supplier' => 'required|string|exists:suppliers,name',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'name.required' => 'Nama barang wajib diisi.',
            'quantity.integer' => 'Jumlah barang harus berupa angka.',
            'category.required' => 'Jenis barang wajib diisi.',
            'category.exists' => 'Jenis barang tidak ditemukan di database.',
            'unit.required' => 'Satuan barang wajib diisi.',
            'unit.exists' => 'Satuan barang tidak ditemukan di database.',
            'brand.required' => 'Merek barang wajib diisi.',
            'brand.exists' => 'Merek barang tidak ditemukan di database.',
            'supplier.required' => 'Pemasok barang wajib diisi.',
            'supplier.exists' => 'Pemasok barang tidak ditemukan di database.',
        ];
    }
}
