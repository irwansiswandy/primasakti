@extends('app')

@section('css')

<link href="/libraries/eonasdan-bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.min.css" rel="stylesheet">

@stop

@section('content')

<div id="orderInController">
	<div class="row row-eq-height">
		<div class="col-sm-3">
			<div class="panel panel-info">
				<div class="panel-heading">
					<b>1 - Pilih Pelanggan</b>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Hari Ini</label>
						<input type="text" value="@{{ formatDate(today_date) }}" class="form-control" readonly>
					</div>
					<div class="form-group">
						<label>No. Tanda Terima</label>
						<input type="text" value="@{{ orderNo }}" class="form-control" disabled>
					</div>
					<hr>
					<div class="form-group">
						<label>Pelanggan</label>
						<select class="form-control"
								v-model="[selectedUser.id, selectedUser.firstname, selectedUser.lastname, selectedUser.email, selectedUser.phone, selectedUser.cellphone, selectedUser.address, selectedUser.city, selectedUser.state]">
							<option value="@{{ ['', '', '', '', '', '', '', '', ''] }}" selected>Pilih Pelanggan</option>
							<option v-for="user in users | orderBy 'firstname'"
									value="@{{ [user.id, user.firstname, user.lastname, user.email, user.phone, user.cellphone, user.address, user.city, user.state] }}">
								@{{ user.firstname + ' ' + user.lastname }}
							</option>
						</select>
					</div>
					<div class="form-group text-center">
						<button class="btn btn-success form-control" data-toggle="modal" data-target="#new_user_modal">
							<span class="glyphicon glyphicon-plus" aria-hidden="true" style="margin-right: 10px"></span>Pelanggan Baru
						</button>
					</div>
					<hr>
					<p>
						Nama Lengkap :<br>
						<span style="color: blue">
							@{{ selectedUser.firstname + ' ' + selectedUser.lastname }}
						</span>
					</p>
					<p>
						E-mail :<br>
						<span style="color: blue">
							@{{ selectedUser.email }}
						</span>
					</p>
					<p>
						No. Telp :<br>
						<span style="color: blue">
							@{{ selectedUser.phone }}
						</span>
					</p>
					<p>
						No. HP :<br>
						<span style="color: blue">
							@{{ selectedUser.cellphone }}
						</span>
					</p>
					<p>
						Alamat :<br>
						<span style="color: blue">
							@{{ selectedUser.address }}<br>
							@{{ selectedUser.city }}, @{{ selectedUser.state }}
						</span>
					</p>
				</div>
			</div>
		</div>
		<div class="col-sm-4">
			<div class="panel panel-info">
				<div class="panel-heading">
					<b>2 - Pilih Order</b>
				</div>
				<div class="panel-body">
					<div v-for="item in items | orderBy 'name'">
						<item-list :item="item"></item-list>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-5">
			<div class="panel panel-info">
				<div class="panel-heading">
					<b>3 - Input Keterangan</b>
				</div>
				<div class="panel-body">
					<div class="form-group">
						<label>Staff Yang Mengerjakan</label>
						<select class="form-control" v-model="selectedStaff.id">
							<option value="" class="form-control" selected>Pilih Staff</option>
							<option v-for="staff in staffs" value="@{{ staff.id }}" class="form-control">
								@{{ staff.firstname + ' ' + staff.lastname }}
							</option>
						</select>
						<p class="help-block">
							<small>Tidak perlu dipilih, apabila tidak ada permintaan khusus dari customer.</small>
						</p>
					</div>
					<div class="form-group">
						<label>Tanggal Order Diambil</label>
						<div id="dateTimePicker" class="input-group date">
							<input type="text" class="form-control" v-model="deadline" :disabled="!selectedUser.id">
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
					<div class="form-group">
						<label>Keterangan / Catatan Tambahan</label>
						<textarea rows="4" class="form-control" v-model="newOrderData.note"></textarea>
					</div>
					<div class="form-group">
						<label>Estimasi Biaya (Rp)</label>
						<input type="text" class="form-control">
						<p class="help-block">
							<small>Bisa dibiarkan kosong, apabila tidak dilakukan estimasi biaya</small>
						</p>
					</div>
					<div class="form-group">
						<label>DP / Uang Muka (Rp)</label>
						<input type="text" class="form-control" v-model="newOrderData.down_payment" :disabled="!selectedStaff.id">
						<p class="help-block">
							<small>Bisa dibiarkan kosong, apabila tidak ada uang muka</small>
						</p>
					</div>
				</div>
			</div>
			<div class="form-group">
				<div class="row">
					<div class="col-sm-4">
						<button class="btn btn-danger form-control" v-on:click="cancelButton" :disabled="!deadline">
							<span class="glyphicon glyphicon-remove" aria-hidden="true" style="margin-right: 10px"></span>Batal
						</button>
					</div>
					<div class="col-sm-4">
						<button class="btn btn-info form-control" v-on:click="cancelButton" :disabled="!deadline">
							<span class="glyphicon glyphicon-envelope" aria-hidden="true" style="margin-right: 10px"></span>E-mail
						</button>
					</div>
					<div class="col-sm-4">
						<button class="btn btn-primary form-control" v-on:click="addOrder" :disabled="!deadline">
							<span class="glyphicon glyphicon-print" aria-hidden="true" style="margin-right: 10px"></span>Print
						</button>
					</div>
				</div>
			</div>
		</div>	
	</div>

	<!-- MODAL: "new_user_modal" -->
	<div class="modal fade" id="new_user_modal" tabindex="-1" role="dialog" aria-labelledby="new_user_modal_label">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header" style="background-color: #00ADEF">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
					<h1 id="new_user_modal_label" class="modal-title text-center"
					    style="color: black; font-family: unda_angleitalic">
						DAFTAR PELANGGAN / USER BARU
					</h1>
				</div>
				<div class="modal-body" style="background-color: white">
					<div style="margin-left: 10px; margin-right: 10px">
						<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Nama Depan</label>
									<input type="text" class="form-control" v-model="newUser.firstname">
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Nama Belakang</label>
									<input type="text" class="form-control" v-model="newUser.lastname">
								</div>
							</div>
						</div>
						<div class="form-group">
							<label>E-mail</label>
							<input type="text" class="form-control" v-model="newUser.email">
						</div>
						<div class="form-group">
							<label>Alamat</label>
							<textarea rows="2" class="form-control" v-model="newUser.address"></textarea>
						</div>
						<div class="row">
							<div class="col-sm-3">
								<div class="form-group">
									<label>Kota</label>
									<input type="text" class="form-control" v-model="newUser.city">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Kode Pos</label>
									<input type="text" class="form-control" v-model="newUser.postcode">
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Provinsi</label>
									<select class="form-control" v-model="newUser.state">
										<option value="" selected>Pilih Provinsi</option>
										@foreach(App\State::all() as $state)
											<option value="{{ $state->name }}">{{ $state->name }}</option>
										@endforeach
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="form-group">
									<label>Negara</label>
									<input type="text" class="form-control" v-model="newUser.country" readonly>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-sm-6">
								<label>No. Telp</label>
								<input type="text" class="form-control" v-model="newUser.phone">
							</div>
							<div class="col-sm-6">
								<label>No. HP</label>
								<input type="text" class="form-control" v-model="newUser.cellphone">
							</div>
						</div>
						<br>
					</div>
					<!-- ERRORS LIST -->
					<ul style="color: red">
						<li v-for="error in register_form_errors">
							<small>@{{ error }}</small>
						</li>
					</ul>
				</div>
				<div class="modal-footer bg-info">
					<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					<button class="btn btn-warning" v-on:click.prevent="clearNewUserForm">Reset Form</button>
					<button class="btn btn-info" v-on:click.prevent="registerUser">Daftar</button>
				</div>
			</div>
		</div>
	</div>

	<!-- COMPONENT TEMPLATE: "item-list" -->
	<template id="item-list-template">
	    <input type="checkbox" v-model="checked" v-on:change="handleCheck"> @{{ item.name | uppercase }}
	    <div class="form-group" v-show="checked">
	    	<label>Keterangan</label>
	    	<textarea rows="2" class="form-control" v-model="description" v-on:keyup="handleDescription"></textarea>
		</div>
	</template>
