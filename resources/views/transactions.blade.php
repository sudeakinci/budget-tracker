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

    <!-- Transaction Modal -->
    <div id="transactionModal"
        class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
        <div class="p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Transaction</h3>

                <form action="{{ route('transactions.store') }}" method="POST">
                    @csrf
                    <div class="mb-4">
                        <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                        <input type="number" name="amount" id="amount" step="0.01" min="0.01" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label for="description"
                            class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" name="description" id="description"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Receiver</label>
                        <div class="flex items-center mb-2">
                            <input type="radio" name="receiver_type" id="select_user" value="select" checked
                                class="mr-2">
                            <label for="select_user" class="text-sm">Select User</label>

                            <input type="radio" name="receiver_type" id="custom_user" value="custom" class="ml-4 mr-2">
                            <label for="custom_user" class="text-sm">Enter Name</label>
                        </div>

                        <select name="user_id" id="user_select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="custom_user" id="user_input" placeholder="Enter receiver name"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 mt-2 hidden">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <div class="flex items-center mb-2">
                            <input type="radio" name="payment_type" id="select_payment" value="select" checked
                                class="mr-2">
                            <label for="select_payment" class="text-sm">Select Method</label>

                            <input type="radio" name="payment_type" id="custom_payment" value="custom"
                                class="ml-4 mr-2">
                            <label for="custom_payment" class="text-sm">Enter Method</label>
                        </div>

                        <select name="payment_term_id" id="payment_select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a payment method</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="payment_term_name" id="payment_input"
                            placeholder="Enter payment method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 mt-2 hidden">
                    </div>

                    <div class="flex justify-end space-x-3">
                        <button type="button" id="closeTransactionModal"
                            class="px-4 py-2 bg-gray-200 text-gray-800 rounded-md hover:bg-gray-300 focus:outline-none">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none">
                            Save
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

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

    <!-- Transaction Details Modal -->
    <div id="transactionDetailsModal"
        class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-600 bg-opacity-50 transition-opacity duration-300 hidden">
        <div class="relative w-full max-w-lg mx-auto animate-fadeIn">
            <div class="bg-white rounded-lg shadow-2xl border border-blue-100">
                <!-- Header -->
                <div
                    class="bg-blue-50 px-6 py-4 rounded-t-lg border-b border-blue-100 flex items-center justify-between">
                    <div class="flex items-center">
                        <span class="flex items-center justify-center bg-blue-100 rounded-full mr-3"
                            style="width:32px;height:32px;">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                                viewBox="0 0 24 24">
                                <circle cx="12" cy="12" r="10" />
                                <line x1="12" y1="16" x2="12" y2="12" />
                                <line x1="12" y1="8" x2="12.01" y2="8" />
                            </svg>
                        </span>
                        <h3 class="text-lg font-bold text-gray-900">Transaction Details</h3>
                    </div>
                </div>

                <!-- Content -->
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="col-span-2 bg-gray-50 p-3 rounded-lg">
                            <span class="text-xs text-gray-500">Amount</span>
                            <p id="detail-amount" class="text-xl font-bold text-gray-800"></p>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs text-gray-500">Description</span>
                            <p id="detail-description" class="font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Receiver</span>
                            <p id="detail-receiver" class="font-medium text-gray-800"></p>
                        </div>
                        <div>
                            <span class="text-xs text-gray-500">Payment Method</span>
                            <p id="detail-payment-method" class="font-medium text-gray-800"></p>
                        </div>
                        <div class="md:col-span-2">
                            <span class="text-xs text-gray-500">Date</span>
                            <p id="detail-date" class="font-medium text-gray-800"></p>
                        </div>
                    </div>

                    <!-- Footer with buttons -->
                    <div class="flex justify-end items-center space-x-3 border-t border-gray-100 pt-4">
                        <button type="button" id="closeDetailsModal"
                            class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 focus:outline-none transition-colors">
                            Close
                        </button>
                        <form id="deleteTransactionForm" method="POST" class="inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700 focus:outline-none transition-colors flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                    </path>
                                </svg>
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const modal = document.getElementById('transactionModal');
            const openBtn = document.getElementById('openTransactionModal');
            const closeBtn = document.getElementById('closeTransactionModal');

            // Radio buttons
            const selectUser = document.getElementById('select_user');
            const customUser = document.getElementById('custom_user');
            const userSelect = document.getElementById('user_select');
            const userInput = document.getElementById('user_input');

            const selectPayment = document.getElementById('select_payment');
            const customPayment = document.getElementById('custom_payment');
            const paymentSelect = document.getElementById('payment_select');
            const paymentInput = document.getElementById('payment_input');

            // Transaction details modal
            const detailsModal = document.getElementById('transactionDetailsModal');
            const closeDetailsBtn = document.getElementById('closeDetailsModal');

            // Close details modal
            closeDetailsBtn.addEventListener('click', function () {
                detailsModal.classList.add('hidden');
            });

            // Close on click outside
            window.addEventListener('click', function (event) {
                if (event.target === detailsModal) {
                    detailsModal.classList.add('hidden');
                }
            });

            // Open modal
            openBtn.addEventListener('click', function () {
                modal.classList.remove('hidden');
            });

            // Close modal
            closeBtn.addEventListener('click', function () {
                modal.classList.add('hidden');
            });

            // Close on click outside
            window.addEventListener('click', function (event) {
                if (event.target === modal) {
                    modal.classList.add('hidden');
                }
            });

            // Toggle user selection/input
            selectUser.addEventListener('change', function () {
                if (this.checked) {
                    userSelect.classList.remove('hidden');
                    userInput.classList.add('hidden');
                }
            });

            customUser.addEventListener('change', function () {
                if (this.checked) {
                    userSelect.classList.add('hidden');
                    userInput.classList.remove('hidden');
                }
            });

            // Toggle payment selection/input
            selectPayment.addEventListener('change', function () {
                if (this.checked) {
                    paymentSelect.classList.remove('hidden');
                    paymentInput.classList.add('hidden');
                }
            });

            customPayment.addEventListener('change', function () {
                if (this.checked) {
                    paymentSelect.classList.add('hidden');
                    paymentInput.classList.remove('hidden');
                }
            });
            ['successToast', 'errorToast'].forEach(function (id) {
                const toast = document.getElementById(id);
                if (toast) {
                    setTimeout(() => {
                        toast.classList.add('opacity-0');
                        setTimeout(() => toast.remove(), 500);
                    }, 3500);
                }
            });
        });

        function showTransactionDetails(id, date, description, amount, receiver, paymentMethod) {
            // Fill in the details
            document.getElementById('detail-date').textContent = date;
            document.getElementById('detail-description').textContent = description;
            document.getElementById('detail-amount').textContent = amount;
            document.getElementById('detail-receiver').textContent = receiver;
            document.getElementById('detail-payment-method').textContent = paymentMethod;

            // Set up the delete form action
            document.getElementById('deleteTransactionForm').action = `/transactions/${id}`;

            // Show the modal
            document.getElementById('transactionDetailsModal').classList.remove('hidden');
        }

    </script>

</x-layout>