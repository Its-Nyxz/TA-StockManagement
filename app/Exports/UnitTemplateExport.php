<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class UnitTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'keterangan'], // Header
            // Example row format (optional, can be empty if you only want headers)
            ['Example Satuan', 'Example description'],
        ];
    }
}
