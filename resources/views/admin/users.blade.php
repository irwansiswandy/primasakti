@extends('app')

@section('content')

<div class="container-fluid">
	<div id="usersController">
		<!-- START: USERS BREADCRUMBS -->
		<ol class="breadcrumb">
			<li style="cursor: pointer" v-on:click="getAll"><b>ALL</b></li>
			<li style="cursor: pointer" v-on:click="getUsers"><b>USERS</b></li>
			<li style="cursor: pointer" v-on:click="getStaff"><b>STAFF</b></li>
		</ol>
		<!-- END: USERS BREADCRUMBS -->

		<div class="row">
			<div class="col-sm-3">
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Summary</b>
					</div>
					<div class="panel-body">
						<div v-show="data_option == 'ALL'">
							<div class="row">
								<div class="col-xs-6">
									<p class="text-left">Total All</p>
									<p class="text-left">Total User</p>
									<p class="text-left">Total Staff</p>
									<p class="text-left">Total Admin</p>
								</div>
								<div class="col-xs-6">
									<p class="text-right"><b>@{{ total.all }}</b></p>
									<p class="text-right"><b>@{{ total.users }}</b></p>
									<p class="text-right"><b>@{{ total.staff }}</b></p>
									<p class="text-right"><b>@{{ total.admin }}</b></p>
								</div>
							</div>
						</div>
						<div v-show="data_option == 'USERS'">
							<div class="row">
								<div class="col-xs-6">
									<p class="text-left">Total Users</p>
								</div>
								<div class="col-xs-6">
									<p class="text-right"><b>@{{ total }}</b></p>
								</div>
							</div>
						</div>
						<div v-show="data_option == 'STAFF'">
							<div class="row">
								<div class="col-xs-6">
									<p class="text-left">Total Staff</p>
								</div>
								<div class="col-xs-6">
									<p class="text-right"><b>@{{ total }}</b></p>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-success"
				     v-show="data_option == 'STAFF'">
					<div class="panel-heading">
						<b>Working Team Formation</b>
					</div>
					<div class="panel-body">
						<p><b>SUPERVISORS :</b></p>
						<span v-show="working_teams.supervisors.length <= 0">-</span>
						<ul v-show="working_teams.supervisors.length > 0"
						    v-for="supervisor in working_teams.supervisors">
							<li>@{{ supervisor.firstname + ' ' + supervisor.lastname }}</li>
						</ul>
						<hr>
						<p><b>STAFF :</b></p>
						<div v-for="working_team in working_teams.teams">
							<p style="color: blue">@{{ working_team.name }}</p>
							<ul v-for="staff in working_team.staff">
								<li>@{{ staff.firstname + ' ' + staff.lastname }}</li>
							</ul>
						</div>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
					<table class="table table-striped">
						<thead style="background-color: #D1D2D4">
							<tr>
								<th>ID</th>
								<th>Full Name</th>
								<th>E-mail Address</th>
								<th>Level</th>
								<th></th>
							</tr>
						</thead>
						<tbody>
							<tr is="user-list" v-for="user in users | orderBy 'firstname'" 
							    :index="users.indexOf(user)" :user="user">
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<!-- START: MODAL FOR 'userDetailsModal' -->
			<div id="userDetailsModal" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog">
					<div class="modal-content">
						<div class="modal-header bg-primary">
							<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							<div class="text-center">
								ID# : <b><span id="staffID">@{{ users[selectedIndex].id }}</span></b><br>
								Name : <b>@{{ users[selectedIndex].firstname + ' ' + users[selectedIndex].lastname }}</b><br>
								User Level : <b>@{{ users[selectedIndex].user_level }}</b>
							</div>
						</div>
						<div class="modal-body">
							<div class="row">
								<div class="col-md-4 col-md-offset-4" v-show="users[selectedIndex].user_level == 'STAFF'">
									<div class="form-group text-center">
										<label>Working Team :</label>
										<select id="workingTeamOption" class="form-control"
											    v-model="users[selectedIndex].working_team[0].name">
												<option value="">No Team</option>
											@foreach(App\WorkingTeam::all() as $team)
												<option value="{{ $team->name }}">{{ $team->name }}</option>
											@endforeach
										</select>
									</div>
								</div>
							</div>
							<div class="text-center">
								<p>
									E-mail Address :<br>
									<b>@{{ users[selectedIndex].email }}</b>
								</p>
								<p v-show="users[selectedIndex].user_level == 'USER'">
									Wrote Review :<br>
									<b>@{{ users[selectedIndex].wrote_review }}</b>
								</p>
								<p>
									Address :<br>
									<b>@{{{ users[selectedIndex].address }}}<br>
										 @{{ users[selectedIndex].city }} @{{ users[selectedIndex].postcode }}<br>
										 @{{ users[selectedIndex].state }}, @{{ users[selectedIndex].country }}</b>
								</p>
								<p>
									Phone :<br>
									<b>@{{ users[selectedIndex].phone }}</b>
								</p>
								<p>
									Cellphone :<br>
									<b>@{{ users[selectedIndex].cellphone }}</b>
								</p>
							</div>
						</div>
						<div class="modal-footer bg-info" v-show="users[selectedIndex].user_level == 'STAFF'">
							<button id="changeWorkingTeam" class="btn btn-info">Update</button>
							<button class="btn btn-danger" data-dismiss="modal">Close</button>
						</div>
					</div>
		</div>
	</div>
	<!-- END: MODAL FOR 'userDetailsModal' -->

</div>

