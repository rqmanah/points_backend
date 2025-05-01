<?php

namespace App\Bll;


use Twilio\Rest\Client;

class Twilio
{
    protected $client;
    protected $twilio_number;
    protected $phone_number;

    public function __construct($phone_number)
    {
        $account_sid = env('TWILIO_SID');
        $auth_token = env('TWILIO_TOKEN');
        $twilio_number = env('TWILIO_FROM');
        
        $this->client = new Client($account_sid, $auth_token);
        $this->twilio_number = $twilio_number;
        $this->phone_number = $phone_number;
    }

    public function sendMessage($message): void
    {
        $this->client->messages->create($this->phone_number,
            ['from' => $this->twilio_number, 'body' => $message]);
    }

}


