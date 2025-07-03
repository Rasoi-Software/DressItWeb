<!DOCTYPE html>
<html>

<head>
    <title>Pusher Event Test</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>

    <h2>Waiting for Pusher Event...</h2>
    <div id="output">No event yet</div>

    <script src="https://js.pusher.com/7.2/pusher.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-echo/dist/echo.iife.js"></script>

    <script>
        Pusher.logToConsole = true;

        window.Echo = new Echo.default({
            broadcaster: 'pusher',
            key: 'eec36fdf79a6fb6cb417', // ← use development key
            cluster: 'ap2',
            forceTLS: true
        });



        window.Echo.channel('chat-channel')
            .listen('.chat-event', (e) => {
                console.log('✅ Event Received:', e);
                document.getElementById('output').innerText = e.message;
                alert("Pusher event received: " + e.message);
            });
    </script>

</body>

</html>