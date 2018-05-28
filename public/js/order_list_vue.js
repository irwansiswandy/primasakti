Vue.component('orderlist', {
  template: '#orderlistTemplate',
    props: [
      'staffid',
      'orderdetails',
      'actions',
      'order'
    ],
    computed: {
      working_team: function() {
        if (this.order.staff.working_team.length > 0) {
          return this.order.staff.working_team[0].name;
        }
        else {
          return '-';
        }
      },
      deadline_less_than_1_day: function() {
        var deadline_in_hours = moment().diff(this.order.deadline, 'hours') * -1;
        if (deadline_in_hours < 24) {
          return true;
        }
        else {
          return false;
        }
      },
      deadline_less_than_2_days: function() {
        var deadline_in_hours = moment().diff(this.order.deadline, 'hours') * -1;
        if (deadline_in_hours < 48) {
          if (deadline_in_hours > 24) {
            return true;
          }
          else {
            return false;
          }
        }
        else {
          return false;
        }
      },
      deadline_more_than_2_days: function() {
        var deadline_in_hours = moment().diff(this.order.deadline, 'hours') * -1;
        if (deadline_in_hours > 48) {
          return true;
        }
        else {
          return false;
        }
      },
      label_urgent: function() {
        if (this.deadline_less_than_1_day) {
          if (this.order.status == 'PENDING') {
            return true;
          }
          else {
            return false;
          }
        }
        else {
          return false;
        }
      },
      label_warning: function() {
        if (this.deadline_less_than_2_days) {
          if (this.order.status == 'PENDING') {
            return true;
          }
          else {
            return false;
          }
        }
        else {
          return false;
        }
      },
      label_ok: function() {
        if (this.order.status == 'FINISHED') {
          return true;
        }
        else {
          return false;
        }
      },
      /* ACTIONS COLUMN CONDITIONING */
      display_orderdetails: function() {
        if (this.orderdetails == 'show') {
          return true;
        }
        else {
          return false;
        }
      },
      display_actions: function() {
        if (this.actions == 'show') {
          return true;
        }
        else {
          return false;
        }
      },
      /* END: ACTIONS COLUMN CONDITIONING */

      /* ACTION BUTTONS CONDITIONING */
      show_takejob_button: function() {
        if (this.order.status == 'PENDING' && !this.order.staff_id) {
          return true;
        }
        else {
          return false;
        }
      },
      show_cancel_button: function() {
        if (this.order.status == 'PENDING' && this.order.staff_id == this.staffid) {
          return true;
        }
        else {
          return false;
        }
      },
      show_process_button: function() {
        if (this.order.status == 'PENDING' && this.order.staff_id == this.staffid) {
          return true;
        }
        else {
          return false;
        }
      },
      show_cancel_process_button: function() {
        if (this.order.status == 'PROCESSED' && this.order.staff_id == this.staffid) {
          return true;
        }
        else {
          return false;
        }
      },
      show_finished_button: function() {
        if (this.order.status == 'PROCESSED' && this.order.staff_id == this.staffid) {
          return true;
        }
        else {
          return false;
        }
      },
      show_taken_button: function() {
        if (this.order.status == 'FINISHED' && !this.order.taken) {
          return true;
        }
        else {
          return false;
        }
      }
      /* END: ACTION BUTTONS CONDITIONING */
    },
    methods: {
      format_date: function(date) {
        return moment(date).format('DD/MM/YYYY (hh:mm A)');
      },
      time_diff: function(date) {
        return moment(date).fromNow();
      },
      takejob_button_pressed: function(order_no, staff_id) {
        return this.$dispatch('a_job_has_been_taken', order_no, staff_id);
      },
      cancel_button_pressed: function(order_no) {
        return this.$dispatch('a_job_has_been_cancelled', order_no);
      },
      finished_button_pressed: function(order_no) {
        return this.$dispatch('a_job_has_been_finished', order_no);
      },
      process_button_pressed: function(order_no) {
        return this.$dispatch('a_job_is_being_processed', order_no);
      },
      cancel_process_button_pressed: function(order_no) {
        return this.$dispatch('a_job_process_has_been_cancelled', order_no);
      },
      taken_button_pressed: function(order_no) {
        return this.$dispatch('customer_has_accepted', order_no);
      }
    }
  });

