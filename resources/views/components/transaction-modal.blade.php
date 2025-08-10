@props(['users', 'paymentTerms'])

<div id="transactionModal"
    class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center h-full w-full hidden z-50">
    <div class="p-0 w-full max-w-md shadow-2xl rounded-2xl bg-white border border-gray-200">
        <div class="flex flex-col items-center pt-8 pb-4 px-8">

            <form action="{{ route('transactions.store') }}" method="POST" class="w-full">
                @csrf
                <input type="hidden" name="transaction_type" id="transaction_type_input" value="income">

                <div class="mb-5">
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-2">Amount</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">₺</span>
                        <input type="number" name="amount" id="amount" required step="0.01" min="0.01"
                            class="w-full pl-8 px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400"
                            placeholder="0.00">
                    </div>
                    <p class="text-xs text-gray-400 mt-1">Enter a positive amount</p>
                </div>

                <div class="mb-5">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">Description</label>
                    <input type="text" name="description" id="description"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400"
                        placeholder="Description">
                </div>

                <div class="mb-5">
                    <label id="person_label" class="block text-sm font-semibold text-gray-700 mb-2">Sender</label>
                    <div class="flex items-center mb-2 gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="receiver_type" id="select_user" value="select" checked
                                class="peer sr-only">
                            <span
                                class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 font-medium peer-checked:bg-blue-500 peer-checked:text-white transition">Select
                                User</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="receiver_type" id="custom_user" value="custom"
                                class="peer sr-only">
                            <span
                                class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 font-medium peer-checked:bg-blue-500 peer-checked:text-white transition">Enter
                                Name</span>
                        </label>
                    </div>

                    <div id="user_select_container">
                        <select name="user_id" id="user_select" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400">
                            <option value="">Select a user</option>
                            @foreach($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="user_input_container" class="hidden">
                        <input type="text" name="custom_user" id="user_input" placeholder="Enter name" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Payment Method</label>
                    <div class="flex items-center mb-2 gap-4">
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_type" id="select_payment" value="select" checked
                                class="peer sr-only">
                            <span
                                class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 font-medium peer-checked:bg-blue-500 peer-checked:text-white transition">Select
                                Method</span>
                        </label>
                        <label class="flex items-center cursor-pointer">
                            <input type="radio" name="payment_type" id="custom_payment" value="custom"
                                class="peer sr-only">
                            <span
                                class="px-3 py-1 rounded-lg border border-gray-300 bg-gray-50 text-gray-700 font-medium peer-checked:bg-blue-500 peer-checked:text-white transition">Enter
                                Method</span>
                        </label>
                    </div>

                    <div id="payment_select_container">
                        <select name="payment_term_id" id="payment_select" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400">
                            <option value="">Select a payment method</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div id="payment_input_container" class="hidden">
                        <input type="text" name="payment_term_name" id="payment_input" required
                            placeholder="Enter payment method"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400">
                    </div>
                </div>

                <div class="flex justify-between gap-4 mt-8">
                    <button type="submit" name="transaction_type" value="income" onclick="submitForm('income')"
                        class="w-1/2 px-4 py-2 bg-green-500 text-white rounded-lg font-bold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-400 transition shadow">
                        Income
                    </button>
                    <button type="submit" name="transaction_type" value="expense" onclick="submitForm('expense')"
                        class="w-1/2 px-4 py-2 bg-red-500 text-white rounded-lg font-bold hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-400 transition shadow">
                        Expense
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modal = document.getElementById('transactionModal');
        const openBtn = document.getElementById('openTransactionModal');
        const form = modal.querySelector('form');

        // Radio buttons
        const selectUser = document.getElementById('select_user');
        const customUser = document.getElementById('custom_user');
        const userSelectContainer = document.getElementById('user_select_container');
        const userInputContainer = document.getElementById('user_input_container');
        const userSelect = document.getElementById('user_select');
        const userInput = document.getElementById('user_input');

        const selectPayment = document.getElementById('select_payment');
        const customPayment = document.getElementById('custom_payment');
        const paymentSelectContainer = document.getElementById('payment_select_container');
        const paymentInputContainer = document.getElementById('payment_input_container');
        const paymentSelect = document.getElementById('payment_select');
        const paymentInput = document.getElementById('payment_input');

        const personLabel = document.getElementById('person_label');
        const transactionTypeInput = document.getElementById('transaction_type_input');

        // Open modal
        if (openBtn && modal) {
            openBtn.addEventListener('click', function () {
                resetForm();
                modal.classList.remove('hidden');
            });
        }

        // Close on click outside
        window.addEventListener('click', function (event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Toggle user selection/input
        selectUser.addEventListener('change', function () {
            if (this.checked) {
                userSelectContainer.classList.remove('hidden');
                userInputContainer.classList.add('hidden');
                userInput.removeAttribute('required');
                userSelect.setAttribute('required', '');
            }
        });

        customUser.addEventListener('change', function () {
            if (this.checked) {
                userSelectContainer.classList.add('hidden');
                userInputContainer.classList.remove('hidden');
                userSelect.removeAttribute('required');
                userInput.setAttribute('required', '');
            }
        });

        // Toggle payment selection/input
        selectPayment.addEventListener('change', function () {
            if (this.checked) {
                paymentSelectContainer.classList.remove('hidden');
                paymentInputContainer.classList.add('hidden');
                paymentInput.removeAttribute('required');
                paymentSelect.setAttribute('required', '');
            }
        });

        customPayment.addEventListener('change', function () {
            if (this.checked) {
                paymentSelectContainer.classList.add('hidden');
                paymentInputContainer.classList.remove('hidden');
                paymentSelect.removeAttribute('required');
                paymentInput.setAttribute('required', '');
            }
        });

        window.submitForm = function (type) {
            transactionTypeInput.value = type;
            if (type === 'expense') {
                personLabel.textContent = 'Receiver';
            } else {
                personLabel.textContent = 'Sender';
            }
            form.submit();
        }

        // Reset form
        function resetForm() {
            form.reset();
            personLabel.textContent = 'Sender';
            transactionTypeInput.value = 'income';

            selectUser.checked = true;
            userSelectContainer.classList.remove('hidden');
            userInputContainer.classList.add('hidden');

            selectPayment.checked = true;
            paymentSelectContainer.classList.remove('hidden');
            paymentInputContainer.classList.add('hidden');
        }
    });
</script>