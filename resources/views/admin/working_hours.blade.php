@extends('app')

@section('content')

<div id="workingHoursController">
	<div class="row">
		<div class="col-sm-3">
			<div class="panel panel-info">
				<div class="panel-heading">
					<b>Add New Working Day</b>
				</div>
				<div class="panel-body">
					<input type="hidden" id="token" value="{{ csrf_token() }}">
					<div class="form-group" v-show="!update_button">
						<label>Days</label>
						<select class="form-control" v-model="new_working_day.day">
							<option value="" selected>Select Day</option>
							<option v-for="day in available_days" value="@{{ day.toString() }}">
								@{{ setDayNumberToReadable(day) }}
							</option>
						</select>
					</div>
					<div class="form-group" v-show="update_button">
						<label>Days</label>
						<select class="form-control" v-model="new_working_day.day" disabled>
							<option v-for="day in days" value="@{{ day.toString() }}">@{{ setDayNumberToReadable(day) }}</option>
						</select>
					</div>
					<hr>
					<div class="form-group">
						<label>Open Time</label>
						<table width="100%">
							<tr>
								<td width="47%">
									<div class="form-group">
										<select class="form-control" v-model="new_working_day.open_hour"
												:disabled="!new_working_day.day">
											<option value="" selected>Hours</option>
											<option v-for="hour in hours_data" value="@{{ hour.toString() }}">@{{ hour }}</option>
										</select>
									</div>
								</td>
								<td width="6%"></td>
								<td width="47%">
									<div class="form-group">
										<select class="form-control" v-model="new_working_day.open_minute"
												:disabled="!new_working_day.open_hour">
											<option value="" selected>Minutes</option>
											<option v-for="minute in minutes_data" value="@{{ minute.toString() }}">@{{ minute }}</option>
										</select>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="form-group">
						<label>Closed Time</label>
						<table width="100%">
							<tr>
								<td width="47%">
									<div class="form-group">
										<select class="form-control" v-model="new_working_day.closed_hour"
												:disabled="!new_working_day.open_minute">
											<option value="" selected>Hours</option>
											<option v-for="hour in hours_data" value="@{{ hour.toString() }}">@{{ hour }}</option>
										</select>
									</div>
								</td>
								<td width="6%"></td>
								<td width="47%">
									<div class="form-group">
										<select class="form-control" v-model="new_working_day.closed_minute"
												:disabled="!new_working_day.closed_hour">
											<option value="" selected>Minutes</option>
											<option v-for="minute in minutes_data" value="@{{ minute.toString() }}">@{{ minute }}</option>
										</select>
									</div>
								</td>
							</tr>
						</table>
					</div>
					<div class="form-group">
						<button class="btn btn-success form-control"
								v-show="!update_button"
								v-on:click="addNewWorkingDay"
								:disabled="!new_working_day.closed_minute">
							Add
						</button>
					</div>
					<div class="form-group">
						<button class="btn btn-info form-control"
								v-show="update_button"
								v-on:click="updateWorkingDay">
							Update
						</button>
					</div>
					<div class="form-group">
						<button class="btn btn-danger form-control"
								v-show="update_button"
								v-on:click="cancelEdit">
							Cancel
						</button>
					</div>
				</div>
			</div>
		</div>
		<div class="col-sm-9">
			<div class="alert alert-info">
				<div class="row">
					<div class="col-sm-4 text-center">
						Total Working Days : <b>@{{ totalWorkingDays }} days / week</b>
					</div>
					<div class="col-sm-4 text-center">
						Total Working Hours : <b>@{{ totalWorkingHours }} hours / week</b>
					</div>
					<div class="col-sm-4 text-center">
						Average : <b>@{{ averageWorkingHours }} hours / day</b>
					</div>
				</div>
			</div>
			<table class="table table-stripped" width="100%">
				<thead style="background-color: #D1D2D4">
					<th>Active Days</th>
					<th>Open at</th>
					<th>Closed at</th>
					<th>Total Hours</th>
					<th></th>
				</thead>
				<tbody>
					<tr v-for="working_day in working_days_data | orderBy 'day'" v-show="taken_days">
						<td>@{{ setDayNumberToReadable(working_day.day) }}</td>
						<td>@{{ setTimeToReadable(working_day.open_hour, working_day.open_minute) }}</td>
						<td>@{{ setTimeToReadable(working_day.closed_hour, working_day.closed_minute) }}</td>
						<td>@{{ calculateHours(working_day.open_hour, working_day.open_minute, working_day.closed_hour, working_day.closed_minute) }} hours</td>
						<td class="text-right">
							<button class="btn btn-info"
									v-on:click="editWorkingDay(working_day.day, working_day.open_hour, working_day.open_minute, working_day.closed_hour, working_day.closed_minute)">
								<span class="glyphicon glyphicon-pencil" aria-hidden="true"></span>
							</button>
							<button class="btn btn-danger"
									v-on:click="deleteWorkingDay(working_day.day)">
								<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
							</button>
						</td>
					</tr>
				</tbody>
			</table>
			<p class="text-center" v-show="!taken_days">You have no working days added</p>
		</div>
	</div>
