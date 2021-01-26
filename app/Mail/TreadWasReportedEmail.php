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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Thread $thread, $type)
    {
        $this->thread = $thread;
        $this->type = $type;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $appUrl = config('app.client_url', config('app.url'));
        $thread_path =  str_replace('/api', '', $appUrl) . '/threads/' . $this->thread->slug;

        $reason  = 'Your item <a href="' . $thread_path . '">here</a> has been flagged as "' . $this->type . '". It is under review & may be hidden from other people.';
        return $this->markdown('emails.thread-reported')
            ->subject($this->type)
            ->with(['url' => $thread_path, 'type' => $this->type, 'reason' => $reason]);
    }
}
