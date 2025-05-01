<?php

namespace App\Modules\Reports\Controllers;

use TCPDF;
use App\Bll\Utility;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\Teachers\Models\Teachers;
use App\Modules\Reports\Models\AddedBehaviours;

class ExportPdfController extends Controller
{
    public function exportTeacherBehaviorReportToPDF()
    {
        $addedBehaviours = AddedBehaviours::query();

        $report = $addedBehaviours
            ->select(
                'user_id',
                DB::raw('SUM(CASE WHEN points < 0 THEN 1 ELSE 0 END) as negative_behaviors'),
                DB::raw('SUM(CASE WHEN points > 0 THEN 1 ELSE 0 END) as good_behaviors')
            )
            ->where('school_id', Utility::school_id())
            ->groupBy('user_id')
            ->get();

        $teachers = Teachers::whereIn('id', $report->pluck('user_id'))
            ->get()
            ->map(function ($teacher) use ($report) {
                $teacherReport = $report->firstWhere('user_id', $teacher->id);
                $teacher->negative_behaviors = $teacherReport->negative_behaviors ?? 0;
                $teacher->good_behaviors = $teacherReport->good_behaviors ?? 0;
                $teacher->total_behaviors = $teacher->negative_behaviors + $teacher->good_behaviors;
                return $teacher;
            })
            ->sortByDesc('total_behaviors') // ترتيب على حسب مجموع السلوكيات
            ->values();

        $teachers = $teachers->map(function ($teacher) {
            return [
                'id' => $teacher->id,
                'name' => $teacher->name,
                'user_name' => $teacher->user_name,
                'image' => $teacher->image,
                'negative_behaviors' => $teacher->negative_behaviors,
                'good_behaviors' => $teacher->good_behaviors,
                'total_behaviors' => $teacher->total_behaviors
            ];
        });

        $pdf = new \TCPDF();

        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('مدرستك');
        $pdf->SetTitle('تقرير سلوك المعلم');
        $pdf->SetHeaderData('', 0, 'تقرير سلوك المعلم', 'تم التوليد في: ' . date('Y-m-d'));

        $pdf->setHeaderFont(['dejavusans', '', 10]);
        $pdf->setFooterFont(['dejavusans', '', 8]);
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->SetMargins(15, 27, 15);
        $pdf->SetHeaderMargin(5);
        $pdf->SetFooterMargin(10);
        $pdf->SetAutoPageBreak(TRUE, 25);

        $pdf->SetFont('dejavusans', '', 12);
        $pdf->AddPage();
        $pdf->setRTL(true);

        $html = '
            <h3>تقرير سلوك المعلم</h3>
            <table border="1" cellpadding="4" width="100%">
                <thead>
                    <tr>
                        <th width="30%">الاسم</th>
                        <th width="25%">اسم المستخدم</th>
                        <th width="20%">السلوكيات الجيدة</th>
                        <th width="25%">السلوكيات السلبية</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($teachers as $teacher) {
            $html .= '
                <tr>
                    <td width="30%">' . $teacher['name'] . '</td>
                    <td width="25%">' . $teacher['user_name'] . '</td>
                    <td width="20%">' . $teacher['good_behaviors'] . '</td>
                    <td width="25%">' . $teacher['negative_behaviors'] . '</td>
                </tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');

        $filePath = storage_path('app/public/teacher_behavior_report.pdf');
        $pdf->Output($filePath, 'F');

        return response()->download($filePath)->deleteFileAfterSend(true);
    }
}
