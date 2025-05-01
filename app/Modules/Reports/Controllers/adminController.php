<?php

namespace App\Modules\Reports\Controllers;

use App\Bll\Utility;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\Students\Models\Students;
use App\Modules\Teachers\Models\Teachers;
use App\Modules\Reports\Models\AddedBehaviours;

class adminController extends Controller
{

    public function mostActiveTeachers()
    {
        $addedBehaviours = AddedBehaviours::query();

        // Get top 20 most active teachers with their total behaviour count and total points
        $mostActiveTeachers = $addedBehaviours->select(
            'user_id',
            DB::raw('COUNT(id) as total_behaviours'),
            DB::raw('SUM(ABS(points)) as total_points')
        )
            ->where('created_at', '>=', now()->subDays(14))
            ->where('school_id', Utility::school_id())
            ->groupBy('user_id')
            ->orderBy('total_behaviours', 'DESC') // Order by behaviour count
            ->take(20)
            ->get();

        $teacherIds = $mostActiveTeachers->pluck('user_id')->filter()->toArray(); // إزالة القيم الفارغة أو null

        if (!empty($teacherIds)) {
            $teachers = Teachers::whereIn('id', $teacherIds)
                ->where('guard', 'teacher')
                ->where('school_id', Utility::school_id()) // تأكد من وجود هذه الشرط في الكود الأصلي
                ->orderByRaw('FIELD(id, ' . implode(',', $teacherIds) . ')')
                ->get()
                ->map(function ($teacher) use ($mostActiveTeachers) {
                    $teacherData = $mostActiveTeachers->firstWhere('user_id', $teacher->id);
                    $teacher->total_behaviours = $teacherData->total_behaviours;
                    $teacher->total_points = $teacherData->total_points;
                    return $teacher;
                });
        } else {
            $teachers = collect();
        }

        $summition_of_behaviours = $teachers->sum('total_behaviours');
        $summition_of_points = $teachers->sum('total_points');

        $teachers = $teachers->sortByDesc('total_behaviours')->map(function ($teacher) {
            return [
                'id'               => $teacher->id,
                'name'             => $teacher->name,
                'user_name'        => $teacher->user_name,
                'image'            => $teacher->image,
                'total_behaviours' => $teacher->total_behaviours,
                'total_points'     => $teacher->total_points
            ];
        });

        $data = [
            'most_active_teachers' => $teachers,
            'summition_of_behaviours' => $summition_of_behaviours,
            'summition_of_points' => $summition_of_points
        ];

        return $this->sendResponse($data, __('api.most active teachers retrieved successfully'));
    }

