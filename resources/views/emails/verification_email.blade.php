<p>
	Kepada<br>
	Yth. {{ $user->firstname.' '.$user->lastname }}
</p>

<p>
	Terima kasih telah mendaftar sebagai user di Primasakti.<br>
	Untuk bisa login, anda wajib melakukan verifikasi e-mail terlebih dahulu dengan meng-klik link verifikasi yang kami kirim dibawah ini.
</p>

<p>
	<a href="{{ URL::action('MyAuthController@register_verify_email', [$user->verification_token, $user->id]) }}">
		Verify Me
	</a>
</p>

<p>
	<b>Catatan :</b><br>
	*) E-mail ini dikirim dari EMAIL-SENDER PRIMASAKTI, harap jangan me-reply ke e-mail ini.
</p>