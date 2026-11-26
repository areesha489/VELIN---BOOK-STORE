<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page expired — VELIN</title>
    <link rel="stylesheet" href="{{ asset('css/velin.css') }}">
</head>
<body>
    <main class="page-head wrap">
        <p class="kicker">419</p>
        <h1>This page expired.</h1>
        <p class="lede">The form sat too long. Go back, refresh, and send it again.</p>
        <a class="btn btn-signal" href="{{ url()->previous() }}">Go back</a>
    </main>
</body>
</html>
