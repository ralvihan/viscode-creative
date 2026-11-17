<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Viscode Creative') }} - Business Management System</title>
        <link rel="icon" type="image/png" href="{{ asset('assets/favicon.png') }}">
        <link rel="apple-touch-icon" href="{{ asset('assets/favicon.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-slate-800 bg-[#F3F5F9] antialiased min-h-full flex flex-col justify-center items-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md text-center mb-6">
            <a href="/" class="inline-flex flex-col items-center gap-2 group">
                <img src="{{ asset('assets/logo.png') }}" alt="Viscode Creative" class="h-14 w-auto object-contain group-hover:scale-105 transition-transform duration-200">
            </a>
            <p class="text-xs text-slate-500 mt-2 font-medium">Business Management System (VC-BMS)</p>
        </div>

        <div class="w-full sm:max-w-md bg-white border border-slate-200 shadow-xl rounded-2xl p-8">
            {{ $slot }}
        </div>

        <div class="mt-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} Viscode Creative. Kelompok 8 Kewirausahaan.
        </div>
    </body>
</html>