</div>

@stop

@section('js')

<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>
<script src="/libraries/moment/moment.js"></script>
<script>
	var workingHoursVue = new Vue({
		http: {
			headers: {
				'X-CSRF-TOKEN': document.querySelector('#token').getAttribute('value')
			}
		},
		el: '#workingHoursController',
		data: {
			days: [0, 1, 2, 3, 4, 5, 6],
			taken_days: [],
			working_days_data: [],
			new_working_day: {
				day: '',
				open_hour: '',
				open_minute: '',
				closed_hour: '',
				closed_minute: ''
			},
			update_button: false,
			workingHoursArray: []
		},
		computed: {
			available_days: function() {
				if (this.taken_days == null) {
					return this.days;
				}
				else {
					var selectable_days = [];
					for (i=0; i<7; i++) {
						if (this.taken_days.indexOf(i) < 0) {
							selectable_days.push(i);
						}
						else {
							continue;
						}
					}
					return selectable_days;
				}
			},
			hours_data: function() {
				var hours = [];
				for (i=1; i<=24; i++) {
					hours.push(i);
				}
				return hours;
			},
			minutes_data: function() {
				var minutes = [];
				for (i=0; i<=60; i++) {
					minutes.push(i);
				}
				return minutes;
			},
			totalWorkingDays: function() {
				return this.taken_days.length;
			},
			totalWorkingHours: function() {
				var total = 0;
				for (i=0; i<this.workingHoursArray.length; i++) {
					total += this.workingHoursArray[i];
				}
				return total;
			},
			averageWorkingHours: function() {
				var average = this.totalWorkingHours / this.workingHoursArray.length;
				return average.toFixed(2);
			}
		},
		methods: {
			getWorkingDays: function() {
				this.$http.get('http://' + document.location.host + '/admin/working_days/get_working_days', function(data) {
					this.$set('taken_days', data[0]);
					this.$set('working_days_data', data[1]);
				}).then(function(ok) {
					for (i=0; i<this.working_days_data.length; i++) {
						var minutes1 = (this.working_days_data[i].closed_hour - this.working_days_data[i].open_hour) * 60;
						var minutes2 = this.working_days_data[i].closed_minute - this.working_days_data[i].open_minute;
						var hours = (minutes1 + minutes2) / 60;
						this.workingHoursArray.push(hours);
					}
				});
			},
			calculateHours: function(hour1, minute1, hour2, minute2) {
				var time1 = (hour1 * 60) + minute1;
				var time2 = (hour2 * 60) + minute2;
				var timeInHours = (time2 - time1) / 60;
				return timeInHours;
			},
			setDayNumberToReadable: function(dayNumber) {
				return moment().day(dayNumber).format('dddd');
			},
			setTimeToReadable: function(hour, minute) {
				return moment().hours(hour).minutes(minute).format('LT');
			},
			addNewWorkingDay: function() {
				this.$http.post('http://' + document.location.host + '/admin/working_days/add', this.new_working_day).then(function(ok) {
					this.new_working_day = {
						day: '',
						open_hour: '',
						open_minute: '',
						closed_hour: '',
						closed_minute: ''
					};
					this.getWorkingDays();
				});
			},
			editWorkingDay: function(day, hour1, minute1, hour2, minute2) {
				this.new_working_day = {
					day: day.toString(),
					open_hour: hour1.toString(),
					open_minute: minute1.toString(),
					closed_hour: hour2.toString(),
					closed_minute: minute2.toString()
				};
				this.update_button = true;
			},
			updateWorkingDay: function() {
				var update_data = {
					day: Number(this.new_working_day.day),
					open_hour: Number(this.new_working_day.open_hour),
					open_minute: Number(this.new_working_day.open_minute),
					closed_hour: Number(this.new_working_day.closed_hour),
					closed_minute: Number(this.new_working_day.closed_minute)
				};
				this.$http.patch('http://' + document.location.host + '/admin/working_days/update', update_data).then(function(ok) {
					this.new_working_day = {
						day: '',
						open_hour: '',
						open_minute: '',
						closed_hour: '',
						closed_minute: ''
					};
					this.getWorkingDays();
				});
				this.update_button = false;
			},
			cancelEdit: function() {
				this.new_working_day = {
					day: '',
					open_hour: '',
					open_minute: '',
					closed_hour: '',
					closed_minute: ''
				};
				this.update_button = false;
			},
			deleteWorkingDay: function(day) {
				this.$http.delete('http://' + document.location.host + '/admin/working_days/delete/' + day).then(function(ok) {
					this.getWorkingDays();
				});
			}
		},
		ready: function() {
			this.getWorkingDays();
		}
	});
</script>	

@stop