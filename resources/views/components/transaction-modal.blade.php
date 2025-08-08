@props(['users', 'paymentTerms'])

<div id="transactionModal"
    class="fixed inset-0 bg-gray-600 bg-opacity-50 flex items-center justify-center h-full w-full hidden z-50">
    <div class="p-5 border w-11/12 md:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">New Transaction</h3>

            <form action="{{ route('transactions.store') }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="amount" class="block text-sm font-medium text-gray-700 mb-1">Amount</label>
                    <input type="number" name="amount" id="amount"  required
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

                        <input type="radio" name="receiver_type" id="custom_user" value="custom"
                            class="ml-4 mr-2">
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

<script>
    document.addEventListener('DOMContentLoaded', function() {
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
        if (openBtn) {
            openBtn.addEventListener('click', function() {
                modal.classList.remove('hidden');
            });
        }

        // Close modal
        closeBtn.addEventListener('click', function() {
            modal.classList.add('hidden');
        });

        // Close on click outside
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.classList.add('hidden');
            }
        });

        // Toggle user selection/input
        selectUser.addEventListener('change', function() {
            if (this.checked) {
                userSelect.classList.remove('hidden');
                userInput.classList.add('hidden');
            }
        });

        customUser.addEventListener('change', function() {
            if (this.checked) {
                userSelect.classList.add('hidden');
                userInput.classList.remove('hidden');
            }
        });

        // Toggle payment selection/input
        selectPayment.addEventListener('change', function() {
            if (this.checked) {
                paymentSelect.classList.remove('hidden');
                paymentInput.classList.add('hidden');
            }
        });

        customPayment.addEventListener('change', function() {
            if (this.checked) {
                paymentSelect.classList.add('hidden');
                paymentInput.classList.remove('hidden');
            }
        });
    });
</script>