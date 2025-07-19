<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }} - Admin</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-light">
        <div class="min-vh-100">
            {{-- Lógica para mostrar la barra de navegación correcta según el rol --}}
            @if (auth()->user()->is_super_admin || auth()->user()->hasRole('Super-Admin'))
                <x-layouts.superadmin-navigation />
            @elseif (auth()->user()->hasRole('Tenant-Admin'))
                <x-layouts.admin-navigation />
            @endif

            <!-- Page Heading -->
            @if (isset($header))
                <header class="bg-white shadow-sm">
                    <div class="container py-3">
                        {{ $header }}
                    </div>
                </header>
            @endif

            <!-- Page Content -->
            <main class="container mt-4">
                {{ $slot }}
            </main>
        </div>
    </body>
</html>