var mainPageVue = new Vue({
  el: '#mainPageController',
  data: {
    server_time: {
      date: '',
      timezone_type: '',
      timezone: ''
    },
    business_days: [],
    today_schedule: ''
  },
  methods: {
    get_server_time: function() {
      return this.$http.get('http://' + document.location.host + '/get_server_time').then(
        (response) => {
          return [
            this.$set('server_time.date', response.data.server_time.date),
            this.$set('server_time.timezone_type', response.data.server_time.timezone_type),
            this.$set('server_time.timezone', response.data.server_time.timezone)
          ];
        },
        (response) => {
          return this.get_server_time();
        });
    },
    get_business_days: function() {
      return this.$http.get('http://' + document.location.host + '/get_business_days').then(
        (response) => {
          return this.$set('business_days', response.data);
        },
        (response) => {
          return this.get_business_days();
        });
    },
    get_today_schedule: function() {
      return this.$http.get('http://' + document.location.host + '/get_today_schedule').then(
        (response) => {
          return this.$set('today_schedule', response.data);
        },
        (response) => {
          return this.get_today_schedule();
        });
    },
    show_live_clock: function() {
      // UPDATE TIME EVERY MINUTE
      return setInterval(this.get_server_time, 60000);
    },
    format_day: function(day) {
      return moment().day(day).format('dddd');
    },
    format_time: function(hours, minutes) {
      return moment().hour(hours).minute(minutes).format('hh:mm A');
    }
  },
  computed: {
    day_date: function() {
      return moment(this.server_time.date).format('dddd, D MMM YYYY');
    },
    time: function() {
      return moment(this.server_time.date).format('hh:mm A');
    },
    shop_status: function() {
      // INITIALIZE CURRENT TIME
      var current_hour = moment(this.server_time.date).hour();
      var current_minute = moment(this.server_time.date).minute();

      // DETERMINE SHOP STATUS IF OPEN/CLOSED
      if (current_hour >= this.today_schedule.open_hour) {
        if (current_hour < this.today_schedule.closed_hour) {
          return 'OPEN';
        }
        else if (current_hour == this.today_schedule.closed_hour) {
          if (current_minute <= this.today_schedule.closed_minute) {
            return 'OPEN';
          }
          else {
            return 'CLOSED';
          }
        }
        else {
          return 'CLOSED';
        }
      }
      else {
        return 'CLOSED';
      }
    }
  },
  ready: function() {
    return [
      this.get_server_time(),
      this.get_business_days(),
      this.get_today_schedule(),
      this.show_live_clock()
    ];
  }
});
