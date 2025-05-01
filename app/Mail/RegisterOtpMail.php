<?php

namespace App\Mail;

use Illuminate\Support\Facades\Log;
use SendGrid;
use SendGrid\Mail\Mail as SendGridMail;

class RegisterOtpMail
{
    protected $sendgrid;

    public function __construct()
    {
        $this->sendgrid = new SendGrid(env('SENDGRID_API_KEY'));
    }

    public function send($to, $subject, $textFallback, $htmlContent)
    {
        $email = new SendGridMail();
        $email->setFrom('no-reply@school-points.com', 'School Points');
        $email->setSubject($subject);
        $email->addTo($to);

        // Add plain text and HTML content
        $email->addContent("text/plain", $textFallback);
        $email->addContent("text/html", $htmlContent);

        try {
            $response = $this->sendgrid->send($email);

            // Log the response for debugging
            Log::info('SendGrid response: ' . $response->statusCode());
            Log::info($response->body());

            return $response;
        } catch (\Exception $e) {
            Log::error('Caught exception while sending OTP email: ' . $e->getMessage());
            return null;
        }
    }
}
