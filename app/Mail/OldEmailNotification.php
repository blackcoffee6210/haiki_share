<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class OldEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

	public $name;

    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Eメールアドレス変更通知')
	                ->view('emails.oldEmail')
	                ->with(['name' => $this->name,]);
    }
}
