@extends('app')

@section('content')

<div id="salesController">
	<div class="visible-print">
		<div id="printed-invoice" class="visible-print-block" style="font-size: 11px">
			<p class="text-center">
				PRIMASAKTI - Digital Copy & Print Shop<br>
				Jl. Raya Tenggilis No. 34 - 34A<br>
				Surabaya 60292 | Telp : (031) 8484808<br>
				E-mail : primasakti_copycenter@yahoo.com
			</p>
			<p class="text-center">
				No. Nota : @{{ invoices[selected_index].invoice_no }} (RP)<br>
				Tanggal : @{{ invoices[selected_index].created_at }}<br>
				Staff : @{{ invoices[selected_index].staff.firstname + ' ' + invoices[selected_index].staff.lastname }}
			</p>
			<table width="100%" style="font-size: 11px">
				<tbody>
					<tr v-for="item in invoices[selected_index].transactions">
						<td class="text-left">
							<p>
								@{{ item.product.category.name | uppercase }}<br>
								@{{ item.product.name | uppercase }}<br>
								<span v-show="item.option">@{{ item.option.name | uppercase }}<br></span>
								(@{{ item.qty }} x @{{ item.price | currency 'Rp ' }})
							</p>
						</td>
						<td class="text-right">
							<p>@{{ (item.qty * item.price) | currency 'Rp ' }}</p>
						</td>
					</tr>
				</tbody>
			</table>
			<table width="100%" style="font-size: 11px">
				<tbody>
					<tr>
						<td width="60%" class="text-right">TOTAL : </td>
						<td width="40%" class="text-right">@{{ invoices[selected_index].total | currency 'Rp ' }}</td>
					</tr>
					<tr v-show="invoices[selected_index].payment_status == 'CASH'">
						<td width="60%" class="text-right">BAYAR : </td>
						<td width="40%" class="text-right">@{{ invoices[selected_index].paid | currency 'Rp ' }}</td>
					</tr>
					<tr v-show="invoices[selected_index].payment_status == 'CASH'">
						<td width="60%" class="text-right">KEMBALI : </td>
						<td width="40%" class="text-right">@{{ invoices[selected_index].change | currency 'Rp ' }}</td>
					</tr>
					<tr v-show="invoices[selected_index].payment_status == 'T2W'">
						<td width="60%" class="text-right">BAYAR : </td>
						<td width="40%" class="text-right">TRANSFER BANK</td>
					</tr>
				</tbody>
			</table>
			<br>
			<p class="text-center" v-show="invoices[selected_index].payment_status == 'T2W'">
				No. Rekening Primasakti :<br>
				BCA 5120169060 (a/n. IRWAN SISWANDY)
			</p>
			<p class="text-center">
				- TERIMA KASIH -<br>
				*) Barang yang sudah dibeli tidak bisa ditukarkan / dikembalikan, kecuali ada perjanjian sebelumnya.
			</p>
		</div>
	</div>
	<div class="hidden-print">
		<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">
		<div class="row">
			<div class="col-sm-3">
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Menu</b>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Pilih Laporan</label>
									<select class="form-control" v-model="data_option">
										<option value="Today">Hari Ini</option>
										<option value="Yesterday">Kemarin</option>
										<option value="ThisWeek">Minggu Ini</option>
										<option value="LastWeek">Minggu Lalu</option>
										<option value="ThisMonth">Bulan Ini</option>
										<option value="LastMonth">Bulan Lalu</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Data per halaman</label>
									<select class="form-control" v-model="pagination_data.per_page">
										<option value="15">15</option>
										<option value="25" disabled>25</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Ringkasan Penjualan</b>
					</div>
					<div class="panel-body">
						<table class="table table-striped" width="100%">
							<tr v-show="staff_data.user_level == 'STAFF'">
								<td width="50%">
									<p class="text-left">Nama Tim</p>
								</td>
								<td width="50%">
									<p class="text-right">
										<b>@{{ team_name }}</b>
									</p>
								</td>
							</tr>
							<tr>
								<td width="50%">
									<p>Bulan / Tahun</p>
								</td>
								<td width="50%">
									<p class="text-right"><b>@{{ month }} / @{{ year }}</b></p>
								</td>
							</tr>
							<tr>
								<td width="50%">
									<p>Jumlah Nota</p>
									<p>Rata2 Penjualan</p>
									<p>Total Penjualan</p>
								</td>
								<td width="50%">
									<p class="text-right"><b>@{{ number_of_invoices }}</b></p>
									<p class="text-right"><b>@{{ sales_average | currency 'Rp ' }}</b></p>
									<p class="text-right"><b>@{{ total_sales | currency 'Rp ' }}</b></p>
								</td>
							</tr>
							<tr v-show="staff_data.user_level == 'STAFF'">
								<td width="50%">
									<p v-show="team_name != '-'">Total Bonus</p>
									<p v-show="team_name != '-'">Bonus (per orang)</p>
								</td>
								<td width="50%">
									<p class="text-right" v-show="team_name != '-'"><b>@{{ total_bonus | currency 'Rp ' }}</b></p>
									<p class="text-right" v-show="team_name != '-'"><b>@{{ bonus_per_member | currency 'Rp ' }}</b></p>
								</td>
							</tr>
							<tr v-show="staff_data.user_level == 'SUPERVISOR'">
								<td width="50%">
									<p class="text-left" style="color: blue">Total Bonus</p>
								</td>
								<td width="50%">
									<p class="text-right" style="color: blue"><b>@{{ total_bonus | currency 'Rp ' }}</b></p>
								</td>
							</tr>
						</table>
						<!-- START: BUTTON "INFORMASI TIM" -->
						<div v-show="staff_data.user_level == 'STAFF'">
							<div class="form-group" v-show="team_name != '-'">
								<a class="btn btn-warning form-control" role="button"
								   data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
									<b>Informasi Tim</b>
								</a>
							</div>
							<div class="collapse" id="collapseExample">
								<div class="panel panel-warning">
									<div class="panel-body bg-warning">
										<p>
											Jumlah Anggota : <b>@{{ total_team_members }}</b>
										</p>
										<ul>
											<li v-for="member in staff_data.working_team[0].staff">
												@{{ member.firstname + ' ' + member.lastname }}
											</li>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<!-- END: BUTTON "INFORMASI TIM" -->
						<!-- START: BUTTON "PENJUALAN INDIVIDU" -->
						<div class="form-group" v-show="team_name != '-'">
							<a class="btn btn-success form-control" role="button"
							   data-toggle="collapse" href="#collapseIndividualReport" aria-expanded="false" aria-controls="collapseIndividualReport">
								<b>Penjualan Individu</b>
							</a>
						</div>
						<div class="collapse" id="collapseIndividualReport">
							<div class="panel panel-success">
								<div class="panel-body bg-success">
									<table width="100%">
										<tr v-for="member in individual_sales_data | orderBy 'staff_name'">
											<td width="50%">
												<p class="text-left" style="font-size: 75%">@{{ member.staff_name }}</p>
											</td>
											<td width="50%">
												<p class="text-right" style="font-size: 75%">
													<b>@{{ member.total_sales | currency 'Rp ' }}</b>
												</p>
											</td>
										</tr>
									</table>
								</div>
							</div>
						</div>
						<!-- END: BUTTON "PENJUALAN PER ORANG" -->
					</div>
				</div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<span v-show="staff_data.user_level == 'STAFF'"><b>Penjualan Tim Lain</b></span>
						<span v-show="staff_data.user_level == 'SUPERVISOR'"><b>Penjualan Tim</b></span>
					</div>
					<div class="panel-body">
						<table class="table table-striped" width="100%">
							<tr v-for="other_team in other_teams_data | orderBy 'total_sales' -1">
								<td width="50%">
									<p>@{{ other_team.team }}</p>
								</td>
								<td width="50%">
									<p class="text-right">
										<b>@{{ other_team.total_sales | currency 'Rp ' }}</b>
									</p>
								</td>
							</tr>
						</table>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<div id="pagination_bar" class="panel panel-default">
					<div class="panel-body text-center">
						<button class="btn btn-default" v-on:click="fast_backward" :disabled="pagination_data.prev_page_url == null">
							<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
						</button>
						<button class="btn btn-default" v-on:click="step_backward" :disabled="pagination_data.prev_page_url == null">
							<span class="glyphicon glyphicon-step-backward" aria-hidden="true"></span>
						</button>
						<span style="margin-left: 30px; margin-right: 30px">
							Halaman @{{ pagination_data.current_page }} dari @{{ pagination_data.last_page }}
						</span>
						<button class="btn btn-default" v-on:click="step_forward" :disabled="pagination_data.next_page_url == null">
							<span class="glyphicon glyphicon-step-forward" aria-hidden="true"></span>
						</button>
						<button class="btn btn-default" v-on:click="fast_forward" :disabled="pagination_data.next_page_url == null">
							<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
						</button>
					</div>
				</div>
				<table width="100%" class="table table-striped" style="margin-bottom: 0px">
					<thead>
						<tr>
							<th class="text-left">ID</th>
							<th class="text-left">Tgl / No. Nota</th>
							<th class="text-right">Total Nota</th>
							<th class="text-center">Pembayaran</th>
							<th class="text-center">Pelanggan</th>
							<th class="text-center">Staff</th>
							<th class="text-right" v-show="staff_data.user_level == 'STAFF'">Bonus Nota</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr is="invoice-list" v-for="invoice in invoices"
							:invoice="invoice"
							:index="invoices.indexOf(invoice)"
							:loggedinid="{{ Auth::id() }}"
							:loggedinstafflevel="staff_data.user_level">
						</tr>
					</tbody>
				</table>
				<div id="no_data_message" v-show="invoices.length <= 0">
					<br>
					<div class="alert alert-danger text-center">
						<b>Data Tidak Ada</b>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!-- START: MODAL FOR "invoiceDetailsModal" -->
	<div class="hidden-print">
		<div id="invoiceDetailsModal" class="modal fade" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg">
			  <div class="modal-content">
			    <div class="modal-header bg-primary">
			    	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	      			<p class="modal-title text-center" style="font-size: 150%">
	      				<b>@{{ invoices[selected_index].invoice_no }}</b>
	      			</p>
	      			<p class="text-center">
	      				Tanggal Nota : <b>@{{ formattedDate(invoices[selected_index].created_at) }}</b><br>
	      				Pelanggan : <b>#@{{ invoices[selected_index].id }} / @{{ invoices[selected_index].user.firstname }} @{{ invoices[selected_index].user.lastname }}</b><br>
	      			</p>
			    </div>
			    <div class="modal-body" style="background-color: white">
			    	<table width="100%">
			    		<tr>
			    			<td width="50%">
									<p>Staff : <b>@{{ invoices[selected_index].staff.firstname + ' ' + invoices[selected_index].staff.lastname }}</b></p>
			    			</td>
			    			<td width="50%">
			    				<p class="text-right">
				      				Total Produk : <b>@{{ invoices[selected_index].transactions.length }}</b>
				      			</p>
			    			</td>
			    		</tr>
			    	</table>
			    	<table class="table table-striped" style="width: 100%">
			    		<thead style="background-color: #D1D2D4">
			    			<tr>
			    				<th>
			    					Keterangan
			    				</th>
			    				<th>Qty</th>
			    				<th class="text-right">
			    					Harga @
			    				</th>
			    				<th class="text-right">
			    					Sub-Total
			    				</th>
			    			</tr>
			    		</thead>
						<tbody>
							<tr v-for="item in invoices[selected_index].transactions">
								<td>
									@{{ item.product.category.name | uppercase }}<br>
									@{{ item.product.name | uppercase }}
								</td>
								<td>@{{ item.qty }}</td>
								<td class="text-right">@{{ item.price | currency 'Rp '}}</td>
								<td class="text-right">@{{ (item.qty * item.price) | currency 'Rp ' }}</td>
							</tr>
						</tbody>
					</table>
					<table width="100%">
						<tr>
							<td width="80%">
								<p class="text-right">Total Nota : </p>
								<p class="text-right">Bayar : </p>
								<p class="text-right">Kembali : </p>
							</td>
							<td width="20%">
								<p class="text-right"><b>@{{ invoices[selected_index].total | currency 'Rp '}}</b></p>
						 		<p class="text-right"><b>@{{ invoices[selected_index].paid | currency 'Rp ' }}</b></p>
								<p class="text-right"><b>@{{ invoices[selected_index].change | currency 'Rp ' }}</b></p>
							</td>
						</tr>
					</table>
			    </div>
					<div class="modal-footer bg-info">
						<div class="text-center">
							<button class="btn btn-success" v-on:click="reprintInvoice(index)">
								<span class="glyphicon glyphicon-print" style="margin-right: 5px" aria-hidden="true"></span> Print Ulang Nota
							</button>
							<button type="button" class="btn btn-danger" data-dismiss="modal">
								<span class="glyphicon glyphicon-remove" style="margin-right: 5px" aria-hidden="true"></span> Close
							</button>
						</div>
					</div>
			  </div>
			</div>
		</div>
	</div>
	<!-- END: MODAL FOR "invoiceDetailsModal" -->
