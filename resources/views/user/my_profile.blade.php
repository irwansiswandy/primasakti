<head>
	<link href="/libraries/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
	<link href="/css/content-footer.css" rel="stylesheet">
</head>

<body>
	<br>
	<div id="profile-controller">
		<div class="container">
			<legend><b>Account Details</b></legend>
			<p>
				Full Name :<br>
				<b>@{{ profile.firstname + ' ' + profile.lastname}}</b>
			</p>
			<p>
				Registered E-mail Address :<br>
				<b>@{{ profile.email }}</b>
			</p>
			<p>
				Account Status :<br>
				<b>@{{ profile.verified }}</b>
			</p>
			<br>
			<legend><b>Personal Details</b></legend>
			<p>
				Address :<br>
				<b>@{{ profile.address }}</b>
			</p>
			<p>
				City :<br>
				<b>@{{ profile.city }}</b>
			</p>
			<p>
				State / Province :<br>
				<b>@{{ profile.state }}</b>
			</p>
			<p>
				Zip / Postcode :<br>
				<b>@{{ profile.postcode }}</b>
			</p>
			<p>
				Country :<br>
				<b>@{{ profile.country }}</b>
			</p>
			<br>
			<legend><b>Contact Details</b></legend>
			<p>
				Phone Number :<br>
				<b>@{{ profile.phone }}</b>
			</p>
			<p>
				Cellphone Number :<br>
				<b>@{{ profile.cellphone }}</b>
			</p>
		</div>
	</div>

	<script src="/js/vue.js"></script>
	<script src="/js/vue-resource.js"></script>
	<script>
		new Vue({
			el: '#profile-controller',
			data: {
				profile: []
			},
			methods: {
				fetchProfile: function() {
					this.$http.get(document.URL + '/fetchProfile', function(data) {
						this.$set('profile', data)
					})
				}
			},
			ready: function() {
				this.fetchProfile()
			}
		});
	</script>

	<!-- START: MYFOOTER -->
	<hr>
	<div class="container">
	  <div id="web-footer">
	    @include('includes/myfooter')
	  </div>
	</div>
	<!-- END: MYFOOTER -->
</body>