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
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Register</h2>
            <p class="text-gray-500 text-sm">Welcome! Please create an account.</p>
        </div>
        <form method="POST" action="/register" class="space-y-5">
            @csrf
            <div>
                <label for="name" class="block text-gray-700 mb-1 font-medium">Name</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-user"></i></span>
                    <input type="text" id="name" name="name" required autofocus
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        placeholder="Name">
                </div>
            </div>
            <div>
                <label for="email" class="block text-gray-700 mb-1 font-medium">Email</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-envelope"></i></span>
                    <input type="email" id="email" name="email" required autofocus
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        placeholder="Email address">
                </div>
            </div>
            <div>
                <label for="password" class="block text-gray-700 mb-1 font-medium">Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-lock"></i></span>
                    <input type="password" id="password" name="password" required
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        placeholder="Password" >
                </div>
            </div>
            <div>
                <label for="password_confirmation" class="block text-gray-700 mb-1 font-medium">Confirm Password</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-lock"></i></span>
                    <input type="password" id="password_confirmation" name="password_confirmation" required
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        placeholder="Confirm Password">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow">Register</button>
        </form>
        <div class="mt-6 text-center">
            <span class="text-gray-500 text-sm">Have an account?</span>
            <a href="/login" class="text-blue-600 hover:underline font-medium ml-1">Log In</a>
        </div>
    </div>
</body>

</html>