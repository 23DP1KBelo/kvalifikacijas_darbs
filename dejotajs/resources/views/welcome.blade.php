<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Laravel 12 with Vue 3</title>
        @vite(['resources/js/app.js'])
        <!-- Fonts -->
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
