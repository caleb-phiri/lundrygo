<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GenericNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $message;
    public $data;

    public function __construct(string $title, string $message, array $data = [])
    {
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject($this->title)
                    ->markdown('emails.generic')
                    ->with([
                        'title' => $this->title,
                        'message' => $this->message,
                        'data' => $this->data,
                    ]);
    }
}