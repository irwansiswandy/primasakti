@extends('app')

@section('content')

<input type="hidden" id="token" name="_token" value="{{ csrf_token() }}">

<div id="adminSales">
	<!-- START: PRINT SHEET RENDERER -->
	<div class="visible-print">
		<p class="text-center">
			SALES REPORT<br>
			<!-- <span v-show="sales_option != 'Today'">Month : @{{ current_month }} / Year: @{{ current_year }}</span> -->
			<span v-show="sales_option == 'Today'">@{{ today_date }}</span>
		</p>
		<table class="table table-stripped" style="font-size: 11px">
			<thead>
				<tr>
					<th>ID #</th>
					<th>Invoice Date</th>
					<th>Invoice No.</th>
					<th class="text-right">Total</th>
					<th class="text-center">Payment</th>
					<th class="text-center">Staff / Team</th>
				</tr>
			</thead>
			<tbody>
				<tr v-for="printed_invoice in invoices_for_printing | orderBy 'created_at'">
					<td>@{{ printed_invoice.id }}</td>
					<td>@{{ rootFormatDate(printed_invoice.created_at) }}</td>
					<td>@{{ printed_invoice.invoice_no }}</td>
					<td class="text-right">@{{ printed_invoice.total | currency 'Rp '}}</td>
					<td class="text-center">@{{ printed_invoice.payment_status }}</td>
					<td class="text-center">@{{ printed_invoice.staff.firstname }} <small>@{{ '(' + printed_invoice.staff.working_team[0].name + ')' }}</td>
				</tr>
			</tbody>
		</table>
		<p class="text-center">
			REPORT SUMMARY
		</p>
		<div class="row">
			<div class="col-xs-6">
				<p class="text-center">SHOP SALES</p>
				<table width="100%" class="table table-stripped" style="font-size: 11px">
					<tbody>
						<tr>
							<td width="50%">Number of Invoices</td>
							<td width="50%">: @{{ number_of_invoices }}</td>
						</tr>
						<tr>
							<td width="50%">Total Sales</td>
							<td width="50%">: @{{ total_sales | currency 'Rp ' }}</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div class="col-xs-6">
				<p class="text-center">TEAM SALES</p>
				<table width="100%" class="table table-stripped" style="font-size: 11px">
					<tbody>
						<tr v-for="team_sales in sales_each_team">
							<td width="50%">@{{ team_sales.team_name }}</td>
							<td width="50%">: @{{ team_sales.total_sales | currency 'Rp ' }}</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
	<!-- END: PRINT SHEET RENDERER-->
	<div class="hidden-print">
		<div class="row">
			<div class="col-sm-3">
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Sales Report Options</b>
					</div>
					<div class="panel-body">
						<div class="row">
							<div class="col-sm-6">
								<div class="form-group">
									<label>Select Range</label>
									<select class="form-control" v-model="sales_option">
										<option value="Today">Today</option>
										<option value="Yesterday">Yesterday</option>
										<option value="ThisWeek">This Week</option>
										<option value="ThisMonth">This Month</option>
										<option value="LastMonth">Last Month</option>
									</select>
								</div>
							</div>
							<div class="col-sm-6">
								<div class="form-group">
									<label>Data per page</label>
									<select class="form-control" v-model="per_page">
										<option value="18">18</option>
										<option value="50" disabled>50</option>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Sales Summary</b>
					</div>
					<div class="panel-body">
						<table class="table table-striped" width="100%">
							<tr>
								<td width="50%">
									<p>Month / Year</p>
								</td>
								<td width="50%">
									<p class="text-right"><b>@{{ current_month + ' / ' + current_year }}</b></p>
								</td>
							</tr>
							<tr>
								<td width="50%">
									<p>Total Invoices</p>
									<p>Average / Invoice</p>
								</td>
								<td width="50%">
									<p class="text-right"><b>@{{ number_of_invoices }}</b></p>
									<p class="text-right"><b>@{{ average_sales | currency 'Rp ' }}</b></p>
								</td>
							</tr>
							<tr>
								<td width="50%">
									<p style="color: blue">Total Sales</p>
								</td>
								<td width="50%">
									<p class="text-right" style="color: blue"><b>@{{ total_sales | currency 'Rp ' }}</b></p>
								</td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Teams Summary</b>
					</div>
					<div class="panel-body">
						<table class="table table-striped" width="100%">
							<tbody>
								<tr v-for="team in sales_each_team | orderBy 'total_sales' -1">
									<td width="50%">
										<p>
											<b>@{{ team.team_name }}</b><br>
											<span style="color: blue; cursor: pointer">
												<small>Details</small>
											</span>
										</p>
									</td>
									<td width="50%">
										<p class="text-right">
											<b>@{{ team.total_sales | currency 'Rp ' }}</b><br>
											<small>(@{{ team.total_bonus | currency 'Rp ' }})</small>
										</p>
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
				<!-- START: BUTTON "BONUS EXPENSE" -->
				<div class="form-group" v-show="team_name != '-'">
					<a class="btn btn-danger form-control" role="button"
					   data-toggle="collapse" href="#collapseBonusExpense" aria-expanded="false" aria-controls="collapseBonusExpense">
						<b>Bonus Expense</b>
					</a>
				</div>
				<div class="collapse" id="collapseBonusExpense">
					<div class="panel panel-success">
						<div class="panel-body bg-danger">
							<table width="100%">
								<tr>
									<td width="50%">
										<p style="color: red">Tim</p>
										<p style="color: red">Supervisor</p>
									</td>
									<td width="50%">
										<p class="text-right" style="color: red"><b>@{{ staff1_bonus_expense | currency 'Rp ' }}</b></p>
										<p class="text-right" style="color: red"><b>@{{ staff2_bonus_expense | currency 'Rp ' }}</b></p>
									</td>
								</tr>
							</table>
						</div>
					</div>
				</div>
				<!-- END: BUTTON "BONUS EXPENSE" -->
				<div class="form-group">
					<button class="btn btn-info form-control"
						   v-on:click="print_report" :disabled="invoices.length <= 0">
						<span class="glyphicon glyphicon-print" aria-hidden="true" style="margin-right: 10px"></span>Print This Report
					</button>
				</div>
			</div>
			<div class="col-sm-9">
				<!-- START: PAGINATION BAR -->
				<div class="panel panel-default" v-show="invoices.length > 0">
					<div class="panel-body text-center">
						<button class="btn btn-default" v-on:click="fast_backward" :disabled="!prev_page_url">
							<span class="glyphicon glyphicon-fast-backward" aria-hidden="true"></span>
						</button>
						<button class="btn btn-default"v-on:click="backward" :disabled="!prev_page_url">
							<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span>
						</button>
						<span style="margin-left: 15px; margin-right: 15px">
							Page @{{ current_page }} of @{{ last_page }}
						</span>
						<button class="btn btn-default" v-on:click="forward" :disabled="!next_page_url">
							<span class="glyphicon glyphicon-arrow-right" aria-hidden="true"></span>
						</button>
						<button class="btn btn-default" v-on:click="fast_forward" :disabled="!next_page_url">
							<span class="glyphicon glyphicon-fast-forward" aria-hidden="true"></span>
						</button>
					</div>
				</div>
				<!-- END: PAGINATION BAR -->
				<table width="100%" class="table table-striped">
					<thead>
						<tr>
							<th class="text-center">ID</th>
							<th class="text-left">Invoice Date</th>
							<th class="text-left">Invoice No.</th>
							<th class="text-right">Total</th>
							<th class="text-center">Payment</th>
							<th class="text-center">Staff / Team</th>
							<th></th>
						</tr>
					</thead>
					<tbody>
						<tr is="invoice" v-for="invoice in invoices | orderBy 'created_at' -1"
							:invoice="invoice" :index="invoices.indexOf(invoice)">
						</tr>
					</tbody>
				</table>
				<div class="alert alert-danger text-center" v-show="invoices.length <= 0">
					No Sales Data
				</div>
			</div>
		</div>
		<!-- START: MODAL FOR INVOICE DETAILS -->
		<div id="invoice-details-modal" class="modal fade bs-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
			<div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header bg-primary">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<div class="text-center">
							No. Nota : <b>@{{ invoices[selected_index].invoice_no }}</b><br>
							Tanggal Nota : <b>@{{ invoices[selected_index].created_at }}</b>
						</div>
					</div>
					<div class="modal-body">
						<div class="row">
							<div class="col-xs-4">
								<div class="text-left">
									Staff : <b>@{{ invoices[selected_index].staff.firstname }} @{{ invoices[selected_index].staff.lastname }}</b>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="text-center">
									Total Item : <b>@{{ invoices[selected_index].transactions.length }}</b>
								</div>
							</div>
							<div class="col-xs-4">
								<div class="text-right">
									Customer : <b>@{{ invoices[selected_index].user.firstname }} @{{ invoices[selected_index].user.lastname }}</b>
								</div>
							</div>
						</div>
						<div class="row" style="margin-top: 15px">
							<div class="col-xs-12">
								<table width="100%" class="table table-striped">
									<thead style="background-color: #D1D2D4">
										<tr>
											<th width="5%">ID</th>
											<th width="50%">Description</th>
											<th width="5%">Qty</th>
											<th width="20%" class="text-right">Price @</th>
											<th width="20%" class="text-right">Sub-Total</th>
										</tr>
									</thead>
									<tbody>
										<tr v-for="item in invoices[selected_index].transactions">
											<td>@{{ item.id }}</td>
											<td>
												@{{ item.product.category.name | uppercase }}<br>
												@{{ item.product.name | uppercase }}
											</td>
											<td>@{{ item.qty }}</td>
											<td class="text-right">@{{ item.price | currency 'Rp ' }}</td>
											<td class="text-right">@{{ (item.qty * item.price) | currency 'Rp ' }}</td>
										</tr>
									</tbody>
								</table>
								<div class="text-right">
									Total : <b>@{{ invoices[selected_index].total | currency 'Rp ' }}</b><br>
									Paid : <b>@{{ invoices[selected_index].paid | currency 'Rp ' }}</b><br>
									Change : <b>@{{ invoices[selected_index].change | currency 'Rp ' }}</b>
								</div>
							</div>
						</div>
					</div>
					<div class="modal-footer bg-info">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
					</div>
				</div>
			</div>
		</div>
		<!-- END: MODAL FOR INVOICE DETAILS -->
	</div>
