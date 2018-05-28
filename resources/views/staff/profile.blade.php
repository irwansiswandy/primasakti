@extends('app')

@section('content')

<div class="container-fluid">
	<div id="profileController">
		<div class="row">
			<div class="col-sm-3">
				<div class="text-center">
					<img src="{{ url('images/no_photo.png') }}" class="img-circle">
				</div>
			</div>
			<div class="col-sm-7">
				<h4>
					<b>Account Info</b>
				</h4>
				<table width="100%">
					<tr>
						<td width="25%">
							<p>Nama Depan</p>
							<p>Nama Belakang</p>
							<p>Alamat E-mail</p>
						</td>
						<td width="75%">
							<p>: @{{ staffData.firstname }}</p>
							<p>: @{{ staffData.lastname }}</p>
							<p>: @{{ staffData.email }}</p>
						</td>
					</tr>
				</table>
				<hr>
				<h4>
					<b>Contact Info</b>
				</h4>
				<table width="100%">
					<tr>
						<td width="25%">
							<p>No. Telp</p>
							<p>No. HP</p>
						</td>
						<td width="75%">
							<p>: @{{ staffData.phone }}</p>
							<p>: @{{ staffData.cellphone }}</p>
						</td>
					</tr>
				</table>
				<hr>
				<h4>
					<b>Address Info</b>
				</h4>
				<table width="100%">
					<tr>
						<td width="25%">
							<p>Alamat</p>
							<p>Kota</p>
							<p>Provinsi</p>
							<p>Negara</p>
						</td>
						<td width="75%">
							<p>: @{{ staffData.address }}</p>
							<p>: @{{ staffData.city }}</p>
							<p>: @{{ staffData.state }}</p>
							<p>: @{{ staffData.country }}</p>
						</td>
					</tr>
				</table>
				<hr>
				<h4>
					<b>Status</b>
				</h4>
				<table width="100%">
					<tr>
						<td width="25%">
							<p>Level User</p>
							<p>Tanggal Terdaftar</p>
						</td>
						<td width="75%">
							<p>: @{{ staffData.user_level }}</p>
							<p>: @{{ staffRegisteredDate }}</p>
						</td>
					</tr>
				</table>
			</div>
			<div class="col-sm-2">
				<ul class="list-group">
					<li class="list-group-item text-center" style="background-color: #00ADEF; color: white">
						<b>Actions</b>
					</li>
					<li class="list-group-item">
						<span class="glyphicon glyphicon-pencil" aria-hidden="true" style="margin-right: 10px"></span>Add Profile Photo
					</li>
					<li class="list-group-item">
						<span class="glyphicon glyphicon-pencil" aria-hidden="true" style="margin-right: 10px"></span>Edit Profile
					</li>
					<li class="list-group-item">
						<span class="glyphicon glyphicon-print" aria-hidden="true" style="margin-right: 10px"></span>Print My Profile
					</li>
				</ul>
			</div>
		</div>
	</div>
</div>

@stop

@section('js')

<script src="/libraries/moment/moment.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script>
	var profileControllerVue = new Vue({
		el: '#profileController',
		data: {
			editProfile: false,
			editPhoto: false,
			staffData: '',
			profilePhotoPath: ''
		},
		methods: {
			getStaffData: function() {
				return this.$http.get('http://' + document.location.host + '/staff/profile/getStaffData/' + {{ Auth::id() }}).then(
					(response) => {
						return this.$set('staffData', response.data);
					},
					(response) => {
						return this.getStaffData();
					}
				);
			}
		},
		computed: {
			staffRegisteredDate: function() {
				return moment(this.staffData.created_at).format('DD/MM/YYYY (hh:mm A)');
			}
		},
		ready: function() {
			this.getStaffData();
		}
	});
</script>

@stop