<ul class="nav navbar-nav">
  <li>
    <a href="{{ URL::action('AdminPagesController@index') }}">
      Dashboard
    </a>
  </li>
  <li>
    <a href="{{ URL::action('AdminPagesController@sales') }}">
      Sales
    </a>
  </li>
  <li class="dropdown">
    <a href=""
       class="dropdown-toggle"
       data-toggle="dropdown"
       role="button"
       aria-haspopup="true"
       aria-expanded="false">
       Manage Data <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      <li>
        <a href="{{ URL::action('AdminPagesController@working_days') }}">
          Working Days
        </a>
      </li>
      <li>
        <a href="{{ URL::action('AdminPagesController@holidays_form') }}">
          Holidays
        </a>
      </li>
      <li>
        <a href="{{ URL::action('UsersController@index') }}">
          Users
        </a>
      </li>
      <li>
        <a href="{{ URL::action('AdminPagesController@categories_index') }}">
          Products
        </a>
      </li>
    </ul>
  </li>
  <li class="dropdown">
    <a href=""
       class="dropdown-toggle"
       data-toggle="dropdown"
       role="button"
       aria-haspopup="true"
       aria-expanded="false">
       Manage Pages <span class="caret"></span>
    </a>
    <ul class="dropdown-menu">
      <li>
        <a href="{{ URL::action('AdminPagesController@manage_gallery') }}">
          Gallery
        </a>
      </li>
    </ul>
  </li>
</ul>