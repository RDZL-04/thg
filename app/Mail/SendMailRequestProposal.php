<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendMailRequestProposal extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        $this->name = $data['name'];
        $this->email = $data['email'];
        $this->phone = $data['phone'];
        $this->hotel_name = $data['hotel_name'];
        $this->hall_name = $data['hall_name'];
        $this->capacity = $data['capacity'];
        $this->proposed_dt = $data['proposed_dt'];
        $this->additional_request = $data['additional_request'];
        $this->category_name = $data['category_name'];
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
            ->subject('Request '.$this->category_name.' Proposal')
            ->view('email.request-proposal')
            ->with( [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'hotel_name' => $this->hotel_name,
                'hall_name' => $this->hall_name,
                'capacity' => $this->capacity,
                'proposed_dt' => $this->proposed_dt,
                'additional_request' => $this->additional_request,
                'category_name' => $this->category_name
            ]);
           
    }
}
