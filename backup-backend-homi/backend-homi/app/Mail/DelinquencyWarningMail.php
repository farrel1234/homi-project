<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class DelinquencyWarningMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $title;
    public $msg;
    public $data;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $title, string $msg, array $data = [])
    {
        $this->name = $name;
        $this->title = $title;
        $this->msg = $msg;
        $this->data = $data;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject($this->title)
                    ->view('emails.delinquency_warning');
    }
}
