<?php

namespace App\Bll;

use App\Mail\RegisterOtpMail;
use \App\Models\Otp as OTPModel;
use Illuminate\Support\Facades\Log;

class OTP
{
    protected $email;
    protected $action;
    protected $user_name;
    private int $maxAttempts = 5;

    /**
     * OTP constructor.
     * @param $action
     * @param $data
     * @param string $type
     * @param string $method
     */

    public function __construct($action, $email, $user_name = null)
    {
        $this->action = $action;
        $this->email  = $email;
        $this->user_name = $user_name;
    }

    public function sendOtp()
    {
        if (!$this->checkMaxAttempts()) {
            return false;
        }
        $otp = $this->generateOtp();
        Log::info('OTP sendOtp called');
        $this->saveOtp($otp);
        return true;
    }

    public function verifyOtp($userOtp)
    {

        $otp = OTPModel::where('email', $this->email)
            ->where('otp', $userOtp)
            ->where('action', $this->action)
            ->where('expire_at', '>', now())
            ->orderBy('id', 'desc')->first();
        if ($otp) {
            OTPModel::where('email', $this->email)
                ->where('otp', $userOtp)
                ->where('action', $this->action)
                ->delete();
            return true;
        }
        return false;
    }

    public function resendOtp()
    {
        if (!$this->checkMaxAttempts()) {
            return false;
        }
        $otp = $this->generateOtp();
        $this->saveOtp($otp);
        return true;
    }

    public function checkVrifyedData()
    {
        $otp = OTPModel::where('email', $this->email)
            ->orderBy('id', 'desc')
            ->where('action', $this->action)
            ->withTrashed()
            ->first();
        return $otp && $otp->deleted_at?->addMinutes(10) > now();
    }


    private function generateOtp(): int
    {
        $code = random_int(1000, 99999);

        // Decide subject and text fallback based on $this->action
        if ($this->action === "forget_password") {
            $subject      = "استعادة كلمة المرور";
            $textFallback = "مرحبًا لإعادة تعيين كلمة المرور، يُرجى استخدام الكود التالي للتفعيل: $code. "
                . "تنتهي صلاحية الكود بعد 10 دقائق.";
        } else {
            $subject      = "إكمال عملية التسجيل";
            $textFallback = "مرحبًا $this->user_name ، لإكمال عملية التسجيل، يُرجى استخدام الكود التالي للتفعيل: $code. "
                . "تنتهي صلاحية الكود بعد 10 دقائق.";
        }

        // Build a styled HTML version with inline <style>
        // Notice dir="rtl" and lang="ar" for proper Arabic rendering.
        $htmlContent = <<<HTML
<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>$subject</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background: #f9fafb;
            font-family: "Tajawal", Arial, sans-serif;
        }
        .container {
            margin: 30px auto;
            max-width: 600px;
            background: #fff;
            border-radius: 6px;
            padding: 25px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            direction: rtl;
        }
        .logo {
            text-align: center;
            margin-bottom: 15px;
        }
        .heading {
            font-size: 22px;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }
        .text {
            font-size: 16px;
            line-height: 1.7;
            color: #555;
            margin-bottom: 20px;
        }
        .otp {
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            color: #444;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 14px;
            margin-top: 30px;
        }
        /* Responsive for smaller screens */
        @media (max-width: 600px) {
            .container {
                margin: 15px;
                padding: 15px;
            }
            .heading {
                font-size: 20px;
            }
            .otp {
                font-size: 22px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img
                src="https://sa.app.school-points.com/assets/sidebar-logo-BneU2QYm.png"
                alt="School Points"
                width="120">
        </div>
        <div class="heading">$subject</div>

        <div class="text">
            $textFallback
        </div>

        <div class="otp">$code</div>

        <div class="footer">
            &copy; 2025 جميع الحقوق محفوظة
        </div>
    </div>
</body>
</html>
HTML;

        // Send the email (see next step to update your RegisterOtpMail::send method)
        $sendgrid = new RegisterOtpMail();
        $sendgrid->send($this->email, $subject, $textFallback, $htmlContent);

        return $code;
    }


    private function checkMaxAttempts()
    {
        // get attempts last 24 hours
        $attempts = OTPModel::where('email', $this->email)
            ->where('created_at', '>', now()->subDay())
            ->withTrashed()
            ->count();

        return $attempts < $this->maxAttempts;
    }

    private function saveOtp($otp)
    {
        OTPModel::create([
            'email' => $this->email,
            'action' => $this->action,
            'otp' => $otp,
            'expire_at' => now()->addMinutes(5)
        ]);
    }
}
