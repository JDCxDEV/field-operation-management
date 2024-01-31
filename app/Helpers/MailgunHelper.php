<?php

namespace App\Helpers;

use Mailgun\Mailgun;

class MailgunHelper
{
    private $msgClient;
    private $domain;

    public function __construct() 
    {
        $this->mgClient = Mailgun::create(
            env('MAILGUN_SECRET') // Mailgun API Key
        );
        $this->domain = env('MAILGUN_DOMAIN');

    }

    public function sendMail($subject = "", $email, $name = "", $link = "",  $template = "") 
    {
        $params = array(
            'from' => env('MAIL_FROM_ADDRESS'),
            'to' => $email,
            'subject' => $subject,
            'template' => $template,
            'v:name' => $name,
            'v:link' => $link
        );

        $this->mgClient->messages()->send($this->domain, $params);
    }

}