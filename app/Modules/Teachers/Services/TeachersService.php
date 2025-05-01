<?php

namespace App\Modules\Teachers\Services;

use App\Bll\Utility;
use App\Services\Store;
use App\Modules\Teachers\Models\Teachers;
use App\Modules\Teachers\Resources\TeachersResource;
use App\Modules\Teachers\Resources\TeachersResourceEdit;
use Illuminate\Support\Facades\Hash;

class TeachersService extends Store
{

    protected $error;
    protected $success;
    protected $saved;

    
    public function __construct()
    {
        $this->resource = TeachersResource::class;
        //set messages
        $this->error = __('api.There is no Teachers');
        $this->success = __('api.All Teachers retrieved successfully');
        $this->saved = __('api.Teacher created successfully');

        parent::__construct(Teachers::query());
    }

    public function GetAll()
    {
        return $this->Get(["users.id", "name", 'national_id', "user_name", "is_active"], []);
    }

    public function storeData()
    {
        if (!Utility::checkTeacherCount()) {
            return __('api.You have reached the maximum number of teachers');
        }
        request()->merge(['user_name' => Utility::generateUserNameTeacher(request()->name)]);
        $this->store(["name", "user_name", 'national_id', "password", "is_active"], [], "");
        return $this->saved;
    }

    public function showData(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Teachers::where('id', $id)->first();
            if ($data != null) {
                $this->data = TeachersResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function edit(int $id)
    {
        if (isset($id) && is_int($id)) {
            $data = Teachers::where('id', $id)->first();
            if ($data != null) {
                $this->data = TeachersResourceEdit::make($data);
            }
        }
        return $this->saved;
    }

    public function updateData(int $id)
    {
        $this->update(["name", "user_name", 'national_id', "password", "is_active"], [], "", $id);
        return $this->saved;
    }

    public function updatePassword($request)
    {
        $teacher = Teachers::where('id', $request->teacher_id)->first();
        if ($teacher == null) {
            return __('api.Teacher not found');
        }
        $teacher->update([
            'password' => Hash::make($request->password)
        ]);
        return __('api.Password updated successfully');
    }

    public function deleteTeachers(array $ids)
    {
        foreach ($ids as $id) {
            Teachers::where('id', $id)->forceDelete();
        }
    }
}
