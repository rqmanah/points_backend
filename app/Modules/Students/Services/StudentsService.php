<?php

namespace App\Modules\Students\Services;

use App\Bll\Paths;
use App\Bll\Utility;
use App\Services\Store;
use App\Modules\Students\Models\Students;
use App\Modules\Students\Models\StudentsExtraData;
use App\Modules\Students\Resources\StudentsResource;
use App\Modules\Students\Resources\StudentsResourceEdit;
use Illuminate\Support\Facades\DB;

class StudentsService extends Store
{
    protected $error;
    protected $success;
    protected $saved;

    public function __construct()
    {
        $this->resource = StudentsResource::class;
        //set messages
        $this->error = __('api.There is no Students');
        $this->success = __('api.All Students retrieved successfully');
        $this->saved = __('api.Students created successfully');

        parent::__construct(Students::query());
    }

    public function GetAll()
    {
        return $this->Get(
            ["users.id", "name", "user_name", 'dialing_code', "is_active", "national_id", 'users.created_at'],
            []
        );
    }

    public function storeData()
    {
        if (!Utility::checkStudentCount()) {
            return __('api.You have reached the maximum number of students');
        }
        try {
            DB::beginTransaction();
            $this->public_path = Paths::get_public_path('schools');
            request()->merge(['user_name' => Utility::generateUserName(request('grade_id'), request('row_id'), request('class_id'))]);

            $this->store(
                ["name", "user_name", "password", 'dialing_code', 'national_id', "is_active"],
                [],
                ""
            );
            if ($this->GetCreated()?->id != null) {
                StudentsExtraData::create([
                    'user_id' => $this->GetCreated()->id,
                    'grade_id' => request('grade_id'),
                    'row_id' => request('row_id'),
                    'class_id' => request('class_id'),
                    'national_id' => request('national_id'),
                    'guardian_phone' => request('guardian_phone'),
                ]);
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $e->getMessage();
        }
        return $this->saved;
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Students::where('id', $id)->first();
            if ($data != null) {
                $this->data = StudentsResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function edit(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Students::where('id', $id)->first();
            if ($data != null) {
                $this->data = StudentsResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function updateData(int $id)
    {
        $this->update(["name", "user_name", 'national_id', "is_active"], [], "", $id);

        $student = StudentsExtraData::where('user_id', $id)->first();
        if ($student) {
            $student->update([
                'grade_id' => request('grade_id'),
                'row_id' => request('row_id'),
                'class_id' => request('class_id'),
                'national_id' => request('national_id'),
                'guardian_phone' => request('guardian_phone'),
            ]);
        }
        return $this->saved;
    }

    public function deleteStds(array $ids)
    {

        foreach ($ids as $id) {
            Students::where('id', $id)->forceDelete();
        }
    }
}
