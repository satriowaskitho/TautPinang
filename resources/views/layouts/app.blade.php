<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Taut Pinang') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Flowbite CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.css" rel="stylesheet" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />

    <link rel="icon" href="{{ asset('img/favicon.png') }}" type="image/png">

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Styles -->
    @livewireStyles
</head>

<body class="bg-gray-50">
    <x-banner />

    <!-- Sidebar -->
    <aside id="logo-sidebar"
        class="fixed top-0 left-0 z-40 w-64 h-screen transition-transform -translate-x-full sm:translate-x-0"
        aria-label="Sidebar">
        <div
            class="h-full px-3 py-4 overflow-y-auto bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700">
            <!-- Logo -->
            <div class="flex items-center ps-2.5 mb-5">
                <img src="{{ asset('img/favicon.png') }}" class="h-6 me-3 sm:h-7" alt="BPS Logo" />
                <span class="self-center text-xl font-semibold whitespace-nowrap dark:text-white">Taut Pinang</span>
            </div>

            <!-- Navigation Links -->
            <ul class="space-y-2 font-medium">
                <li>
                    <a href="{{ route('dashboard') }}" wire:navigate
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('dashboard') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 22 21">
                            <path
                                d="M16.975 11H10V4.025a1 1 0 0 0-1.066-.998 8.5 8.5 0 1 0 9.039 9.039.999.999 0 0 0-1-1.066h.002Z" />
                            <path
                                d="M12.5 0c-.157 0-.311.01-.565.027A1 1 0 0 0 11 1.02V10h8.975a1 1 0 0 0 1-.935c.013-.188.028-.374.028-.565A8.51 8.51 0 0 0 12.5 0Z" />
                        </svg>
                        <span class="ms-3">Laman Utama</span>
                    </a>
                </li>

                <!-- Buat Tautan - Now Active -->
                <li>
                    <a href="{{ route('buat-tautan') }}" wire:navigate
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('buat-tautan') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 20 20">
                            <path
                                d="M10 .5a9.5 9.5 0 1 0 9.5 9.5A9.51 9.51 0 0 0 10 .5ZM9.5 4a1.5 1.5 0 1 1 0 3 1.5 1.5 0 0 1 0-3ZM12 15H8a1 1 0 0 1 0-2h1v-3H8a1 1 0 0 1 0-2h2a1 1 0 0 1 1 1v4h1a1 1 0 0 1 0 2Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Buat Tautan</span>
                    </a>
                </li>

                <!-- Kelola Tautan - Updated Link -->
                <li>
                    <a href="{{ route('kelola-tautan') }}" wire:navigate
                        class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('kelola-tautan') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                        <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                            aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                            viewBox="0 0 18 18">
                            <path
                                d="M6.143 0H1.857A1.857 1.857 0 0 0 0 1.857v4.286C0 7.169.831 8 1.857 8h4.286A1.857 1.857 0 0 0 8 6.143V1.857A1.857 1.857 0 0 0 6.143 0Zm10 0h-4.286A1.857 1.857 0 0 0 10 1.857v4.286C10 7.169 10.831 8 11.857 8h4.286A1.857 1.857 0 0 0 18 6.143V1.857A1.857 1.857 0 0 0 16.143 0Zm-10 10H1.857A1.857 1.857 0 0 0 0 11.857v4.286C0 17.169.831 18 1.857 18h4.286A1.857 1.857 0 0 0 8 16.143v-4.286A1.857 1.857 0 0 0 6.143 10Zm10 0h-4.286A1.857 1.857 0 0 0 10 11.857v4.286c0 1.026.831 1.857 1.857 1.857h4.286A1.857 1.857 0 0 0 18 16.143v-4.286A1.857 1.857 0 0 0 16.143 10Z" />
                        </svg>
                        <span class="flex-1 ms-3 whitespace-nowrap">Kelola Tautan</span>
                    </a>
                </li>

                <!-- User Management - Admin Only -->
                @if(auth()->user() && auth()->user()->isAdmin())
                    <li>
                        <a href="{{ route('user-management') }}" wire:navigate
                            class="flex items-center p-2 text-gray-900 rounded-lg dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700 group {{ request()->routeIs('user-management') ? 'bg-gray-100 dark:bg-gray-700' : '' }}">
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 18">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"/>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2 1 1 0 000 2H6a2 2 0 100 4h2a2 2 0 100 4H6a1 1 0 100 2 2 2 0 002-2v-1a1 1 0 112 0v1a4 4 0 11-8 0V5z" clip-rule="evenodd"/>
                            </svg>
                            <span class="flex-1 ms-3 whitespace-nowrap">User Management</span>
                        </a>
                    </li>
                @endif

            </ul>

            <!-- Divider -->
            <div class="pt-4 mt-4 space-y-2 font-medium border-t border-gray-200 dark:border-gray-700">
                <!-- User Menu -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                        class="flex items-center w-full p-2 text-gray-900 transition duration-75 rounded-lg group hover:bg-gray-100 dark:text-white dark:hover:bg-gray-700">
                        @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                            <img class="w-6 h-6 rounded-full" src="{{ Auth::user()->profile_photo_url }}"
                                alt="{{ Auth::user()->name }}" />
                        @else
                            <svg class="flex-shrink-0 w-5 h-5 text-gray-500 transition duration-75 dark:text-gray-400 group-hover:text-gray-900 dark:group-hover:text-white"
                                aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                                viewBox="0 0 20 20">
                                <path
                                    d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z" />
                            </svg>
                        @endif
                        <span class="flex-1 text-left ms-3 whitespace-nowrap">Account</span>
                        <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                            viewBox="0 0 10 6">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="m1 1 4 4 4-4" />
                        </svg>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" x-transition:enter="transition ease-out duration-100"
                        x-transition:enter-start="transform opacity-0 scale-95"
                        x-transition:enter-end="transform opacity-100 scale-100"
                        x-transition:leave="transition ease-in duration-75"
                        x-transition:leave-start="transform opacity-100 scale-100"
                        x-transition:leave-end="transform opacity-0 scale-95" @click.away="open = false"
                        class="absolute right-0 z-50 my-4 text-base list-none bg-white divide-y divide-gray-100 rounded shadow dark:bg-gray-700 dark:divide-gray-600">

                        <div class="px-4 py-3">
                            <span class="block text-sm text-gray-900 dark:text-white">{{ Auth::user()->name }}</span>
                            <span
                                class="block text-sm font-medium text-gray-500 truncate dark:text-gray-400">{{ Auth::user()->email }}</span>
                        </div>

                        <ul class="py-2">
                            <li>
                                <a href="{{ route('profile.show') }}" wire:navigate
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Profile</a>
                            </li>
                            @if (Laravel\Jetstream\Jetstream::hasApiFeatures())
                                <li>
                                    <a href="{{ route('api-tokens.index') }}" wire:navigate
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">API
                                        Tokens</a>
                                </li>
                            @endif
                            <li>
                                <form method="POST" action="{{ route('logout') }}" x-data>
                                    @csrf
                                    <a href="{{ route('logout') }}" @click.prevent="$root.submit();"
                                        class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 dark:hover:bg-gray-600 dark:text-gray-200 dark:hover:text-white">Sign
                                        out</a>
                                </form>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="p-4 sm:ml-64">
        <!-- Mobile menu button -->
        <div class="sm:hidden">
            <button data-drawer-target="logo-sidebar" data-drawer-toggle="logo-sidebar" aria-controls="logo-sidebar"
                type="button"
                class="inline-flex items-center p-2 mt-2 ml-3 text-sm text-gray-500 rounded-lg hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 dark:focus:ring-gray-600">
                <span class="sr-only">Open sidebar</span>
                <svg class="w-6 h-6" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20"
                    xmlns="http://www.w3.org/2000/svg">
                    <path clip-rule="evenodd" fill-rule="evenodd"
                        d="M2 4.75A.75.75 0 012.75 4h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 4.75zm0 10.5a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5a.75.75 0 01-.75-.75zM2 10a.75.75 0 01.75-.75h14.5a.75.75 0 010 1.5H2.75A.75.75 0 012 10z">
                    </path>
                </svg>
            </button>
        </div>

        <!-- Page Heading -->
        @if (isset($header))
            <header class="mb-6 bg-white rounded-lg shadow">
                <div class="px-4 py-6 sm:px-6 lg:px-8">
                    {{ $header }}
                </div>
            </header>
        @endif

        <!-- Page Content -->
        <main>
            {{ $slot }}
        </main>
    </div>

    @stack('modals')

    @livewireScripts

    <!-- Flowbite JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/flowbite/2.0.0/flowbite.min.js"></script>

    <!-- Custom JS for seamless navigation -->
    <script>
        // Fungsi untuk deteksi dan mengatur tema otomatis berdasarkan preferensi sistem
        function detectAndSetSystemTheme() {
            const savedTheme = localStorage.getItem('theme');
            const systemPrefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

            // Jika tidak ada tema yang tersimpan, gunakan preferensi sistem
            if (!savedTheme) {
                const theme = systemPrefersDark ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
            } else {
                // Gunakan tema yang tersimpan
                document.documentElement.setAttribute('data-theme', savedTheme);
            }
        }

        // Inisialisasi tema sebelum DOM loaded
        detectAndSetSystemTheme();

        // Listener untuk perubahan preferensi sistem
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
            const savedTheme = localStorage.getItem('theme');
            // Hanya update otomatis jika user tidak pernah mengatur tema manual
            if (!savedTheme || savedTheme === 'system') {
                const newTheme = e.matches ? 'dark' : 'light';
                document.documentElement.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
            }
        });

        // Debug Livewire events
        document.addEventListener('livewire:navigating', () => {
            console.log('Livewire navigating...');
            document.body.style.cursor = 'wait';
        });

        document.addEventListener('livewire:navigated', () => {
            console.log('Livewire navigated!');
            document.body.style.cursor = 'default';

            // Re-apply theme after navigation
            detectAndSetSystemTheme();

            // Reinitialize Flowbite components
            if (typeof initFlowbite !== 'undefined') {
                initFlowbite();
            }
        });
    </script>

    <!-- Dark Mode Styles for Layout -->
    <style>
        /* Main Background - Dark Mode */
        [data-theme="dark"] .bg-gray-50 {
            background: #111827 !important;
        }

        /* Sidebar - Dark Mode */
        [data-theme="dark"] .bg-white {
            background: rgba(17, 24, 39, 0.95) !important;
        }

        [data-theme="dark"] .border-gray-200 {
            border-color: rgba(55, 65, 81, 0.3) !important;
        }

        [data-theme="dark"] .border-gray-700 {
            border-color: rgba(55, 65, 81, 0.3) !important;
        }

        /* Navigation Items - Dark Mode */
        [data-theme="dark"] .text-gray-900 {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .text-gray-500 {
            color: #9ca3af !important;
        }

        [data-theme="dark"] .text-gray-400 {
            color: #6b7280 !important;
        }

        [data-theme="dark"] .hover\:text-gray-900:hover {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .hover\:text-white:hover {
            color: #f3f4f6 !important;
        }

        /* Hover States - Dark Mode */
        [data-theme="dark"] .hover\:bg-gray-100:hover {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .hover\:bg-gray-700:hover {
            background: rgba(55, 65, 81, 0.7) !important;
        }

        /* Active States - Dark Mode */
        [data-theme="dark"] .bg-gray-100 {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .bg-gray-700 {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        /* Page Header - Dark Mode */
        [data-theme="dark"] header.bg-white {
            background: rgba(17, 24, 39, 0.95) !important;
        }

        /* Mobile Menu Button - Dark Mode */
        [data-theme="dark"] .text-gray-500 {
            color: #9ca3af !important;
        }

        [data-theme="dark"] .hover\:bg-gray-100:hover {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        /* User Dropdown - Dark Mode */
        [data-theme="dark"] .bg-white {
            background: rgba(17, 24, 39, 0.95) !important;
        }

        [data-theme="dark"] .divide-gray-100 > * {
            border-color: rgba(55, 65, 81, 0.3) !important;
        }

        [data-theme="dark"] .text-gray-700 {
            color: #d1d5db !important;
        }

        [data-theme="dark"] .hover\:bg-gray-600:hover {
            background: rgba(75, 85, 99, 0.5) !important;
        }

        [data-theme="dark"] .text-gray-200 {
            color: #e5e7eb !important;
        }

        /* Mobile Menu - Dark Mode */
        [data-theme="dark"] .bg-gray-50 {
            background: #111827 !important;
        }

        /* Shadow Effects - Dark Mode */
        [data-theme="dark"] .shadow {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
        }

        [data-theme="dark"] .shadow-lg {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2) !important;
        }

        /* Transitions */
        [data-theme="dark"] * {
            transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
        }

        /* User Management Menu - Additional Dark Mode Support */
        [data-theme="dark"] .hover\:bg-gray-100:hover {
            background: rgba(55, 65, 81, 0.5) !important;
        }

        [data-theme="dark"] .group:hover .group-hover\:text-gray-900 {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .group:hover .group-hover\:text-white {
            color: #f3f4f6 !important;
        }

        [data-theme="dark"] .route-active {
            background: rgba(55, 65, 81, 0.5) !important;
        }
    </style>
</body>

</html>
