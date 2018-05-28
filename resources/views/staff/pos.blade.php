@extends('app')

@section('css')

<link href="/css/pos_system.css" rel="stylesheet">

@stop

@section('content')

<div id="posSystem">
	<!-- START: INVOICE DISPLAY (ONLY SHOWN WHEN PRINTING) -->
	<div id="printed-invoice" class="visible-print-block">
		<p class="text-center">
			PRIMASAKTI - DIGITAL COPY & PRINT SHOP<br>
			Jl. Raya Tenggilis No. 34 - 34A, Surabaya 60292<br>
			Jawa Timur - Indonesia | Telp : (031) 8484808<br>
			E-mail : primasakti_copycenter@yahoo.com
		</p>
		<p class="text-center">
			No. Nota : @{{ invoiceCode }}<br>
			Tanggal : @{{ todayDate }}<br>
			Staff : @{{ staff.firstname + ' ' + staff.lastname }}
		</p>
		<table width="100%">
			<tbody>
				<tr v-for="item in itemListForInvoice">
					<td class="text-left">
						<p>
							@{{ item.category | uppercase }}<br>
							@{{ item.product | uppercase }}<br>
							<span v-show="item.option">@{{ item.option | uppercase }}<br></span>
							(@{{ item.qty }} x @{{ item.price | currency 'Rp ' }})
						</p>
					</td>
					<td class="text-right">
						<p>@{{ item.sub_total | currency 'Rp ' }}</p>
					</td>
				</tr>
			</tbody>
		</table>
		<table width="100%">
			<tbody>
				<tr>
					<td width="60%" class="text-right">TOTAL : </td>
					<td width="40%" class="text-right">@{{ total | currency 'Rp ' }}</td>
				</tr>
				<tr v-show="paymentStatus=='cash'">
					<td width="60%" class="text-right">BAYAR : </td>
					<td width="40%" class="text-right">@{{ paid | currency 'Rp ' }}</td>
				</tr>
				<tr v-show="paymentStatus=='cash'">
					<td width="60%" class="text-right">KEMBALI : </td>
					<td width="40%" class="text-right">@{{ change | currency 'Rp ' }}</td>
				</tr>
				<tr v-show="paymentStatus=='t2w'">
					<td width="60%" class="text-right">BAYAR : </td>
					<td width="40%" class="text-right">TRANSFER BANK</td>
				</tr>
			</tbody>
		</table>
		<br>
		<p class="text-center" v-show="paymentStatus == 't2w'">
			No. Rekening Primasakti :<br>
			BCA 5120169060 (a/n. IRWAN SISWANDY)		
		</p>
		<p class="text-center">
			- TERIMA KASIH -<br>
			*) Barang yang sudah dibeli tidak bisa ditukarkan / dikembalikan, kecuali ada perjanjian sebelumnya.
		</p>
	</div>
	<!-- END: INVOICE DISPLAY -->
	<div class="hidden-print">
		<div class="alert alert-danger text-center"
			 v-show="pos_timeout == true">
			<b>MESSAGE (ADMIN) :</b> Untuk mengantisipasi masih adanya bug dalam system, sebelum memulai buat nota harap di refresh page dulu.
		</div>
		<input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">
		<!-- START: TOP PANEL -->
		<div class="row">
			<div class="col-sm-3">
				<div class="alert alert-info">
					<label>
						<span class="glyphicon glyphicon-user" aria-hidden="true" style="margin-right: 10px"></span>
						<span v-show="staff.user_level == 'STAFF'">Yang membuat nota</span>
						<span v-show="staff.user_level == 'SUPERVISOR'">Buat nota untuk staff</span>
					</label>
					<select class="form-control" v-model="[selectedTeamMate.id, selectedTeamMate.firstname, selectedTeamMate.lastname]">
						<option value="@{{ ['', '', ''] }}" selected>Pilih Staff</option>
						<option value="@{{ [mate.id, mate.firstname, mate.lastname] }}"
							    v-for="mate in teamMate | orderBy 'firstname'">@{{ mate.firstname + ' ' + mate.lastname }}</option>
					</select>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="alert alert-info text-center">
			    	Tanggal : <b>@{{ todayDate }}</b><br />
			    	Staff : <span v-show="!selectedTeamMate.id"><b>@{{ staff.firstname + ' ' + staff.lastname }}</b></span>
			    			<span v-show="selectedTeamMate"><b>@{{ selectedTeamMate.firstname + ' ' + selectedTeamMate.lastname }}</b></span>
			    	<span v-show="staff.working_team"><br>Team : <b>@{{ staff.working_team }}</b></span>
				</div>
			</div>
			<div class="col-sm-3">
				<div class="alert alert-info text-center">
			  		<input type="hidden" value="@{{ invoiceCode }}" v-model="addedInvoiceList.invoiceNo">
			  		No. Nota :<br>
			  		<b>@{{ invoiceCode }}</b><br />
			  		(Total Produk : <b>@{{ invoiceList.length }}</b>)
			  	</div>
			</div>
			<div class="col-sm-3">
				<div class="alert alert-info text-center">
			  		Pelanggan :<br>
			  		<b>@{{ selectedUser.firstname + ' ' + selectedUser.lastname }} (@{{ selectedUser.id }})<br />
			  		@{{ selectedUser.email }}</b>
			  	</div>
			</div>
		</div>
		<!-- END: TOP PANEL -->
		<div class="row">
			<!-- START: POS LEFT PANEL -->
			<div class="hidden-print">
				<div class="col-md-4">
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Cari Pelanggan <small>(by Nama Depan)</small></label>
								<input type="text" class="form-control" v-model="searchBoxUser" v-on:keyup="findUser">
								<div id="search-result-list" v-show="searchBoxUser">
									<ul class="list-group">
										<li class="list-group-item"
										    v-for="foundUser in foundUsers"
										    v-on:click="selectFoundUser(foundUser.id, foundUser.firstname, foundUser.lastname, foundUser.email)">
											@{{ foundUser.firstname + ' ' + foundUser.lastname + ' (' + foundUser.email + ')' + ' / ' + foundUser.id }}
										</li>
									</ul>
								</div>
							</div>
							<hr>
							<div class="form-group">
								<label><span class="glyphicon glyphicon-user" aria-hidden="true"></span> Pelanggan</label>
								<select name="customer" class="form-control"
										v-model="[
											selectedUser.id,
											selectedUser.firstname,
											selectedUser.lastname,
											selectedUser.email,
										]"
										v-on:change="selectUser(selectedUser.id)">
									<option value="@{{ [1, 'N/A', '', 'N/A'] }}" selected>Pelanggan Umum</option>
									<option v-for="user in users | orderBy 'firstname'"  value="@{{ [user.id, user.firstname, user.lastname, user.email] }}">
										@{{ user.firstname + ' ' + user.lastname + ' / ' + user.email }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<div class="panel panel-default">
						<div class="panel-body">
							<div class="form-group">
								<label><span class="glyphicon glyphicon-search" aria-hidden="true"></span> Cari Produk <small>(by Kategori)</small></label>
								<input type="text" class="form-control" v-model="searchBoxCategory" v-on:keyup="findCategory">
								<div id="search-result-list" v-show="searchBoxCategory">
									<ul class="list-group">
										<li class="list-group-item"
											v-for="foundCategory in foundCategories"
											v-on:click="selectFoundCategory(foundCategory.id, foundCategory.name, foundCategory.option_id)">
											@{{ foundCategory.name + ' / ' + foundCategory.id + ' / ' + foundCategory.option_id }}
										</li>
									</ul>
								</div>
							</div>
							<hr>
							<div class="form-group">
								<label>Kategori : <span id="selected"><b>@{{ selected.category.name | uppercase}}</b></span></label>
								<select name="category" class="form-control"
										v-model="[
											selected.category.id,
											selected.category.name,
											selected.category.option_id
										]"
										v-on:change="getProducts(selected.category.id)">
									<option value="@{{ ['', '', ''] }}">Pilih Kategori</option>
									<option v-for="category in categories | orderBy 'name'"
											value="@{{ [category.id, category.name, category.option_id] }}">
										@{{ category.name | uppercase }}
									</option>
								</select>
							</div>
							<div class="form-group">
								<label>Produk : <span id="selected"><b>@{{ selected.product.name | uppercase }}</b></span></label>
								<div id="product-list-border-product">
									<table>
										<tr v-for="product in products | orderBy 'name'"
											v-on:click="selectProduct(
													product.id,
													product.name,
													product.price1,
													product.qty1,
													product.price2,
													product.qty2,
													product.price3,
													product.qty3,
													product.price4,
													product.qty4
											)">
											<td v-show="selected.category.id">@{{ product.name | uppercase }}</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="form-group">
								<label>Tambahan (@{{ optionCategoryName | uppercase }}) : <span id="selected"><b>@{{ selected.option.name | uppercase }}</b></span></label>
								<div id="product-list-border-option">
									<table>
										<tr v-for="option in options | orderBy 'name'"
											v-on:click="selectOption(
													option.id,
													option.name,
													option.price1,
													option.qty1,
													option.price2,
													option.qty2,
													option.price3,
													option.qty3,
													option.price4,
													option.qty4
											)">
											<td v-show="selected.category.option_id">@{{ option.name | uppercase }}</td>
										</tr>
									</table>
								</div>
							</div>
							<div class="form-group">
								<button class="btn btn-primary form-control"
										v-on:click="createInvoiceList"
										:disabled="!selected.product.id">
									Tambahkan
								</button>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- END: POS LEFT PANEL -->

			<!-- START: POS RIGHT PANEL -->
			<div class="col-md-8">
				<div class="row">
					<div class="col-md-12">
						<div id="invoice-list-border">
							<table class="table table-condensed">
								<thead>
									<th class="text-center" style="width: 8%">ID #</th>
									<th class="text-center" style="width: 32%">KETERANGAN</th>
									<th class="text-center" style="width: 10%">QTY</th>
									<th class="text-center" style="width: 17%">HARGA @</th>
									<th class="text-center" style="width: 19%">SUB-TOTAL</th>
									<th class="text-center" style="width: 4%"></th>
								</thead>
								<tbody>
									<tr is="invoice-list"
										v-for="list in invoiceList"
										:category="list.category"
										:product="list.product"
										:option="list.option"
										delete="&times"
										:index="$index">
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				<br>
				<!-- START: DISPLAY TOTAL & CHANGE PANEL -->
	    		<div class="hidden-print">
		    		<div class="alert alert-info">
			    		<div class="row">
			    			<div class="col-md-6">
			    				<div class="form-horizontal">
				    				<div class="form-group">
					    				<label class="col-sm-4 control-label">Pembayaran :</label>
				    					<div class="col-sm-8">
						    				<select class="form-control" v-model="paymentStatus">
						    					<option value="cash" selected>Tunai</option>
						    					<option value="dc-bca">Kartu Debit BCA</option>
						    					<option value="dc-mdr">Kartu Debit Mandiri</option>
						    					<option value="cc">Kartu Kredit (+ 2.5 %)</option>
						    					<option value="t2w">Transfer (maks. 2 minggu)</option>
						    				</select>
				    					</div>
				    				</div>
				    				<div class="form-group">
						    			<label class="col-sm-4 control-label">No. Kartu :</label>
						    			<div class="col-sm-8">
						    				<input type="text" class="form-control"
						    					   v-model="ccNumber"
						    					   :disabled="(paymentStatus=='cash') || (paymentStatus=='t-2w')">
						    			</div>
						    		</div>
						    	</div>
			    			</div>
				    		<div class="col-md-6">
				    			<div class="form-horizontal">
					    			<div class="form-group">
						    			<label class="col-sm-4 control-label">Total :</label>
						    			<div class="col-sm-8">
							    			<input type="text" value="@{{ total | currency 'Rp ' }}"
							    				   v-show="paymentStatus!='cc'"
							    				   class="form-control" style="font-weight: bold" readonly>
							    			<input type="text" value="@{{ ccTotal | currency 'Rp ' }}"
							    				   v-show="paymentStatus=='cc'"
							    				   class="form-control" style="font-weight: bold" readonly>
						    			</div>
					    			</div>
					    			<div class="form-group">
						    			<label class="col-sm-4 control-label">Bayar :</label>
						    			<div class="col-sm-8">
						    				<input type="text" class="form-control" style="font-weight: bold"
						    					   v-model="paid" :disabled="!total" v-on:keyup.enter="printInvoice">
					    				</div>
					    			</div>
					    			<div class="form-group" v-show="paymentStatus=='cash'">
						    			<label class="col-sm-4 control-label">Kembali : </label>
						    			<div class="col-sm-8">
						    				<input type="text" value="@{{ change | currency 'Rp ' }}" class="form-control" style="font-weight: bold" readonly>
					    				</div>
					    			</div>
					    		</div>
				    		</div>
				    	</div>
					</div>
				</div>
				<!-- END: DISPLAY TOTAL & CHANGE PANEL -->
			</div>
			<!-- END: POS RIGHT PANEL -->
		</div>
		<div class="row">
			<div class="col-md-4">
				<div class="form-group">
					<button class="btn btn-info form-control" v-on:click="createPDF">
						<b>Print & E-mail Nota</b>
					</button>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<button class="btn btn-info form-control" v-on:click="saveInvoice" :disabled="(!paid) || (change < 0)">
						<b>Simpan Nota</b>
					</button>
				</div>
			</div>
			<div class="col-md-4">
				<div class="form-group">
					<button class="btn btn-primary form-control" v-on:click="printInvoice" :disabled="(!paid) || (change < 0)">
						<b>Print Nota</b> [Enter]
					</button>
				</div>
			</div>
		</div>
	</div>
