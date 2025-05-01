<?php

namespace App\Modules\TeachersAuth\Controllers;

use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Modules\TeachersAuth\Models\Teacher;
use App\Modules\TeachersAuth\Requests\LoginRequest;
use App\Modules\TeachersAuth\Requests\UpdateRequest;
use App\Modules\TeachersAuth\Resources\TeacherResource;
use App\Modules\TeachersAuth\Requests\UpdatePasswordRequest;

class AuthController extends Controller
{
    public function login(LoginRequest $request): JsonResponse
    {
        // login with user_name or phone
        $user = Teacher::query()
            ->where('user_name', $request->user_name)
            ->first();

        if (!$user) {
            return $this->sendError(__('api.Incorrect UserName Or Password'));
        }

        if ($user->school_id) {
            //check if the school is deleted
            $school = $user->school;
            if (!$school) {
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
            $token = $user->createToken('manager', ['teacher'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            $user['token'] = $token;
            return $this->sendResponse(TeacherResource::make($user), __('api.login successfully'));
        }

        return $this->sendError(__('api.Incorrect username or phone or password'));
    }

    public function update(UpdateRequest $request)
    {
        $teacherId = Auth::guard('sanctum')?->id();
        $teacher = Teacher::where('id', $teacherId)->first();
        if (!$teacher) {
            return $this->sendError(__('api.User not found'));
        }
        $teacher->update([
            'name'     => $request->name,
            'image'    => $request->image ?: $teacher->image,
        ]);
        return $this->sendResponse(TeacherResource::make($teacher), __('api.Updated successfully'));
    }

    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            Auth::user()?->tokens()->delete();
            return $this->sendResponse([], __('api.Logged out successfully'));
        }
        return $this->sendError(__('api.User not found'));
    }

    // update password
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $teacherId = Auth::guard('sanctum')?->id();
        $teacher = Teacher::where('id', $teacherId)->first();
        if (!$teacher) {
            return $this->sendError(__('api.User not found'));
        }
        if (!Hash::check($request->old_password, $teacher->password)) {
            return $this->sendError(__('api.Old password is incorrect'));
        }
        $teacher->update([
            'password' => Hash::make($request->password),
        ]);
        return $this->sendResponse(TeacherResource::make($teacher), __('api.Updated successfully'));
    }
}
