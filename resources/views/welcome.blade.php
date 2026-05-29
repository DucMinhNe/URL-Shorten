<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="refresh" content="0; url={{ route('home') }}">
    <title>LinkPay</title>
</head>
<body>
    <p>Redirecting to <a href="{{ route('home') }}">LinkPay</a>...</p>
</body>
</html>
