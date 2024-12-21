<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $verificationUrl;

    /**
     * Create a new message instance.
     *
     * @param string $name
     * @param string $verificationUrl
     */
    public function __construct($name, $verificationUrl)
    {
        $this->name = $name;
        $this->verificationUrl = $verificationUrl;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verify_email')
                    ->subject('Verify Your Email Address');
    }
}