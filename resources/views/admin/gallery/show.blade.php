@extends('admin.dashboard')

@section('header')

<link href="/css/dropzone.css" rel="stylesheet">

@stop

@section('content')

<!-- START: PAGE HEADER -->
<h3 id="page-header-admin">
  <div class="container">
    <table style="width: 100%">
      <tr>
        <td style="width: 50%" class="text-left">
          <b>Manage Photos : {{ $category->name }}</b>
        </td>
        <td style="width: 50%" class="text-right">
          <a href="{{ URL::action('AdminPagesController@gallery_add_category') }}">
            <button class="btn btn-info">
              <span class="glyphicon glyphicon-backward" aria-hidden="true"></span> Back
            </button>
          </a>
        </td>
      </tr>
    </table>
  </div>
</h3>
<br>
<!-- END: PAGE HEADER -->
<div class="container">
  <!-- DISPLAY PHOTOS -->
  <div class="row">
    @foreach ($photos as $photo)
      <div class="col-xs-12 col-md-2">
        <a class="thumbnail" href="{{ URL::to('/').'/'.$photo->file_path }}">
          <img src="{{ URL::to('/').'/'.$photo->thumb_path }}">
        </a>
        <p class="text-center">
          <a href="{{ URL::action('AdminPagesController@gallery_delete_photo', [$category->id, $photo->id]) }}">
            <button class="btn btn-danger">Delete This Photo</button>
          </a>
        </p>
        <br>
      </div>
    @endforeach
  </div>
  <hr />
  <!-- START: DISPLAY DROPZONE -->
  <p><b>Upload Photo(s)</b> <small>(max. filesize {{ ini_get('upload_max_filesize') }})</small></p>
  <form id="photosUploader"
        action=""
        class="dropzone">
    {!! csrf_field() !!}
  </form>
  <!-- END: DISPLAY DROPZONE -->

@stop

@section('footer')

<script src="/js/dropzone.js"></script>
<script>
  Dropzone.options.photosUploader = {
    url: document.URL + '/add_photos',
    acceptedFiles: 'image/*',
    paramName: 'file', // The name that will be used to transfer the file
    maxFilesize: 10, // MB
    accept: function(file, done) {
      if (file.name == "justinbieber.jpg") {
        done("Naha, you don't.");
      }
      else { 
        done();
      }
    }
  };
</script>

@stop
