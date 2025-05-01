<?php

namespace App\Modules\Students\Controllers;

use App\Bll\Twilio;
use App\Bll\Utility;
use Illuminate\Http\Request;
use App\Exports\StudentsExport;
use App\Imports\StudentsImport;
use App\Imports\ErrorRowsExport;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Modules\Students\Models\Students;
use App\Modules\Behaviors\Models\Behaviors;
use App\Http\Controllers\AdminBaseController;
use App\Modules\Students\Requests\StoreRequest;
use App\Modules\Students\Requests\UpdateRequest;
use App\Modules\Students\Models\StudentsBehaviors;
use App\Modules\Students\Services\StudentsService;
use App\Modules\Students\Requests\DeleteStdsRequest;
use App\Modules\Students\Requests\StoreExcelRequest;
use App\Modules\Students\Resources\StudentsResource;
use App\Modules\Students\Requests\AddBehaviorRequest;
use App\Modules\Students\Requests\UpdatePasswordRequest;
use App\Modules\Students\Requests\AddBehaviorTeacherRequest;
use App\Modules\Students\Resources\StudentBehaviorDetailsResource;

class adminController extends AdminBaseController
{
    public function __construct()
    {
        $this->service = new StudentsService();
        $this->StoreRequest = new StoreRequest();
        $this->UpdateRequest = new UpdateRequest();
    }

    public function studentsIndex(Request $request)
    {
        $limit = $request->input('limit', 20);
        $students = Students::query();

        $filters = ['row_id', 'grade_id', 'class_id'];
        foreach ($filters as $filter) {
            if ($request->filled($filter)) {
                $students->whereHas('student', function ($query) use ($request, $filter) {
                    $query->where($filter, $request->$filter);
                });
                $limit = $request->input('limit', 100);
            }
        }

        if ($request->filled('term')) {
            $students->where('name', 'like', '%' . $request->term . '%');
        }

        $students = $students->paginate($limit);

        $response = [
            'status' => 'success',
            'result' => [
                'data' => StudentsResource::collection($students),
                'links' => [
                    'first' => $students->url(1),
                    'last' => $students->url($students->lastPage()),
                    'prev' => $students->previousPageUrl(),
                    'next' => $students->nextPageUrl(),
                ],
                'meta' => [
                    'current_page' => $students->currentPage(),
                    'from' => $students->firstItem(),
                    'last_page' => $students->lastPage(),
                    'links' => $students->linkCollection()->toArray(),
                    'path' => $students->path(),
                    'per_page' => $students->perPage(),
                    'to' => $students->lastItem(),
                    'total' => $students->total(),
                ]
            ],
            'message' => __('api.Students retrieved successfully'),
        ];

        return response()->json($response, 200);
    }


