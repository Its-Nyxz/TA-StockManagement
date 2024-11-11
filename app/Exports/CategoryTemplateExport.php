<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class CategoryTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'description'], // Header
            // Example row format (optional, can be empty if you only want headers)
            ['Example Jenis', 'Example description'],
        ];
    }
}