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
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead>
                        <tr class="bg-blue-50">
                            <th class="py-3 px-4 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">Date
                            </th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Description</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Amount</th>
                            <th class="py-3 px-4 text-left text-xs font-medium text-blue-700 uppercase tracking-wider">
                                Payment Method</th>
                            <th class="w-10"></th> <!-- empty title for icon -->

                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($transactions as $transaction)
                            <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                                <td class="py-2 px-4 border-b border-gray-200 w-40">
                                    {{ $transaction->created_at->format('d.m.Y H:i') }}
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 w-64">{{ $transaction->description }}</td>
                                <td
                                    class="py-2 px-4 border-b border-gray-200 font-semibold w-32 {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                    {{ $transaction->amount < 0 ? '-' : '+' }}{{ number_format(abs($transaction->amount), 2) }}
                                    ₺
                                </td>
                                <td class="py-2 px-4 border-b border-gray-200 w-48">
                                    {{ $transaction->payment_term_name }}
                                </td>
                                <td class="px-2 border-b border-gray-200 w-10 text-center align-middle">
                                    <button class="group focus:outline-none" title="View Details" onclick="showTransactionDetails(
                                        {{ $transaction->id }},
                                        '{{ $transaction->created_at->format('d.m.Y H:i') }}',
                                        '{{ $transaction->description ?: 'No description' }}',
                                        '{{ $transaction->amount < 0 ? '-' : '+' }}{{ number_format(abs($transaction->amount), 2) }} ₺',
                                        '{{ optional($transaction->user)->name ?: '-' }}',
                                        '{{ $transaction->payment_term_name }}'
                                    )">
                                        <div class="relative w-4 h-4 inline-block">
                                            <svg class="absolute inset-0 text-gray-400 group-hover:text-blue-500 transition-colors duration-200"
                                                xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                                                stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <line x1="12" y1="16" x2="12" y2="12"></line>
                                                <line x1="12" y1="8" x2="12.01" y2="8"></line>
                                            </svg>
                                        </div>
                                    </button>
                                </td>
                            </tr>
                        @endforeach

                        @for($i = $transactions->count(); $i < 5; $i++)
                            <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }}">
                                <td class="py-2 px-4 border-b border-gray-200 w-40">&nbsp;</td>
                                <td class="py-2 px-4 border-b border-gray-200 w-64"></td>
                                <td class="py-2 px-4 border-b border-gray-200 w-32"></td>
                                <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                                <td class="px-2 border-b border-gray-200 w-10"></td>
                            </tr>
                        @endfor
                    </tbody>
                </table>
            </div>

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

        <a href="#" id="openTransactionModalBtn"
            class="bg-white p-4 rounded-lg shadow border border-blue-100 hover:border-blue-300 transition flex items-center">
            <div class="rounded-full bg-blue-100 p-3 mr-3">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
            </div>
            <div>
                <h4 class="font-medium">New Transfer</h4>
                <p class="text-xs text-gray-500">Create a new money transfer</p>
            </div>
        </a>
        <x-transaction-modal :users="$users" :paymentTerms="$paymentTerms" />

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
            });
        </script>
</x-layout>