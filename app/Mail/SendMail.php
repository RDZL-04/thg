<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->fullname = $data['full_name'];
        $this->otp = $data['otp'];
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        // return $this->from('noreply@yTHG.com')
        return $this->from('info@transhotelgroup.com')
            ->view('email')
            ->with(
                [
                    'nama' => $this->fullname,
                    'otp' => $this->otp
                ]);
    }
}