</div>

<template id="invoice-list-template">
	<tr>
		<input type="hidden" value="@{{ product.price1 }}" v-model="productInList.price1">
		<input type="hidden" value="@{{ product.qty1 }}" v-model="productInList.qty1">
		<input type="hidden" value="@{{ product.price2 }}" v-model="productInList.price2">
		<input type="hidden" value="@{{ product.qty2 }}" v-model="productInList.qty2">
		<input type="hidden" value="@{{ product.price3 }}" v-model="productInList.price3">
		<input type="hidden" value="@{{ product.qty3 }}" v-model="productInList.qty3">
		<input type="hidden" value="@{{ product.price4 }}" v-model="productInList.price4">
		<input type="hidden" value="@{{ product.qty4 }}" v-model="productInList.qty4">
		<input type="hidden" value="@{{ option.id }}" v-model="optionInList.id">
		<input type="hidden" value="@{{ option.price1 }}" v-model="optionInList.price1">
		<input type="hidden" value="@{{ option.qty1 }}" v-model="optionInList.qty1">
		<input type="hidden" value="@{{ option.price2 }}" v-model="optionInList.price2">
		<input type="hidden" value="@{{ option.qty2 }}" v-model="optionInList.qty2">
		<input type="hidden" value="@{{ option.price3 }}" v-model="optionInList.price3">
		<input type="hidden" value="@{{ option.qty3 }}" v-model="optionInList.qty3">
		<input type="hidden" value="@{{ option.price4 }}" v-model="optionInList.price4">
		<input type="hidden" value="@{{ option.qty4 }}" v-model="optionInList.qty4">
		<input type="hidden" value="@{{ index }}" v-model="index">
		<td>
			PS-@{{ category.id }}/@{{ product.id }}/@{{ optionId }}
		</td>
		<td>
			@{{ category.name | uppercase }}<br>
			@{{ product.name | uppercase }}<br>@{{ option.name | uppercase }}</td>
		<td>
			<div class="hidden-print">
				<input type="text" class="form-control" style="width: 70px" v-model="qty" v-on:keyup="addTotal">
			</div>
			<div class="visible-print-block">
				@{{ qty }}
			</div>
		</td>
		<td class="text-right">
			@{{ price | currency 'Rp ' }}
		</td>
		<td class="text-right">
			@{{ subTotal | currency 'Rp ' }}
		</td>
		<td class="text-center">
			<div class="hidden-print">
				<span style="cursor: pointer; line-height: 50%" v-on:click="removeThisList(componentIndex)">
					<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
				</span>
			</div>
		</td>
	<tr>