<!-- START: TEMPLATE FOR <user-list-component> -->
<template id="user-list-template">
	<tr>
		<td>@{{ user.id }}</td>
		<td>
			@{{ user.firstname + ' ' + user.lastname }}
			<span v-show="user.user_level == 'STAFF'"><br><small style="color: blue">@{{ user.working_team[0].name }}</small></span>
		</td>
		<td>
			@{{ user.email }}<br>
			<small><span style="color: #A7A9AB">Registered : @{{ dateTimeMoment(user.created_at) }}</span></small>
		</td>
		<td>
			<select class="form-control"
			        v-model="user.user_level"
			        v-on:change="userLevelChanged">
				<option value="USER">USER</option>
				<option value="STAFF">STAFF</option>
				<option value="SUPERVISOR">SUPERVISOR</option>
				<option value="ADMIN">ADMIN</option>
			</select>
		</td>
		<td class="text-right">
			<button id="displayUserDetails" class="btn btn-success" data-toggle="modal" data-target="#userDetailsModal" :data-index="index">
				<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
			</button>
			<button class="btn btn-info" :disabled="!updateButtonStatus" v-on:click.prevent="changeUserLevel(user.id)">
				<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
			</button>
			<button class="btn btn-danger" v-on:click="removeUser(user.id)">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
			</button>
		</td>
	</tr>
</template>
<!-- END: TEMPLATE FOR <user-list-component> -->

@stop

@section('js')

<script src="/libraries/moment/moment.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script>
	Vue.component('user-list', {
		template: '#user-list-template',
		props: [
			'index',
			'user'
		],
		data: function() {
			return {
				updateButtonStatus: false,
				defaultUserLevel: this.user.user_level
			}
		},
		methods: {
			/* ENABLING UPDATE BUTTON */
			userLevelChanged: function() {
				if (this.defaultUserLevel != this.user.user_level) {
					this.updateButtonStatus = true;
				}
				else {
					this.updateButtonStatus = false;
				}
			},
			/* HANDLE UPDATE USER_LEVEL BUTTON */
			changeUserLevel: function(id) {
				this.$dispatch('submitChangeUserLevel', [id, this.user.user_level]);
			},
			changeWorkingTeam: function() {
				this.$dispatch('submitChangeWorkingTeam');
			},
			dateTimeMoment: function(dateTime) {
				return moment(dateTime).format('DD/MM/YYYY (hh:mm A)');
			},
			removeUser: function(userID) {
				this.$dispatch('handleRemoveUser', userID);
			}
		}
	});

	var usersControllerVue = new Vue({
		el: '#usersController',
		http: {
			headers: {
				'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
			}
		},
		data: {
			response: '',
			data_option: 'ALL',
			users: [],
			total: '',
			working_teams: [],
			newUserLevel: {
				id: '',
				user_level: ''
			},
			workingTeam: {
				id: '',
				name: ''
			},
			selectedIndex: '',
			staffTeamSelectBox: false
		},
		methods: {
			getAll: function() {
				this.data_option = 'ALL';
				return this.$http.get('http://' + document.location.host + '/admin/users/getAllUsers').then((response) => {
					this.$set('users', response.data[0]);
					this.$set('total', response.data[1]);
				});
			},
			getUsers: function() {
				this.data_option = 'USERS';
				return this.$http.get('http://' + document.location.host + '/admin/users/getOnlyUsers').then((response) => {
					this.$set('users', response.data[0]);
					this.$set('total', response.data[1]);
				});
			},
			getStaff: function() {
				this.data_option = 'STAFF';
				return this.$http.get('http://' + document.location.host + '/admin/users/getOnlyStaffs').then((response) => {
					this.$set('users', response.data[0]);
					this.$set('total', response.data[1]);
					this.getWorkingTeams();
				});
			},
			getWorkingTeams: function() {
				return this.$http.get('http://' + document.location.host + '/admin/users/get_working_team').then((response) => {
					this.$set('working_teams', response.data);
				});
			},
			updateUserLevel: function() {
				var newUserLevelData = {
					id: this.newUserLevel.id,
					user_level: this.newUserLevel.user_level
				};
				return this.$http.post('http://' + document.location.host + '/admin/users/update_level', newUserLevelData).then((response) => {
					if (response.data.user_level == 'USER') {
						return this.getUsers();
					}
					else if (response.data.user_level == 'STAFF') {
						return this.getStaff();
					}
					else if (response.data.user_level == 'SUPERVISOR') {
						return this.getStaff();
					}
				});
			},
			updateWorkingTeam: function() {
				return this.$http.post(document.URL + '/update_team', this.workingTeam).then((response) => {
					if (response.ok) {
						return this.getStaff();
					}
				});
			}
		},
		events: {
			'submitChangeUserLevel': function([id, newLevel]) {
				var acceptedID = id;
				var acceptedNewLevel = newLevel;

				this.newUserLevel = {
					id: acceptedID,
					user_level: newLevel
				}
				
				return this.updateUserLevel();
			},
			'handleRemoveUser': function(userID) {
				alert('User with ID : ' + userID + ' will be removed');
			}
		},
		ready: function() {
			this.getAll();
		}
	});

	/* START: JQUERY SCRIPT FOR 'userDetailsModal' */
	$(document).on('click', '#displayUserDetails', function() {
		var passed_index = $(this).data('index');
		usersControllerVue.$data.selectedIndex = passed_index;
		$('#userDetailsModal').modal('show');
	});

	$('#userDetailsModal').on('change', '#workingTeamOption', function() {
		var selectedUser = $('#staffID').text();
		var selectedTeam = $('#workingTeamOption').val();
		usersControllerVue.$set('workingTeam', {'id': selectedUser, 'name': selectedTeam});
	});

	$('#userDetailsModal').on('click', '#changeWorkingTeam', function() {
		usersControllerVue.updateWorkingTeam();
		$('#userDetailsModal').modal('hide');
	});
	/* END: JQUERY SCRIPT FOR 'userDetailsModal' */
</script>

@stop
