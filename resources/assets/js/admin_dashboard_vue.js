var adminDashboardVue = new Vue({
	el: '#adminDashboard',
	data: {
		/* DAILY SALES CHART */
		daily_sales: {
			start_date: '',
			end_date: '',
			days: [],
			dates: [],
			sales: []
		},
		/* TODAY SALES PANEL */
		today_sales: {
			invoices: []
		}
	},
	computed: {
		/* TODAY SALES PANEL */
		today_sales_total: function() {
			var total = 0;
			for (i=0; i<this.today_sales.invoices.length; i++) {
				total += Number(this.today_sales.invoices[i].total);
			}
			return total;
		},
		today_sales_number_of_invoices: function() {
			return this.today_sales.invoices.length;
		},
		/* DAILY SALES CHART */
		labels_for_chart: function() {
			var labels = [];
			for (i=0; i<this.daily_sales.days.length; i++) {
				labels[i] = moment(this.daily_sales.dates[i].date).format('dddd') + ' (' + this.daily_sales.days[i] + ')';
			}
			return labels;
		}
	},
	methods: {
		formatTime: function(date) {
			return moment(date).format('hh:mm A');
		},
		formatDate: function(date) {
			return moment(date).format('DD/MM/YYYY');
		},
		/* THIS MONTH CHART METHODS */
		get_daily_sales_chart_data: function() {
			return this.$http.get('http://' + document.location.host + '/admin/dashboard/get_daily_sales_chart_data').then(
				(response) => {
					return [
						this.$set('daily_sales.start_date', response.data.start_date),
						this.$set('daily_sales.end_date', response.data.end_date),
						this.$set('daily_sales.days', response.data.days),
						this.$set('daily_sales.dates', response.data.dates),
						this.$set('daily_sales.sales', response.data.sales),
						set_dailySalesChart_data(this.labels_for_chart, this.daily_sales.sales)
					];
				},
				(response) => {
					return this.get_daily_sales_chart_data();
				}
			);
		},
		handle_sales_updated: function(option, data) {
			var updated_day = moment(data.created_at).date();
			var days_index = Number(updated_day) - 1;

			if (option == 'add') {
				return update_dailySalesChart_data(days_index, data.total);
			}
			else if (option == 'delete') {
				var total_in_minus = data.total * -1;
				return update_dailySalesChart_data(days_index, total_in_minus);
			}
			else {
				// DO NOTHING
			}
		},
		/* TODAY SALES METHODS */
		get_today_sales_data: function() {
			return this.$http.get('http://' + document.location.host + '/admin/dashboard/get_today_sales_data').then(
				(response) => {
					return this.$set('today_sales.invoices', response.data);
				},
				(response) => {
					return this.get_today_sales_data();
				}
			);
		}
	},
	ready: function() {
		return [
			this.get_today_sales_data(),
			this.get_daily_sales_chart_data()
		];
	}
});