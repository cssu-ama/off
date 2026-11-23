<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Lalezar&display=swap');
    </style>

    <style>
        body {
            width: calc(100vw - 50px);
            height: calc(100vh - 50px);
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
            direction: rtl;
        }

        .lalezar-regular {
            font-family: "Lalezar", sans-serif;
            font-weight: 500;
            font-style: normal;
        }
    </style>
</head>
<body>
<h1 class="lalezar-regular">ایران پیروز است!</h1>
</body>
</html>
