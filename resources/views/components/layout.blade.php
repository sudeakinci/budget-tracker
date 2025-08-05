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
        }

        .transition-all {
            transition: all 0.3s ease;
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
            <nav class="hidden md:flex space-x-6">
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">Ana Sayfa</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">İşlemler</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">Hesabım</a>
                <a href="#" class="text-gray-600 hover:text-blue-600 transition-all">Yardım</a>
            </nav>
            <div class="flex items-center space-x-2">
                @auth
                    <form id="logout-form" action="/logout" method="POST">
                        @csrf
                        <button type="button" onclick="confirmLogout()" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded">
                            <i class="fa fa-sign-out-alt mr-2"></i>Logout
                        </button>
                    </form>
                @else
                    <a href="/login" class="text-blue-600 hover:text-blue-800 transition-all">Giriş</a>
                    <a href="/register"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-all">Kayıt Ol</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-grow container mx-auto px-4 py-8">
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

    <!-- Footer -->
    <!-- <footer class="bg-gray-800 text-gray-300 py-6">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center space-x-2">
                        <i class="fas fa-exchange-alt text-blue-400"></i>
                        <span class="font-semibold">Para Transfer</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Kolay, hızlı ve güvenli para transferi</p>
                </div>

                <div class="flex space-x-6 text-sm">
                    <a href="#" class="hover:text-white transition-all">Gizlilik</a>
                    <a href="#" class="hover:text-white transition-all">Şartlar</a>
                    <a href="#" class="hover:text-white transition-all">İletişim</a>
                </div>
            </div>

            <div class="border-t border-gray-700 mt-4 pt-4 text-xs text-center text-gray-400">
                &copy; {{ date('Y') }} Para Transfer. Tüm hakları saklıdır.
            </div>
        </div>
    </footer>
</body> -->

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
    </script>

</html>