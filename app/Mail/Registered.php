<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class Registered extends Mailable
{
    use Queueable, SerializesModels;

    public $userId;
    public $username;
    public $token;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($userId, $username, $token)
    {
        $this->userId = $userId;
        $this->username = $username;
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('email verification')
            ->from('admin@totp-auth.com', 'admin')
            ->view('mail.registered');
    }
}
