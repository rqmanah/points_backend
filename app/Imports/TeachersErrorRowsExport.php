<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class TeachersErrorRowsExport implements FromArray, WithHeadings
{
    protected $errorRows;

    public function __construct(array $errorRows)
    {
        $this->errorRows = $errorRows;
    }

    // Data to be exported
    public function array(): array
    {
        return array_map(function ($row) {
            return $row['data'] ?? []; // If $row['data'] is null, default to an empty array
        }, $this->errorRows);
    }

    // Define the headings
    public function headings(): array
    {
        return ['Name', 'National ID'];
    }
}
