<?php

namespace App\Imports;

use App\Bll\Utility;
use App\Models\Users;
use App\Rules\CheckClasses;
use App\Rules\CheckRowSchool;
use App\Rules\CheckGradeSchool;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Modules\Auth\Models\Schools\Rows;
use App\Modules\Students\Models\Students;
use Illuminate\Support\Facades\Validator;
use App\Modules\Auth\Models\Grades\Grades;
use Maatwebsite\Excel\Concerns\Importable;
use App\Modules\Auth\Models\Classes\Classes;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Modules\Students\Models\StudentsExtraData;

class StudentsImport implements ToModel, WithHeadingRow
{
    use Importable;

    protected $errorCount = 0; // To count the number of errors
    protected $errorRows = []; // To store the row numbers with errors
    protected $currentRow = 0; // To track the current row number
    protected $maxStudents = 0; // Maximum number of students allowed

    public function model(array $row)
    {
        $this->currentRow++;

        $values = array_values($row);
        $newKeys = range(1, count($values));
        $row = array_combine($newKeys, $values);

        // Basic validation for required fields
        // dd($row);
        if (
            empty($row[1]) ||
            empty($row[2]) ||
            empty($row[3]) ||
            empty($row[4]) ||
            empty($row[5])
        ) {
            $this->errorCount++;
            $this->errorRows[] = ['row' => $this->currentRow, 'data' =>  $row];
            return null;
        }

        $validator = Validator::make([
            'field_1' => $row[1],
            'field_2' => $row[2],
            'field_3' => $row[3],
            'field_4' => $row[4],
            'field_5' => $row[5]
        ], [
            'field_1' => 'required|string|max:255|min:3',
            'field_2' => ['required', new CheckGradeSchool()],
            'field_3' => ['required', new CheckRowSchool()],
            'field_4' => ['required', new CheckClasses()],
            'field_5' => 'required|max:25',
        ]);

        if ($validator->fails()) {
            $this->errorCount++;
            $this->errorRows[] = ['row' => $this->currentRow, 'data' =>  $row];
            return null; // Skip the row
        }

        if (!Utility::checkStudentCount()) {
            $this->maxStudents = 1;
            return null; // Skip the row
        }

        // Get related IDs
        $grade_id = Grades::whereHas('Data', function ($query) use ($row) {
            $query->where('title', $row[2]);
        })->first()?->id;

        $row_id = Rows::whereHas('Data', function ($query) use ($row) {
            $query->where('title', $row[3]);
        })->first()?->id;

        $class_id = Classes::whereHas('Data', function ($query) use ($row) {
            $query->where('title', $row[4]);
        })->first()?->id;

        // Return the created StudentsExtraData instance to indicate success
        $guardian_phone = $row[5];
        // delete dialing code from guardian phone
        $guardian_phone = str_replace(env('GUARDIAN_DAILING_CODE'), '', $guardian_phone);

        $user_name = null;

        $user_name = Utility::generateUserName($grade_id, $row_id, $class_id);

        // Check if the generated username is unique
        $user_name = $this->checkIfUserNamesExists($user_name)
            ? $user_name . rand(1, 1000)
            : $user_name;

        // Proceed to create the student record if validation passes
        $user = Students::create([
            'name'         => $row[1],
            'user_name'    => $user_name,
            'password'     => bcrypt($user_name),
            'dialing_code' => env('GUARDIAN_DAILING_CODE'),
            'is_active'    => 1
        ]);

        return new StudentsExtraData([
            'user_id'        => $user->id,
            'grade_id'       => $grade_id,
            'row_id'         => $row_id,
            'class_id'       => $class_id,
            'guardian_phone' => $guardian_phone,
        ]);
    }

    // Handle errors and store the row number
    public function onError(\Throwable $e)
    {
        $this->errorCount++;
        $this->errorRows[] = ['row' => $this->currentRow, 'data' => null];
    }

    // Get the count of users that had errors
    public function getErrorCount()
    {
        return $this->errorCount;
    }

    // Get the row numbers with errors
    public function getErrorRows()
    {
        return $this->errorRows;
    }

    public function getMaxStudents()
    {
        return $this->maxStudents;
    }

    private function checkIfUserNamesExists($user_name)
    {
        return Users::where('user_name', $user_name)->exists();
    }
}
