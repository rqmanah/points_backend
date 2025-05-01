<?php

namespace App\Modules\Auth\Controllers;

use App\Bll\OTP;
use App\Bll\Utility;
use App\Models\Users;
use App\Bll\MyFatoorah;
use App\Models\Payments;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Modules\Auth\Models\Manager;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\Auth\Models\SchoolMaster;
use App\Modules\Auth\Models\Schools\Rows;
use App\Modules\Auth\Models\Grades\Grades;
use App\Modules\Auth\Requests\LoginRequest;
use App\Modules\Auth\Models\Classes\Classes;
use App\Modules\Auth\Models\Packages\Coupon;
use App\Modules\Auth\Models\Schools\Schools;
use App\Modules\Auth\Requests\SchoolRequest;
use App\Modules\Auth\Resources\RowsResource;
use App\Modules\StudentAuth\Models\Students;
use App\Modules\TeachersAuth\Models\Teacher;
use App\Modules\Auth\Models\Packages\Packages;
use App\Modules\Auth\Requests\RegisterRequest;
use App\Modules\Auth\Resources\GradesResource;
use App\Modules\Auth\Requests\otpVerifyRequest;
use App\Modules\Auth\Resources\ClassesResource;
use App\Modules\Auth\Resources\ManagerResource;
use App\Modules\Auth\Resources\SchoolsResource;
use App\Modules\Auth\Models\Countries\Countries;
use App\Modules\Auth\Requests\SchoolCouponCheck;
use App\Modules\Auth\Requests\UpdateStoreRequest;
use App\Modules\Auth\Resources\CountriesResource;
use App\Modules\Auth\Models\Packages\PackagesPlan;
use App\Modules\Auth\Requests\RestPasswordRequest;
use App\Modules\Auth\Models\Schools\SchoolsPackage;
use App\Modules\Auth\Requests\profileUpdateRequest;
use App\Modules\Auth\Requests\resetPasswordRequest;
use App\Modules\Auth\Resources\Store\StoreResource;
use App\Modules\Auth\Requests\forgetPasswordRequest;
use App\Modules\Auth\Requests\SchoolPackagesRequest;
use App\Modules\StudentAuth\Resources\StudentsResource;
use App\Modules\Auth\Models\Packages\PackagesPermission;
use App\Modules\Auth\Resources\Packages\PackagesResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request): JsonResponse
    {

        try {
            DB::beginTransaction();
            $phone = (new Utility)->removeZeroFomphone($request->phone);
            $user_name = Utility::generateUserNameManager($request->name , $phone);
            $user = Manager::create([
                'name' => $request->name,
                'user_name' => $user_name,
                'dialing_code' => $request->dialing_code,
                'phone' => $phone,
                'email' => $request->email,
                'gender' => $request->gender,
                'national_id' => $request->national_id,
                'password' => bcrypt($request->password),
            ]);
            if (!$user) {
                return $this->sendError(__('api.failed to register'));
            }

            $token = $user->createToken('manager', ['manager'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            $user['token'] = $token;

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(__('api.failed to register'));
        }

        $otp = new OTP('verify_email', $user->email , $user_name);
        $otp->sendOtp();
        // End send otp
        return $this->sendResponse(ManagerResource::make($user), __('api.register successfully, please verify your phone number'));
    }

    public function verifyOtp(otpVerifyRequest $request): JsonResponse
    {
        $user = Auth::user();

        if (!$user) {
            return $this->sendError(__('api.User not found'));
        }

        if ($user->phone_verified_at != null) {
            return $this->sendError(__('api.This Account has been verified before'));
        }

        $otp = new OTP('verify_email', $user->email);
        $otp = $otp->verifyOtp($request->otp);

        if (!$otp) {
            return $this->sendError(__('api.Incorrect OTP'));
        }
        $user->update(['phone_verified_at' => now()]);

        return $this->sendResponse(ManagerResource::make($user), __('api.Phone number verified successfully'));
    }

    public function reSendOtp()
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendError(__('api.User not found'));
        }

        if ($user->phone_verified_at != null) {
            return $this->sendError(__('api.This Account has been verified before'));
        }

        $otp = new OTP('verify_email', $user->email);
        $otp->resendOtp();

        return $this->sendResponse([], __('api.OTP sent successfully'));
    }

    public function forgetPassword(forgetPasswordRequest $request)
    {

        $user = Manager::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError(__('api.User not found or not manager'));
        }
        // Start send otp
        $otp = new OTP('forget_password', $user->email);
        $otp->sendOtp();
        // End send otp
        return $this->sendResponse($user, __('api.OTP sent successfully'));
    }

    public function resetPassword(resetPasswordRequest $request)
    {

        $user = Manager::where('email', $request->email)->first();
        if (!$user) {
            return $this->sendError(__('api.User not found or not manager'));
        }

        $otp = new OTP('forget_password', $user->email);
        $otp = $otp->verifyOtp($request->otp);
        if (!$otp) {
            return $this->sendError(__('api.Incorrect OTP'));
        }
        $user->update(['password' => bcrypt($request->password)]);
        return $this->sendResponse([], __('api.Password updated successfully ,you can login now'));
    }

    public function login(LoginRequest $request): JsonResponse
    {
        // login with user_name or phone
        $user = SchoolMaster::query()
            ->where('user_name', $request->user_name)
            ->whereIn('guard', ['manager', 'student', 'teacher'])
            ->first();
        if (!$user) {
            return $this->sendError(__('api.Incorrect User name or password'));
        }
        if ($user->guard == 'student') {
            $user = Students::query()
                ->where('user_name', $request->user_name)
                ->first();

            if (!$user->schools?->hasPackage()) {
                return $this->sendError(__('api.School not have package'));
            }
        }

        if ($user->guard == 'teacher') {
            $user = Teacher::query()
                ->where('user_name', $request->user_name)
                ->first();
            if (!$user->schools?->hasPackage()) {
                return $this->sendError(__('api.School not have package'));
            }
        }
        if ($user->school_id) {
            //check if the school is deleted
            $school = $user->schools;
            if (!$school) {
                return $this->sendError(__('api.School not found'));
            }
        }
        if ($user->user_name == $request->user_name && Hash::check($request->password, $user->password)) {
            if ($user->is_active != 1) {
                return $this->sendError(__('api.This Account has been blocked, check our policies'));
            }

            // if env local
            if (env('APP_ENV') !== 'local') {
                $user->tokens()->delete();
            }
            if ($user->guard == 'manager') {
                $token = $user->createToken('manager', ['manager'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            } else if ($user->guard == 'student') {
                $token = $user->createToken('student', ['student'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            } else if ($user->guard == 'teacher') {
                $token = $user->createToken('teacher', ['teacher'], now()->addMinutes(config('sanctum.expiration')))->plainTextToken;
            }
            $user['token'] = $token;
            if ($user->guard == 'student') {
                return $this->sendResponse(new StudentsResource($user), __('api.login successfully'));
            }

            return $this->sendResponse(ManagerResource::make($user), __('api.login successfully'));
        }

        return $this->sendError(__('api.Incorrect User name or password'));
    }

    public function logout(): JsonResponse
    {
        if (Auth::check()) {
            Auth::user()?->tokens()->delete();
            return $this->sendResponse([], __('api.Logged out successfully'));
        }
        return $this->sendError(__('api.User not found'));
    }

    public function profile(): JsonResponse
    {
        $user = Auth::user();
        if (!$user) {
            return $this->sendError(__('api.User not found'));
        }
        return $this->sendResponse(ManagerResource::make($user), __('api.User data'));
    }

    public function updatePassword(RestPasswordRequest $request): JsonResponse
    {
        $user = Auth::guard('sanctum')?->user();
        if (!$user) {
            return $this->sendError(__('api.User not found'));
        }
        if (!Hash::check($request->old_password, $user->password)) {
            return $this->sendError(__('api.Old password is incorrect'));
        }
        $user->password = Hash::make($request->password);
        $user->save();
        return $this->sendResponse([], __('api.Password updated successfully'));
    }

    public function updateProfile(profileUpdateRequest $request)
    {
        $user = Auth::guard('sanctum')?->user();
        if (!$user) {
            return $this->sendError(__('api.User not found'));
        }
        $user->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'user_name'   => $request->user_name,
            'national_id' => $request->national_id,
            'gender'      => $request->gender,
            "dialing_code" => $request->dialing_code,
            "phone"       => $request->phone,
        ]);
        return $this->sendResponse(ManagerResource::make($user), __('api.Profile updated successfully'));
    }

    // list grades
    public function grades()
    {
        $grades = Grades::all()->load('Data');

        return $this->sendResponse(GradesResource::collection($grades), __('api.Grades list'));
    }

    public function countries()
    {
        $countries = Countries::all()->load('Data')->where('active', 1);

        return $this->sendResponse(CountriesResource::collection($countries), __('api.Countries list'));
    }

    public function moveImage($image_name, $objectId)
    {
        $imagePath = public_path('temp/' . $image_name);

        // Define the new directory structure
        $uploadsDir = public_path('uploads/schools/' . $objectId);

        // Create the directories if they do not exist
        if (!File::exists($uploadsDir)) {
            File::makeDirectory($uploadsDir, 0755, true);
        }

        // Move the image to the new directory
        $newImagePath = $uploadsDir . '/' . $image_name;
        File::move($imagePath, $newImagePath);

        $storedImagePath = 'uploads/schools/' . $objectId . '/' . $image_name;
        return $storedImagePath;
    }

    public function addSchoolsData(SchoolRequest $request)
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        if (!$user_id) {
            return $this->sendError(__('api.User not found'));
        }
        $school = Schools::where('user_id', $user_id)->first();
        if ($school) {
            return $this->sendError(__('api.School already exists'));
        }
        try {
            DB::beginTransaction();
            // add school
            $school = Schools::create([
                'user_id'    => $user_id,
                'type'       => $request->type,
                'gender'     => $request->gender,
                'country_id' => $request->country_id,
                'is_active'  => 1
            ]);
            // add school data
            $school->Data()->create([
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
            ]);
            // add school grades
            $school->grades()->sync(request('grades_id'));

            $user_id = Auth::guard('sanctum')?->user()?->id;
            // update users set school_id
            Manager::where('id', $user_id)->update([
                'school_id'      => $school->id,
            ]);
            // check if image in temp folder
            if ($request->image) {
                $image = $this->moveImage($request->image, $school->id);
                $school->update(['image' => $image]);
            }
            // add free package 14 day
            SchoolsPackage::create([
                'school_id' => $school->id,
                'free' => 1,
                'package_started_at' => now(),
                'package_ended_at' => now()->addDays(14),
            ]);

            DB::commit();
            return $this->sendResponse(new SchoolsResource($school), __('api.school added successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function updateSchoolsData(SchoolRequest $request)
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        if (!$user_id) {
            return $this->sendError(__('api.User not found'));
        }
        $school = Schools::where('user_id', $user_id)->first();
        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        try {
            DB::beginTransaction();
            if ($request->image) {
                $image = $this->moveImage($request->image, $school->id);
            } else {
                $image = $school->image;
            }
            // update school
            $school->update([
                'type' => $request->type,
                'gender' => $request->gender,
                'image' => $image,
                'country_id' => $request->country_id,
            ]);
            // update school data
            $school->Data()->update([
                'title' => $request->title,
                'description' => $request->description,
                'address' => $request->address,
            ]);
            // update school grades
            if (request()->has('grades_id')) {
                $school->grades()->sync(request('grades_id'));
            }

            DB::commit();
            return $this->sendResponse(new SchoolsResource($school), __('api.school updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError(__('api.failed to update school'));
        }
    }

    // list of packages
    public function packages()
    {
        $packages = Packages::all()->load('Data', 'Plans');

        return $this->sendResponse(PackagesResource::collection($packages), __('api.Packages list'));
    }

    public function assignPackage(SchoolPackagesRequest $request)
    {
        try {
            DB::beginTransaction();

            $user_id = Auth::guard('sanctum')?->user()?->id;

            // check school
            $school = Schools::where('user_id', $user_id)->first();
            if (!$school) {
                return $this->sendError(__('api.School not found'));
            }

            // check package
            $package = Packages::where('id', $request->package_id)->first();
            if (!$package) {
                return $this->sendError(__('api.Package not found'));
            }

            // check package plan price
            $packagePlan = PackagesPlan::where('package_id', $request->package_id)->first();
            if (!$packagePlan) {
                return $this->sendError(__('api.Package plan not found'));
            }

            $price = $packagePlan->price;

            // if ($price == 0) {
            //     return $this->sendError(__('api.Package plan price is 0'));
            // }

            // check if there is a coupon
            $checkCoupon = false;
            if ($request->coupon) {
                $coupon = Coupon::where('code', $request->coupon)
                    ->where('count', '>', 0)
                    ->where('from_date', '<=', now())
                    ->where('to_date', '>=', now())
                    ->first();
                if (!$coupon) {
                    return $this->sendError(__('api.Coupon not found'));
                }

                if ($coupon->type === 'percentage') {
                    $price = $packagePlan->price - ($packagePlan->price * $coupon->value / 100);
                } else {
                    $price = $packagePlan->price - $coupon->value;
                }
                $checkCoupon = true;
                $coupon->update(['count' => $coupon->count - 1]);
            }

            // check if Payments already exists delete.
            Payments::where('CustomerReference', $user_id)->where('status', 'Pending')->delete();

            // if price = 0 or < 0 after coupon
            if ($price <= 0) {
                $price = 0;
                // end active package
                SchoolsPackage::where('school_id', $school->id)->where('package_ended_at', '>', now())->update([
                    'package_ended_at' => now()
                ]);


                Payments::create([
                    'CustomerReference'  => $user_id,
                    'status'             => 'Paid',
                    'package_id'         => $request->package_id,
                    'package_plan_id'    => $packagePlan->id,
                    'school_id'          => $school->id,
                    'package_plan_price' => 0,
                    'package_started_at' => $packagePlan->start_at,
                    'package_ended_at'   => $packagePlan->end_at,
                    'coupon'             => $checkCoupon ? $request->coupon : null,
                    'original_price'     => $packagePlan->price,
                    'coupon_type'        => $checkCoupon ? $coupon->type : null,
                    'coupon_value'       => $checkCoupon ? $coupon->value : null,
                ]);
                // add new package
                SchoolsPackage::create([
                    'package_id'         => $request->package_id,
                    'package_plan_id'    => $packagePlan->id,
                    'school_id'          => $school->id,
                    'package_plan_price' => ($price * 15 / 100) + $price,
                    'package_started_at' => $packagePlan->start_at,
                    'package_ended_at'   => $packagePlan->end_at,
                ]);
                DB::commit();
                return $this->sendResponse([], __('api.Package added successfully'));
            }

            $pay = new MyFatoorah();
            $params = [
                'InvoiceValue' => ($price * 15 / 100) + $price,
                'NotificationOption' => 'LNK',
                'Language' => 'AR',
                'CustomerReference' => Auth::guard('sanctum')?->user()?->id,
                'Currency' => MyFatoorah::$currency,
                'CustomerName' => Auth::guard('sanctum')?->user()?->name,
                'DisplayCurrencyIso' => MyFatoorah::$currency,
                'MobileCountryCode' => Auth::guard('sanctum')?->user()?->dialing_code,
                'CustomerMobile' => Auth::guard('sanctum')?->user()?->phone,
                'CustomerEmail' => Auth::guard('sanctum')?->user()?->email,
                'CallBackUrl' => env('MYFATOORAH_SUCCESS_URL'),
                'ErrorUrl' => env('MYFATOORAH_ERROR_URL'),
            ];
            $payments = $pay::createInvoice($params);

            if ($payments->IsSuccess == false) {
                return $this->sendError(__('api.Payment failed'));
            }
            $pm = Payments::create([
                'InvoiceId' => $payments->Data->InvoiceId,
                'InvoiceURL' => $payments->Data->InvoiceURL,
                'CustomerReference' => Auth::guard('sanctum')?->user()?->id,
                'status' => 'Pending',
                'package_id' => $request->package_id,
                'package_plan_id' => $packagePlan->id,
                'school_id'          => $school->id,
                'package_plan_price' => ($price * 15 / 100) + $price,
                'package_started_at' => $packagePlan->start_at,
                'package_ended_at' => $packagePlan->end_at,
                'coupon' => $checkCoupon ? $request->coupon : null,
                'original_price'     => $packagePlan->price,
                'coupon_type' => $checkCoupon ? $coupon->type : null,
                'coupon_value' => $checkCoupon ? $coupon->value : null,
            ]);

            $schoolPackage = [
                'id' => $pm->id,
                'InvoiceId' => $payments->Data->InvoiceId,
                'InvoiceURL' => $payments->Data->InvoiceURL,
                'CustomerReference' => Auth::guard('sanctum')?->user()?->id
            ];

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }

        return $this->sendResponse($schoolPackage, __('api.Package added successfully'));
    }

    public function checkPayment()
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        if (!$user_id) {
            return $this->sendError(__('api.User not found'));
        }
        $paymentX = Payments::where('CustomerReference', $user_id)->where('status', 'Pending')->latest()->first();
        if ($paymentX) {
            $pay = new MyFatoorah();
            $payment = $pay::status($paymentX->InvoiceId);
            if ($payment->IsSuccess == true && $payment->Data->InvoiceStatus === 'Paid') {

                Payments::where('id', $paymentX->id)->where('InvoiceId', $payment->Data->InvoiceId)->update(['status' => 'Paid']);

                SchoolsPackage::where('school_id', $paymentX->school_id)->where('package_ended_at', '>', now())->update([
                    'package_ended_at' => now()
                ]);

                SchoolsPackage::create([
                    'package_id' => $paymentX->package_id,
                    'package_plan_id' => $paymentX->package_plan_id,
                    'school_id' => $paymentX->school_id,
                    'package_plan_price' => $paymentX->package_plan_price,
                    'package_started_at' => $paymentX->package_started_at,
                    'package_ended_at' => $paymentX->package_ended_at,
                    'coupon' => $paymentX->coupon,
                ]);
                // decrease coupon count
                if ($paymentX->coupon) {
                    Coupon::where('code', $paymentX->coupon)->decrement('count');
                }

                // delete extra data from students, teachers,prizes
                $packagePer = PackagesPermission::where('package_id', $paymentX->package_id)->first();
                $users = Users::where('school_id', $paymentX->school_id)->get();
                // students
                $studentsCount = $users->where('guard', 'student')->count();
                if ($studentsCount > $packagePer->students_count) {
                    $students = $users->where('guard', 'student')->take($studentsCount - $packagePer->students_count);
                    foreach ($students as $student) {
                        $student->delete();
                    }
                }
                // teachers
                $teachersCount = $users->where('guard', 'teacher')->count();
                if ($teachersCount > $packagePer->teachers_count) {
                    $teachers = $users->where('guard', 'teacher')->take($teachersCount - $packagePer->teachers_count);
                    foreach ($teachers as $teacher) {
                        $teacher->delete();
                    }
                }
                // prizes
                $prizesCount = Prizes::where('school_id', $paymentX->school_id)->count();
                if ($prizesCount > $packagePer->prizes) {
                    $prizes = Prizes::where('school_id', $paymentX->school_id)->take($prizesCount - $packagePer->prizes);
                    foreach ($prizes as $prize) {
                        $prize->delete();
                    }
                }

                return $this->sendResponse([], __('api.Payment Done'));
            }
            return $this->sendError(__('api.Payment not found or not paid'));
        }
        return $this->sendError(__('api.Payment not found or not paid'));
    }

    public function checkCoupon(SchoolCouponCheck $request)
    {
        $coupon = Coupon::where('code', $request->coupon)
            ->where('count', '>', 0)
            ->where('from_date', '<=', now())
            ->where('to_date', '>=', now())
            ->first();
        if (!$coupon) {
            return $this->sendError(__('api.Coupon not found'));
        }
        $coupon = [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'value' => $coupon->value
        ];
        return $this->sendResponse($coupon, __('api.Coupon found'));
    }

    public function MySchool()
    {
        $user_id = Auth::guard('sanctum')?->user()?->id;
        if (!$user_id) {
            return $this->sendError(__('api.User not found'));
        }
        $school = Schools::where('user_id', $user_id)->with(['Data', 'grades', 'packages'])->first();
        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        return $this->sendResponse(new SchoolsResource($school), __('api.School data'));
    }

    public function rows()
    {
        $rows = Rows::all()->load('Data');
        return $this->sendResponse(RowsResource::collection($rows), __('api.Rows list'));
    }

    // List School Grades
    public function schoolGrades()
    {
        $school_id = Auth::guard('sanctum')?->user()?->school_id;
        $school = Schools::where('id', $school_id)->first();

        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        $grades = $school->grades()->pluck('grade_id');

        $grades = Grades::whereIn('id', $grades)->get()->load('Data');

        return $this->sendResponse(GradesResource::collection($grades), __('api.Grades list'));
    }

    public function classes()
    {
        $classes = Classes::all()->load('Data');
        return $this->sendResponse(ClassesResource::collection($classes), __('api.Classes list'));
    }

    public function editStoreData()
    {
        $schoolId = Utility::school_id();
        $school = Schools::where('id', $schoolId)->with('Data')->first();
        return $this->sendResponse(new StoreResource($school), __('api.Store data'));
    }

    public function updateStoreData(UpdateStoreRequest $request)
    {
        $schoolId = Utility::school_id();
        $school = Schools::where('id', $schoolId)->first();
        if (!$school) {
            return $this->sendError(__('api.School not found'));
        }
        try {
            DB::beginTransaction();
            // update school data
            $school->update([
                'store_activation' => $request->store_activation,
            ]);
            $school->Data()->update([
                'store_name' => $request->store_name,
                'store_message' => $request->store_message,
            ]);
            DB::commit();
            return $this->sendResponse(new StoreResource($school), __('api.Store data updated successfully'));
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->sendError($e->getMessage());
        }
    }

    public function getCountry(): JsonResponse
    {
        try {
            $ip = request()?->ip(); // Get the user's IP address
            $response = file_get_contents("http://ipinfo.io/{$ip}/json");
            $details = json_decode($response);
            $country = $details->country;
            if (!in_array($country, ['AE', 'SA', 'EG'])) {
                $country = 'SA';
            }
            return $this->sendResponse($country, __('api.Country'));
        } catch (\Exception $e) {
            return $this->sendResponse('SA', __('api.Country'));
        }
    }
}
