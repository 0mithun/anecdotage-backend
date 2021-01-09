<?php

namespace App\Mail;


use App\Models\Thread;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TreadWasReportedEmail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;
    protected $type;
    protected $thread;
    protected $reason;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, $type,  $reason )
    {
        $this->thread = $thread;
        $this->type = $type;
        $this->reason = $reason;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.thread-reported')
                ->subject($this->type)
                ->with(['url'=> url($this->thread->path()), 'type'=> $this->type, 'reason'=> $this->reason]);
    }
}
