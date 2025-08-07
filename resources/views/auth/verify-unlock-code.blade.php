<!DOCTYPE html>
<html lang="tr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hesap Kilidi Açma</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
</head>

<body class="bg-gradient-to-br from-blue-100 via-white to-blue-200 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md bg-white rounded-2xl shadow-2xl p-10 border border-blue-100">
        <div class="flex flex-col items-center mb-8">
            <h2 class="text-3xl font-extrabold text-gray-800 mb-2">Unlock Your Account</h2>
            <p class="text-gray-500 text-sm text-center">Please enter the 6-digit code sent to your email address</p>
        </div>

        <x-error-toast :errors="$errors" />

        @php
            $expiresAt = session('unlock_code_expires_at', time() + 180);
            $now = time();
            $timeLeft = max(0, $expiresAt - $now);
            $minutes = floor($timeLeft / 60);
            $seconds = $timeLeft % 60;
            $initialCountdown = $timeLeft > 0 ? sprintf('%d:%02d', $minutes, $seconds) : 'Expired code';
        @endphp

        <div class="mb-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
            <div class="flex items-center">
                <i class="fas fa-info-circle text-blue-600 mr-3"></i>
                <div>
                    <p class="text-sm text-gray-700">Verification code has been sent to <span
                            class="font-semibold">{{ $email }}</span></p>
                    <p class="text-xs text-gray-500 mt-1">
                        Code validity period: <span id="countdown"
                            class="font-medium {{ $timeLeft > 0 ? 'text-blue-600' : 'text-red-600' }}">{{ $initialCountdown }}</span>
                    </p>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('unlock.account.verify') }}" class="space-y-6">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">

            <div>
                <label for="code" class="block text-gray-700 mb-1 font-medium">Verification Code</label>
                <div class="relative">
                    <span class="absolute left-3 top-2.5 text-gray-400"><i class="fa fa-key"></i></span>
                    <input type="text" id="code" name="code" required autofocus
                        class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-400 transition"
                        placeholder="6-digit code" maxlength="6" inputmode="numeric" pattern="[0-9]*"
                        oninput="this.value = this.value.replace(/[^0-9]/g, '')"
                        onkeypress="return event.charCode >= 48 && event.charCode <= 57">
                </div>
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2.5 rounded-lg font-semibold hover:bg-blue-700 transition shadow">
                Unlock Account
            </button>
        </form>

        <div class="mt-6 text-center">
            <span class="text-gray-500 text-sm">Didn’t receive a code?</span>
            <form method="POST" action="{{ route('unlock.account.send') }}" class="inline">
                @csrf
                <input type="hidden" name="email" value="{{ $email }}">
                <button type="submit"
                    class="text-blue-600 hover:underline font-medium ml-1 bg-transparent border-none p-0 cursor-pointer">
                    Resend
                </button>
            </form>
        </div>
        <div class="mt-6 text-center">
            <a href="/login" class="text-blue-600 hover:underline text-sm">
                <i class="fas fa-arrow-left mr-1"></i> Back to Login
            </a>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const expiresAt = {{ session('unlock_code_expires_at', time() + 180) }};
            const now = Math.floor(Date.now() / 1000);

            let timeLeft = Math.max(0, expiresAt - now); //remaining time in seconds

            const countdownEl = document.getElementById('countdown');

            const countdownTimer = setInterval(function () {
                const minutes = Math.floor(timeLeft / 60);
                const seconds = timeLeft % 60;

                countdownEl.textContent = `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;

                if (timeLeft <= 0) {
                    clearInterval(countdownTimer);
                    countdownEl.textContent = 'Expired code';
                    countdownEl.classList.remove('text-blue-600');
                    countdownEl.classList.add('text-red-600');
                }

                timeLeft--;
            }, 1000);
        });
    </script>
</body>

</html>