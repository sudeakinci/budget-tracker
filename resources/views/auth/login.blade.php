<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giriş Yap</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>
<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10 border border-blue-100">
        <div class="flex flex-col items-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Log In</h2>
            <p class="text-gray-500 text-sm">Welcome! Please log in to your account.</p>
        </div>
        <form method="POST" action="/login" class="space-y-5">
            @csrf
            <div>
                <label for="email" class="block text-gray-700 mb-1 font-medium">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-envelope"></i></span>
                    <input type="email" id="email" name="email" required autofocus class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition" placeholder="Email address" value="{{ old('email') }}">
                </div>
            </div>
            <div>
                <label for="password" class="block text-gray-700 mb-1 font-medium">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-lock"></i></span>
                    <input type="password" id="password" name="password" required class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition" placeholder="Password">
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow">Log In</button>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-500 text-sm">Don't have an account?</span>
            <a href="/register" class="text-blue-600 hover:underline font-medium ml-1">Register</a>
        </div>
    </div>
</body>
</html>
