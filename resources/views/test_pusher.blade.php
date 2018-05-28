<!DOCTYPE HTML>

<html>

<head>
  <title>Pusher Test</title>
</head>

<body>
  <script src="https://js.pusher.com/3.2/pusher.min.js"></script>
  <script>

    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('937c40c45b8b04b84c49', {
      cluster: 'ap1',
      encrypted: true
    });

    var channel = pusher.subscribe('my_channel');
    channel.bind('my_event', function(data) {
      return alert(data.message);
    });
  </script>
</body>

</html>