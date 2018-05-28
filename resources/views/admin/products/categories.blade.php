@extends('app')

@section ('content')

<div class="container-fluid">
	<div id="categoriesController">
		<div class="row">
			<div class="col-sm-3">
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Add New Category</b>
					</div>
					<div class="panel-body">
						<form method="POST" v-on:submit.prevent="addNewCategory">
							<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
							<div class="form-group">
								<label>Category Name <small style="color: red" v-show="!newCategory.name">* required</small></label>
								<input type="text" name="category_name" class="form-control" v-model="newCategory.name">
							</div>
							<div class="form-group">
								<label>Additional Options</label>
								<select name="option_id" class="form-control" v-model="newCategory.option_id" :disabled="!newCategory.name">
									<option value="">No Additional Options</option>
									<option value="@{{ category.id }}" v-for="category in categories">
										@{{ category.name | uppercase }}
									</option>
								</select>
							</div>
							<div class="form-group">
								<label>Staff's Bonus</label>
								<table width="100%">
									<tr>
										<td width="90%">
											<input type="text" name="staff_bonus" class="form-control"
												   v-model="newCategory.staff_bonus" :disabled="!newCategory.name">
										</td>
										<td width="10%">
											<span style="font-size: 180%; margin-left: 7px"><b>%</b></span>
										</td>
									</tr>
								</table>
							</div>
							<br>
							<div class="form-group">
								<button type="submit" class="btn btn-success form-control"
										:disabled="!newCategory.name" v-show="!editStatus">
									Add this Category
								</button>
							</div>
							<div class="form-group">
								<button class="btn btn-info form-control"
										:disabled="!newCategory.name" v-show="editStatus" v-on:click="updateCategory(newCategory.id)">
									Update this Category
								</button>
							</div>
						</form>
						<div class="form-group">
							<button class="btn btn-danger form-control"
									v-show="editStatus" v-on:click="cancelUpdate">
								Cancel
							</button>
						</div>
					</div>
				</div>
				<div class="panel panel-info">
					<div class="panel-heading">
						<b>Info</b>
					</div>
					<div class="panel-body">
						<p>
							Total Categories :<br>
							<b>@{{ totalCategories }}</b>
						</p>
						<p>
							Total Products :<br>
							<b>@{{ totalProducts }}</b>
						</p>
					</div>
				</div>
			</div>
			<div class="col-sm-9">
				<table class="table table-striped">
					<thead style="background-color: #D1D2D4">
						<th>
							ID #
						</th>
						<th>Category Name</th>
						<th>Products</th>
						<th>Option</th>
						<th class="text-right">Bonus (%)</th>
						<th class="text-right">Created</th>
						<th class="text-right">Last Update</th>
						<th></th>
					</thead>
					<tbody>
						<tr v-for="category in categories | orderBy 'name'">
							<td>@{{ category.id }}</td>
							<td style="color: blue">@{{ category.name | uppercase }}</td>
							<td>@{{ category.products.length }}</td>
							<td>@{{ category.option_id }}</td>
							<td class="text-right">@{{ category.staff_bonus }}</td>
							<td class="text-right">@{{ momentFormat(category.created_at) }}</td>
							<td class="text-right">@{{ momentFormat(category.updated_at) }}</td>
							<td class="text-right">
								<button class="btn btn-info" v-on:click="editCategory(category.id)">
									<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
								</button>
								<button class="btn btn-danger" v-on:click="deleteCategory(category.id, category.name)">
									<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
								</button>
								<a href="../products/categories/@{{ category.id }}/manage">
									<button class="btn btn-success">
										Manage
									</button>
								</a>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

@stop

@section('js')
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/libraries/moment/moment.js"></script>
<script>
	new Vue({
		http: {
	  		root: '/root',
	  		headers: {
	    		'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
	  		}
		},
		el: '#categoriesController',
		data: {
			sortBy: '',
			categories: [],
			// VARIABLE FOR INPUT FIELD
			newCategory: {
				id: '',
				name: '',
				option_id: '',
				staff_bonus: ''
			},
			deleteMessage: '',
			editStatus: false,
			totalCategories: '',
			totalProducts: ''
		},
		methods: {
			getCategories: function() {
				return this.$http.get('/admin/products/categories/all').then(
					(response) => {
						return [
							this.$set('categories', response.data.categories),
							this.$set('totalCategories', response.data.total_categories),
							this.$set('totalProducts', response.data.total_products)
						];
					},
					(response) => {
						return this.getCategories();
					}
				);
			},
			addNewCategory: function() {
				var category = this.newCategory;
				this.newCategory = {
					id: '',
					name: '',
					option_id: ''
				};

				return this.$http.post('/admin/products/categories/add', category).then(
					(response) => {
						return this.getCategories();
					}
				);
			},
			editCategory: function(id) {
				this.editStatus = true;

				return this.$http.get('/admin/products/categories/' + id).then(
					(response) => {
						return [
							this.newCategory.id = response.data.id,
							this.newCategory.name = response.data.name,
							this.newCategory.option_id = response.data.option_id,
							this.newCategory.staff_bonus = response.data.staff_bonus
						];
				});
			},
			updateCategory: function(id) {
				var category = this.newCategory;
				this.newCategory = {
					id: '',
					name: '',
					option_id: '',
					staff_bonus: ''
				};

				return this.$http.patch('/admin/products/categories/' + id, category).then(
					(response) => {
						return [
							this.editStatus = false,
							this.getCategories()
						];
					}
				);
			},
			deleteCategory: function(id, name) {
				var confirmBox = confirm('Are you sure want to delete this category ?\n' + name);

				if (confirmBox) {
					return this.$http.delete('/admin/products/categories/' + id + '/delete').then(
						(response) => {
							var self = this;
							this.deleteFlash = true;
							this.deleteMessage = 'Category ' + name + ' (' + id + ') Has Been Deleted';

							return [
								setTimeout(function() {
									self.deleteFlash = false
								}, 4000),
								this.getCategories()
							];
						}
					);
				}
			},
			momentFormat: function(date) {
				return moment(date).format('DD/MM/YYYY');
			},
			cancelUpdate: function() {
				return [
					this.newCategory = {
						id: '',
						name: '',
						option_id: '',
						staff_bonus: ''
					},
					this.editStatus = false
				];
			}
		},
		ready: function() {
			this.getCategories()
		}
	});
</script>

@stop
