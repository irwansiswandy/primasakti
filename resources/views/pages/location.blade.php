@extends('app')

@section('content')

<!-- START: DISPLAY MAP -->
<div class="container">
  <div id="googleMap" style="width:100%; height:450px; margin-left: auto; margin-right: auto"></div>
</div>
<!-- END: DISPLAY MAP -->
<br>
<p class="text-center">
  <b>LOKASI WORKSHOP :</b><br>
  Jl. Raya Tenggilis No. 34 - 34A<br>
  Surabaya 60292<br>
  Jawa Timur, Indonesia
</p>

@stop

@section('js')

<script src="http://maps.googleapis.com/maps/api/js"></script> <!-- GOOGLE MAP JS -->
<script>
  var myCenter = new google.maps.LatLng(-7.321216,112.750288);
  function initialize() {
    var mapProp = {
      center:myCenter,
      zoom:17,
      mapTypeId:google.maps.MapTypeId.ROADMAP
    };
    var map = new google.maps.Map(document.getElementById("googleMap"),mapProp);
    var marker = new google.maps.Marker({
      position:myCenter,
    });
    marker.setMap(map);
  }
  google.maps.event.addDomListener(window, 'load', initialize);
</script>

@stop
