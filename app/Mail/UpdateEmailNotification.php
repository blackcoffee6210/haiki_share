<?php

namespace App\Mail;

use App\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateEmailNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $token;

	public function __construct(User $user, $token)
	{
		$this->user  = $user;
		$this->token = $token;
	}

	public function build()
	{
		$url = "https://haiki-share.net/email-confirmation?token={$this->token}";

		return $this->subject('Eメールアドレス更新の確認')
					->view('emails.updateEmail')
					->with([
						'name' => $this->user->name,
						'url'  => $url,
					]);
	}
}