    public function mostActiveStudents()
    {
        $addedBehaviours = AddedBehaviours::query();

        $mostActiveStudents = $addedBehaviours
            ->select(
                'student_id',
                DB::raw('COUNT(id) as total_behaviours'),
                DB::raw('SUM(ABS(points)) as total_points')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->where('school_id', Utility::school_id())
            ->groupBy('student_id')
            ->orderBy('total_points', 'DESC')
            ->take(20)
            ->get();

        $students = Students::whereIn('id', $mostActiveStudents->pluck('student_id'))
            ->orderByRaw('FIELD(id, ' . implode(',', $mostActiveStudents->pluck('student_id')->toArray()) . ')') // يحافظ على ترتيب الـ mostActiveStudents
            ->get()
            ->map(function ($student) use ($mostActiveStudents) {
                $studentData = $mostActiveStudents->firstWhere('student_id', $student->id);
                $student->total_behaviours = $studentData->total_behaviours;
                $student->total_points = $studentData->total_points;
                return $student;
            });

        $summition_of_behaviours = $students->sum('total_behaviours');
        $summition_of_points = $students->sum('total_points');

        $students = $students->sortByDesc('total_points')->map(function ($student) {
            return [
                'id'               => $student->id,
                'name'             => $student->name,
                'user_name'        => $student->user_name,
                'image'            => $student->image,
                'total_behaviours' => $student->total_behaviours,
                'total_points'     => $student->total_points,
                'class'            => $student?->student?->class ? $student?->student?->class?->Data?->first()?->title : null,
                'grade'            => $student?->student?->grade ? $student?->student?->grade?->Data?->first()?->title : null,
                'row'              => $student?->student?->row ? $student?->student?->row?->Data?->first()?->title : null,
            ];
        });

        $data = [
            'most_active_students'    => $students,
            'summition_of_behaviours' => $summition_of_behaviours,
            'summition_of_points'     => $summition_of_points
        ];

        return $this->sendResponse($data, __('api.most active students retrieved successfully'));
    }

    public function teacherBehaviorReport()
    {
        $addedBehaviours = AddedBehaviours::query();

        $report = $addedBehaviours
            ->select(
                'user_id',
                DB::raw('CAST(SUM(CASE WHEN points < 0 THEN 1 ELSE 0 END) AS UNSIGNED) as negative_behaviors'),
                DB::raw('CAST(SUM(CASE WHEN points > 0 THEN 1 ELSE 0 END) AS UNSIGNED) as good_behaviors')
            )
            ->where('school_id', Utility::school_id())
            ->groupBy('user_id')
            ->get();

        $teachers = Teachers::whereIn('id', $report->pluck('user_id'))
            ->get()
            ->map(function ($teacher) use ($report) {
                $teacherReport = $report->firstWhere('user_id', $teacher->id);
                $teacher->negative_behaviors = $teacherReport->negative_behaviors;
                $teacher->good_behaviors = $teacherReport->good_behaviors;
                return $teacher;
            });

        $teachers = $teachers->map(function ($teacher) {
            $total_behaviors = $teacher->negative_behaviors + $teacher->good_behaviors;
            return [
                'id'                 => $teacher->id,
                'name'               => $teacher->name,
                'user_name'          => $teacher->user_name,
                'image'              => $teacher->image,
                'negative_behaviors' => $teacher->negative_behaviors,
                'good_behaviors'     => $teacher->good_behaviors,
                'total_behaviors'    => $total_behaviors,
            ];
        });

        $teachers = $teachers->sortByDesc('total_behaviors')->values();

        return $this->sendResponse($teachers, __('api.teacher behavior report retrieved successfully'));
    }

    public function classRowPointsReport()
    {
        $report = DB::table('students_behaviors')
            ->select(
                'students.class_id',
                'students.row_id',
                DB::raw('SUM(ABS(students_behaviors.points)) as total_points')
            )
            ->where('school_id', Utility::school_id())
            ->join('students', 'students_behaviors.student_id', '=', 'students.user_id') // Link to students table
            ->groupBy('students.class_id', 'students.row_id')
            ->orderBy('students.row_id', 'asc') // ترتيب حسب الـ row_id تصاعدياً
            ->orderBy('students.class_id', 'asc') // ترتيب حسب الـ class_id تصاعدياً
            ->get();

        $formattedReport = $report->map(function ($item) {
            $classTitle = DB::table('classes_data')
                ->where('class_id', $item->class_id)
                ->value('title');

            $rowTitle = DB::table('rows_data')
                ->where('row_id', $item->row_id)
                ->value('title');

            return [
                'class'  => $classTitle ?: 'Unknown',
                'row'    => $rowTitle ?: 'Unknown',
                'points' => $item->total_points,
            ];
        });

        $data = [
            'report' => $formattedReport,
        ];

        return $this->sendResponse($data, __('api.class row summary report retrieved successfully'));
    }

    public function behaviorReport()
    {
        $data = DB::table('students_behaviors')
            ->join('behaviors', 'students_behaviors.behavior_id', '=', 'behaviors.id')
            ->join('behaviors_data', 'behaviors_data.behavior_id', '=', 'behaviors.id')
            ->select(
                'behaviors.id',
                'behaviors_data.title',
                'students_behaviors.points',
                DB::raw('COUNT(students_behaviors.behavior_id) as times_assigned'),
                DB::raw('SUM(students_behaviors.points) as total_points')
            )
            ->where('students_behaviors.school_id', Utility::school_id())
            ->orderByRaw('COUNT(students_behaviors.behavior_id) DESC') // <-- sort by times_assigned DESC
            ->groupBy('behaviors.id', 'behaviors_data.title', 'students_behaviors.points')
            ->get();

        // Transform data to match the desired output structure
        $report = $data->map(function ($item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'points' => $item->points,
                'times_assigned' => $item->times_assigned,
                'total_points' => $item->total_points,
            ];
        });

        return $this->sendResponse($report, __('api.behavior report retrieved successfully'));
    }

