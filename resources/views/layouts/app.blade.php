<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Viscode BMS') }}</title>

        <!-- Fonts (Inter) -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F3F5F9] text-[#172033] selection:bg-blue-600 selection:text-white">
        <div {{ $attributes->merge(['class' => 'min-h-screen bg-[#F3F5F9] text-[#172033]']) }}>
            <div x-data="{ sidebarOpen: false, sidebarCollapsed: false }" class="min-h-screen flex">
                <!-- Mobile Backdrop Drawer -->
                <div x-show="sidebarOpen" style="display: none;"
                     @click="sidebarOpen = false"
                     x-transition:enter="transition-opacity ease-linear duration-200"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-40 bg-slate-900/60 backdrop-blur-sm lg:hidden"></div>

                <!-- Sidebar Navigation (Deep Navy #17243B) -->
                @include('layouts.sidebar')

                <!-- Main Content Area -->
                <div class="flex-1 flex flex-col min-w-0 transition-all duration-300"
                     :class="sidebarCollapsed ? 'lg:pl-20' : 'lg:pl-64'">
                    
                    <!-- Top Navbar (Pure White #FFFFFF) -->
                    @include('layouts.topbar')

                    <!-- Main Page Container (Soft Grey #F3F5F9) -->
                    <main class="flex-1 p-4 sm:p-6 lg:p-8 bg-[#F3F5F9]">
                        <div class="max-w-7xl mx-auto space-y-6">
                            @isset($header)
                                <div class="mb-2">
                                    {{ $header }}
                                </div>
                            @endisset

                            {{ $slot }}
                        </div>
                    </main>
                </div>
            </div>
        </div>

        @if(session('success'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.showToast) {
                        window.showToast(@js(session('success')), 'success');
                    }
                });
            </script>
        @endif

        @if(session('error'))
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (window.Swal) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Peringatan Operasional',
                            text: @js(session('error')),
                            confirmButtonColor: '#2563EB',
                            customClass: {
                                popup: 'rounded-2xl border border-slate-200 shadow-xl font-sans',
                                confirmButton: 'px-5 py-2.5 rounded-xl font-semibold text-sm shadow-sm cursor-pointer'
                            },
                            buttonsStyling: false
                        });
                    }
                });
            </script>
        @endif
    </body>
</html>
