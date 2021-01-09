<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ConfirmNewEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public  $data;

    /**
     * ConfirmNewEmail constructor.
     * @param $data
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
     * Create a new message instance.
     *
     * @return void
     */


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.confirmnewemail');
    }
}
