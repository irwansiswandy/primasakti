@extends('app')

@section('content')

<div class="container">
  <!-- START: WARNING -->
  <div class="alert alert-danger text-center">
    <span style="font-size: 12px">
      <b>FORM INI BUKAN UNTUK ONLINE ORDER.</b> Online order hanya bisa dilakukan melalui halaman user / login terlebih dahulu.
    </span>
  </div>
  <!-- END: WARNING -->
  @include('includes.validation_errors')
  {!! Form::open(['method' => 'POST', 'action' => 'PagesController@email_send', 'files' => true]) !!}
    {!! csrf_field() !!}
    <div class="row">
      <div class="col-sm-5">
        <div class="form-group">
          {!! Form::label('about', 'Pick Category') !!}
          {!! Form::select('about', [
            'Penawaran' => 'Penawaran Barang/Harga',
            'Saran'     => 'Saran/Masukan',
            'Keluhan'   => 'Keluhan',
            'Lamaran'   => 'Lamaran Kerja',
            'Lainnya'   => 'Lain-lain'
          ], null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
          {!! Form::label('name', 'Name') !!}
          {!! Form::text('name', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
          <label>Company Name <small>(if no company, just leave this blank)</small></label>
          <input name="company" type="text" class="form-control">
        </div>
        <div class="form-group">
          {!! Form::label('email', 'E-mail Address') !!}
          {!! Form::text('email', null, ['class' => 'form-control']) !!}
        </div>
        <div class="form-group">
          <label>Contact Number <small>(your phone or cellphone number)</small></label>
          {!! Form::text('contact', null, ['class' => 'form-control']) !!}
        </div>
      </div>
      <div class="col-sm-7">
        <div class="form-group">
          {!! Form::label('message', 'Message') !!}
          {!! Form::textarea('message', null, ['rows' => '16', 'class' => 'form-control']) !!}
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          <label for="file">Attach File(s) <small>(if you have no file(s) to attach, just ignore this)</small></label>
          <input type="file" name='file[]' class="form-control" multiple="true">
        </div>
      </div>
      <div class="col-sm-12">
        <div class="form-group">
          {!! Form::submit('Send', ['class' => 'btn btn-primary form-control']) !!}
        </div>
      </div>
    </div>
  {!! Form::close() !!}
</div>

@stop
