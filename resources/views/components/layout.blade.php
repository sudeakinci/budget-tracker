<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Money Transfer Application">
    <meta name="author" content="Your Company">
    <title>{{ $title ?? config('app.name') }}</title>
    <!-- Stylesheet links -->
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            overflow-y: scroll; /* Prevents body from scrolling */
        }

        .transition-all {
            transition: all 0.3s ease;
        }

        .nav-link {
            @apply text-gray-600 transition-all relative;
            opacity: 1;
        }

        .nav-link:hover {
            opacity: 0.7;
        }

        .nav-link::after {
            content: '';
            display: block;
            position: absolute;
            left: 0;
            bottom: -2px;
            width: 100%;
            height: 2px;
            background: #2563eb;
            /* Tailwind blue-600 */
            border-radius: 2px;
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(.4, 0, .2, 1);
        }

        .nav-link.active {
            font-weight: 600;
        }

    </style>
</head>

<body class="bg-gradient-to-br from-blue-50 via-white to-blue-100 min-h-screen flex flex-col">
    <!-- Header Navigation -->
    <header class="bg-white shadow-sm">
        <div class="container mx-auto px-4 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <i class="fas fa-exchange-alt text-blue-600 text-xl"></i>
                <span class="font-bold text-xl text-gray-800">{{ config('app.name') }}</span>
            </div>
            <!-- Hamburger Button (Mobile) -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-600 hover:text-blue-600 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center space-x-6">
                <nav class="flex space-x-6">
                    <a href="{{ route('dashboard') }}"
                        class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">Dashboard</a>
                    <a href="{{ route('transactions') }}"
                        class="nav-link {{ request()->routeIs('transactions') ? 'active' : '' }}">Transactions</a>
                    <a href="#" class="nav-link">My Account</a>
                </nav>
                <div class="flex items-center space-x-2 ml-4">
                    @auth
                        <form id="logout-form" action="/logout" method="POST">
                            @csrf
                            <button type="button" onclick="confirmLogout()"
                                class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center">
                                <i class="fa fa-sign-out-alt ml-1"></i>
                            </button>
                        </form>
                    @else
                        <a href="/login" class="text-blue-600 hover:text-blue-800 transition-all">Giriş</a>
                        <a href="/register"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all">Kayıt
                            Ol</a>
                    @endauth
                </div>
            </div>
        </div>
        <!-- Mobile Menu -->
        <div id="mobile-menu" class="md:hidden hidden px-4 pb-4">
            <nav class="flex flex-col space-y-2 mb-2">
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">Dashboard</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">Transactions</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">My Account</a>
            </nav>
            <div class="flex flex-col space-y-2">
                @auth
                    <form id="logout-form-mobile" action="/logout" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogoutMobile()"
                            class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded flex items-center justify-center">
                            <i class="fa fa-sign-out-alt mr-2"></i>
                        </button>
                    </form>
                @else
                    <a href="/login" class="text-blue-600 hover:text-blue-800 transition-all text-center">Giriş</a>
                    <a href="/register"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all text-center">Kayıt
                        Ol</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-4">
        @if(session('status'))
            <div class="mb-6 p-4 bg-green-100 text-green-800 rounded-lg border border-green-200 shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-check-circle mr-3 text-green-500"></i>
                    <span>{{ session('status') }}</span>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 text-red-800 rounded-lg border border-red-200 shadow-sm">
                <div class="flex items-center">
                    <i class="fas fa-exclamation-circle mr-3 text-red-500"></i>
                    <span>{{ session('error') }}</span>
                </div>
            </div>
        @endif

        {{ $slot }}
    </main>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmLogout() {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: "Your session will be terminated.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
        function confirmLogoutMobile() {
            Swal.fire({
                title: 'Are you sure you want to log out?',
                text: "Your session will be terminated.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, log out',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form-mobile').submit();
                }
            });
        }
        // hmbrgr menu toggle
        document.addEventListener('DOMContentLoaded', function () {
            const btn = document.getElementById('mobile-menu-button');
            const menu = document.getElementById('mobile-menu');
            btn.addEventListener('click', function () {
                menu.classList.toggle('hidden');
            });
        });
    </script>
</body>

</html>