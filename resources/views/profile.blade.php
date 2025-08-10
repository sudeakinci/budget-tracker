<x-layout :title="'My Profile'">
    {{-- Toast Success --}}
    <x-success-toast />

    {{-- Toast Error --}}
    <x-error-toast :errors="$errors" />

    <style>
        /* remove spinner buttons from number inputs */
        input[type=number]::-webkit-inner-spin-button,
        input[type=number]::-webkit-outer-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        input[type=number] {
            -moz-appearance: textfield;
        }
    </style>

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
                    <button type="button" id="editBalanceBtn" class="ml-2 text-blue-500 hover:text-blue-700">
                        <i class="fa fa-edit"></i>
                    </button>
                </span>
            </div>
        </div>

        <!-- Balance Edit Modal -->
        <div id="balanceEditModal"
            class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
            <div class="bg-white p-6 rounded-lg shadow-lg w-80">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Edit Balance</h3>
                <form action="{{ route('profile.balance.update', ['id' => $user->id]) }}" method="POST"
                    id="balanceEditForm">
                    @csrf
                    <div class="mb-4">
                        <label for="new_balance" class="block text-sm font-medium text-gray-700 mb-1">New
                            Balance</label>
                        <input type="number" name="balance" id="new_balance" step="0.01" min="0"
                            value="{{ $user->balance }}"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="closeBalanceModal"
                            class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300 text-gray-800">Cancel</button>
                        <button type="submit"
                            class="px-4 py-2 rounded bg-blue-600 hover:bg-blue-700 text-white">Save</button>
                    </div>
                </form>
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

        <!-- payment Terms Section -->
        <div class="mt-12 border border-gray-200 rounded-lg bg-gray-50">
            <div class="px-4 py-3 border-b border-gray-200 flex items-center bg-blue-100">
                <h3 class="text-base font-semibold text-blue-700">Payment Terms</h3>
            </div>
            <div class="p-0">
                @if(count($paymentTerms ?? []) > 0)
                    <ul>
                        @foreach($paymentTerms as $term)
                            <li class="px-4 py-3 flex items-center justify-between border-b border-gray-100">
                                <span class="text-gray-700">{{ $term->name }}</span>
                                <div class="flex space-x-2">
                                    <button onclick="editPaymentTerm({{ $term->id }}, '{{ $term->name }}')"
                                        class="text-gray-500 hover:text-gray-700 p-1.5 rounded transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('payment-terms.destroy', $term->id) }}" method="POST" class="inline"
                                        id="deletePaymentTermForm-{{ $term->id }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button"
                                            class="delete-payment-term-btn text-red-500 hover:text-red-700 p-1.5 rounded transition-colors"
                                            data-form-id="deletePaymentTermForm-{{ $term->id }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none"
                                                viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <div class="py-8 px-4 text-center text-gray-500">
                        No payment terms found.
                    </div>
                @endif
            </div>
        </div>

        <!-- edit Payment Term Modal -->
        <x-edit-payment-term-modal />

        <!-- Danger Zone Section -->
        <div class="mt-12 p-5 border border-red-200 rounded-lg bg-red-50">
            <h3 class="text-lg font-medium text-red-800 mb-2">Danger Zone</h3>
            <p class="text-sm text-gray-600 mb-4">The actions below are destructive and cannot be reversed.</p>

            <form action="{{ route('profile.delete', ['id' => $user->id]) }}" method="POST" class="flex justify-end"
                id="deleteAccountForm">
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

    document.getElementById('editBalanceBtn').addEventListener('click', function () {
        document.getElementById('balanceEditModal').classList.remove('hidden');
    });
    document.getElementById('closeBalanceModal').addEventListener('click', function () {
        document.getElementById('balanceEditModal').classList.add('hidden');
    });
    window.addEventListener('click', function (event) {
        const modal = document.getElementById('balanceEditModal');
        if (event.target === modal) {
            modal.classList.add('hidden');
        }
    });

</script>