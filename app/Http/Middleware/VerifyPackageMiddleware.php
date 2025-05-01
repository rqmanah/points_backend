<?php

namespace App\Http\Middleware;

use App\Bll\MyFatoorah;
use App\Models\Payments;
use App\Models\Users;
use App\Modules\Auth\Models\Packages\Coupon;
use App\Modules\Auth\Models\Packages\PackagesPermission;
use App\Modules\Auth\Models\Schools\SchoolsPackage;
use App\Modules\Prizes\Models\Prizes;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class VerifyPackageMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response) $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::guard('sanctum')?->user()?->hasSchool()) {

            $user_id = Auth::guard('sanctum')?->user()?->id;
            $paymentX = Payments::where('CustomerReference', $user_id)->where('status', 'Pending')->first();
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

                }
            }

        }


        // check is authenticated and phone is verified
        if (
            Auth::check() &&
            (
                !Auth::guard('sanctum')?->user()?->hasSchool()
                || !Auth::guard('sanctum')?->user()?->school?->hasPackage()
                || Auth::guard('sanctum')?->user()?->school?->schoolPackage()?->latest()->first()?->is_active == 0
            )
        ) {
            $response = [
                'status' => 'error',
                'message' => 'This Account has not been buy any package yet',
                'result' => null
            ];

            return response()->json($response, 401);
        }
        return $next($request);
    }
}