</template>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/js/sweetalert.min.js"></script>
<script>
	Vue.component('invoice-list', {
		template: '#invoice-list-template',
		props: [
			'category',
			'product',
			'option',
			'delete',
			'index'
		],
		data: function() {
			return {
				// ALL VARIABLES BELOW STORES ALL DATA PASSED FROM ROOT VUE VIA TEMPLATE'S HIDDEN INPUT
				qty: '',
				index: '',
				productInList: {
					price1: '',
					qty1  : '',
					price2: '',
					qty2  : '',
					price3: '',
					qty3  : '',
					price4: '',
					qty4  : ''
				},
				optionInList: {
					id    : '',
					price1: '',
					qty1  : '',
					price2: '',
					qty2  : '',
					price3: '',
					qty3  : '',
					price4: '',
					qty4  : ''
				},
				same_product_qty: ''
			}
		},
		computed: {
			optionId: function() {
				if (this.optionInList.id == '' || this.optionInList.id == '0') {
					return 0
				}
				else {
					return this.optionInList.id
				}
			},
			componentIndex: function() {
				return this.index
			},
			productPrice: function() {
				var temp_qty = Number(this.qty) + Number(this.same_product_qty);
				// CHECK IF QTY IS POSITIVE NUMBER
				if (Number(temp_qty) >= Number(this.productInList.qty1)) {
					// CHECK IF QTY IS HIGHER THAN QTY2
					if (Number(this.productInList.qty2) > 0 && Number(temp_qty) >= Number(this.productInList.qty2)) {
						// CHECK IF QTY IS HIGHER THAN QTY3
						if (Number(this.productInList.qty3) > 0 && Number(temp_qty) >= Number(this.productInList.qty3)) {
							// CHECK IF QTY IS HIGHER THAN QTY4
							if (Number(this.productInList.qty4) > 0 && Number(temp_qty) >= Number(this.productInList.qty4)) {
								return this.productInList.price4
							}
							// IF QTY IS LOWER THAN QTY4
							return this.productInList.price3
						}
						// IF QTY IS LOWER THAN QTY3
						return this.productInList.price2
					}
					// IF QTY IS LOWER THAN QTY2
					return this.productInList.price1
				}
				// IF QTY IS 0 OR NEGATIVE NUMBER
				else {
					return 'N/A'
				}
			},
			optionPrice: function() {
				// CHECK IF QTY IS POSITIVE NUMBER
				if (Number(this.qty) >= Number(this.optionInList.qty1)) {
					// CHECK IF QTY IS HIGHER THAN QTY2
					if (Number(this.optionInList.qty2) > 0 && Number(this.qty) >= Number(this.optionInList.qty2)) {
						// CHECK IF QTY IS HIGHER THAN QTY3
						if (Number(this.optionInList.qty3) > 0 && Number(this.qty) >= Number(this.optionInList.qty3)) {
							// CHECK IF QTY IS HIGHER THAN QTY4
							if (Number(this.optionInList.qty4) > 0 && Number(this.qty) >= Number(this.optionInList.qty4)) {
								return this.optionInList.price4
							}
							// IF QTY IS LOWER THAN QTY4
							return this.optionInList.price3
						}
						// IF QTY IS LOWER THAN QTY3
						return this.optionInList.price2
					}
					// IF QTY IS LOWER THAN QTY2
					return this.optionInList.price1
				}
				// IF QTY IS 0 OR NEGATIVE NUMBER
				else {
					return 'N/A'
				}
			},
			price: function() {
				if (!this.optionInList.price1) {
					return this.productPrice;
				}
				else {
					return Number(this.productPrice) + Number(this.optionPrice)
				}
			},
			subTotal: function() {
				return Number(this.qty) * Number(this.price)
			},
			// THESE FUNCTIONS BELOW ARE USED TO KEEP ROOT VUE'S totalArray TO THE SAME ORDER
			totalForProduct: function() {
				return Number(this.qty) * Number(this.productPrice)
			},
			totalForOption: function() {
				return Number(this.qty) * Number(this.optionPrice)
			}
		},
		methods: {
			addTotal: function() {
				if (!this.optionInList.price1) {
					// PASS TOTAL TO ROOT EVENT
					this.$dispatch('grandTotal', [this.totalForProduct, 0], this.componentIndex, [this.qty, 0]);
					this.$dispatch('setInvoiceDetailsData', this.componentIndex, [this.product.id, ''], [this.product.name, ''], [this.qty, 0], [this.productPrice, 0]);
				}
				else {
					// PASS TOTAL TO ROOT EVENT
					this.$dispatch('grandTotal', [this.totalForProduct, this.totalForOption], this.componentIndex, [this.qty, this.qty])
					this.$dispatch('setInvoiceDetailsData', this.componentIndex, [this.product.id, this.option.id], [this.product.name, this.option.name], [this.qty, this.qty], [this.productPrice, this.optionPrice]);
				}

				var itemData = {
					index: this.componentIndex,
					category: this.category.name,
					product: this.product.name,
					option: this.option.name,
					qty: this.qty,
					price: this.price,
					sub_total: this.subTotal
				};
				this.$dispatch('itemListForInvoice', itemData);
				this.$dispatch('passProductID', this.index, this.product.id);
			},
			removeThisList: function(componentIndex) {
				// SEND INDEX THAT WILL BE REMOVED TO ROOT EVENT
				this.$dispatch('removeThisList', componentIndex)
			},
			handleFoundSameProducts: function(sameProducts) {
				var total_qty = 0;
				for (i=0; i<sameProducts.qty.length; i++) {
					total_qty += Number(sameProducts.qty[i]);
				}
				if (this.product.id == sameProducts.id) {
					return this.same_product_qty = total_qty - this.qty;
				}
				else {
					return this.same_product_qty = 0;
				}
			}
		},
		events: {
			'foundSameProductID': function(sameProducts) {
				this.handleFoundSameProducts(sameProducts);
			}
		}
	})

	var invoiceVue = new Vue({
		el: '#posSystem',
		http: {
	      headers: {
	        'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
	      }
	    },
		data: {
			pos_timeout: false,
			staff: {
				id: '',
				firstname: '',
				lastname: '',
				working_team: '',
				user_level: ''
			},
			teamMate: [],
			selectedTeamMate: {
				id: '',
				firstname: '',
				lastname: ''
			},
			searchBoxUser: '',
			foundUsers: [],
			searchBoxCategory: '',
			foundCategories: [],
			users: [],
			categories: [],
			products: [],
			// USED TO FIND PRODUCT IN DB
			selectedUser: {
				id       : 1,
				firstname: 'N/A',
				lastname : '',
				email    : 'N/A'
			},
			selected: {
				category: {
					id       : '',
					name     : '',
					option_id: ''
				},
				product: {
					id    : '',
					name  : '',
					price1: '',
					qty1  : '',
					price2: '',
					qty2  : '',
					price3: '',
					qty3  : '',
					price4: '',
					qty4  : ''
				},
				option: {
					id    : '',
					name  : '',
					price1: '',
					qty1  : '',
					price2: '',
					qty2  : '',
					price3: '',
					qty3  : '',
					price4: '',
					qty4  : ''
				}
			},
			optionCategoryName: '',
			addedInvoiceList: {
				userId: '',
				invoiceNo: '',
				total: '',
				product: {
					id : [],
					qty: []
				}
			},
			// THIS STORES ARRAY FOR INVOICE-LIST COMPONENT'S DATA
			invoiceList: [],
			// THIS STORES ARRAY OF TOTAL PASSED FROM INVOICE-LIST COMPONENT
			totalArray: [],
			// THIS STORES AMOUNT OF PAID MONEY BY CUSTOMER
			paymentStatus: '',
			paid: '',
			invoiceData: { // USED FOR STORING TO DB (TABLE: invoices)
				staff_id: '',
				user_id: 1,
				invoice_no: '',
				total: '',
				paid: '',
				change: '',
				payment_status: ''
			},
			invoiceDetailsData: { // USED FOR STORING TO DB (TABLE: invoice_details)
				invoice_no: '',
				product_id: [],
				qty: [],
				price: []
			},
			cancelledInvoice: {
				invoice_no: ''
			},
			itemListForInvoice: []
		},
		computed: {
			staffIDforInvoice: function() {
				if (!this.selectedTeamMate.id) {
					return this.staff.id;
				}
				else {
					return this.selectedTeamMate.id;
				}
			},
			browserType: function() {
				// Opera 8.0+
				var isOpera = (!!window.opr && !!opr.addons) || !!window.opera || navigator.userAgent.indexOf(' OPR/') >= 0;
				// Firefox 1.0+
				var isFirefox = typeof InstallTrigger !== 'undefined';
				// At least Safari 3+: "[object HTMLElementConstructor]"
				var isSafari = Object.prototype.toString.call(window.HTMLElement).indexOf('Constructor') > 0;
				// Internet Explorer 6-11
				var isIE = /*@cc_on!@*/false || !!document.documentMode;
				// Chrome 1+
				var isChrome = !!window.chrome && !!window.chrome.webstore;

				if (isOpera) {
					return "Opera";
				}
				else if (isFirefox) {
					return "Firefox"
				}
				else if (isSafari) {
					return "Safari"
				}
				else if (isIE) {
					return "Internet Explorer"
				}
				else if (isChrome) {
					return "Google Chrome"
				}
			},
			todayDate: function() {
				var nowDate = new Date()
				var dd = nowDate.getDate()
				var mm = nowDate.getMonth() + 1
				var yyyy = nowDate.getFullYear()
				return dd + '/' + mm + '/' + yyyy
			},
			todayTime: function() {
				var today = new Date()
				var hours = today.getHours()
				var minutes = today.getMinutes()
				var seconds = today.getSeconds()
				return hours + '/' + minutes + '/' + seconds
			},
			// USED TO GENERATE INVOICE NUMBER
			invoiceCode: function() {
				return 'PS-' + this.selectedUser.id + '-' + this.staff.id + '-' + this.todayDate + '-' + this.todayTime;
			},
			// THIS SUMS TotalArray
			total: function() {
				var sumTotalArray = 0
				for (i=0; i<this.totalArray.length; i++) {
					sumTotalArray += Number(this.totalArray[i])
				}
				return sumTotalArray;
			},
			ccTotal: function() {
				return this.total * 1.025
			},
			// THIS CALCULATES THE AMOUNT OF CHANGE
			change: function() {
				return Number(this.paid) - Number(this.total)
			}
		},
		methods: {
			watch_timeout: function() {
				return setTimeout(this.set_pos_timeout_true, 300000);
			},
			set_pos_timeout_true: function() {
				return this.pos_timeout = true;
			},
			getStaff: function() {
				return this.$http.get('http://' + document.location.host + '/staff/pos/getStaffAndWorkingTeamData/' + {{ Auth::id() }}).then(
					(response) => {
						this.$set('staff.id', response.data.id);
						this.$set('staff.firstname', response.data.firstname);
						this.$set('staff.lastname', response.data.lastname);
						this.$set('staff.user_level', response.data.user_level);
						if (data.working_team.length > 0) {
							this.$set('staff.working_team', response.data.working_team[0].name);
						}
					},
					(response) => {
						return this.getStaff();
					}
				);
			},
			getTeammate: function() {
				return this.$http.get('http://' + document.location.host + '/staff/pos/getTeammate/' + {{ Auth::id() }}).then(
					(response) => {
						return this.$set('teamMate', response.data);
					},
					(response) => {
						return this.getTeammate();
					}
				);	
			},
			findUser: function() {
				this.$http.get('http://' + document.location.host + '/staff/pos/search/user/' + this.searchBoxUser, function(data) {
					this.$set('foundUsers', data);
				})
			},
			selectFoundUser: function(id, firstname, lastname, email) {
				this.selectedUser.id        = id;
				this.selectedUser.firstname = firstname;
				this.selectedUser.lastname  = lastname;
				this.selectedUser.email     = email;

				this.searchBoxUser = '';
			},
			getUsers: function() {
				return this.$http.get('/staff/pos/users').then(
					(response) => {
						return this.$set('users', response.data);
					},
					(response) => {
						return this.getUsers();
					}
				);
			},
			selectUser: function(id) {
				return [
					this.addedInvoiceList.userId = id,
					this.invoiceData.user_id = this.addedInvoiceList.userId
				];
			},
			findCategory: function() {
				return this.$http.get('http://' + document.location.host + '/staff/pos/search/product_category/' + this.searchBoxCategory).then(
					(response) => {
						return this.$set('foundCategories', response.data);
					},
					(response) => {
						return this.findCategory();
					}
				);
			},
			selectFoundCategory: function(id, name, option_id) {
				this.selected.category.id = id,
				this.selected.category.name = name,
				this.selected.category.option_id = option_id,
				this.searchBoxCategory = ''
				return this.getProducts(this.selected.category.id);
			},
			selectProduct: function(id, name, price1, qty1, price2, qty2, price3, qty3, price4, qty4) {
				return [
					this.selected.product.id = id,
					this.selected.product.name = name,
					this.selected.product.price1 = price1,
					this.selected.product.qty1 = qty1,
					this.selected.product.price2 = price2,
					this.selected.product.qty2 = qty2,
					this.selected.product.price3 = price3,
					this.selected.product.qty3 = qty3,
					this.selected.product.price4 = price4,
					this.selected.product.qty4 = qty4
				];
			},
			selectOption: function(id, name, price1, qty1, price2, qty2, price3, qty3, price4, qty4) {
				return [
					this.selected.option.id = id,
					this.selected.option.name = name,
					this.selected.option.price1 = price1,
					this.selected.option.qty1 = qty1,
					this.selected.option.price2 = price2,
					this.selected.option.qty2 = qty2,
					this.selected.option.price3 = price3,
					this.selected.option.qty3 = qty3,
					this.selected.option.price4 = price4,
					this.selected.option.qty4 = qty4
				];
			},
			getProductCategories: function() {
				return this.$http.get('/staff/pos/categories').then(
					(response) => {
						return this.$set('categories', response.data);
					},
					(response) => {
						return this.getProductCategories();
					}
				);
			},
			getProducts: function(id) {
				if (this.selected.category.id) {
					this.$http.get('http://' + document.location.host + '/staff/pos/products/' + id, function(data) {
						this.$set('products', data);
					});
					if (this.selected.category.option_id) {
						this.$http.get('/staff/pos/category/' + this.selected.category.option_id, function(data) {
							this.$set('optionCategoryName', data)
						})
						this.$http.get('/staff/pos/products/' + this.selected.category.option_id, function(data) {
							this.$set('options', data)
						})
					}
				}
				else {
					/* DO NOTHING */
				}
			},
			// THIS WILL ADD INVOICE-LIST COMPONENT TO VARIABLE "INVOICE LIST"
			createInvoiceList: function() {
				this.invoiceList.push(this.selected);
				this.addedInvoiceList.product.id.push([this.selected.product.id, this.selected.option.id]);

				// CLEAR SELECT BOX IN ADD PRODUCT PANEL
				this.optionCategoryName = ''
				this.selected = {
					category: {
						id  	 : '',
						name 	 : '',
						option_id: ''
					},
					product: {
						id    : '',
						name  : '',
						price1: '',
						qty1  : '',
						price2: '',
						qty2  : '',
						price3: '',
						qty3  : '',
						price4: '',
						qty4  : ''
					},
					option: {
						id    : '',
						name  : '',
						price1: '',
						qty1  : '',
						price2: '',
						qty2  : '',
						price3: '',
						qty3  : '',
						price4: '',
						qty4  : ''
					}
				}
			},
			printInvoice: function() {
				if (this.change >= 0) {
					window.print();
					return showConfirmAlert('info', 'Konfirmasi Transaksi', 'Apakah transaksi ini sudah selesai ?');
				}
				else {
					return showAlert(
						'error',
						'Pembayaran tidak cukup',
						'Jumlah uang yang dibayarkan harus lebih besar dari jumlah total nota'
					)
				}
			},
			saveInvoice: function() {
				return showConfirmAlert('info', 'Konfirmasi Transaksi', 'Apakah transaksi ini sudah selesai ?');
			},
			storeInvoiceToDB: function() {
				this.invoiceData.staff_id       = this.staffIDforInvoice;
				this.invoiceData.invoice_no     = this.addedInvoiceList.invoiceNo;
				this.invoiceData.total          = this.addedInvoiceList.total;
				this.invoiceData.paid           = this.paid;
				this.invoiceData.change         = this.change;
				this.invoiceData.payment_status = this.paymentStatus;
				
				this.invoiceDetailsData.invoice_no = this.addedInvoiceList.invoiceNo;

				return this.$http.post(document.URL + '/invoice/store_invoice', this.invoiceData).then(
					(response) => {
						return this.$http.post(document.URL + '/invoice/store_transaction/' + response.data, this.invoiceDetailsData).then(
							(response) => {
								return location.reload();
							});
					},
					(response) => {
						return [
							this.cancelledInvoice.invoice_no = this.addedInvoiceList.invoiceNo,
							this.$http.post('http://' + document.location.host + '/staff/pos/invoice/cancel', this.cancelledInvoice),
							alert('Invoice failed to stored')
						];
					});
			},
			createPDF: function() {
				return createInvoicePDF();
			},
			sendToEmail: function() {

			},
			renderItemListForInvoice: function(itemData) {
				if (this.itemListForInvoice[itemData.index] == undefined) {
					this.itemListForInvoice.push(itemData);
				}
				else {
					this.itemListForInvoice.splice(itemData.index, 1, itemData);
				}
			},
			checkSameProductID: function(index, id) {
				var sameProducts = {
					id: id,
					index: [],
					qty: []
				};
				for (i=0; i<this.invoiceDetailsData.product_id.length; i++) {
					if (this.invoiceDetailsData.product_id[i] == id) {
						sameProducts.index.push(i);
						sameProducts.qty.push(this.invoiceDetailsData.qty[i]);
					}
					else {
						// IGNORE
					}
				}
				this.$broadcast('foundSameProductID', sameProducts);
			}
		},
		events: {
			'grandTotal': function([totalProduct, totalOption], componentIndex, [qtyProduct, qtyOption]) {
				var sumTotalProductOption = Number(totalProduct) + Number(totalOption)

				if (this.totalArray[componentIndex] == undefined) {
					this.totalArray.push(sumTotalProductOption);
				}
				else {
					this.totalArray.splice(componentIndex, 1, sumTotalProductOption);
				}
				this.addedInvoiceList.total = this.total

				if (this.addedInvoiceList.product.qty[componentIndex] == undefined) {
					this.addedInvoiceList.product.qty.push([qtyProduct, qtyOption])
				}
				else {
					this.addedInvoiceList.product.qty.splice(componentIndex, 1, [qtyProduct, qtyOption])
				}
			},
			'setInvoiceDetailsData': function(componentINDEX, [productID, optionID], [productNAME, optionNAME], [productQTY, optionQTY], [productPRICE, optionPRICE]) {
				var startINDEX = Number(componentINDEX)*2;
				var endINDEX = Number((componentINDEX)*2)+1;

				if (this.invoiceDetailsData.product_id[startINDEX] == undefined) {
					this.invoiceDetailsData.product_id.push(productID, optionID);
					this.invoiceDetailsData.qty.push(productQTY, optionQTY);
					this.invoiceDetailsData.price.push(productPRICE, optionPRICE);
				}
				else {
					this.invoiceDetailsData.product_id.splice(startINDEX, 2, productID, optionID);
					this.invoiceDetailsData.qty.splice(startINDEX, 2, productQTY, optionQTY);
					this.invoiceDetailsData.price.splice(startINDEX, 2, productPRICE, optionPRICE);
				}
			},
			'removeThisList': function(componentIndex) {
				this.invoiceList.splice(Number(componentIndex), 1); // COMPONENT: INVOICE-LIST
				this.totalArray.splice(Number(componentIndex), 1);
				this.addedInvoiceList.product.id.splice(Number(componentIndex), 1);
				this.addedInvoiceList.product.qty.splice(Number(componentIndex), 1);
				// REMOVES 2-ARRAYS: invoiceDetailsData
				var startINDEX = Number(componentIndex)*2;
				var endINDEX = Number((componentIndex)*2)+1;
				this.invoiceDetailsData.product_id.splice(endINDEX, 2);
				this.invoiceDetailsData.qty.splice(endINDEX, 2);
				this.invoiceDetailsData.price.splice(endINDEX, 2);
			},
			'itemListForInvoice': function(itemData) {
				this.renderItemListForInvoice(itemData);
			},
			'passProductID': function(index, id) {
				this.checkSameProductID(index, id);
			}
		},
		ready: function() {
			this.getTeammate();
			this.getStaff();
			this.getUsers();
			this.getProductCategories();
			this.watch_timeout();
		}
	});

	function showConfirmAlert(message_type, message_title, message) {
		return swal({
			type: message_type,
			title: message_title,
			text: message,
			showCancelButton: true,
			confirmButtonText: 'Selesai',
			cancelButtonText: 'Belum'
		},
		function(isConfirm) {
			if (isConfirm) {
				return invoiceVue.storeInvoiceToDB();
			}
		});
	}

	function showAlert(message_type, message_title, message) {
		return swal({
			type: message_type,
			title: message_title,
			text: message
		});
	}

	function createInvoicePDF() {
		return alert('createPDF');
	}
</script>

@stop
