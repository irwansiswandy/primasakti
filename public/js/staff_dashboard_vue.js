var staffDashboardVue = new Vue({
	el: '#staff-dashboard',
	data: {
		/* INFO STAFF PANEL */
		info_staff: '',
		this_month_sales: {
			team_names: [],
			team_sales: []
		}
	},
	computed: {
		this_month: function() {
			return moment().format('MMMM');
		},
		this_year: function() {
			return moment().format('YYYY');
		},
		/* INFO STAFF */
		team_name: function() {
			if (this.info_staff.working_team.length == 0) {
				return '-';
			}
			else {
				return this.info_staff.working_team[0].name;
			}
		},
		total_team_members: function() {
			if (this.info_staff.working_team.length == 0) {
				return '-';
			}
			else {
				return this.info_staff.working_team[0].staff.length + ' orang';
			}
		}
	},
	methods: {
		formatDate: function(date) {
			return moment(date).format('DD/MM/YYYY (hh:mm A)');
		},
		/* INFO STAFF */
		get_info_staff_data: function() {
			return this.$http.get(document.location.href + '/get_info_staff_data').then(
				(response) => {
					return this.$set('info_staff', response.data);
				},
				(response) => {
					return this.get_info_staff_data();
				}
			);
		},
		get_this_month_sales_data: function() {
			return this.$http.get(document.location.href + '/get_this_month_sales_data').then(
				(response) => {
					return [
						this.$set('this_month_sales.team_names', response.data.team_names),
						this.$set('this_month_sales.team_sales', response.data.team_sales),
						set_monthlySalesChart_data(this.this_month_sales.team_names, this.this_month_sales.team_sales)
					];
				},
				(response) => {
					return this.get_this_month_sales_data();
				}
			);
		},
		handle_sales_updated: function(data) {
			var team_name = data.staff.working_team[0].name;
			var team_index = this.this_month_sales.team_names.indexOf(team_name);

			return update_monthlySalesChart_data(team_index, data.total);
		},
		handle_invoice_deleted: function(data) {
			var team_name = data.staff.working_team[0].name;
			var team_index = this.this_month_sales.team_name.indexOf(team_name);
			var total_in_minus = data.total * -1;

			return update_monthlySalesChart_data(team_index, total_in_minus);
		}
	},
	ready: function() {
		return [
			this.get_info_staff_data(),
			this.get_this_month_sales_data()
		];
	}
});