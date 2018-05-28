@extends('admin.dashboard')

@section('content')

<!-- START: PAGE HEADER -->
<h3 id="page-header-admin">
  <div class="container">
    <b>Manage Gallery</b>
  </div>
</h3>
<br>
<!-- END: PAGE HEADER -->

@include('includes.validation_errors')

<!-- START: -->
<div class="container">
  <div class="well">
    <form method="POST" action="">
      {!! csrf_field() !!}
      <div class="form-group">
        <label>Add New Category</label>
        <input type="text" name="name" class="form-control">
      </div>
      <div class="form-group">
        <button type="submit" class="btn btn-primary form-control">Add New Category</button>
      </div>
    </form>
  </div>
</div>
<!-- END: -->

<!-- START: CATEGORIES LIST -->
<div class="container">
  <table class="table table-striped">
    <tr>
      <th># ID</th>
      <th>Category Name</th>
      <th>Total Photos</th>
      <th></th>
    </tr>
    @foreach ($categories as $category)
    <tr>
      <td>{{ $category->id }}</td>
      <td>{{ $category->name }}</td>
      <td>{{ count($category->photos) }}</td>
      <td class="text-right">
        <a href="{{ URL::action('AdminPagesController@gallery_show', $category->id) }}"><button class="btn btn-primary">Manage</button></a>
        <a href="{{ URL::action('AdminPagesController@gallery_delete_category', $category->id) }}"><button class="btn btn-danger">Delete</button></a>
      </td>
    </tr>
    @endforeach
  </table>
</div>
<!-- END: CATEGORIES LIST -->

@stop
