<?php

namespace App\Helpers;

use Twilio\Rest\Client;
use Twilio\Exceptions\TwilioException;

class TwilioHelper 
{

    private $account_sid;
    private $auth_token;
    private $twilio_number;

    public function __construct() 
    {
        $this->account_sid = getenv("TWILIO_SID");
        $this->auth_token = getenv("TWILIO_AUTH_TOKEN");
        $this->twilio_number = getenv("TWILIO_NUMBER");
    }

    /**
     * Sends sms to user
     * 
     * @param string $message Body of sms
     * @param array $recipient string of phone number of recepient
     */
    public function sendMessage($recipient, $message)
    {
        $client = new Client($this->account_sid, $this->auth_token);
        $client->messages->create($recipient, 
            ['from' => $this->twilio_number, 'body' => $message] 
        );

    }

    /**
     * Validate phone number
     * 
     * @param $phone string
     */
    public function validatePhoneNumber($phone)
    {
        try {
            $client = new Client($this->account_sid, $this->auth_token);
            $phone_number = $client->lookups->v1->phoneNumbers($phone)
                            ->fetch(["countryCode" => "US"]);
            return [
                "status" => "valid",
                "phone" => $phone_number->phoneNumber
            ];
        } catch (TwilioException $e) {
            return [
                "status" => "invalid",
                "error" => $e
            ];
        }

    }

}