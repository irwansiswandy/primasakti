@extends('app')

@section('content')

<div id="adminDashboard">
	<div class="row">
		<!-- START: TODAY SALES -->
		<div class="col-sm-3">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<b>Today Sales</b>
				</div>
				<div class="panel-body">
					<p class="text-left">
						<a href="{{ URL::action('AdminPagesController@sales') }}">
							<small>View Details</small>
						</a>
					</p>
					<div class="row">
						<div class="col-xs-6">
							<p class="text-left">
								<b>Sales : </b>
							</p>
						</div>
						<div class="col-xs-6">
							<p class="text-right">
								<b>@{{ today_sales_total | currency 'Rp ' }}</b>
							</p>
						</div>
					</div>
					<div style="width: 100%; height: 522px; overflow-y: scroll; border-style: solid; border-width: 1px; border-color: #D1D2D4">
						<table class="table table-stripped"
							   style="font-size: 75%">
							<tbody>
								<tr v-for="invoice in today_sales.invoices | orderBy 'created_at' -1">
									<td class="text-left">
										@{{ formatTime(invoice.created_at) }}<br>
										@{{ invoice.staff.firstname }}
										<div class="label label-info" v-show="invoice.staff.user_level == 'STAFF'">@{{ invoice.staff.working_team[0].name }}</div>
										<div class="label label-default" v-show="invoice.staff.user_level == 'SUPERVISOR'">@{{ invoice.staff.user_level }}</div>
									</td>
									<td class="text-right">
										@{{ invoice.total | currency 'Rp ' }}
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<!-- END: TODAY SALES -->
		<!-- START: CHART -->
		<div class="col-sm-9">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<b>This Month Sales Chart</b>
				</div>
				<div class="panel-body">
					<p class="text-left">
						<a href="{{ URL::action('AdminPagesController@sales') }}">
							<small>View Details</small>
						</a>
					</p>
					<canvas id="dailySalesChart"></canvas>
					<div style="margin-top: 15px">
						<p class="text-right">
							<small>
								Start Date : @{{ formatDate(daily_sales.start_date.date) }}<br />
								End Date : @{{ formatDate(daily_sales.end_date.date) }}
							</small>
						</p>
					</div>
				</div>
			</div>
		</div>
		<!-- END: CHART -->
	</div>
</div>

@stop

@section('js')

<script type="text/javascript" src="/libraries/chartjs/dist/Chart.js"></script>
<script type="text/javascript" src="https://js.pusher.com/3.2/pusher.min.js"></script>
<script type="text/javascript" src="/js/initialize_pusher.js"></script>
<script type="text/javascript" src="/js/helpers.js"></script>
<script type="text/javascript" src="/js/admin_dashboard_vue.js"></script>

<!-- START: PUSHER SCRIPT -->
<script>
	var channel = pusher.subscribe('primasakti_channel');
    
    channel.bind('new_invoice_added', function(data) {
    	return [
    		adminDashboardVue.today_sales.invoices.push(data),
    		adminDashboardVue.handle_sales_updated('add', data)
    	];
    });

    channel.bind('invoice_deleted', function(data) {
    	var invoice_index = findWithAttribute(adminDashboardVue.today_sales.invoices, 'id', data.id);
    	if (invoice_index < 0) {
    		return adminDashboardVue.handle_sales_updated('delete', data);
    	}
    	else {
    		return [
    			adminDashboardVue.today_sales.invoices.splice(invoice_index, 1),
    			adminDashboardVue.handle_sales_updated('delete', data)
    		];
    	}
    });
</script>
<!-- END: PUSHER SCRIPT -->

<!-- START: CHARTJS SCRIPT -->
<script>
	/* START: INITIALIZE CHARTS */
	var chartSection = document.getElementById('dailySalesChart');
	var dailySalesChart = new Chart(chartSection, {
	    type: 'line',
	    data: {
	        labels: [],
	        datasets: [{
	           	label: 'Daily Sales (IDR)',
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
	                    beginAtZero:true
	                }
	            }]
	        }
	    }
	});
	/* END: INTIALIZE CHARTS */

	function set_dailySalesChart_data(passed_labels, passed_data) {
		for (i=0; i<passed_labels.length; i++) {
			dailySalesChart.data.labels.push(passed_labels[i]);
		}
		for (i=0; i<passed_data.length; i++) {
			dailySalesChart.data.datasets[0].data.push(passed_data[i]);
		}
		return dailySalesChart.update();
	}

	function update_dailySalesChart_data(index, passed_total) {
		dailySalesChart.data.datasets[0].data[index] = Number(dailySalesChart.data.datasets[0].data[index]) + Number(passed_total);
		return dailySalesChart.update();
	}
</script>
<!-- END: CHARTJS SCRIPT -->

@stop
