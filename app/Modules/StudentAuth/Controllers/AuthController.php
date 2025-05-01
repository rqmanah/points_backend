<?php

namespace App\Modules\StudentAuth\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\StudentAuth\Models\Students;
use App\Modules\StudentAuth\Requests\LoginRequest;
use App\Modules\StudentAuth\Requests\UpdateRequest;
use App\Modules\StudentAuth\Resources\StudentsResource;
use App\Modules\StudentAuth\Requests\UpdatePasswordRequest;

class AuthController extends Controller
{

    public function login(LoginRequest $request): JsonResponse
    {
        $user = Students::query()
            ->where('username', $request->username)->first();

        if (!$user) {
            return $this->sendError(__('api.Incorrect user name or password'));
        }

        if($user->school_id){
            //check if the school is deleted
            $school = $user->school;
            if(!$school){
                return $this->sendError(__('api.School not found'));
            }
        }

        if (Hash::check($request->password, $user->password)) {
            Auth::login($user);
            $user = Auth::user();
            if ($user->is_active != 1) {
                return $this->sendError(__('api.This Account has been blocked, check our policies'));
            }
            // if env local
            if (env('APP_ENV') !== 'local') {
                $user->tokens()->delete();
            }
            $token = $user->createToken('student', ['student'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            $user['token'] = $token;
            return $this->sendResponse(StudentsResource::make($user), __('api.login successfully'));
        }

        return $this->sendError(__('api.Incorrect national id or password'));
    }

    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            Auth::user()?->tokens()->delete();
            return $this->sendResponse([], __('api.Logged out successfully'));
        }
        return $this->sendError(__('api.User not found'));
    }

    public function update(UpdateRequest $request)
    {
        $studentId = Auth::guard('sanctum')?->id();
        $student = Students::where('id', $studentId)->first();
        if (!$student) {
            return $this->sendError(__('api.User not found'));
        }
        $student->update([
            'name'     => $request->name,
            'image'    => $request->image ?: $student->image,
        ]);
        return $this->sendResponse(StudentsResource::make($student), __('api.Updated successfully'));
    }

    public function updatePassword(UpdatePasswordRequest $request)
    {
        $studentId = Auth::guard('sanctum')?->id();
        $student = Students::where('id', $studentId)->first();
        if (!$student) {
            return $this->sendError(__('api.User not found'));
        }
        if(!Hash::check($request->old_password, $student->password)) {
            return $this->sendError(__('api.Old password is incorrect'));
        }
        $student->update([
            'password' => Hash::make($request->password),
        ]);
        return $this->sendResponse(StudentsResource::make($student), __('api.Updated successfully'));
    }

}
