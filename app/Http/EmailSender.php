<?php

namespace App\Http;

use Mail;
use App\User;

class EmailSender
{
	private function create($email_type, $to, $subject)
	{
		// FIND USER BY E-MAIL ADDRESS
		$user = User::where('email', $to)->first();

		// SEND E-MAIL
		Mail::send($email_type, ['user' => $user], function($email) use($to, $user, $subject) {
			$email->from('primasakti1.sby@gmail.com', '[E-MAIL SENDER] Primasakti - Digital Copy & Print Shop');
			$email->to($to, $user->firstname.' '.$user->lastname)
				  ->subject($subject);
		});
	}

	public function new_user_verification($to)
	{
		return $this->create('emails.verification_email', $to, 'Verifikasi E-mail User Baru');
	}

	public function new_user_registered_at_workshop($to)
	{
		return $this->create('emails.new_user_registered_at_workshop', $to, 'INFO : User Account Primasakti');
	}
}