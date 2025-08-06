<x-layout :title="'My Profile'">
    {{-- Toast Success --}}
    @if(session('status'))
        <div id="successToast"
            class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-green-100 border border-green-400 text-green-700 px-6 py-3 rounded shadow-lg flex items-center space-x-2 transition-opacity duration-500">
            <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <path d="M9 12l2 2l4 -4" />
            </svg>
            <span>{{ session('status') }}</span>
            <button onclick="document.getElementById('successToast').remove()"
                class="ml-4 text-green-700 hover:text-green-900 font-bold">&times;</button>
        </div>
    @endif

    {{-- Toast Error --}}
    @if($errors->any())
        <div id="errorToast"
            class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded shadow-lg flex items-center space-x-2 transition-opacity duration-500">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12" y2="16" />
            </svg>
            <span>{{ $errors->first() }}</span>
            <button onclick="document.getElementById('errorToast').remove()"
                class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    <script>
        ['successToast', 'errorToast'].forEach(function (id) {
            const toast = document.getElementById(id);
            if (toast) {
                setTimeout(() => {
                    toast.classList.add('opacity-0');
                    setTimeout(() => toast.remove(), 500);
                }, 3500);
            }
        });
    </script>

    <div class="max-w-2xl mx-auto mt-10 bg-white rounded-xl shadow-lg p-8 border border-blue-100">
        <div class="flex items-center space-x-6 mb-8">
            <div class="flex-shrink-0">
                <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-600 font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-800 mb-1">{{ $user->name }}</h2>
                <p class="text-gray-500">{{ $user->email }}</p>
                <span class="inline-block mt-2 px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-xs font-semibold">
                    Balance: {{ number_format($user->balance, 2) }} ₺
                </span>
            </div>
        </div>

        <form action="{{ route('profile', ['id' => $user->id]) }}" method="POST" class="space-y-6">
            @csrf
            @method('POST')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                <input type="password" name="password" id="password"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Leave blank to keep current password">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" id="password_confirmation"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Repeat new password">
            </div>
            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold transition">Update Profile</button>
            </div>
        </form>
    </div>
</x-layout>