</div>

<template id="invoice-list-template">
	<tr>
		<td class="text-left">@{{ invoice.id }}</td>
		<td class="text-left">
			@{{ formattedDate }}<br>
			<span style="color: #808284"><small>@{{ invoice.invoice_no }}</small></span>
		</td>
		<td class="text-right">
			@{{ invoice.total | currency 'Rp '}}
			<span style="color: #808284" v-show="invoice.staff.user_level == 'STAFF'">
				<br>
				<small>(%) @{{ invoice.staff_bonus | currency 'Rp ' }}</small>
			</span>
		</td>
		<td class="text-center">@{{ invoice.payment_status }}</td>
		<td class="text-center">@{{ invoice.user.firstname }}</td>
		<td class="text-center">
			@{{ invoice.staff.firstname }}
			<span style="color: #808284" v-show="loggedinstafflevel == 'SUPERVISOR'">
				<br>
				<small>@{{ invoice.staff.working_team[0].name }}</small>
			</span>
		</td>
		<td class="text-right"
			v-show="staff_data.user_level == 'STAFF'">
			<small>@{{ invoice.staff_bonus | currency 'Rp ' }}</small>
		</td>
		<td class="text-right">
			<button class="btn btn-info view-details"
					data-toggle="modal"
					data-target="#invoiceDetailsModal"
					:data-index="index">
				<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
			</button>
			<button class="btn btn-danger" :disabled="invoice.staff_id != loggedinid"
					v-on:click="deleteInvoice(index)" v-show="loggedinstafflevel == 'STAFF'">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
			</button>
			<button class="btn btn-danger"
					v-on:click="deleteInvoice(index)" v-show="loggedinstafflevel == 'SUPERVISOR'">
				<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
			</button>
		</td>
	</tr>
