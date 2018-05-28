@extends('app')

@section('content')

<div id="priceListController">
	<div class="container">
		<div class="hidden-print">
			<p class="text-right">
				<!--
				<button class="btn btn-info">Save to PDF</button>
				-->
				<button class="btn btn-success" v-on:click="print">
					<span class="glyphicon glyphicon-print" aria-hidden="true"></span> Print
				</button>
			</p>
		</div>
	</div>
	<div class="container">
		<div class="text-center" style="margin-bottom: 10px">
			<span style="color: red">
				<small>*) Daftar harga bersifat tidak mengikat, bisa berubah sewaktu-waktu tanpa pemberitahuan terlebih dahulu.</small>
			</span>
		</div>
		<div v-for="category in categories | orderBy 'name'">
			<legend><b>@{{ category.name | uppercase }}</b></legend>
			<table class="table table-striped" style="width: 100%">
				<thead style="background-color: #D1D2D4">
					<tr style="font-size: 13px">
						<th style="width: 5%">ID #</th>
						<th style="width: 53%">NAMA PRODUK</th>
						<th style="width: 14%" class="text-right">HARGA 1 (QTY)</th>
						<th style="width: 14%" class="text-right">HARGA 2 (QTY)</th>
						<th style="width: 14%" class="text-right">HARGA 3 (QTY)</th>
					</tr>
				</thead>
				<tbody>
					<tr v-for="product in category.products | orderBy 'name'" style="font-size: 13px">
						<td style="width: 5%"><b>@{{ product.id }}</b></td>
						<td style="width: 53%">@{{ category.name | uppercase }} : <b>@{{ product.name | uppercase }}</b></td>
						<td style="width: 14%" class="text-right"><b>@{{ product.price1 | currency 'Rp ' }}</b> (< @{{ product.qty2 }})</td>
						<td style="width: 14%" class="text-right"><b>@{{ product.price2 | currency 'Rp ' }}</b> (< @{{ product.qty3 }})</td>
						<td style="width: 14%" class="text-right"><b>@{{ product.price3 | currency 'Rp ' }}</b> (>= @{{ product.qty3 }})</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>

<script>
	var priceListVue = new Vue({
		el: '#priceListController',
		data: {
			categories: []
		},
		methods: {
			getPriceList: function() {
				return this.$http.get('http://' + document.location.host + '/price_list/all').then(
					(response) => {
						return this.$set('categories', response.data);
					},
					(response) => {
						return this.getPriceList();
					}
				);
			},
			print: function() {
				return window.print();
			}
		},
		ready: function() {
			this.getPriceList();
		}
	});
</script>

@stop
