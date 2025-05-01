<?php

namespace App\Imports;

use App\Bll\Utility;
use App\Models\Users;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Modules\Teachers\Models\Teachers;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TeachersImport implements ToModel, WithHeadingRow
{
    use Importable;

    protected $errorCount = 0; // To count the number of errors
    protected $errorRows = []; // To store the row numbers with errors
    protected $currentRow = 0; // To track the current row number

    protected $maxTeachers = 0; // Maximum number of teachers allowed

    public function model(array $row)
    {
        $this->currentRow++;
        // Basic validation for required fields

        $values = array_values($row);
        $newKeys = range(1, count($values));
        $row = array_combine($newKeys, $values);
        if (
            empty($row[1])
        ) {
            $this->errorCount++;
            $this->errorRows[] = ['row' => $this->currentRow, 'data' =>  $row];
            return null;
        }

        // dd('heer');
        $validator = Validator::make(['field_1' => $row[1]], [
            'field_1' => 'required|string|max:255|min:3|unique:users,user_name',
        ]);


        if ($validator->fails()) {
            $this->errorCount++;
            $this->errorRows[] = ['row' => $this->currentRow, 'data' =>  $row];
            return null; // Skip the row
        }
        if (!Utility::checkTeacherCount()) {
            $this->maxTeachers = 1;
            return null; // Skip the row
        }
        $user_name = Utility::generateUserNameTeacher($row[1]);
        $user_name = $this->checkIfUserNamesExists($user_name)
            ? $user_name . rand(1, 1000)
            : $user_name;
        return new Teachers(
            [
                'name'        => $row[1],
                'user_name'   => $user_name,
                'password'    => bcrypt($user_name),
                'is_active'   => 1
            ]
        );
    }

    // Handle errors and store the row number
    public function onError(\Throwable $e)
    {
        $this->errorCount++;
        $this->errorRows[] = ['row' => $this->currentRow, 'data' =>  null];
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

    public function getMaxTeachers()
    {
        return $this->maxTeachers;
    }

    private function checkIfUserNamesExists($user_name)
    {
        return Users::where('user_name', $user_name)->exists();
    }
}
