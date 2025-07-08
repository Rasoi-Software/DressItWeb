<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailVerificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function build()
    {
       return $this->subject($this->data['subject'] ?? 'Verify Your Email with OTP')
                    ->view('emails.email-verification')
                    ->with('data', $this->data);
    }
}
