<?php

namespace App\Modules\Students\Controllers;

use TCPDF;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Modules\Students\Models\Students;

class ExportPdfController extends Controller
{
    public function exportStudentBehaviorTopToPDF(Request $request)
    {
        $term     = $request->term;
        $row_id   = $request->row_id;
        $class_id = $request->class_id;

        $students = Students::query();

        // Apply filters
        if ($row_id != '') {
            $students->whereHas('student', function ($query) use ($row_id) {
                $query->where('row_id', $row_id);
            });
        }
        if ($class_id != '') {
            $students->whereHas('student', function ($query) use ($class_id) {
                $query->where('class_id', $class_id);
            });
        }

        // Apply search by term
        if ($term != '') {
            $students->where('name', 'like', '%' . $term . '%');
        }

        $students = $students->get();

        $students = $students->map(function ($student) {
            return [
                'id'                  => $student->id,
                'name'                => $student->name,
                'row'                 => $student->student?->row ? $student->student->row->Data->first()->title : null,
                'class'               => $student->student?->class ? $student->student->class->Data->first()->title : null,
                'grade'               => $student->student?->grade ? $student->student->grade->Data->first()->title : null,
                'points'              => $student->student?->sumPoints() ?? 0,
                'good_behaviors_count' => $student->behaviors->where('points', '>', 0)->count(),
                'bad_behaviors_count' => $student->behaviors->where('points', '<', 0)->count(),
                'good_points_count'   => $student->student?->sumGoodPoints() ?? 0,
                'bad_points'          => $student->student?->sumBadPoints() ?? 0,
            ];
        });

        // Filter and sort students for top 10 based on points
        if (Auth::user()->guard == 'teacher') {
            $students = $students->where('points', '!=', 0)->sortByDesc('points')->values()->take(10);
        } else {
            $students = $students->sortByDesc('points')->values()->take(10);
        }

        // Create a new PDF document
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('مدرستك');
        $pdf->SetTitle('تقرير أفضل الطلاب');
        $pdf->SetHeaderData('', 0, 'تقرير أفضل الطلاب', 'تم التوليد في: ' . date('Y-m-d'));

        $pdf->setHeaderFont(['dejavusans', '', 8]);
        $pdf->setFooterFont(['dejavusans', '', 8]);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 25);

        // Set the font to DejaVu Sans, which supports Arabic
        $pdf->SetFont('dejavusans', '', 12);

        // Add a new page
        $pdf->AddPage();

        // Enable RTL for Arabic text
        $pdf->setRTL(true);

        // Table header in Arabic
        $html = '
        <h3>تقرير أفضل الطلاب</h3>
        <table border="1" cellpadding="4" width="100%">
            <thead>
                <tr>
                    <th width="46%">الاسم</th>
                    <th width="10%">الصف</th>
                    <th width="10%">الفصل</th>
                    <th width="10%">النقاط</th>
                    <th width="12%">سلوكيات إيجابية</th>
                    <th width="12%">سلوكيات سلبية</th>

                </tr>
            </thead>
            <tbody>';

        // <th width="7%">مجموع النقاط الجيدة</th>
        // <th width="8%">مجموع النقاط السلبية</th>

        // Add table rows
        foreach ($students as $student) {
            $html .= '<tr>
                        <td width="46%">' . $student['name'] . '</td>
                        <td width="10%">' . $student['row'] . '</td>
                        <td width="10%">' . $student['class'] . '</td>
                        <td width="10%">' . $student['points'] . '</td>
                        <td width="12%">' . $student['good_behaviors_count'] . '</td>
                        <td width="12%">' . $student['bad_behaviors_count'] . '</td>

                    </tr>';
        }

        // <td width="7%">' . $student['good_points_count'] . '</td>
        // <td width="8%">' . $student['bad_points'] . '</td>
        // <td width="10%">' . $student['grade'] . '</td>

        $html .= '</tbody></table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Save the PDF to a temporary file
        $filePath = storage_path('app/public/student_behavior_top_report.pdf');
        $pdf->Output($filePath, 'F');

        // Return the file as a response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }

    public function exportStudentsIndexToPDF(Request $request)
    {
        $perPage = $request->input('per_page', 20);

        $students = Students::query();

        // Apply filters
        if ($request->has('row_id') && $request->row_id != '') {
            $students->whereHas('student', function ($query) use ($request) {
                $query->where('row_id', $request->row_id);
            });
            $perPage = $request->input('per_page', 100);
        }
        if ($request->has('grade_id') && $request->grade_id != '') {
            $students->whereHas('student', function ($query) use ($request) {
                $query->where('grade_id', $request->grade_id);
            });
            $perPage = $request->input('per_page', 100);
        }
        if ($request->has('class_id') && $request->class_id != '') {
            $students->whereHas('student', function ($query) use ($request) {
                $query->where('class_id', $request->class_id);
            });
            $perPage = $request->input('per_page', 100);
        }

        // Apply search by term
        if ($request->has('term') && $request->term != '') {
            $students->where('name', 'like', '%' . $request->term . '%');
        }

        // Paginate the results
        $students = $students->paginate($perPage);

        // Map the students data for the PDF
        $studentData = $students->map(function ($student) {
            return [
                'id'    => $student->id,
                'name'  => $student->name,
                'row'   => $student->student?->row ? $student->student->row->Data->first()->title : null,
                'class' => $student->student?->class ? $student->student->class->Data->first()->title : null,
                'grade' => $student->student?->grade ? $student->student->grade->Data->first()->title : null,
                'points' => $student->student?->sumPoints(),
                'total_points' => $student->student?->totalPoints(),
                'good_behavior_count' => $student->count_good,
                'bad_behavior_count' => $student->count_bad,
            ];
        });

        // Create a new PDF document
        $pdf = new TCPDF();
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('مدرستك');
        $pdf->SetTitle('تقرير قائمة الطلاب');
        $pdf->SetHeaderData('', 0, 'تقرير قائمة الطلاب', 'تم التوليد في: ' . date('Y-m-d'));

        $pdf->setHeaderFont(['dejavusans', '', 10]);
        $pdf->setFooterFont(['dejavusans', '', 8]);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(true, 25);

        // Set the font to DejaVu Sans, which supports Arabic
        $pdf->SetFont('dejavusans', '', 12);

        // Add a new page
        $pdf->AddPage();

        // Enable RTL for Arabic text
        $pdf->setRTL(true);
        $html = '
        <h3>تقرير قائمة الطلاب</h3>
        <table border="1" cellpadding="4" width="100%">
           <thead>
               <tr>
                   <th width="50%">الاسم</th>
                   <th width="7%">صف</th>
                   <th width="7%">فصل</th>
                   <th width="18%">سلوكيات إيجابية</th>
                   <th width="18%">سلوكيات سلبية</th>
               </tr>
           </thead>
           <tbody>';

        // Add table rows
        foreach ($studentData as $student) {
            $html .= '<tr>
            <td width="50%">' . $student['name'] . '</td>
            <td width="7%">' . $student['row'] . '</td>
            <td width="7%">' . $student['class'] . '</td>
            <td width="18%">' . $student['good_behavior_count'] . '</td>
            <td width="18%">' . $student['bad_behavior_count'] . '</td>
        </tr>';
        }

        $html .= '</tbody></table>';

        // Output the HTML content
        $pdf->writeHTML($html, true, false, true, false, '');

        // Save the PDF to a temporary file
        $filePath = storage_path('app/public/students_index_report.pdf');
        $pdf->Output($filePath, 'F');

        // Return the file as a response
        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
