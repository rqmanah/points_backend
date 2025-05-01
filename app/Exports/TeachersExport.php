<?php

namespace App\Exports;

use App\Modules\Teachers\Models\Teachers;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class TeachersExport implements FromCollection, WithHeadings
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Teachers::select('name', 'user_name', 'is_active')->get()
            ->map(function ($teacher) {
                return [
                    'name' => $teacher->name,
                    'user_name' => $teacher?->user_name ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'name - الاسم',
            'user_name - اسم المستخدم',
        ];
    }

}
