<?php

namespace App\Notifications\Users\Auth;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\HtmlString;

class VerificationCode extends Notification
{
    use Queueable;

    public $code;
    public $url;
    public $expiration;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($code, $url, $expiration)
    {
        $this->code = $code;
        $this->url = $url;
        $this->expiration = $expiration;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
                    ->subject('Login Verification Code')
                    ->line(new HtmlString("Your verification code is <strong>{$this->code}</strong>. Valid for <strong>{$this->expiration}</strong> minutes"))
                    ->action("Verify", url($this->url))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
