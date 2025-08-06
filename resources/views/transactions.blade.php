<x-layout :title="'Transactions'">

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
    <div id="transactionModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Transaction</h3>

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

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
                        </tr>
                    @endforeach

                    @for($i = $transactions->count(); $i < 20; $i++)
                        <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }}">
                            <td class="py-2 px-4 border-b border-gray-200 w-48">&nbsp;</td>
                            <td class="py-2 px-4 border-b border-gray-200 w-60"></td>
                            <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                            <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            <div class="mt-2">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif

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
        });
    </script>

</x-layout>