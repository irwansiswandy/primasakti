<!DOCTYPE HTML>

<html>

<head>

  <title>
    Primasakti - Digital Copy & Print Shop | Surabaya, Jawa Timur - Indonesia
  </title>

  <link href="/libraries/bootstrap/dist/css/bootstrap.css" rel="stylesheet"> <!-- BOOTSTRAP CSS -->
  <link href="/css/webfontkit-unda/stylesheet.css" rel="stylesheet"> <!-- PRIMASAKTI FONT CSS -->
  <link href="/css/top-navigation-user.css" rel="stylesheet"> <!-- USER TOP NAVIGATION CSS -->
  <link href="/css/content-footer.css" rel="stylesheet"> <!-- CONTENT & FOOTER CSS -->
  <link href="/css/sweetalert.css" rel="stylesheet"> <!-- SWEETALERT CSS -->
  @yield('header')

</head>

<body>

<div id="dashboardController">
  <!-- START: USER NAVIGATION BAR -->
  <nav class="navbar navbar-default">
    <div class="container-fluid">
      <div class="navbar-header">
        <!-- START: PRIMASAKTI BRAND -->
        <a class="navbar-brand"
           href="{{ URL::action('StaffPagesController@index') }}"
           style="font-family: unda_angleitalic">
           PRIMASAKTI
        </a>
        <!-- END: PRIMASAKTI BRAND -->
      </div>
      <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
        <ul class="nav navbar-nav">
          <li>
            <a style="cursor: pointer" v-on:click.prevent="goToLink('{{ URL::action('UserPagesController@main', $user->id) }}')">Dashboard</a>
          </li>
        </ul>
        <ul class="nav navbar-nav navbar-right">
          <li><a>Logged in as <b>{{ $user->firstname.' '.$user->lastname }}</b></a></li>    
          <li class="dropdown">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Account <span class="caret"></span></a>
            <ul class="dropdown-menu">
              <li class="text-center">
                <a style="cursor: pointer" v-on:click.prevent="goToLink('{{ URL::action('UserPagesController@my_profile', $user->id) }}')">My Profile</a>
              </li>
              <li role="separator" class="divider"></li>
              <li class="text-center"><a href="{{ URL::action('MyAuthController@logout') }}">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- END: USER NAVIGATION BAR -->

  <!-- START: DISPLAY DATE & TIME PANEL -->
  <div style="background-color: #FFF100; padding-top: 8px">
    <div class="container">
      <table style="width: 100%">
        <tr>
          <td style="width: 50%">
            <p class="text-left" style="font-size: 18px">
              <span class="glyphicon glyphicon-phone-alt" aria-hidden="true" style="margin-right: 10px"></span> <b>+62-31-8484808</b>
            </p>
          </td>
          <td style="width: 50%">
            <p class="text-right">
              <button class="btn btn-success" v-on:click.prevent="callChatSystem">Chat System</button>
            </p>
          </td>
        </tr>
      </table>
    </div>
  </div>
  <!-- END: DISPLAY DATE & TIME PANEL -->

  <!-- START: USER PAGES CONTENT -->
  <iframe style="position: absolute; width: 100%; height: 86%; border-style: none" :src="url"></iframe>
  <!-- END: USER PAGES CONTENT -->
</div>

<script src="/js/jquery-2.2.0.js"></script>
<script src="/libraries/bootstrap/dist/js/bootstrap.js"></script>
<script src="/js/sweetalert.min.js"></script>
<script src="/js/vue.js"></script>
<script src="/js/vue-resource.js"></script>

<!-- START: DISPLAY FLASH MESSAGE -->
@if (Session::has('message'))
<script>
  swal({
    type: "success",
    title: "Welcome",
    text: "{{ Session::get('message') }}",
    timer: 1500,
    showConfirmButton: false
  });
</script>
@elseif (Session::has('flash_message'))
<script>
  swal({
    type: "{{ Session::get('flash_type') }}",
    title: "{{ Session::get('flash_title') }}",
    text: "{{ Session::get('flash_message') }}",
    timer: 3000,
    showConfirmButton: false
  });
</script>
@endif
<!-- END: DISPLAY FLASH MESSAGE -->

<!-- START: VUE'S SCRIPT -->
<script>
  var dashboardControllerVue = new Vue({
    el: '#dashboardController',
    data: {
      url: '{{ URL::action("UserPagesController@main", $user->id) }}'
    },
    methods: {
      goToLink: function(passed_url) {
        var acceptedURL = passed_url;
        this.url = acceptedURL;
      },
      callChatSystem: function() {
        var chatSystemURL = 'http://' + document.location.host;
        var chatWindow = window.open(chatSystemURL, '', 'width = 600, height = 600');
        var content = document.body.innerHTML;
        chatWindow.document.write(content);
      }
    }
  });
</script>
<!-- END: VUE'S SCRIPT -->

</body>

</html>
