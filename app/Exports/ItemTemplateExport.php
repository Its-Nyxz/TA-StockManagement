<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class ItemTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'jumlah', 'jenis', 'merk', 'satuan', 'supplier'], // Header
            // Example row with placeholders or example data
            ['Example Barang Name', '0', 'Example Jenis', 'Example Merk', 'Example Satuan', 'Example Supplier'],
        ];
    }
}