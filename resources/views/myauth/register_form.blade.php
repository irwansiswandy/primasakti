@extends('app')

@section('content')

<div class="container">
	<div class="row">
		<div class="col-sm-8 col-sm-offset-2">
			<div id="registerForm">
				<p class="text-center" style="font-family: unda_angleitalic; font-size: 300%">
					FORM PENDAFTARAN USER BARU
				</p>
				<!-- START: VALIDATION ERROR(S) MESSAGE -->
				@include('includes.validation_errors')
				<br>
				<!-- START: VALIDATION ERROR(S) MESSAGE -->
				<form method="POST" url="{{ URL::action('MyAuthController@register_post') }}">
					{!! csrf_field() !!}
					<!-- START: ACCOUNT DETAILS SECTION -->
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nama Depan</label>
								<input type="text" name="firstname" class="form-control">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Nama Belakang</label>
								<input type="text" name="lastname" class="form-control">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Alamat E-mail</label>
						<input type="email" name="email" class="form-control">
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Password</label>
								<input type="password" name="password" class="form-control">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Konfirmasi Password</label>
								<input type="password" name="password_confirmation" class="form-control">
							</div>
						</div>
					</div>
					<!-- END: ACCOUNT DETAILS SECTION -->
					<hr>
					<!-- START: PERSONAL DETAILS SECTION -->
					<div class="form-group">
						<label>Alamat</label>
						<textarea name="address" rows="3" class="form-control"></textarea>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Kota</label>
								<input type="text" name="city" class="form-control">
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Kode Pos</label>
								<input type="text" name="postcode" class="form-control">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-sm-6">
							<div class="form-group">
								<label>Provinsi</label>
								<select name="state" class="form-control" v-model="selected.state" :disabled="!selected.countryName">
									<option value="">Select State</option>
									<option v-for="state in states | orderBy 'name'" value="@{{ state.name }}">
										@{{ state.name }}
									</option>
								</select>
							</div>
						</div>
						<div class="col-sm-6">
							<div class="form-group">
								<label>Negara</label>
								<select name="country" class="form-control"
										v-model="[selected.countryId, selected.countryName, selected.countryPhonecode]"
										v-on:change="selectCountry">
									<option value="@{{ ['', '', ''] }}" selected>Select Country</option>
									<option v-for="country in countries | orderBy 'name'" value="@{{ [country.id, country.name, country.phonecode] }}">
										@{{ country.name }}
									</option>
								</select>
							</div>
						</div>
					</div>
					<!-- END: PERSONAL DETAILS SECTION -->
					<hr>
					<!-- START: CONTACT DETAILS SECTION -->
					<div class="form-group">
						<label>No. Telp</label>
						<table width="100%">
							<tr>
								<td width="14%">
									<input type="text" name="country_phonecode" value="@{{ selected.countryPhonecode }}" class="form-control" readonly>
									<p class="help-block"><small>Kode Negara</small></p>
								</td>
								<td width="2%"></td>
								<td width="14%">
									<input type="text" name="city_phonecode" class="form-control">
									<p class="help-block"><small>Kode Area</small></p>
								</td>
								<td width="2%"></td>
								<td width="64%">
									<input type="text" name="phone" class="form-control">
									<p class="help-block"><small>No. Telp</small></p>
								</td>
							</tr>
						</table>
					</div>
					<div class="form-group">
						<label>No. HP</label>
						<table width="100%">
							<tr>
								<td width="14%">
									<input type="text" value="@{{ selected.countryPhonecode }}" class="form-control" readonly>
									<p class="help-block"><small>Kode Negara</small></p>
								</td>
								<td width="2%"></td>
								<td width="84%">
									<input type="text" name="cellphone" class="form-control">
									<p class="help-block"><small>No. HP</small></p>
								</td>
							</tr>
						</table>
					</div>
					<br>
					<div class="form-group text-right">
						<button type="submit" class="btn btn-success">Daftar</button>
					</div>
					<!-- END: CONTACT DETAILS SECTION -->
				</form>
			</div>
		</div>
	</div>
</div>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/js/sweetalert.min.js"></script>

@include('includes/flash')

<!-- START: VUE SCRIPT -->
<script>
	var registerVue = new Vue({
		el: '#registerForm',
		data: {
			countries: [],
			states: [],
			selected: {
				userTitle: '',
				countryId: '',
				countryName: '',
				countryPhonecode: '',
				state: ''
			}
		},
		methods: {
			getCountries: function() {
				this.$http.get(document.URL + '/countries', function(data) {
					this.$set('countries', data);
				})
			},
			getContactTypes: function() {
				this.$http.get(document.URL + '/contact_types', function(data) {
					this.$set('contact_types', data);
				})
			},
			selectCountry: function() {
				var selectedCountryId = this.selected.countryId;

				this.$http.get(document.URL + '/' + selectedCountryId + '/states', function(data) {
					this.$set('states', data);
				})
			}
		},
		ready: function() {
			this.getCountries();
		}
	});
</script>
<!-- END: VUE SCRIPT -->

@stop
