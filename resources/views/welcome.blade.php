<!DOCTYPE html>
<html>
<head>
  <title>Pusher Test</title>
  <script src="https://js.pusher.com/8.4.0/pusher.min.js"></script>
  <script>
    Pusher.logToConsole = true;

    const pusher = new Pusher('06e2099b131fedf4043a', {
      cluster: 'ap2',
      forceTLS: true
    });

    const channel = pusher.subscribe('my-channel');

    channel.bind('my-event', function(data) {
      console.log('hello');
      alert('Received: ' + JSON.stringify(data));
    });
  </script>
</head>
<body>
  <h1>Listening with Pusher</h1>
</body>
</html>