    public function topStudentsByPoints()
    {
        $data = DB::table('students_behaviors')
            ->join('users', 'students_behaviors.student_id', '=', 'users.id')
            ->select(
                'users.id as student_id',
                'users.name as student_name',
                DB::raw('SUM(students_behaviors.points) as total_points'),
                DB::raw('COUNT(students_behaviors.behavior_id) as behaviors_assigned')
            )
            ->where('students_behaviors.school_id', Utility::school_id())
            ->groupBy('users.id', 'users.name')
            ->orderBy('total_points', 'DESC')
            ->limit(20)
            ->get();

        $topStudents = $data->map(function ($item) {
            return [
                'id' => $item->student_id,
                'name' => $item->student_name,
                'total_points' => $item->total_points,
                'behaviors_assigned' => $item->behaviors_assigned,
            ];
        });

        // Return the report as JSON
        return $this->sendResponse($topStudents, __('api.top students by points retrieved successfully'));
    }

    public function classRowPointsDelayReport()
    {
        $schoolId  = Utility::school_id();
        $start_date = request()->get('start_date');
        $end_date   = request()->get('end_date');

        // Fetch all class-row combinations from the students table (filtered by school)
        $classRowCombinations = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id') // ✅ ربط مع users لجلب school_id
            ->select('students.class_id', 'students.row_id')
            ->where('users.school_id', $schoolId) // ✅ فلترة بناءً على school_id
            ->distinct()
            ->get();

        // Fetch delay & absence counts
        $report = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id') // ✅ ربط مع users لجلب school_id
            ->leftJoin('students_behaviors', 'students.user_id', '=', 'students_behaviors.student_id')
            ->select(
                'students.class_id',
                'students.row_id',
                DB::raw('SUM(CASE WHEN students_behaviors.behavior_id = 82 THEN 1 ELSE 0 END) as total_delays'),
                DB::raw('SUM(CASE WHEN students_behaviors.behavior_id = 81 THEN 1 ELSE 0 END) as total_absences')
            )
            ->where('users.school_id', $schoolId) // ✅ فلترة بناءً على school_id
            ->when($start_date || $end_date, function ($query) use ($start_date, $end_date) {
                if ($start_date && $end_date) {
                    return $query->whereBetween('students_behaviors.created_at', [$start_date, $end_date]);
                } elseif ($start_date) {
                    return $query->where('students_behaviors.created_at', '>=', $start_date);
                } elseif ($end_date) {
                    return $query->where('students_behaviors.created_at', '<=', $end_date);
                }
            })
            ->groupBy('students.class_id', 'students.row_id')
            ->get();

        // Get student count per class and row
        $studentCounts = DB::table('students')
            ->join('users', 'students.user_id', '=', 'users.id') // ✅ ربط مع users لجلب school_id
            ->select('students.class_id', 'students.row_id', DB::raw('COUNT(students.user_id) as total_students'))
            ->where('users.school_id', $schoolId) // ✅ فلترة بناءً على school_id
            ->groupBy('students.class_id', 'students.row_id')
            ->get();

        // Format the final report
        $formattedReport = $classRowCombinations->map(function ($item) use ($report, $studentCounts) {
            $classTitle = DB::table('classes_data')
                ->where('class_id', $item->class_id) // ❌ إزالة school_id لأنه مش موجود هنا
                ->value('title');

            $rowTitle = DB::table('rows_data')
                ->where('row_id', $item->row_id) // ❌ إزالة school_id لأنه مش موجود هنا
                ->value('title');

            $record = $report->first(fn($r) => $r->class_id == $item->class_id && $r->row_id == $item->row_id);

            // Find the corresponding student count
            $studentCount = $studentCounts->first(fn($s) => $s->class_id == $item->class_id && $s->row_id == $item->row_id);

            return [
                'class_id'       => $item->class_id,
                'row_id'         => $item->row_id,
                'class'          => $classTitle ?: 'Unknown',
                'row'            => $rowTitle  ?: 'Unknown',
                'total_delays'   => $record ? $record->total_delays : 0,
                'total_absences' => $record ? $record->total_absences : 0,
                'total_students' => $studentCount ? $studentCount->total_students : 0,
            ];
        })
            ->sortBy([['row_id', 'asc'], ['class_id', 'asc']])
            ->values();

        return $this->sendResponse(['report' => $formattedReport], __('api.class row delay and absence report retrieved successfully'));
    }
}