    public function storeExcel(StoreExcelRequest $request)
    {
        if (!Utility::checkStudentCount()) {
            return $this->sendResponse([], __('api.You have reached the maximum number of students'));
        }
        try {
            DB::beginTransaction();
            $file = public_path('temp' . DIRECTORY_SEPARATOR . $request->file);
            // Instantiate the import class
            $importer = new StudentsImport();
            // Import the file
            Excel::import($importer, $file);
            // Get the error count after the import
            $errorCount = $importer->getErrorCount();
            $errorRows = $importer->getErrorRows();
            $maxStudents = $importer->getMaxStudents();
            unlink($file);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
        if ($maxStudents == 1) {
            return $this->sendResponse([], __('api.You have reached the maximum number of students'));
        }
        if ($errorCount > 0) {

            // Export the error rows to a new Excel file
            $file = Excel::download(new ErrorRowsExport($errorRows), 'error_students_rows.xlsx');
            return $file;
            // Return a response or handle the result as needed
            //            return $this->sendResponse([
            //                'error_count' => $errorCount,
            //                'error_rows' => $errorRows,
            //                'file_url' => $file->,
            //                'file' => $file->getFile()
            //            ], __('api.Students created successfully'));
        }
        return $this->sendResponse([], __('api.Students created successfully'));
    }

    public function addBehavior(AddBehaviorRequest $request)
    {
        $studentsBehavior = new StudentsBehaviors();
        $studentsIds = $request->student_ids;
        $behaviorId = $request->behavior_id;
        $behaviors = Behaviors::where('id', $behaviorId)->first();
        try {
            DB::beginTransaction();
            foreach ($studentsIds as $studentId) {
                if ($behaviors) {
                    $studentsBehavior->create([
                        'student_id' => $studentId,
                        'behavior_id' => $behaviors->id,
                        'points' => $behaviors->points,
                        'note' => $request->note ?? null,
                    ]);
                }
            }
            // $this->sendGuardianMessage($studentId);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse([], __('api.Behavior added successfully'));
    }

    // private function sendGuardianMessage($studentId)
    // {
    //     // get last 3 behaviors of this student
    //     $StudentsBehaviors = StudentsBehaviors::where('student_id', $studentId)->orderBy('id', 'desc')->take(3)->get();
    //     if ($StudentsBehaviors->count() == 3) {
    //         $good_behaviors_triger = 0;
    //         $bad_behaviors_triger = 0;
    //         foreach ($StudentsBehaviors as $behavior) {
    //             if ($behavior->points < 0) {
    //                 $bad_behaviors_triger++;
    //             }
    //             if ($behavior->points > 0) {
    //                 $good_behaviors_triger++;
    //             }
    //         }
    //         $student = Students::where('id', $studentId)->first();
    //         $twilio = new Twilio($student->guardian_phone);
    //         if ($good_behaviors_triger == 3) {
    //             $twilio->sendMessage('Your child ' . $student->name . ' has shown good behavior for 3 consecutive times');
    //         }
    //         if ($bad_behaviors_triger == 3) {
    //             $twilio->sendMessage('Your child ' . $student->name . ' has shown bad behavior for 3 consecutive times');
    //         }
    //     }
    // }

    public function addBehaviorTeacher(AddBehaviorTeacherRequest $request)
    {
        $studentsBehavior = new StudentsBehaviors();
        $studentsIds = $request->student_ids;
        $behaviorId = $request->behavior_id;
        $behaviors = Behaviors::where('id', $behaviorId)->first();
        try {
            DB::beginTransaction();
            foreach ($studentsIds as $studentId) {
                if ($behaviors) {
                    $studentsBehavior->create([
                        'student_id' => $studentId,
                        'behavior_id' => $behaviors->id,
                        'points' => $behaviors->points,
                        'note' => $request->note ?? null,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse([], __('api.Behavior added successfully'));
    }

    public function studentBehaviorDetails($id)
    {
        $student = Students::where('id', $id)->with('behaviors')->first();
        if (!$student) {
            return $this->sendError(__('api.Student not found'));
        }
        $data = [
            'behaviors' => StudentBehaviorDetailsResource::collection($student->behaviors->whereNotNull('behavior_id')),
            'good_points' => $student->sumGoodPoints(),
            'bad_points' => $student->sumBadPoints(),
            'current_points' => $student->sumAllPoints(),
            'total_behaviors' => $student->behaviors->count(),
            'good_behaviors_count' => $student->behaviors->where('points', '>', 0)->count(),
            'bad_behaviors_count' => $student->behaviors->where('points', '<', 0)->count(),
            'students' => [
                'id' => $student->id,
                'name' => $student->name,
                'grade' => $student->student?->grade ? $student->student->grade->Data->first()->title : null,
                'class' => $student->student?->class ? $student->student->class->Data->first()->title : null,
                'row' => $student->student?->row ? $student->student->row->Data->first()->title : null,
            ]
        ];
        return $this->sendResponse($data, __('api.Behaviors retrieved successfully'));
    }

    public function studentBehaviorTop()
    {
        $students = Students::all();
        $students = $students->map(function ($student) {
            return [
                'id'          => $student->id,
                'name'        => $student->name,
                'row'         => $student->student?->row   ? $student->student->row->Data->first()->title : null,
                'class'       => $student->student?->class ? $student->student->class->Data->first()->title : null,
                'grade'       => $student->student?->grade ? $student->student->grade->Data->first()->title : null,
                'points'      => $student->student?->sumPoints() ?? 0,
                'good_behaviors_count' => $student->behaviors->where('points', '>', 0)->count(),
                'bad_behaviors_count' => $student->behaviors->where('points', '<', 0)->count(),
                'good_points_count' => $student->student?->sumGoodPoints() ?? 0,
                'bad_points' => $student->student?->sumBadPoints() ?? 0,
            ];
        });

        if (Auth::user()->guard == 'teacher') {
            $students = $students->where('points', '!=', 0)->sortByDesc('points')->values()->take(10);
            return $this->sendResponse($students, __('api.Top students retrieved successfully'));
        }

        $students = $students->sortByDesc('points')->values()->take(10);

        return $this->sendResponse($students, __('api.Top students retrieved successfully'));
    }

    // update student password
    public function updatePassword(UpdatePasswordRequest $request)
    {
        $student = Students::where('id', $request->student_id)->first();
        if (!$student) {
            return $this->sendError(__('api.Student not found'));
        }
        $student->update([
            'password' => Hash::make($request->password)
        ]);
        return $this->sendResponse([], __('api.Password updated successfully'));
    }

    // download excel file
    public function downloadExcel()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }

    public function deleteStds(DeleteStdsRequest $request)
    {
        $this->service->deleteStds($request->ids);
        return $this->sendResponse([], __('api.Students deleted successfully'));
    }
}
