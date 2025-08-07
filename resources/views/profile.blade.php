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
    <div class="max-w-4xl mx-auto mt-4 bg-white rounded-xl shadow-lg p-8 border border-blue-100">
        <div class="flex flex-col md:flex-row items-center md:space-x-6 mb-8">
            <div class="flex-shrink-0 mb-4 md:mb-0">
                <div
                    class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center text-3xl text-blue-600 font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            </div>
            <div class="text-center md:text-left">
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

            <div class="p-5 border border-blue-50 rounded-lg mb-6">
                <h3 class="text-lg font-medium text-blue-800 mb-4">Personal Information</h3>
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div class="mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input type="email" id="email" value="{{ $user->email }}" readonly
                        class="w-full px-3 py-2 border border-gray-200 bg-gray-50 rounded-md text-gray-600 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-500">Email cannot be changed</p>
                </div>
            </div>

            <div class="p-5 border border-blue-50 rounded-lg">
                <h3 class="text-lg font-medium text-blue-800 mb-4">Change Password</h3>
                <p class="text-sm text-gray-600 mb-4">Leave password fields blank if you don't want to change your
                    password</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New
                            Password</label>
                        <input type="password" name="password" id="password"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Leave blank to keep current password">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm
                            New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500"
                            placeholder="Repeat new password">
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-md font-semibold transition">Update
                    Profile</button>
            </div>
        </form>
        <!-- Danger Zone Section -->
        <div class="mt-12 p-5 border border-red-200 rounded-lg bg-red-50">
            <h3 class="text-lg font-medium text-red-800 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-600 mb-4">The actions below are destructive and cannot be reversed.</p>

            <form action="{{ route('profile.delete', ['id' => $user->id]) }}" method="POST" class="flex justify-end">
                @csrf
                @method('DELETE')
                <button type="button" id="deleteAccountBtn"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-md font-semibold transition shadow">
                    Delete My Account
                </button>
            </form>
        </div>
    </div>
</x-layout>

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
    document.getElementById('deleteAccountBtn').addEventListener('click', function (e) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Your account will be permanently deleted. This action cannot be undone!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete my account',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteAccountForm').submit();
            }
        });
    });
</script>