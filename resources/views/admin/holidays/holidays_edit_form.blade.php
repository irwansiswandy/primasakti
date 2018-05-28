@extends('admin.holidays.template')

@section('holidays_form')

<div class="well">

  {!! Form::model($current_holiday, ['method' => 'PATCH', 'action' => ['AdminPagesController@holiday_update', $current_holiday->id]]) !!}

    <h2 class="text-center">
      {{ $form_title }}
    </h2>

    <br />

    {!! csrf_field() !!}

    <div class="row">

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('date', 'Pick A Date') !!}
          {!! Form::input('date', 'date', $current_holiday->date->toDateString(), ['class' => 'form-control']) !!}
        </div>
      </div>

      <div class="col-md-6">
        <div class="form-group">
          {!! Form::label('theme', 'Holiday\'s Theme') !!}
          {!! Form::text('theme', null, ['class' => 'form-control']) !!}
        </div>
      </div>

    </div>

    <div class="form-group">
      {!! Form::submit($button_title, ['class' => 'btn btn-primary form-control']) !!}
    </div>

  {!! Form::close() !!}

</div>

@stop
