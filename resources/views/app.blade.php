<head>

  <!-- CSS -->
  <link href="/libraries/bootstrap/dist/css/bootstrap.css" rel="stylesheet">
  <link href="/css/sweetalert.css" rel="stylesheet">
  <link href="/css/all.css" rel="stylesheet">

  @yield('css')

</head>

<body>

<!-- START: NAVIGATION BAR -->
<div id="mainPageController">
  <div id="topNavigation">

    <nav class="navbar navbar-default">
      <div class="container-fluid">

        <!-- Brand and toggle get grouped for better mobile display -->
        <div class="navbar-header">
          <button type="button"
                  class="navbar-toggle collapsed"
                  data-toggle="collapse"
                  data-target="#bs-example-navbar-collapse-1"
                  aria-expanded="false">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>

          <!-- START: PRIMASAKTI BRAND -->
          @if(Auth::check())
            <a class="navbar-brand" style="font-family: unda_angleitalic">
              PRIMASAKTI
            </a>
          @else
            <a class="navbar-brand" style="font-family: unda_angleitalic"
               href="{{ URL::action('PagesController@main') }}">
               PRIMASAKTI
            </a>
          @endif
          <!-- END: PRIMASAKTI BRAND -->

        </div>

        <!-- Collect the nav links, forms, and other content for toggling -->
        <div id="bs-example-navbar-collapse-1" class="collapse navbar-collapse">
          @if (Auth::check() == false)
            @include('includes/public_nav')
          @else
            @if (Auth::user()->user_level == 'USER')
              @include('includes/user_nav')
            @elseif (Auth::user()->user_level == 'STAFF' || Auth::user()->user_level == 'SUPERVISOR')
              @include('includes/staff_nav')
            @elseif(Auth::user()->user_level == 'ADMIN')
              @include('includes/admin_nav')
            @endif
          @endif
          <ul class="nav navbar-nav navbar-right">
            <li>
              <a data-toggle="modal" data-target="#businessHoursModal"
                 style="cursor: pointer; color: white">
                <small><b>@{{ day_date }} / @{{ time }}</b></small>
              </a>
            </li>
            <li>
              <a>
                <small>
                  <span v-show="shop_status == 'OPEN'"
                        style="color: green; font-weight: bold">BUKA</span>
                  <span v-show="shop_status == 'CLOSED'"
                        style="color: red; font-weight: bold">TUTUP</span>
                </small>
              </a>
            </li>
            @if (Auth::check() == false)
              <li>
                <a>Welcome, <b>Guest</b></a>
              </li>
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span class="glyphicon glyphicon-user" aria-hidden="true" style="margin-right: 8px"></span>
                  User<span class="caret" style="margin-left: 8px"></span>
                  <ul class="dropdown-menu">
                    <li class="text-right">
                      <a style="cursor: pointer" href="{{ URL::action('MyAuthController@login_form') }}">
                        Login
                      </a>
                    </li>
                    <li class="text-right">
                      <a style="cursor: pointer" href="{{ URL::action('MyAuthController@register_form') }}">
                        Register
                      </a>
                    </li>
                  </ul>
                </a>
              </li>
            @elseif (Auth::check() == true)
              @if (Auth::user()->user_level == 'STAFF' || Auth::user()->user_level == 'SUPERVISOR')
                <li>
                  <a><small>Logged in as <b>STAFF</b></small></a>
                </li>
              @elseif (Auth::user()->user_level == 'ADMIN')
                <li>
                  <a><small>Logged in as <b>ADMIN</b></small></a>
                </li>
              @endif
              <li class="dropdown">
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                  <span class="glyphicon glyphicon-user" aria-hidden="true" style="margin-right: 8px"></span>
                  {{ Auth::user()->firstname.' '.Auth::user()->lastname }}<span class="caret" style="margin-left: 8px"></span>
                  <ul class="dropdown-menu">
                    @if (Auth::user()->user_level == 'STAFF' || Auth::user()->user_level == 'SUPERVISOR')
                      <li class="text-right">
                        <a href="{{ URL::action('StaffPagesController@profile', Auth::id()) }}">Profile Saya</a>
                      </li>
                      <li class="text-right">
                        <a href="{{ URL::action('StaffPagesController@sales', Auth::user()->id) }}">Laporan Penjualan</a>
                      </li>
                    @endif
                    <li class="text-right">
                      <a style="cursor: pointer" href="{{ URL::action('MyAuthController@logout') }}">
                        Logout
                      </a>
                    </li>
                  </ul>
                </a>
              </li>
            @endif
          </ul>
        </div>
      </div>
    </nav>

    <!-- START: MODAL SETUP FOR "WORKING HOURS" -->
    <div id="businessHoursModal" class="modal fade" tabindex="-1" role="dialog">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header bg-primary">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h1 class="modal-title text-center" style="font-family: unda_angleitalic">
              HARI & JAM KERJA
            </h1>
          </div>
          <div class="modal-body">
            <table width="100%">
              <tbody>
                <p class="text-center" v-for="day in business_days">
                  <b>@{{ format_day(day.day) }}</b> <small>(@{{ format_time(day.open_hour, day.open_minute) }} - @{{ format_time(day.closed_hour, day.closed_minute) }})</small>
                </p>
              </tbody>
            </table>
            <div class="text-center">
              <small style="color: red">
                *) Hari Raya / Libur Nasional TUTUP
              </small>
            </div>
          </div>
          <div class="modal-footer bg-info">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
    <!-- END: MODAL SETUP FOR "WORKING HOURS" -->

  </div>
</div>
<!-- END: NAVIGATION BAR -->

<!-- START: CONTENT -->
<div id="content">
  @yield('content')
</div>
<!-- END: CONTENT -->

<div id="footer">
  @include('includes/myfooter')
</div>

<script type="text/javascript" src="/js/jquery-2.2.0.js"></script>
<script type="text/javascript" src="/libraries/bootstrap/dist/js/bootstrap.js"></script>
<script type="text/javascript" src="/js/sweetalert.min.js"></script>
<script type="text/javascript" src="/libraries/moment/moment.js"></script>
<script type="text/javascript" src="/js/vue.js"></script>
<script type="text/javascript" src="/js/vue-resource.js"></script>
<script type="text/javascript" src="/js/app.js"></script>

@yield('js')
@include('includes/flash')

</body>
