<?php

namespace App\Exports;

use App\Modules\Students\Models\Students;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class StudentsExport implements FromCollection, WithHeadings
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Students::select('id', 'name', 'user_name', 'is_active')
            ->with('student') 
            ->get()
            ->map(function ($student) {
                return [
                    'name' => $student->name,
                    'grade_id' => $student?->student?->grade ? $student?->student?->grade?->Data?->first()?->title : '',
                    'row_id' => $student?->student?->row ? $student?->student?->row?->Data?->first()?->title : '',
                    'class_id' => $student?->student?->class ? $student?->student?->class?->Data?->first()?->title : '',
                    'user_name' => $student?->user_name ?? '',
                    'guardian_phone' => $student?->student?->guardian_phone ?? '',
                ];
            });
    }

    public function headings(): array
    {
        return [
            'name - الاسم',
            'grade - المرحلة الدراسية',
            'row - الصف',
            'class - الفصل',
            'user_name - اسم المستخدم',
            'guardian_phone - رقم الهاتف',
        ];
    }
}
