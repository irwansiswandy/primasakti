@extends('admin.dashboard')

@section('content')

@include('includes.validation_errors')

{{-- HOLIDAYS FORM --}}
@yield('holidays_form')
{{-- END HOLIDAYS FORM --}}

{{-- DISPLAY HOLIDAYS LIST --}}
  <h2 class="text-center">
    Holidays List<br />
    <small>
      Total ({{ $total_holidays }}) Added Holidays
    </small>
  </h2>

  <table class="table table-striped">
    <tr>
      <th>Dates</th>
      <th>Themes</th>
      <th>Added</th>
      <th></th>
    </tr>
    @foreach ($holidays as $holiday)
      <tr>
        <td><span style="color: red">{{ $holiday->date->toFormattedDateString() }}</span></td>
        <td><span style="color: red">{{ $holiday->theme }}</span></td>
        <td>{{ $holiday->created_at->toDayDateTimeString() }}</td>
        <td class="text-right">
          <a href="{{ URL::action('AdminPagesController@holiday_edit', $holiday->id) }}"><button class="btn btn-primary">Edit</button></a>
          <a href="{{ URL::action('AdminPagesController@holiday_delete', $holiday->id) }}"><button class="btn btn-danger">Delete</button></a>
        </td>
      </tr>
    @endforeach
  </table>
{{-- END DISPLAY HOLIDAYS LIST --}}

@stop
