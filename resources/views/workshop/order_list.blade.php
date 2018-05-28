@extends('app')

@section('content')

<div id="orderlist">
	<div class="panel panel-default">
		<div class="panel-body">
			<div class="row">
				<div class="col-xs-3 text-center">
					Total Order Masuk : <b>@{{ total_order }}</b>
				</div>
				<div class="col-xs-3 text-center">
					Pending : <b>-</b>
				</div>
				<div class="col-xs-3 text-center">
					Sedang dikerjakan : <b>-</b>
				</div>
				<div class="col-xs-3 text-center">
					Selesai : <b>-</b>
				</div>
			</div>
		</div>
	</div>
	<table class="table table-striped" width="100%">
		<thead>
			<tr>
				<th class="text-left">ID #</th>
				<th class="text-left">No. Order & Customer</th>
				<th class="text-left">Order Tags</th>
				<th class="text-right">Tanggal Masuk</th>
				<th class="text-right">Tanggal Deadline</th>
				<th class="text-right">Staff / Team</th>
				<th class="text-right">Status</th>
			</tr>
		</thead>
		<tbody>
			<tr is="orderlist" v-for="order in orders | orderBy 'deadline'"
				:order="order">
			</tr>
		</tbody>
	</table>
</div>

@include('includes/vue_templates/orderlist_template')

@stop

@section('js')

<script type="text/javascript" src="/js/order_list_vue.js"></script>

@stop
