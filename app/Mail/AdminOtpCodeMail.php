<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminOtpCodeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otpCode;

    public function __construct($otpCode)
    {
        $this->otpCode = $otpCode;
    }

    public function build()
    {
        return $this->subject('Tu código de seguridad de acceso (OTP)')
                    ->view('emails.admin_otp_code')
                    ->with([
                        'otpCode' => $this->otpCode,
                    ]);
    }
}
