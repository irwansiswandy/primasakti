@extends('app')

@section('css')

<meta name="csrf-token" content="{{ csrf_token() }}">

@stop

@section('content')

<div id="staff-dashboard">
	<div class="row">
		<div class="col-sm-3">
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6 text-left">
							<b>Info Staff</b>
						</div>
						<div class="col-xs-6 text-right">
							<a href="{{ action('StaffPagesController@profile', Auth::id()) }}">
								<small>Lihat Profile</small>
							</a>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<div class="row">
						<div class="col-xs-4">
							<p class="text-right">Nama / ID :</p>
							<p class="text-right">Level :</p>
							<p class="text-right">Nama Tim :</p>
							<p class="text-right">Total Tim :</p>
							<p class="text-right">Anggota :</p>
						</div>
						<div class="col-xs-8">
							<p>
								<b>@{{ info_staff.firstname + ' ' + info_staff.lastname }} / @{{ info_staff.id }}</b>
							</p>
							<p>
								<b>@{{ info_staff.user_level }}</b>
							</p>
							<p>
								<b>@{{ team_name }}</b>
							</p>
							<p>
								<b>@{{ total_team_members }}</b>
							</p>
							<p>
								<span v-for="member in info_staff.working_team[0].staff" style="display: block">
									<b>@{{ member.firstname + ' ' + member.lastname }}</b>
								</span>
								<span v-show="info_staff.working_team.length < 1">
									<b>-</b>
								</span>
							</p>
						</div>
					</div>
				</div>
			</div>
			{{-- SALES CHART --}}
			<div class="panel panel-default">
				<div class="panel-heading">
					<div class="row">
						<div class="col-xs-6 text-left">
							<b>@{{ this_month + ' ' + this_year }}</b>
						</div>
						<div class="col-xs-6 text-right">
							<a href="{{ action('StaffPagesController@sales', Auth::id()) }}">
								<small>Lihat Penjualan</small>
							</a>
						</div>
					</div>
				</div>
				<div class="panel-body">
					<canvas id="salesPerTeamChart" width="100%" height="50%"></canvas>
				</div>
			</div>
			{{-- END: SALES CHART --}}
			{{-- ACTION BUTTONS --}}
			<div class="row">
				<div class="col-xs-6">
					<div class="form-group">
						<a href="{{ URL::action('WorkshopController@order_in') }}">
							<button class="btn btn-info form-control">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true" style="margin-right: 5px"></span> Terima Order
							</button>
						</a>
					</div>
					<div class="form-group">
						<a href="{{ URL::action('StaffPagesController@pos_system', Auth::user()->id) }}">
							<button class="btn btn-primary form-control">
								<span class="glyphicon glyphicon-usd" aria-hidden="true" style="margin-right: 5px"></span> Nota (POS)
							</button>
						</a>
					</div>
				</div>
				<div class="col-xs-6">
					<div class="form-group">
						<a href="{{ URL::action('StaffPagesController@price_list') }}">
							<button class="btn btn-warning form-control">
								<span class="glyphicon glyphicon-list" aria-hidden="true" style="margin-right: 5px"></span> Daftar Harga
							</button>
						</a>
					</div>
					<div class="form-group">
						<a href="{{ URL::action('StaffPagesController@bonus_table') }}">
							<button class="btn btn-success form-control">
								<span class="glyphicon glyphicon-list" aria-hidden="true" style="margin-right: 5px"></span> Tabel Bonus
							</button>
						</a>
					</div>
				</div>
			</div>
			{{-- END: ACTION BUTTONS --}}
		</div>
		<div class="col-sm-9">
			<div id="orderlist">
				<table class="table table-striped" style="font-size: 95%">
					<thead>
						<tr>
							<th class="text-left">#</th>
							<th class="text-left">Customer / No. Order</th>
							<th class="text-left">Tags</th>
							<th class="text-right">Tgl Masuk</th>
							<th class="text-right">Tgl Deadline</th>
							<th class="text-right">Staff / Team</th>
							<th class="text-right">Status</th>
							<th></th>
						</tr>
					</thead>
					  <tbody>
							  <tr is="orderlist" v-for="order in orders" v-on:testbroadcasted="handle_test_broadcasted"
								  :order="order" orderdetails="show" actions="show" staffid="{{ Auth::id() }}"></tr>
						</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@include('includes/vue_templates/orderlist_template')

@stop

@section('js')

<script type="text/javascript" src="/js/vue.js"></script>
<script type="text/javascript" src="/js/vue-resource.js"></script>
<script type="text/javascript" src="/libraries/moment/moment.js"></script>
<script type="text/javascript" src="/libraries/chartjs/dist/Chart.js"></script>
<script type="text/javascript" src="https://js.pusher.com/3.2/pusher.min.js"></script>

<script type="text/javascript" src="/js/order_list_vue.js"></script>
<script type="text/javascript" src="/js/helpers.js"></script>
<script type="text/javascript" src="/js/staff_dashboard_vue.js"></script>

<!-- START: PUSHER SCRIPT -->
<script>
	/* INITIALIZE PUSHER */
	var pusher = new Pusher('937c40c45b8b04b84c49', {
      cluster: 'ap1',
      encrypted: true
    });

    var channel = pusher.subscribe('primasakti_channel');
    
    channel.bind('new_invoice_added', function(data) {
    	return staffDashboardVue.handle_sales_updated(data);
    });
    channel.bind('invoice_deleted', function(data) {
    	return staffDashboardVue.handle_invoice_deleted(data);
    });

    channel.bind('new_order_added', function(data) {
    	return orderlistVue.orders.push(data);
    });
</script>
<!-- END: PUSHER SCRIPT -->

<!-- START: CHARTJS SCRIPT -->
<script>
	/* START: INITIALIZE CHARTS */
	var chartSection = document.getElementById('salesPerTeamChart');
	var monthlySalesChart = new Chart(chartSection, {
	    type: 'bar',
	    data: {
	        labels: [],
	        datasets: [{
	           	label: 'Penjualan (Rp)',
	            data: [],
	            backgroundColor: 'rgba(54, 162, 235, 0.2)',
	            borderColor: 'rgba(54, 162, 235, 1)',
	            borderWidth: 2
	        }]
	    },
	    options: {
	        scales: {
	            yAxes: [{
	                ticks: {
	                    beginAtZero: true
	                }
	            }]
	        }
	    }
	});
	/* END: INTIALIZE CHARTS */

	function set_monthlySalesChart_data(passed_labels, passed_data) {
		for (i=0; i<passed_labels.length; i++) {
			monthlySalesChart.data.labels.push(passed_labels[i]);
		}
		for (i=0; i<passed_data.length; i++) {
			monthlySalesChart.data.datasets[0].data.push(passed_data[i]);
		}
		return monthlySalesChart.update();
	}

	function update_monthlySalesChart_data(index, passed_total) {
		monthlySalesChart.data.datasets[0].data[index] = Number(monthlySalesChart.data.datasets[0].data[index]) + Number(passed_total);
		return monthlySalesChart.update();
	}
</script>
<!-- END: CHARTJS SCRIPT -->

@stop
