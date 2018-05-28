@extends('app')

@section('content')

  <!-- START: SLIDESHOW -->
  <div id="carousel-example-generic" class="carousel slide" data-ride="carousel">
    <!-- Indicators -->
    <ol class="carousel-indicators">
      <li data-target="#carousel-example-generic" data-slide-to="0" class="active"></li>
      <li data-target="#carousel-example-generic" data-slide-to="1"></li>
      <li data-target="#carousel-example-generic" data-slide-to="2"></li>
      <li data-target="#carousel-example-generic" data-slide-to="3"></li>
      <li data-target="#carousel-example-generic" data-slide-to="4"></li>
    </ol>
    <!-- Wrapper for slides -->
    <div class="carousel-inner" role="listbox">
      <div class="item active">
        <img src="/slideshow/01.jpg" alt="...">
        <div class="carousel-caption">
          ...
        </div>
      </div>
      <div class="item">
        <img src="/slideshow/02.jpg" alt="...">
        <div class="carousel-caption">
          ...
        </div>
      </div>
      <div class="item">
        <img src="/slideshow/03.jpg" alt="...">
        <div class="carousel-caption">
          ...
        </div>
      </div>
      <div class="item">
        <img src="/slideshow/04.jpg" alt="...">
        <div class="carousel-caption">
          ...
        </div>
      </div>
      <div class="item">
        <img src="/slideshow/05.jpg" alt="...">
        <div class="carousel-caption">
          ...
        </div>
      </div>
    </div>
    <!-- Controls -->
    <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
      <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
      <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>
  </div>
  <!-- END: SLIDESHOW -->

  <br />

  <!-- START: QUICK TOOLS -->
  <div class="row">
    <div class="col-sm-4">
      <div id="orderTracker" class="panel panel-info">
        <input type="hidden" id="token" value="{{ csrf_token() }}" />
        <div class="panel-heading">
          <span class="glyphicon glyphicon-search" aria-hidden="true"></span><span style="margin-left: 10px"><b>Track Order</b></span>
        </div>
        <div class="panel-body">
          <div class="form-group">
            <label>No. Tanda Terima Order</label>
            <div class="row">
              <div class="col-xs-9">
                <input type="text" class="form-control" v-model="order_no">
              </div>
              <div class="col-xs-3">
                <button class="btn btn-info form-control" v-on:click.prevent="track_order">Track</button>
              </div>
            </div>
          </div>
          <ul style="color: red" v-show="errors">
            <li v-for="error in errors">
                <small>@{{ error }}</small>
            </li>
          </ul>
          <div class="well" v-show="order_status">
            <small>
              @{{ order_status }}
            </small>
          </div>
        </div>
      </div>
    </div>
  </div>
  <!-- END: QUICK TOOLS -->

  <!-- OUR SERVICES -->
  <legend class="text-center">
    <h3><b>OUR SERVICES</b></h3>
  </legend>

  <div class="row">
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Copy & Print</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">BW Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Color Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Large-Format</p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Copy & Print</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">BW Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Color Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Large-Format</p>
      </div>
    </div>
  </div>
  <!-- END OUR SERVICES-->

  <!-- OUR SERVICES -->
  <legend class="text-center">
    <h3><b>POPULAR PRODUCTS</b></h3>
  </legend>

  <div class="row">
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Copy & Print</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">BW Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Color Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Large-Format</p>
      </div>
    </div>
  </div>

  <div class="row">
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Copy & Print</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">BW Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Color Printing</p>
      </div>
    </div>
    <div class="col-sm-3">
      <div class="well">
        <p class="text-center">Large-Format</p>
      </div>
    </div>
  </div>
  <!-- END OUR SERVICES-->

@stop

@section('js')

<script type="text/javascript">
  var orderTrackerVue = new Vue({
    http: {
      headers: {
        'X-CSRF-TOKEN': $('#token').val()
      }
    },
    el: '#orderTracker',
    data: {
      order_no: '',
      order_status: '',
      errors: ''
    },
    methods: {
      track_order: function() {
        var data = {
          order_no: this.order_no
        };

        this.errors = '';

        return this.$http.post(location.href + 'track_order', data).then(
          (response) => {
            return this.order_status = response.data;
          },
          (response) => {
            return this.$set('errors', response.data);
          }
        );
      }
    }
  });
</script>

@stop
