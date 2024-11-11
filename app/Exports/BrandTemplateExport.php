<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class BrandTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'description'], // Header
            // Example row format (optional, can be empty if you only want headers)
            ['Example Merk', 'Example description'],
        ];
    }
}
