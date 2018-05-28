<template>
  <tr>
    <td class="text-left">
      {{ order.id }}
    </td>
    <td class="text-left">
      {{ order.user.firstname + ' ' + order.user.lastname }}
      <span class="label label-danger" style="margin-left: 5px" v-show="label_urgent">
        <small>!</small>
      </span>
      <span class="label label-warning" style="margin-left: 5px" v-show="label_warning">
        <small>!</small>
      </span>
      <span class="label label-success" style="margin-left: 5px" v-show="label_ok">
        <small>OK</small>
      </span>
      <br>
      <span style="color: #6D6E70">
        <small>{{ order.order_no }}</small>
      </span>
      <br>
      <span style="cursor: pointer" v-show="display_orderdetails">
        <small>
          <a role="button" data-toggle="collapse" href="#collapseDetailOrder{{ order.id }}" aria-expanded="false" aria-controls="collapseDetailOrder">
            Lihat Detail Order
          </a>
          <div class="collapse" id="collapseDetailOrder{{ order.id }}">
            <div style="margin-top: 10px; color: blue">
              <strong>Keterangan :</strong><br />
              {{ order.order_details[0].description }}
              <span style="margin-top: 5px" v-show="order.note">
                <hr style="margin-top: 5px; margin-bottom: 5px"/>
                <strong>Catatan Tambahan :</strong><br />
                {{ order.note }}
              </span>
            </div>
          </div>
        </small>
      </span>
    </td>
    <td class="text-left" width="200px">
      <span class="label label-default" style="display: inline-block; margin-right: 3px; margin-bottom: 3px"
            v-for="item in order.order_details">
        <small>{{ item.categories.name }}</small>
      </span>
    </td>
    <td class="text-right">
      {{ format_date(order.created_at) }}<br>
      <span style="color: #6D6E70">
        <small>{{ 'added ' + time_diff(order.created_at) }}</small>
      </span>
    </td>
    <td class="text-right">
      {{ format_date(order.deadline) }}
      <span v-show="!label_ok">
        <br>
        <span style="color: red" v-show="deadline_less_than_1_day">
          <small>{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
        <span style="color: orange" v-show="deadline_less_than_2_days">
          <small>{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
        <span style="color: #6D6E70" v-show="deadline_more_than_2_days">
          <small>{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
      </span>
    </td>
    <td class="text-right">
      <span v-show="!order.staff_id">
        -
      </span>
      <span v-show="order.staff_id">
        {{ order.staff.firstname }}
        <span style="color: #6D6E70" v-show="order.staff.working_team.length > 0">
          <br>
          <small>{{ order.staff.working_team[0].name }}</small>
        </span>
      </span>
    </td>
    <td class="text-right">
      <span style="color: green" v-show="order.status == 'FINISHED'">
        <small>
          {{ order.status }}<br />
          <span style="color: #6D6E70">{{ time_diff(order.finished_at) }}</span>
        </small>
      </span>
      <span style="color: blue" v-show="order.status == 'PROCESSED'">
        <small>{{ order.status }}</small>
      </span>
      <span style="color: red" v-show="order.status == 'PENDING'">
        <small>{{ order.status }}</small>
      </span>
    </td>
    <td v-show="display_actions">
      <span v-show="order.status != 'FINISHED'">
        <ul class="text-right" style="list-style: none">
          <li v-show="!order.staff_id" v-on:click="take_job_button_pressed(order.order_no)">
            <small>
              <a style="cursor: pointer">Ambil Job</a>
            </small>
          </li>
          <li v-show="order.staff_id" v-on:click="cancel_job_button_pressed(order.order_no)">
            <small>
              <a style="cursor: pointer">Batal</a>
            </small>
          </li>
          <li v-show="order.staff_id" v-on:click="job_done_button_pressed(order.order_no)">
            <small>
              <a style="cursor: pointer">Selesai</a>
            </small>
          </li>
        </ul>
      </span>
    </td>
  </tr>
</template>

<script>
  export default {
    props: [
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
      }
    },
    methods: {
      format_date: function(date) {
        return moment(date).format('DD/MM/YYYY (hh:mm A)');
      },
      time_diff: function(date) {
        return moment(date).fromNow();
      },
      take_job_button_pressed: function(order_no) {
        return this.$dispatch('job_is_taken', order_no);
      },
      cancel_job_button_pressed: function(order_no) {
        return this.$dispatch('job_is_cancelled', order_no);
      },
      job_done_button_pressed: function(order_no) {
        return this.$dispatch('a_job_has_done', order_no);
      }
    }
  }
</script>