</div>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/libraries/moment/moment.js"></script>
<script src="/libraries/eonasdan-bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js"></script>

<!-- JQUERY SCRIPT: dateTimePicker -->
<script>
	$(function() {
		$('#dateTimePicker').datetimepicker();
	});
</script>

<!-- VUE SCRIPT: orderInVue -->
<script>
	Vue.component('item-list', {
		template: '#item-list-template',
		props: [
			'item'
		],
		data: function() {
			return {
				checked: false,
				description: ''
			}
		},
		watch: {
			'checked': function() {
				return this.handleCheck();
			}
		},
		methods: {
			handleCheck: function() {
				if (this.checked == true) {
					return this.selectItem();
				}
				else {
					return this.unselectItem();
				}
			},
			selectItem: function() {
				return this.$dispatch('itemSelected', this.item.id);
			},
			unselectItem: function() {
				return [
					this.description = '',
					this.$dispatch('itemUnselected', this.item.id)
				];
			},
			handleDescription: function() {
				return this.$dispatch('descriptionAdded', this.item.id, this.description);
			}
		}
	});

	var orderInVue = new Vue({
		el: '#orderInController',
		http: {
			headers: {
				'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
			}
		},
		data: {
			ajax_response: '',
			today_date: '',
			users: [],
			register_form_errors: [],
			staffs: [],
			items: [],
			deadline: '',

			newUser: {
				firstname: '',
				lastname: '',
				email: '',
				address: '',
				city: '',
				state: '',
				postcode: '',
				country: '',
				phone: '',
				cellphone: '',
				country: 'INDONESIA'
			},
			newOrderData: {
				user_id: '',
				staff_id: '',
				order_no: '',
				deadline: '',
				note: '',
				down_payment: '',
				itemIds: [],
				itemDescriptions: []
			},

			selectedUser: {
				id: '',
				firstname: '',
				lastname: '',
				email: '',
				address: '',
				city: '',
				state: '',
				phone: '',
				cellphone: ''
			},
			selectedStaff: {
				id: ''
			},
			selectedItems: {
				id: [],
				description: []
			}
		},
		computed: {
			order_no_staff_id: function() {
				if (!this.selectedStaff.id) {
					return '0';
				}
				else {
					return this.selectedStaff.id;
				}
			},
			orderNo: function() {
				var day = moment(this.today_date).date();
				var month = moment(this.today_date).month() + 1;
				var year = moment(this.today_date).year();
				var hour = moment(this.today_date).hour();
				var minute = moment(this.today_date).minute();
				var second = moment(this.today_date).second();

				return 'PS/PO-' + this.selectedUser.id + '/' + this.order_no_staff_id + '-' + day + '/' + month + '/' + year + '-' + hour + '/' + minute + '/' + second;
			},
			properDeadlineFormat: function() {
				return moment(this.deadline).format('YYYY-MM-DD HH:mm:ss');
			}
		},
		methods: {
			get_today_date: function() {
				return this.$http.get('http://' + document.location.host + '/workshop/order_in/get_today_date').then(
					(response) => {
						return this.$set('today_date', response.data);
					},
					(response) => {
						return this.get_today_date();
					});
			},
			formatDate: function(date) {
				return moment(date).format('dddd, DD/MM/YYYY');
			},
			getUsers: function() {
				return this.$http.get('http://' + document.location.host + '/workshop/order_in/get_users').then(
					(response) => {
						return this.$set('users', response.data);
					},
					(response) => {
						return this.getUsers();
					});
			},
			getUserData: function(index) {
				return alert(index);
			},
			cancelButton: function() {
				return location.reload();
			},
			getStaffs: function() {
				return this.$http.get('http://' + document.location.host + '/workshop/order_in/get_staffs').then(
					(response) => {
						return this.$set('staffs', response.data);
					},
					(response) => {
						return this.getStaffs();
					});
			},
			getItems: function() {
				return this.$http.get('http://' + document.location.host + '/workshop/order_in/get_items').then(
					(response) => {
						return this.$set('items', response.data);
					},
					(response) => {
						return this.getItems();
					});
			},
			handleItemSelected: function(item_id) {
				var added_item_index = this.selectedItems.id.indexOf(item_id);
				if (added_item_index < 0) {
					return this.selectedItems.id.push(item_id);
				}
				else {
					return this.selectedItems.id.splice(added_item_index, 1, item_id);
				}
			},
			handleDescriptionAdded: function(item_id, description) {
				var index_for_description = this.selectedItems.id.indexOf(item_id);
				if (index_for_description < 0) {
					return this.selectedItems.description[index_for_description].push(description);
				}
				else {
					return this.selectedItems.description.splice(index_for_description, 1, description);
				}
			},
			handleUnselectItem: function(item_id) {
				var added_item_index = this.selectedItems.id.indexOf(item_id);
				if (added_item_index < 0) {
					// DO NOTHING
				}
				else {
					return [
						this.selectedItems.id.splice(added_item_index, 1),
						this.selectedItems.description.splice(added_item_index, 1)
					];
				}
			},
			clearNewUserForm: function() {
				return [
					this.newUser = {
						title: '',
						firstname: '',
						lastname: '',
						email: '',
						address: '',
						city: '',
						state: '',
						phone: '',
						cellphone: '',
						country: 'INDONESIA'
					},
					this.register_form_errors = []
				];
			},
			registerUser: function() {
				return this.$http.post('http://' + document.location.host + '/workshop/order_in/register_new_user', this.newUser).then(
					// IF SUCCESS
					(response) => {
						return location.reload();
					},
					// IF FAILED
					(response) => {
						return this.$set('register_form_errors', response.data);
					});
			},
			addOrder: function() {
				this.newOrderData.order_no = this.orderNo,
				this.newOrderData.deadline = this.properDeadlineFormat,
				this.newOrderData.itemIds = this.selectedItems.id,
				this.newOrderData.itemDescriptions = this.selectedItems.description;
				this.newOrderData.user_id = this.selectedUser.id;
				this.newOrderData.staff_id = this.selectedStaff.id;

				return this.$http.post('http://' + document.location.host + '/workshop/order_in/add_order', this.newOrderData).then(
					(response) => {
						return location.reload();
					}
				);
			}
		},
		events: {
			'itemSelected': function(item_id) {
				return this.handleItemSelected(item_id);
			},
			'descriptionAdded': function(item_id, description) {
				return this.handleDescriptionAdded(item_id, description);
			},
			'itemUnselected': function(item_id) {
				return this.handleUnselectItem(item_id);
			}
		},
		ready: function() {
			this.get_today_date();
			this.getUsers();
			this.getStaffs();
			this.getItems();
		}
	});
</script>

@stop