@extends('app')

@section('content')

<div class="container">
	<div id="bonus-table">
		<table width="100%" class="table table-striped">
			<thead style="background-color: #D1D2D4">
				<th>ID #</th>
				<th>Nama Kategori</th>
				<th class="text-right">Bonus Penjualan</th>
			</thead>
			<tbody>
				<tr v-for="category in categories | orderBy 'name'">
					<td>@{{ category.id }}</td>
					<td>@{{ category.name | uppercase}}</td>
					<td class="text-right">@{{ category.staff_bonus }} %</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script>
	var bonusTableVue = new Vue({
		el: '#bonus-table',
		data: {
			categories: []
		},
		methods: {
			getCategories: function() {
				return this.$http.get('http://' + document.location.host + '/staff/bonus_table/categories').then(
					(response) => {
						return this.$set('categories', response.data);
					},
					(response) => {
						return this.getCategories();
					}
				);
			}
		},
		ready: function() {
			this.getCategories();
		}
	});
</script>

@stop