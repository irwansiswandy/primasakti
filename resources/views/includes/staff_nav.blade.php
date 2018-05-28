<ul class="nav navbar-nav">
  <li>
    <a href="{{ URL::action('StaffPagesController@index', Auth::user()->id) }}">Dashboard</a>
  </li>
  <li>
    <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
      Tools <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      <li><a href="#">Kirim SMS</a></li>
    </ul>
  </li>
</ul>