</template>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/libraries/moment/moment.js"></script>
<script src="https://js.pusher.com/3.2/pusher.min.js"></script>

<!-- START: PUSHER SCRIPT -->
<script>
	/* INITIALIZE PUSHER */
	var pusher = new Pusher('937c40c45b8b04b84c49', {
      cluster: 'ap1',
      encrypted: true
    });

    var invoice_channel = pusher.subscribe('primasakti_channel');
    invoice_channel.bind('new_invoice_added', function(data) {
    	if (data.staff.working_team[0].id == salesControllerVue.staff_data.working_team[0].id) {
    		return salesControllerVue.get_data();
    	}
    });
</script>
<!-- END: PUSHER SCRIPT -->

<!-- START: VUE SCRIPT (#salesController) -->
<script>
	Vue.component('invoice-list', {
		template: '#invoice-list-template',
		props: [
			'index',
			'invoice',
			'loggedinid',
			'loggedinstafflevel'
		],
		computed: {
			formattedDate: function() {
				return moment(this.invoice.created_at).format('DD-MM-YYYY (hh:mm A)');
			}
		},
		methods: {
			deleteInvoice: function(index) {
				this.$dispatch('invoiceDeleted', index);
			}
		}
	});
	var salesControllerVue = new Vue({
		http: {
			headers: {
				'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
			}
		},
		el: '#salesController',
		data: {
			// PAGINATION DATA
			pagination_data: {
				total: '',
				per_page: 15,
				current_page: '',
				last_page: '',
				next_page_url: '',
				prev_page_url: '',
				from: '',
				to: ''
			},

			// VUE DATA
			data_option: 'Today',
			month: '',
			year: '',
			staff_data: '',
			invoices: [],
			selected_index: '',
			total_sales: '',
			total_bonus: '',
			other_teams_data: [],
			individual_sales_data: []
		},
		computed: {
			api_url: function() {
				return 'http://' + document.location.host + '/staff/sales/get_sales/' + {{ Auth::id() }} + '/' + this.data_option;
			},
			team_name: function() {
				if (this.staff_data.working_team.length <= 0) {
					return '-';
				}
				else {
					return this.staff_data.working_team[0].name;
				}
			},
			total_team_members: function() {
				if (this.team_name != '-') {
					return this.staff_data.working_team[0].staff.length;
				}
				else {
					return 0;
				}
			},
			first_page_url: function() {
				return this.api_url + '?page=' + 1;
			},
			last_page_url: function() {
				return this.api_url + '?page=' + this.pagination_data.last_page;
			},
			number_of_invoices: function() {
				return this.pagination_data.total;
			},
			sales_average: function() {
				if (this.total_sales == 0) {
					return 0;
				}
				else {
					return this.total_sales / this.number_of_invoices;
				}
			},
			bonus_per_member: function() {
				if (this.total_team_members > 0) {
					return this.total_bonus / this.total_team_members;
				}
				else {
					return this.total_bonus;
				}
			}
		},
		watch: {
			'data_option': function() {
				return this.get_data();
			}
		},
		methods: {
			get_data: function(pagination_url) {
				if (!pagination_url) {
					return this.$http.get(this.api_url).then(
						(response) => {
							return [
								this.$set('month', response.data.month),
								this.$set('year', response.data.year),
								this.$set('staff_data', response.data.staff_data),
								this.$set('total_sales', response.data.total_sales),
								this.$set('total_bonus', response.data.total_bonus),
								this.$set('other_teams_data', response.data.other_teams_data),
								this.$set('individual_sales_data', response.data.individual_sales_data),

								this.$set('invoices', response.data.invoices.data),
								this.$set('pagination_data.total', response.data.invoices.total),
								this.$set('pagination_data.per_page', response.data.invoices.per_page),
								this.$set('pagination_data.current_page', response.data.invoices.current_page),
								this.$set('pagination_data.last_page', response.data.invoices.last_page),
								this.$set('pagination_data.next_page_url', response.data.invoices.next_page_url),
								this.$set('pagination_data.prev_page_url', response.data.invoices.prev_page_url),
								this.$set('pagination_data.from', response.data.invoices.from),
								this.$set('pagination_data.to', response.data.invoices.to)
							];
						}
					);
				}
				else {
					return this.$http.get(pagination_url).then(
						(response) => {
							return [
								this.$set('invoices', response.data.invoices.data),
								this.$set('pagination_data.total', response.data.invoices.total),
								this.$set('pagination_data.per_page', response.data.invoices.per_page),
								this.$set('pagination_data.current_page', response.data.invoices.current_page),
								this.$set('pagination_data.last_page', response.data.invoices.last_page),
								this.$set('pagination_data.next_page_url', response.data.invoices.next_page_url),
								this.$set('pagination_data.prev_page_url', response.data.invoices.prev_page_url),
								this.$set('pagination_data.from', response.data.invoices.from),
								this.$set('pagination_data.to', response.data.invoices.to)
							];
						}
					);
				}
			},
			selectIndex: function(index) {
				return this.selectedIndex = index;
			},
			handleDeleteInvoice: function(index) {
				return this.$http.delete('http://' + document.location.host + '/staff/sales/invoice/delete/' + this.invoices[index].id).then(
					(response) => {
						return this.get_data();
					}
				);
			},
			itemData: function(item_id) {
				return this.$http.get('http://' + document.location.host + '/staff/getItemData/' + item_id, function(data) {
					return data;
				});
			},
			getUserName: function(user_id) {
				return this.$http.get('http://' + document.location.host + '/staff/sales/get_user_name/' + user_id, function(data) {
					return data;
				});
			},
			formattedDate: function(date) {
				return moment(date).format('DD/MM/YYYY (hh:mm A)');
			},
			fast_backward: function() {
				return this.get_data(this.first_page_url);
			},
			step_backward: function() {
				return this.get_data(this.pagination_data.prev_page_url);
			},
			step_forward: function() {
				return this.get_data(this.pagination_data.next_page_url);
			},
			fast_forward: function() {
				return this.get_data(this.last_page_url);
			},
			reprintInvoice: function() {
				return window.print();
			},
			formatDate: function() {
				return moment(this.invoice.created_at).format('DD-MM-YYYY (hh:mm A)');
			}
		},
		events: {
			'invoiceDeleted': function(index) {
				return confirmDeleteFlash(
					index,
					'Anda yakin ?',
					'Data yang akan dihapus :' + '\n\n' + this.invoices[index].invoice_no + '\n' + 'Total : Rp ' + this.invoices[index].total
				);
			}
		},
		ready: function() {
			return [
				this.get_data()
			];
		}
	});

	/* START: JQUERY MODAL SCRIPT */
	$(document).on('click', '.view-details', function() {
		var passed_index = $(this).data('index');
		salesControllerVue.$data.selected_index = passed_index;
		$('#invoiceDetailsModal').modal('show');
	});
	/* END: JQUERY MODAL SCRIPT */

	/* START: CONFIRM FLASH SCRIPT */
	function confirmDeleteFlash(index, title, message) {
		return swal({
			title: title,
			text: message,
			type: 'warning',
			showCancelButton: true,
			confirmButtonText: 'OK',
			cancelButtonText: 'Cancel',
			closeOnConfirm: false,
			closeOnCancel: false
		}, function(isConfirm) {
			if (isConfirm) {
				salesControllerVue.handleDeleteInvoice(index);
				swal('Selesai !', 'Data sudah berhasil dihapus', 'success');
			}
			else {
				swal('Batal', 'Data tidak jadi dihapus', 'error');
			}
		});
	}
	/* END: CONFIRM FLASH SCRIPT */
</script>
<!-- END: VUE SCRIPT (#salesController) -->

@stop