</div>

<template id="invoice-template">
	<tr>
		<td class="text-center">@{{ invoice.id }}</td>
		<td class="text-left">@{{ formatDate(invoice.created_at) }}</td>
		<td class="text-left">@{{ invoice.invoice_no }}</td>
		<td class="text-right">
			@{{ invoice.total | currency 'Rp ' }}<br>
			@{{ invoice.staff_bonus | currency 'Rp ' }}
		</td>
		<td class="text-center">@{{ invoice.payment_status }}</td>
		<td class="text-center">@{{ invoice.staff.firstname }} <small>@{{ '(' + invoice.staff.working_team[0].name + ')' }}<small></td>
		<td class="text-right">
			<button id="invoice-details" class="btn btn-info" data-toggle="modal" data-target="#invoice-details-modal" :data-index="index">
				<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span>
			</button>
			<button class="btn btn-danger" v-on:click="delete_invoice(index)">
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

    var channel = pusher.subscribe('invoice_channel');
    channel.bind('new_invoice_added', function(data) {
    	return adminSalesVue.get_invoices();
    });
</script>
<!-- END: PUSHER SCRIPT -->

<script>
	/* START: VUE SCRIPT */
	Vue.component('invoice', {
		template: '#invoice-template',
		props: [
			'invoice',
			'index'
		],
		methods: {
			formatDate: function(date) {
				return moment(date).format('DD/MM/YYYY (hh:mm A)');
			},
			delete_invoice: function(index) {
				this.$dispatch('invoice_deleted', index);
			}
		}
	});
	var adminSalesVue = new Vue({
		http: {
			headers: {
				'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
			}
		},
		el: '#adminSales',
		data: {
			// PAGINATION DATA
			number_of_invoices: '',
			per_page: '',
			current_page: '',
			last_page: '',
			next_page_url: '',
			prev_page_url: '',
			from: '',
			to: '',
			// VUE DATA
			current_month: '',
			current_year: '',
			sales_option: 'Today',
			invoices: [],
			invoices_for_printing: [],
			total_sales: '',
			staff1_bonus_expense: '',
			staff2_bonus_expense: '',
			sales_each_team: [],
			selected_index: '',
			teams: []
		},
		computed: {
			api_url: function() {
				return 'http://' + document.location.host + '/admin/sales/get_sales/' + this.sales_option;
			},
			average_sales: function() {
				if (this.total_sales > 0) {
					return this.total_sales / this.number_of_invoices;
				}
				else {
					return 0;
				}
			},
			first_page_url: function() {
				return this.api_url + '/?page=' + 1;
			},
			last_page_url: function() {
				return this.api_url + '/?page=' + this.last_page;
			},
			today_date: function() {
				return mainPageVue.day_date;
			}
		},
		watch: {
			'sales_option': function() {
				return this.get_invoices();
			},
			'page_number': function() {
				return this.get_invoices();
			}
		},
		methods: {
			rootFormatDate: function(date) {
				return moment(date).format('DD/MM/YYYY (hh:mm A)');
			},
			get_invoices: function(pagination_url) {
				if (!pagination_url) {
					return this.$http.get(this.api_url).then(
						(response) => {
							return [
								this.$set('number_of_invoices', response.data.invoices.total),
								this.$set('per_page', response.data.invoices.per_page),
								this.$set('current_page', response.data.invoices.current_page),
								this.$set('last_page', response.data.invoices.last_page),
								this.$set('next_page_url', response.data.invoices.next_page_url),
								this.$set('prev_page_url', response.data.invoices.prev_page_url),
								this.$set('from', response.data.invoices.from),
								this.$set('to', response.data.invoices.to),
								this.$set('invoices', response.data.invoices.data),

								this.$set('current_month', response.data.current_month),
								this.$set('current_year', response.data.current_year),
								this.$set('total_sales', response.data.total_sales),
								this.$set('staff1_bonus_expense', response.data.staff1_bonus_expense),
								this.$set('staff2_bonus_expense', response.data.staff2_bonus_expense),
								this.$set('sales_each_team', response.data.teams)
							];
						}
					);
				}
				else {
					return this.$http.get(pagination_url).then(
						(response) => {
							return [
								this.$set('number_of_invoices', response.data.invoices.total),
								this.$set('per_page', response.data.invoices.per_page),
								this.$set('current_page', response.data.invoices.current_page),
								this.$set('last_page', response.data.invoices.last_page),
								this.$set('next_page_url', response.data.invoices.next_page_url),
								this.$set('prev_page_url', response.data.invoices.prev_page_url),
								this.$set('from', response.data.invoices.from),
								this.$set('to', response.data.invoices.to),
								this.$set('invoices', response.data.invoices.data)
							];
						}
					);
				}
			},
			fast_backward: function() {
				return this.get_invoices(this.first_page_url);
			},
			backward: function() {
				return this.get_invoices(this.prev_page_url);
			},
			forward: function() {
				return this.get_invoices(this.next_page_url);
			},
			fast_forward: function() {
				return this.get_invoices(this.last_page_url);
			},
			handle_invoice_deleted: function(index) {
				var deleted_invoice_id = this.invoices[index].id;
				return this.$http.delete('http://' + document.location.host + '/admin/sales/delete_invoice/' + deleted_invoice_id).then(
					(response) => {
						return this.get_invoices();
					}
				);
			},
			print_report: function() {
				return this.$http.get('http://' + document.location.host + '/admin/sales/get_invoices_for_printing/' + this.sales_option).then(
					(response) => {
						return [
							this.$set('invoices_for_printing', data),
							window.print()
						];
					}
				);
			}
		},
		events: {
			'invoice_deleted': function(index) {
				return confirmDeleteFlash(
					index,
					'Are you sure ?',
					'This invoice is going to be deleted permanently :' + '\n\n' + this.invoices[index].invoice_no + ' (Total = Rp ' + this.invoices[index].total + ')'
				);
			}
		},
		ready: function() {
			return [
				this.get_invoices()
			];
		}
	});
	/* END: VUE SCRIPT */

	/* START: JQUERY SCRIPT */
	$(document).on('click', '#invoice-details', function() {
		var invoice_index = $(this).data('index');
		adminSalesVue.$data.selected_index = invoice_index;
		$('#invoice-details-modal').modal('show');
	});
	/* END: JQUERY SCRIPT */

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
				adminSalesVue.handle_invoice_deleted(index);
				swal('Done !', 'Data has been deleted', 'success');
			}
			else {
				swal('Cancelled', 'Data has not been deleted', 'error');
			}
		});
	}
	/* END: CONFIRM FLASH SCRIPT */
</script>

@stop
