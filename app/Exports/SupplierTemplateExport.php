<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class SupplierTemplateExport implements FromArray
{
    public function array(): array
    {
        return [
            ['name', 'address', 'phone_number', 'email', 'website'], // Header
            // Example row format (optional, can be empty if you only want headers)
            ['Example Supplier', '123 Example St', '123456789', 'example@example.com', 'http://example.com'],
        ];
    }
}
