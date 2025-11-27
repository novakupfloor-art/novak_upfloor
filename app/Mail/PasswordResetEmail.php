<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class PasswordResetEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $resetToken;
    public $userEmail;

    public function __construct($userEmail, $resetToken)
    {
        $this->userEmail = $userEmail;
        $this->resetToken = $resetToken;
    }

    public function build()
    {
        $resetUrl = config('app.url') . '/reset-password?token=' . $this->resetToken . '&email=' . $this->userEmail;

        return $this->subject('Reset Password - Waisaka Property')
                    ->view('emails.password-reset-link')
                    ->with([
                        'resetUrl' => $resetUrl,
                        'token' => $this->resetToken
                    ]);
    }
}
