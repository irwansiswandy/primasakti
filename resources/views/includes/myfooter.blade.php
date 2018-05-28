<div class="hidden-print">
	@if (Auth::check() == false || Auth::user()->is_user())
		<p id="app-footer-navigation">
			<a href="#">Tentang Kami</a>
			<a href="{{ URL::action('PagesController@email_form') }}">Hubungi Kami</a>
			<a href="{{ URL::action('PagesController@location') }}">Lokasi Kami</a>
			<a href="#">Syarat dan Kondisi</a>
		</p>
	@endif
	<p>
	  	<b>Copyright &copy 2012 - {{ Date('Y') }} | All Rights Reserved | Primasakti - Digital Copy & Print Shop</b><br>
	  	Created and developed by <b>irwan_up</b> | +6281294060954 | irwansiswandymks@gmail.com
	</p>
</div>