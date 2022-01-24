<html>
<head>
  <meta charset="utf-8">
  <title>{{ config('app.name') }}</title>
  <script>
    window.opener.postMessage({ token: "{{ $token }}" }, "{{ config('app.spa_url') }}")
    window.close()
  </script>
</head>
<body>
</body>
</html>