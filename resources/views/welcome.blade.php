<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Agendacon</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

        <!-- Vite -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body>
        <div class="container mt-5">
            <div class="alert alert-success" role="alert">
                <h4 class="alert-heading">
                    <i class="fa-solid fa-check-circle"></i> ¡Bienvenido a Agendacon v2!
                </h4>
                <p>El entorno de frontend con Bootstrap 5 y Font Awesome 6 está funcionando correctamente.</p>
                <hr>
                <p class="mb-0">Ahora puedes empezar a construir tus vistas.</p>
            </div>
        </div>
    </body>
</html>