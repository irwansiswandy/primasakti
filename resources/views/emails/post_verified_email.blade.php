<p>
	INFO	
</p>

<p>
	A newly registered user account with details below has just been verified.<br>
	<br>
	First Name : <b>{{ $user->firstname }}</b><br>
	Last Name : <b>{{ $user->lastname }}</b><br>
	E-mail Address : <b>{{ $user->email }}</b><br>
	<br>
	*) If this user is a regular user, you can just ignore this e-mail.<br>
	*) If this user is your staff, you can change the user's status to staff-level by clicking this link <a href="{{ URL::action('MyAuthController@verify_as_staff', [$user->id, $user->firstname, $user->lastname]) }}">Change Level to STAFF</a>
</p>