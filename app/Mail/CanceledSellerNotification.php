<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class CanceledSellerNotification extends Mailable
{
    use Queueable, SerializesModels;

	private $_params = [];

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($_params)
    {
        $this->_params = $_params;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
    	return $this->subject('商品がキャンセルされました。')
	                ->with('params', $this->_params)
		            ->view('emails.canceledSeller');
    }
}
