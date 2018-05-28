@extends('admin.gallery.template')

@section('add_form')

<h2 class="text-center">
  {{ $form_title }}
</h2>

@include('includes.validation_errors')

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

@stop
