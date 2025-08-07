<x-layout :title="'Transactions'" :hideNotifications="true">
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

    @if($errors->has('message'))
        <div id="errorToast"
            class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-red-100 border border-red-400 text-red-700 px-6 py-3 rounded shadow-lg flex items-center space-x-2 transition-opacity duration-500">
            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <circle cx="12" cy="12" r="10" />
                <line x1="12" y1="8" x2="12" y2="12" />
                <line x1="12" y1="16" x2="12" y2="16" />
            </svg>
            <span>{{ $errors->first('message') }}</span>
            <button onclick="document.getElementById('errorToast').remove()"
                class="ml-4 text-red-700 hover:text-red-900 font-bold">&times;</button>
        </div>
    @endif

    <script>
        // Toast'ları otomatik olarak gizle
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

    <div class="container mx-auto px-1 py-3 flex justify-between items-center">

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

        <h1 class="text-2xl font-bold text-gray-800">Transactions</h1>
    </div>

    <div class="flex items-end mb-4  justify-between">
        <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded px-3 py-1 shadow text-sm flex items-center">
            <i class="fas fa-wallet mr-1"></i>
            <span class="font-semibold">Balance:</span>
            <span class="ml-1">{{ number_format($balance, 2) }} ₺</span>
        </div>

        <button type="button" id="openTransactionModal"
            class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none ">New
            Transaction</button>
    </div>

    <!-- transaction modal -->
    <x-transaction-modal :users="$users" :paymentTerms="$paymentTerms" />

    <!-- transaction details modal -->
    <x-transaction-details-modal />

    @if($transactions->isEmpty())
        <p class="mt-4 text-gray-600">No transactions found.</p>
    @else
        <div class="overflow-x-auto">
            <table class="min-w-full bg-white rounded-lg shadow text-sm">
                <thead>
                    <tr class="bg-blue-50 border-b border-blue-200">
                        <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Date</th>
                        <th class="py-3 px-4 text-left font-semibold text-blue-700 w-60">Description</th>
                        <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Amount</th>
                        <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Receiver</th>
                        <th class="w-10"></th> <!-- empty title for icon -->
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $transaction)
                        <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                            <td class="py-2 px-4 border-b border-gray-200 w-40">
                                {{ $transaction->created_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 w-64">{{ $transaction->description }}</td>
                            <td
                                class="py-2 px-4 border-b border-gray-200 font-semibold w-32 {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                                {{ number_format($transaction->amount, 2) }} ₺
                            </td>
                            <td class="py-2 px-4 border-b border-gray-200 w-48">
                                {{ optional($transaction->user)->name ?: '-' }}
                            </td>
                            <td class="px-2 border-b border-gray-200 w-10 text-center align-middle">
                                <button class="group focus:outline-none" title="View Details" onclick="showTransactionDetails({{ $transaction->id }}, 
                                                        '{{ $transaction->created_at->format('d.m.Y H:i') }}', 
                                                        '{{ $transaction->description ?: 'No description' }}', 
                                                        '{{ number_format($transaction->amount, 2) }} ₺', 
                                                        '{{ optional($transaction->user)->name ?: '-' }}',
                                                        '{{ $transaction->payment_term_name }}')">
                                    <div class="relative w-4 h-4 inline-block">
                                        <!-- info icon -->
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

                    @for($i = $transactions->count(); $i < 20; $i++)
                        <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }}">
                            <td class="py-2 px-4 border-b border-gray-200 w-48">&nbsp;</td>
                            <td class="py-2 px-4 border-b border-gray-200 w-60"></td>
                            <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                            <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                            <td class="px-2 border-b border-gray-200 w-10"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <div class="mt-2">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif
</x-layout>