@extends('app')

@section('content')

<div class="container-fluid">
	<table width="100%" style="margin-bottom: 20px">
		<tr>
			<td width="50%">
				<p class="text-left">
					Category Name : <b>{{ $category_name }}</b>
				</p>
			</td>
			<td width="50%">
				<p class="text-right">
					<a href="{{ URL::action('AdminPagesController@categories_index') }}">
						<button class="btn btn-danger">Back to Categories</button>
					</a>
				</p>
			</td>
		</tr>
	</table>

	<div id="productsController">
		<div class="panel panel-info">
			<div class="panel-heading">
				<b>Add New Product</b>
			</div>
			<div class="panel-body">
				<form method="POST" v-on:submit.prevent="addNewProduct">
					<input type="hidden" id="token" name="_token" value="{!! csrf_token() !!}">
					<div class="row">
						<div class="col-md-10">
							<div class="form-group">
								<label>Product Name <small style="color: red" v-show="!newProduct.name">* required</small></label>
								<input type="text" name="name" class="form-control" v-model="newProduct.name">
							</div>
						</div>
						<div class="col-md-2">
							<div class="form-group">
								<label>Price Level</label>
								<select class="form-control" v-model="setPriceLevel" :disabled="!newProduct.name">
									<option value="">Choose Level</option>
									<option value="1">1-Level</option>
									<option value="2">2-Levels</option>
									<option value="3">3-Levels</option>
									<option value="4">4-Levels</option>
								</select>
							</div>
						</div>
					</div>
					<div class="row" v-show="setPriceLevel">
						<div class="col-md-3">
							<div class="panel panel-info" v-show="setPriceLevel >= 1">
								<div class="panel-heading">
									<h3 class="panel-title text-center">
									Level 1
									</h3>
								</div>
								<div class="panel-body">
									<div class="form-group">
										<label>Price</label>
										<input type="text" name="price1" class="form-control" v-model="newProduct.price1">
									</div>
									<div class="form-group">
										<label>Minimum Qty</label>
										<input type="text" name="qty1" class="form-control" v-model="newProduct.qty1">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-info" v-show="setPriceLevel >= 2">
								<div class="panel-heading">
									<h3 class="panel-title text-center">
									Level 2
									</h3>
								</div>
								<div class="panel-body">
									<div class="form-group">
										<label>Price</label>
										<input type="text" name="price2" class="form-control" v-model="newProduct.price2">
									</div>
									<div class="form-group">
										<label>Minimum Qty</label>
										<input type="text" name="qty2" class="form-control" v-model="newProduct.qty2">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-info" v-show="setPriceLevel >= 3">
								<div class="panel-heading">
									<h3 class="panel-title text-center">
										Level 3
									</h3>
								</div>
								<div class="panel-body">
									<div class="form-group">
										<label>Price</label>
										<input type="text" name="price3" class="form-control" v-model="newProduct.price3">
									</div>
									<div class="form-group">
										<label>Minimum Qty</label>
										<input type="text" name="qty3" class="form-control" v-model="newProduct.qty3">
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-3">
							<div class="panel panel-info" v-show="setPriceLevel >= 4">
								<div class="panel-heading">
									<h3 class="panel-title text-center">
									Level 4
									</h3>
								</div>
								<div class="panel-body">
									<div class="form-group">
										<label>Price</label>
										<input type="text" name="price4" class="form-control" v-model="newProduct.price4">
									</div>
									<div class="form-group">
										<label>Minimum Qty</label>
										<input type="text" name="qty4" class="form-control" v-model="newProduct.qty4">
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="row" v-show="!editButton">
						<div class="col-sm-12">
							<button type="submit" class="btn btn-primary form-control" :disabled="!newProduct.name">Add this Product</button>
						</div>
					</div>
					<div class="row" v-show="editButton">
						<div class="col-sm-6">
							<div class="form-group">
								<button class="btn btn-info form-control" :disabled="!newProduct.name" v-on:click.prevent="updateProduct(newProduct.id)">Update this Product</button>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<button class="btn btn-warning form-control" v-show="editButton" v-on:click.prevent="cancelButton">Cancel</button>
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>

		<!-- FLASH MESSAGES -->
		<div class="alert alert-danger text-center" v-show="deleteFlash">
			<b>@{{ deleteMessage }}</b>
		</div>
		<!-- END: FLASH MESSAGES -->

		<legend><b>Added Products</b> <small>(Total : @{{ products.length }})</small></legend>
		<table class="table table-striped">
			<thead style="background-color: #D1D2D4">
				<th>ID<br>#</th>
				<th>Product Name</th>
				<th>
					<table width="100%">
						<tr>
							<p class="text-center">Level 1</p>
						</tr>
						<tr class="text-center">
							<th class="text-center">Price (Min. Qty)</th>
						</tr>
					</table>
				</th>
				<th>
					<table width="100%">
						<tr>
							<p class="text-center">Level 2</p>
						</tr>
						<tr>
							<th class="text-center">Price (Min. Qty)</th>
						</tr>
					</table>
				</th>
				<th>
					<table width="100%">
						<tr>
							<p class="text-center">Level 3</p>
						</tr>
						<tr>
							<th class="text-center">Price (Min. Qty)</th>
						</tr>
					</table>
				</th>
				<th>
					<table width="100%">
						<tr>
							<p class="text-center">Level 4</p>
						</tr>
						<tr>
							<th class="text-center">Price (Min. Qty)</th>
						</tr>
					</table>
				</th>
				<th></th>
			</thead>
			<tbody>
				<tr v-for="product in products | orderBy 'name'">
					<td>
						@{{ product.id }}
					</td>
					<td>
						<small>@{{ product.category.name | uppercase }}</small><br>
						<span style="color: blue">@{{ product.name | uppercase }}</span>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td class="text-center">
									@{{ product.price1 | currency 'Rp '}} (@{{ product.qty1 }})
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td class="text-center">
									@{{ product.price2 | currency 'Rp '}} (@{{ product.qty2 }})
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td class="text-center">
									@{{ product.price3 | currency 'Rp '}} (@{{ product.qty3 }})
								</td>
							</tr>
						</table>
					</td>
					<td>
						<table width="100%">
							<tr>
								<td class="text-center">
									@{{ product.price4 | currency 'Rp '}} (@{{ product.qty4 }})
								</td>
							</tr>
						</table>
					</td>
					<td class="text-right">
						<a style="margin-right: 10px; cursor: pointer">
							<small>Price History</small>
						</a>
						<button class="btn btn-info" v-on:click="editProduct(product.id)">
							<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
						</button>
						<button class="btn btn-danger" v-on:click="deleteProduct(product.id, product.name)">
							<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
						</button>
					</td>
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
	var productsControllerVue = new Vue({
		http: {
	  		root: '/root',
	  		headers: {
	    		'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
	  		}
		},

		el: '#productsController',

		data: {
			setPriceLevel: '',
			// USED FOR ADDING A NEW PRODUCT
			newProduct: {
				name: '',
				price1: '',
				qty1: '',
				price2: '',
				qty2: '',
				price3: '',
				qty3: '',
				price4: '',
				qty4: ''
			},
			// USED FOR UPDATING A PRODUCT
			product_id: '',
			// USED FOR STORING JSON FETCHED FROM SERVER
			products: [],
			editButton: false,
			deleteFlash: false,
			deleteMessage: ''
		},

		methods: {
			getProducts: function() {
				return this.$http.get(document.URL + '/all').then(
					(response) => {
						return this.$set('products', response.data);
					},
					(response) => {
						return this.getProducts();
					}
				);
			},
			addNewProduct: function() {
				var product = this.newProduct;

				this.newProduct = {
					name: '',
					price1: '',
					qty1: '',
					price2: '',
					qty2: '',
					price3: '',
					qty3: '',
					price4: '',
					qty4: ''
				};
				this.setPriceLevel = '';

				return this.$http.post(document.URL + '/add', product).then(
					(response) => {
						return this.getProducts();
					}
				);
			},
			editProduct: function(id) {
				this.editButton = true;

				return this.$http.get(document.URL + '/show/' + id).then(
					(response) => {
						this.product_id = response.data.id;
						this.newProduct.name = response.data.name;
						this.newProduct.price1 = response.data.price1;
						this.newProduct.qty1 = response.data.qty1;
						this.newProduct.price2 = response.data.price2;
						this.newProduct.qty2 = response.data.qty2;
						this.newProduct.price3 = response.data.price3;
						this.newProduct.qty3 = response.data.qty3;
						this.newProduct.price4 = response.data.price4;
						this.newProduct.qty4 = response.data.qty4;

						// TO SETUP LEVEL FOR PRICE & QTY INPUT FIELDS
						if (this.newProduct.price1 > 0) {
							if (this.newProduct.price2 > 0) {
								if (this.newProduct.price3 > 0) {
									if (this.newProduct.price4 > 0) {
										return this.setPriceLevel = 4;
									}
									else {
										return this.setPriceLevel = 3;
									}
								}
								else {
									return this.setPriceLevel = 2;
								}
							}
							else {
								return this.setPriceLevel = 1
							}
						}
					});
			},
			updateProduct: function() {
				var product_id = this.product_id;
				var product_data = this.newProduct;

				return savePriceToHistory(product_id, product_data);
			},
			cancelButton: function() {
				return [
					this.newProduct = {
						id: '',
						name: '',
						price1: '',
						qty1: '',
						price2: '',
						qty2: '',
						price3: '',
						qty3: '',
						price4: '',
						qty4: ''
					},
					this.setPriceLevel = '',
					this.editButton = false,
				];
			},
			deleteProduct: function(id, name) {
				var confirmBox = confirm('Are you sure want to delete this product ?\n' + name);
				if (confirmBox) {
					return this.$http.delete(document.URL + '/delete/' + id).then(
						(response) => {
							var self = this;
							this.deleteFlash = true;
							this.deleteMessage = 'Product : ' + name + ' (' + id + ') Has Been Deleted';

							return [
								setTimeout(function() {
									self.deleteFlash = false
								}, 4000),
								this.getProducts()
							];
						}
					);
				}
				else {
					// DO NOTHING
				}
			}
		},

		ready: function() {
			this.getProducts();
		}
	});

	function savePriceToHistory(product_id, product_data)
	{
		return swal({
			title: 'Would you like to save this price in history ?',
			text: 'By saving to history, you can track price history in the future.',
			type: 'info',
			showCancelButton: true,
			confirmButtonColor: '#00ADEF',
			confirmButtonText: 'OK',
			cancelButtonText: 'No',
			closeOnConfirm: false
		}, function(isConfirm) {
			if (isConfirm) {
				productsControllerVue.$data.product_id = '';
				productsControllerVue.$data.newProduct = {
					category_id: '',
					name: '',
					price1: '',
					qty1: '',
					price2: '',
					qty2: '',
					price3: '',
					qty3: '',
					price4: '',
					qty4: ''
				};
				productsControllerVue.$data.setPriceLevel = '';
				productsControllerVue.$data.editButton = false;

				productsControllerVue.$http.post(document.URL + '/save_history/' + product_id).then(function(OK) {
					productsControllerVue.$http.patch(document.URL + '/update/' + product_id, product_data);
					return productsControllerVue.getProducts();
				});
				swal('Done', 'The price you updated has been added to history.', 'success');
			}
			else {
				productsControllerVue.$data.product_id = '';
				productsControllerVue.$data.newProduct = {
					category_id: '',
					name: '',
					price1: '',
					qty1: '',
					price2: '',
					qty2: '',
					price3: '',
					qty3: '',
					price4: '',
					qty4: ''
				};
				productsControllerVue.$data.setPriceLevel = '';
				productsControllerVue.$data.editButton = false;

				productsControllerVue.$http.patch(document.URL + '/update/' + product_id, product_data).then(function(OK) {
					return productsControllerVue.getProducts();
				});
			}
		});
	}
</script>

@stop
