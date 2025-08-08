<x-layout :title="'Dashboard'">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Dashboard</h1>
    </div>

    <!-- balance card -->
    <div class="bg-white rounded-lg shadow-lg p-6 border border-blue-100 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-bold mb-1">Welcome, {{ Auth::user()->name }}!</h2>
                <p class="text-gray-500">Your current balance</p>
            </div>
            <div class="text-right">
                <span class="text-3xl font-bold text-blue-600">{{ number_format(Auth::user()->balance, 2) }} ₺</span>
                <p class="text-xs text-gray-500 mt-1">Last updated: {{ Auth::user()->updated_at->format('d.m.Y H:i') }}
                </p>
            </div>
        </div>
    </div>

    <!-- latest transactions -->
    <div class="bg-white rounded-lg shadow-lg border border-blue-100">
        <div class="px-6 py-4 border-b border-blue-100">
            <h3 class="text-lg font-bold text-gray-800">Recent Transactions</h3>
        </div>

        @if($transactions->isEmpty())
            <div class="p-6 text-center text-gray-500">
                <p>No transactions found.</p>
                <a href="{{ route('transactions') }}" class="text-blue-600 hover:underline mt-2 inline-block">Make your
                    first transaction</a>
            </div>
        @else
            <x-transactions-table :transactions="$transactions" :show-receiver="false" :show-payment-method="true" :row-count="5" />
            <div class="px-6 py-3 bg-gray-50 text-right">
                <a href="{{ route('transactions') }}" class="text-blue-600 hover:underline text-sm font-medium">
                    View All Transactions
                </a>
            </div>
        @endif
    </div>

    <!-- transaction details modal -->
    <x-transaction-details-modal />

    <!-- quick Links -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mt-6">
        <a href="{{ route('profile') }}"
            class="bg-white p-4 rounded-lg shadow border border-blue-100 hover:border-blue-300 transition flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-medium">My Profile</h4>
                <p class="text-xs text-gray-500">Update your information</p>
            </div>
        </a>

        <a href="{{ route('transactions') }}"
            class="bg-white p-4 rounded-lg shadow border border-blue-100 hover:border-blue-300 transition flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2">
                    </path>
                </svg>
            </div>
            <div>
                <h4 class="font-medium">All Transactions</h4>
                <p class="text-xs text-gray-500">View your transaction history</p>
            </div>
        </a>

        <a href="{{ route('ledger') }}"
            class="bg-white p-4 rounded-lg shadow border border-blue-100 hover:border-blue-300 transition flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <rect x="4" y="4" width="16" height="16" rx="2" stroke-width="2" stroke="currentColor" fill="none" />
                    <line x1="8" y1="4" x2="8" y2="20" stroke-width="2" stroke="currentColor" />
                    <line x1="16" y1="8" x2="8" y2="8" stroke-width="1.5" stroke="currentColor" />
                    <line x1="16" y1="12" x2="8" y2="12" stroke-width="1.5" stroke="currentColor" />
                    <line x1="16" y1="16" x2="8" y2="16" stroke-width="1.5" stroke="currentColor" />
                </svg>
            </div>
            <div>
                <h4 class="font-medium">Ledger</h4>
                <p class="text-xs text-gray-500">View your ledger entries</p>
            </div>
        </a>
        
        <x-transaction-modal :users="$users" :paymentTerms="$paymentTerms" />
        <x-transaction-edit-modal :paymentTerms="$paymentTerms" />

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openBtn = document.getElementById('openTransactionModalBtn');
                const modal = document.getElementById('transactionModal');
                if (openBtn && modal) {
                    openBtn.addEventListener('click', function (e) {
                        e.preventDefault();
                        modal.classList.remove('hidden');
                    });
                }

                window.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                    }
                });

                // dropdown toggle function
                window.toggleDropdown = function (id) {
                    const dropdown = document.getElementById(`dropdown-${id}`);
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        if (menu.id !== `dropdown-${id}`) {
                            menu.classList.add('hidden');
                        }
                    });
                    dropdown.classList.toggle('hidden');
                }

                // close dropdown when clicking outside
                document.addEventListener('click', function (event) {
                    if (!event.target.closest('.dropdown')) {
                        document.querySelectorAll('.dropdown-menu').forEach(menu => {
                            menu.classList.add('hidden');
                        });
                    }
                });
            });
        </script>
</x-layout>