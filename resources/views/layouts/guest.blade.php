<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Taut Pinang') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('img/favicon.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    <!-- Anime.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animejs/3.2.1/anime.min.js"></script>

    <!-- Dark Mode Styles -->
    <style>
        :root {
            color-scheme: light dark;
        }

        html {
            color-scheme: light dark;
        }

        /* Light theme */
        [data-theme="light"] body {
            background: linear-gradient(to bottom right, #eff6ff, #ffffff, #ecfdf5);
            color: #111827;
        }

        /* Dark theme */
        [data-theme="dark"] body {
            background: linear-gradient(to bottom right, #111827, #1e3a8a, #064e3b);
            color: #f9fafb;
        }

        /* Theme-based text colors */
        [data-theme="light"] .text-gray-900 {
            color: #111827 !important;
        }

        [data-theme="dark"] .text-gray-900 {
            color: #f9fafb !important;
        }

        /* Smooth transitions */
        body {
            transition: background 0.3s ease, color 0.3s ease;
        }
    </style>
</head>

<body
    class="min-h-screen font-sans antialiased text-gray-900">
    <div class="min-h-screen">
        {{ $slot }}
    </div>

    <!-- Dark Mode Detection Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');
            const htmlElement = document.documentElement;

            function enableDarkMode() {
                htmlElement.setAttribute('data-theme', 'dark');
            }

            function enableLightMode() {
                htmlElement.setAttribute('data-theme', 'light');
            }

            function initTheme() {
                const savedTheme = localStorage.getItem('theme');

                if (savedTheme) {
                    if (savedTheme === 'dark') {
                        enableDarkMode();
                    } else {
                        enableLightMode();
                    }
                } else {
                    if (darkModeQuery.matches) {
                        enableDarkMode();
                    } else {
                        enableLightMode();
                    }
                }
            }

            // Listener untuk perubahan preferensi sistem
            darkModeQuery.addEventListener('change', (e) => {
                if (!localStorage.getItem('theme')) {
                    if (e.matches) {
                        enableDarkMode();
                    } else {
                        enableLightMode();
                    }
                }
            });

            // Inisialisasi tema
            initTheme();
        });
    </script>
    @livewireScripts
</body>

</html>
