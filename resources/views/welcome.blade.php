<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name') }} | </title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>

<body id="app" class="layout-fixed sidebar-open">
    <main-component></main-component>
</body>

</html>