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
                    <label for="transaction_date" class="block text-sm font-semibold text-gray-700 mb-2">Transaction Date</label>
                    <input type="datetime-local" name="transaction_date" id="transaction_date"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400"
                        value="{{ now()->format('Y-m-d\TH:i') }}">
                    <p class="text-xs text-gray-400 mt-1">Select date and time for this transaction</p>
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

                    <div id="user_input_container" class="hidden" style="position:relative;">
                        <input type="text" name="custom_user" id="user_input" placeholder="Enter name" required
                            autocomplete="off"
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition shadow-sm hover:border-blue-400">
                        <div id="user_suggestions" class="absolute left-0 right-0 bg-white border border-gray-300 rounded-lg shadow-lg z-10 hidden"></div>
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
                    
                    <div class="mt-3">
                        <label class="inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_included" id="is_included" value="1" checked
                                class="h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2 text-sm text-gray-700">Include in processing</span>
                        </label>
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
                
                // Remove any selected_user_id field when switching to select mode
                const existingHiddenInput = document.getElementById('selected_user_id');
                if (existingHiddenInput) {
                    existingHiddenInput.remove();
                }
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
        
        // Clear any selected user ID when typing manually
        userInput.addEventListener('keydown', function() {
            const existingHiddenInput = document.getElementById('selected_user_id');
            if (existingHiddenInput) {
                existingHiddenInput.remove();
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

            // Reset date to current time
            document.getElementById('transaction_date').value = getCurrentDateTime();

            selectUser.checked = true;
            userSelectContainer.classList.remove('hidden');
            userInputContainer.classList.add('hidden');

            selectPayment.checked = true;
            paymentSelectContainer.classList.remove('hidden');
            paymentInputContainer.classList.add('hidden');
        }
        
        // Helper function to get current date time in the format needed for datetime-local input
        function getCurrentDateTime() {
            const now = new Date();
            return new Date(now.getTime() - (now.getTimezoneOffset() * 60000))
                .toISOString()
                .slice(0, 16); // Format: YYYY-MM-DDTHH:MM
        }

        // User search dropdown for custom input
        userInput.addEventListener('input', function () {
            const query = userInput.value.trim();
            const suggestionsBox = document.getElementById('user_suggestions');
            if (query.length < 1) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.classList.add('hidden');
                return;
            }
            fetch(`/users/search?q=${encodeURIComponent(query)}`)
                .then(res => res.json())
                .then(users => {
                    if (users.length === 0) {
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.classList.add('hidden');
                        return;
                    }
                    suggestionsBox.innerHTML = users.map(user =>
                        `<div class='px-4 py-2 cursor-pointer hover:bg-blue-100' data-name='${user.name}' data-id='${user.id}'>${user.name}</div>`
                    ).join('');
                    suggestionsBox.classList.remove('hidden');
                });
        });
        // Select suggestion
        document.getElementById('user_suggestions').addEventListener('mousedown', function (e) {
            if (e.target && e.target.dataset.name) {
                userInput.value = e.target.dataset.name;
                // Create a hidden input field to store the user ID when selecting from dropdown
                const existingHiddenInput = document.getElementById('selected_user_id');
                if (existingHiddenInput) {
                    existingHiddenInput.value = e.target.dataset.id;
                } else {
                    const hiddenInput = document.createElement('input');
                    hiddenInput.type = 'hidden';
                    hiddenInput.id = 'selected_user_id';
                    hiddenInput.name = 'selected_user_id';
                    hiddenInput.value = e.target.dataset.id;
                    form.appendChild(hiddenInput);
                }
                this.classList.add('hidden');
            }
        });
        // Hide suggestions on blur
        userInput.addEventListener('blur', function () {
            setTimeout(() => {
                document.getElementById('user_suggestions').classList.add('hidden');
            }, 150);
        });
    });
</script>