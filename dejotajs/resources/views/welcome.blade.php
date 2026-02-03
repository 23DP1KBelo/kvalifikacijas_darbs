<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>Dejotājs</title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap"rel="stylesheet"/>
        <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Jost:ital,wght@0,100..900;1,100..900&family=Julius+Sans+One&family=Monsieur+La+Doulaise&display=swap" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=DM+Serif+Text:ital@0;1&family=Jost:ital,wght@0,100..900;1,100..900&family=Julius+Sans+One&family=Monsieur+La+Doulaise&family=Red+Hat+Text:ital,wght@0,300..700;1,300..700&display=swap" rel="stylesheet">
        @vite(['resources/js/app.js'])
        <!-- Fonts -->
    </head>
    <body>
        <div id="app"></div>
    </body>
</html>
