<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use Crypt;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    private $user;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email)
    {
        $this->email = $email;
    }
   

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $encryptedEmail = Crypt::encryptString($this->email);
        $link = ('https://www.transhotelgroup.com/activation?token='.$encryptedEmail);
        // return $this->from('noreply@yTHG.com')
        return $this->from('info@transhotelgroup.com')
            ->with('link', $link)
            ->view('email.verify');
           
    }
}
