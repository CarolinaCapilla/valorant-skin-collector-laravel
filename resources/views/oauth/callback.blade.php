<!doctype html>
<html>

<head>
    <meta charset="utf-8">
    <title>OAuth complete</title>
</head>

<body>
    <script>
        (function() {
            const payload = {
                status: 'success',
                user: @json($user)
            };
            // send to opener window (frontend must check origin)
            try {
                window.opener.postMessage(payload, window.location.origin || '*');
            } catch (e) {
                // fallback - redirect to frontend home with hash
                window.location = 'http://localhost:3000/?auth=success';
            }
            window.close();
        })();
    </script>
    <p>Closing...</p>
</body>

</html>
