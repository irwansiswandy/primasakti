<p>
	Kepada<br>
	Yth. {{ $user->firstname.' '.$user->lastname }}
</p>

<p>
	Terima kasih telah menjadi pelanggan Primasakti.
</p>

<p>
	Staff kami baru saja memasukkan data anda ke database Primasakti, sehingga mulai sekarang anda telah memiliki user account Primasakti yang bisa anda gunakan untuk : ONLINE ORDER, ORDER TRACKING, DLL.
</p>

<p>
	User account Primasakti anda adalah sebagai berikut :<br>
	E-mail : <b>{{ $user->email }}</b><br>
	Password : <b>{{ $user->password }}</b>
</p>

<p>
	Password yang kami berikan adalah password default, yang bisa anda gunakan untuk pertama kali melakukan login.<br>
	Untuk mengganti password default anda, bisa dilakukan melalui pilihan menu "Ganti Password" setelah login.
</p>

<p>
	Selamat ! Semoga kami bisa terus melayani anda,<br>
	Terima kasih... 
</p>

<p>
	<b>Catatan :</b><br>
	*) E-mail ini dikirim dari EMAIL-SENDER PRIMASAKTI, harap jangan me-reply ke e-mail ini.
</p>