var orderlistVue = new Vue({
	http: {
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			}
	},
	el: '#orderlist',
	data: {
		orders: []
	},
	computed: {
		total_order: function() {
			return this.orders.length;
		}
	},
	methods: {
		get_orders: function() {
			return this.$http.get(document.location.href + '/order_list/get_orders').then(
				(response) => {
					return this.$set('orders', response.data);
				}
			);
		},
		handle_a_job_has_been_taken: function(order_no, staff_id) {
      var url_name = 'a_job_taken';

			var passed_data = {
				order_no: order_no,
        staff_id: staff_id
			};

			return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
				(response) => {
					return this.get_orders();
				}
			);
		},
		handle_a_job_has_been_cancelled: function(order_no) {
      var url_name = 'a_job_cancelled';
			
      var passed_data = {
				order_no: order_no,
        staff_id: ''
			};

			return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
				(response) => {
					return this.get_orders();
				}
			);
		},
		handle_a_job_has_been_finished: function(order_no) {
      var url_name = 'a_job_finished';

      var current_year = moment().year();
      var current_month = moment().month() + 1;
      var current_day = moment().date();
      var current_hour = moment().hour();
      var current_minute = moment().minute();
      var current_second = moment().second();
      var date_time = current_year + '-' + current_month + '-' + current_day + ' ' + current_hour + ':' + current_minute + ':' + current_second;

			var passed_data = {
				order_no: order_no,
        status: 2,
        finished_at: date_time
			};
			
      return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
				(response) => {
					return this.get_orders();
				}
			);
		},
    handle_a_job_is_being_processed: function(order_no) {
      var url_name = 'a_job_processed';

      var passed_data = {
        order_no: order_no,
        status: 1
      };

      return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
        (response) => {
          return this.get_orders();
        }
      );
    },
    handle_a_job_process_has_been_cancelled: function(order_no) {
      var url_name = 'cancel_job_processing';

      var passed_data = {
        order_no: order_no,
        status: 0
      };

      return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
        (response) => {
          return this.get_orders();
        }
      );
    },
    handle_customer_has_accepted: function(order_no) {
      var url_name = 'order_received_by_customer';

      var current_year = moment().year();
      var current_month = moment().month() + 1;
      var current_day = moment().date();
      var current_hour = moment().hour();
      var current_minute = moment().minute();
      var current_second = moment().second();
      var date_time = current_year + '-' + current_month + '-' + current_day + ' ' + current_hour + ':' + current_minute + ':' + current_second;

      var passed_data = {
        order_no: order_no,
        taken: true,
        taken_at: date_time
      };

      return this.$http.post(document.location.href + '/order_list/update/' + url_name, passed_data).then(
        (response) => {
          return this.get_orders();
        }
      );
    }
	},
	events: {
		'a_job_has_been_taken': function(order_no, staff_id) {
			return this.handle_a_job_has_been_taken(order_no, staff_id);
		},
		'a_job_has_been_cancelled': function(order_no) {
      return this.handle_a_job_has_been_cancelled(order_no);
		},
		'a_job_has_been_finished': function(order_no) {
			return this.handle_a_job_has_been_finished(order_no);
		},
    'a_job_is_being_processed': function(order_no) {
      return this.handle_a_job_is_being_processed(order_no);
    },
    'a_job_process_has_been_cancelled': function(order_no) {
      return this.handle_a_job_process_has_been_cancelled(order_no);
    }
	},
	ready: function() {
		return [
			this.get_orders()
		];
	}
});
