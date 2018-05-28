<template id="orderlistTemplate">
  <tr>
    <td class="text-left">
      @{{ order.id }}
    </td>
    <td class="text-left">
      <a style="cursor: pointer" type="button"
         data-toggle="modal" data-target="#userInfoModal@{{ order.id }}">
        @{{ order.user.firstname + ' ' + order.user.lastname }}
      </a>
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
        <small>@{{ order.order_no }}</small>
      </span>
      <br>
      <span style="cursor: pointer" v-show="display_orderdetails">
        <small>
          <a role="button" data-toggle="collapse" href="#collapseDetailOrder@{{ order.id }}" aria-expanded="false" aria-controls="collapseDetailOrder">
            Lihat Detail Order
          </a>
          <div class="collapse" id="collapseDetailOrder@{{ order.id }}">
            <div style="color: blue; margin-top: 5px" v-show="order.down_payment > 0">
              <strong>Uang Muka (DP) :</strong><br>
              @{{ order.down_payment | currency 'Rp ' }}
            </div>
            <div style="color: blue; margin-top: 5px">
              <strong>Keterangan :</strong><br />
              <span v-for="detail in order.order_details" style="display: block">
                @{{ detail.description }}
              </span>
            <div style="color: blue; margin-top: 5px" v-show="order.note">
              <strong>Catatan Tambahan :</strong><br />
              @{{ order.note }}
            </div>
          </div>
        </small>
      </span>
    </td>
    <td class="text-left">
      <span style="display: block" v-for="item in order.order_details">
        <small><strong>@{{ item.categories.name }}</strong></small>
      </span>
    </td>
    <td class="text-right">
      @{{ format_date(order.created_at) }}<br>
      <span style="color: #6D6E70">
        <small>@{{ 'added ' + time_diff(order.created_at) }}</small>
      </span>
    </td>
    <td class="text-right">
      @{{ format_date(order.deadline) }}
      <span v-show="!label_ok">
        <br>
        <span style="color: red" v-show="deadline_less_than_1_day">
          <small>@{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
        <span style="color: orange" v-show="deadline_less_than_2_days">
          <small>@{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
        <span style="color: #6D6E70" v-show="deadline_more_than_2_days">
          <small>@{{ 'must finished ' + time_diff(order.deadline) }}</small>
        </span>
      </span>
    </td>
    <td class="text-right">
      <span v-show="!order.staff_id">
        -
      </span>
      <span v-show="order.staff_id">
        @{{ order.staff.firstname }}
        <span style="color: #6D6E70" v-show="order.staff.working_team.length > 0">
          <br>
          <small>@{{ order.staff.working_team[0].name }}</small>
        </span>
      </span>
    </td>
    <td class="text-right">
      <span style="color: green" v-show="order.status == 'FINISHED'">
        <small>
          <strong>@{{ order.status }}</strong><br />
          <span style="color: #6D6E70">
            @{{ format_date(order.finished_at) }}<br>
            @{{ time_diff(order.finished_at) }}
          </span>
        </small>
      </span>
      <span style="color: blue" v-show="order.status == 'PROCESSED'">
        <small><strong>@{{ order.status }}</strong></small>
      </span>
      <span style="color: red" v-show="order.status == 'PENDING'">
        <small><strong>@{{ order.status }}</strong></small>
      </span>
    </td>
    <td v-show="display_actions">
      <ul class="text-right" style="list-style: none">
        <li v-show="show_takejob_button" v-on:click="takejob_button_pressed(order.order_no, staffid)">
          <small>
            <a style="cursor: pointer">Ambil Job</a>
          </small>
        </li>
        <li v-show="show_cancel_button" v-on:click="cancel_button_pressed(order.order_no)">
          <small>
            <a style="cursor: pointer">Batalkan Job</a>
          </small>
        </li>
        <li v-show="show_process_button" v-on:click="process_button_pressed(order.order_no)">
          <small>
            <a style="cursor: pointer">Kerjakan</a>
          </small>
        </li>
        <li v-show="show_cancel_process_button" v-on:click="cancel_process_button_pressed(order.order_no)">
          <small>
            <a style="cursor: pointer">Batalkan Kerja</a>
          </small>
        </li>
        <li v-show="show_finished_button" v-on:click="finished_button_pressed(order.order_no)">
          <small>
            <a style="cursor: pointer">Selesai</a>
          </small>
        </li>
        <li v-show="show_taken_button" v-on:click="taken_button_pressed(order.order_no)">
          <small>
            <a style="cursor: pointer">Sudah<br>diambil</a>
          </small>
        </li>
      </ul>
    </td>
  </tr>

  <!-- USER INFO MODAL -->
  <div class="modal fade" id="userInfoModal@{{ order.id }}" tabindex="-1" role="dialog" aria-labelledby="userInfoModalLabel">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
          <h1 class="text-center" style="font-family: unda_angleitalic">
            DETAIL PELANGGAN
          </h1>
        </div>
        <div class="modal-body">
          <div class="row">
            <div class="col-xs-4">
              <p class="text-right">ID : </p>
              <p class="text-right">Nama Lengkap : </p>
              <p class="text-right">E-mail : </p>
              <p class="text-right">No. Telp : </p>
              <p class="text-right">No. HP : </p>
              <p class="text-right">Alamat : </p>
            </div>
            <div class="col-xs-8">
              <p class="text-left">@{{ order.user.id }}</p>
              <p class="text-left">@{{ order.user.firstname + ' ' + order.user.lastname }}</p>
              <p class="text-left">@{{ order.user.email }}</p>
              <p class="text-left">@{{ order.user.phone }}</p>
              <p class="text-left">@{{ order.user.cellphone }}</p>
              <p class="text-left">
                @{{ order.user.address }}<br>
                @{{ order.user.city + ' ' + order.user.postcode }}<br>
                @{{ order.user.state + ', ' + order.user.country }}
              </p>
            </div>
          </div>
        </div>
        <div class="modal-footer bg-info">
          <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
      </div>
    </div>
  </div>
  <!-- END: USER INFO MODAL -->
</template>
