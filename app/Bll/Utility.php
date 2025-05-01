<?php

namespace App\Bll;

use App\Models\Users;
use App\Enums\Session;
use App\Models\Language;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use App\Modules\Prizes\Models\Prizes;
use App\Modules\Auth\Models\Schools\Rows;
use App\Modules\Auth\Models\Grades\Grades;
use App\Modules\Auth\Models\Classes\Classes;
use App\Modules\Auth\Models\Schools\Schools;
use App\Modules\Auth\Models\Countries\Countries;

class Utility
{

    public static function school_id()
    {
        if (Auth::guard('sanctum')?->user()?->school_id != null) {
            return Auth::guard('sanctum')?->user()?->school_id;
        }
        return null;
    }

    private static function GetLangObject()
    {
        $firstLang = Language::where('code', App::getLocale())->first();
        if ($firstLang == null)
            $firstLang = Language::first();
        if ($firstLang != null) {
            session(Session::SCHOOL_LANG->value, $firstLang);
            return $firstLang;
        }
        $firstLang = Language::create(["code" => App::getLocale(), "title" => App::getLocale()]);
        session(Session::SCHOOL_LANG->value, $firstLang);

        return $firstLang;
    }

    public static function lang_id()
    {

        if (App::getLocale() != Session::getLangCode()) {
            session()->remove(Session::SCHOOL_LANG->value);
            return Utility::GetLangObject()->id;
        }
        if ((session()->input(Session::SCHOOL_LANG->value)))
            return session()->input(Session::SCHOOL_LANG->value)->id;

        return Utility::GetLangObject()->id ?? 1;
    }

    public static function get_dialing_code()
    {
        return +966;
    }

    public function removeZeroFomphone($phone)
    {
        if (substr($phone, 0, 1) == '0') {
            return substr($phone, 1);
        }
        return $phone;
    }

    public static function checkTeacherCount()
    {
        if (Auth::guard('sanctum')?->user()?->school_id != null && Auth::guard('sanctum')?->user()?->school->schoolPackage != null) {
            $school_id = Auth::guard('sanctum')?->user()?->school_id;
            $package = Auth::guard('sanctum')?->user()?->school->schoolPackage;
            if ($package->free === 1) {
                return true;
            }
            $teacherCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package->Permissions->teachers_count;
            $schoolTeachers = Users::where('school_id', $school_id)->where('guard', 'teacher')->count();
            return $schoolTeachers < $teacherCount;
        }
        return false;
    }

    public static function checkStudentCount()
    {
        if (Auth::guard('sanctum')?->user()?->school_id != null && Auth::guard('sanctum')?->user()?->school->schoolPackage != null) {
            $school_id = Auth::guard('sanctum')?->user()?->school_id;
            $package = Auth::guard('sanctum')?->user()?->school->schoolPackage;
            if ($package->free === 1) {
                return true;
            }
            $studentCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package->Permissions->students_count;
            $schoolStudents = Users::where('school_id', $school_id)->where('guard', 'student')->count();
            return $schoolStudents < $studentCount;
        }
        return false;
    }

    public static function checkPrizesCount()
    {
        if (Auth::guard('sanctum')?->user()?->school_id != null && Auth::guard('sanctum')?->user()?->school->schoolPackage != null) {
            $school_id = Auth::guard('sanctum')?->user()?->school_id;
            $package = Auth::guard('sanctum')?->user()?->school->schoolPackage;
            if ($package->free === 1) {
                return true;
            }
            $prizesCount = Auth::guard('sanctum')?->user()?->school->schoolPackage->package->Permissions->prizes_count;
            $schoolPrizes = Prizes::where('school_id', $school_id)->count();
            return $schoolPrizes < $prizesCount;
        }
        return false;
    }

    // genereate user name using incoming name although it arabic or english
    /**
     * @param string $name
     * @param string $row_id
     * @param string $class_id
     * @param string $phone
     */
    public static function generateUserName($garde_id = null, $row_id = null, $class_id = null)
    {
        $random_number = random_int(1000, 9999);
        $country_code  = null;
        $school_title  = null;
        
        $school_id     = self::school_id();
        $schools       = Schools::where('id', $school_id)->first();

        if ($schools) {
            $school_title = $schools?->Data?->first()?->title;
            $country_id   = $schools->country_id;
            $country_code = Countries::where('id', $country_id)->first()?->code;
        }

        if($row_id){
            $row_id       = Rows::where('id', $row_id)->first();
            $row_id       = $row_id?->Data?->first()?->title;
            $row_id       = explode(' ', $row_id)[0];
            $row_id       = Str::ascii($row_id);
        } else {
            $row_id = random_int(1, 9);
        }

        if($class_id){
            $class_id       = Classes::where('id', $class_id)->first();
            $class_id       = $class_id?->Data?->first()?->title;
            $class_id       = explode(' ', $class_id)[0];
            $class_id       = Str::ascii($class_id);
        } else {
            $class_id = random_int(1, 9);
        }
      
        $school_title       = explode(' ', $school_title)[0];
        $school_title       = Str::ascii($school_title);
        $school_title       = preg_replace('/[^a-zA-Z0-9]/', '', $school_title);
        $username           = strtolower($school_title);
        $username           =  $username . $country_code . $row_id . $class_id . $random_number;

        while (Users::where('user_name', $username)->exists()) {
            $username .= random_int(0, 9);
        }
        return $username;
    }

    public static function generateUserNameManager($name, $number)
    {
        // get last 4 digits from phone number
        $number             = substr($number, -4);
        $name               = explode(' ', $name)[0];
        $name               = Str::ascii($name);
        $name               = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        $username           = strtolower($name);
        $username           = $username . $number;
        while (Users::where('user_name', $username)->exists()) {
            $username .= random_int(0, 9);
        }
        return $username;
    }

    public static function generateUserNameTeacher($name)
    {
        // get last 4 digits from phone number
        $school_id          = self::school_id();
        if ($school_id) {
            $school_id = Schools::where('id', $school_id)->first();
            $country_id = $school_id?->country_id;
            $country_code = Countries::where('id', $country_id)->first()?->code;
        }
        $number             = random_int(1000, 9999);
        $name               = explode(' ', $name)[0];
        $name               = Str::ascii($name);
        $name               = preg_replace('/[^a-zA-Z0-9]/', '', $name);
        $username           = strtolower($name);
        $username           = $username . $country_code . $number;
        while (Users::where('user_name', $username)->exists()) {
            $username .= random_int(0, 9);
        }
        return $username;
    }
}
