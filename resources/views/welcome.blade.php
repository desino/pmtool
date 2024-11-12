<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="{{ asset('favicon.ico') }}" />
    <meta name="robots" content="noindex, nofollow">
    <title>{{ config('app.name') }} | </title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body id="app" class="layout-fixed sidebar-open">
    <main-component></main-component>
</body>

</html>