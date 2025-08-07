@props(['paymentTerms'])

<div id="transactionEditModal"
    class="fixed inset-0 z-50 overflow-y-auto flex items-center justify-center bg-gray-600 bg-opacity-50 transition-opacity duration-300 hidden">
    <div class="relative w-full max-w-lg mx-auto animate-fadeIn">
        <div class="bg-white rounded-lg shadow-2xl border border-blue-100">
            <!-- Header -->
            <div class="bg-blue-50 px-6 py-4 rounded-t-lg border-b border-blue-100 flex items-center justify-between">
                <div class="flex items-center">
                    <span class="flex items-center justify-center bg-blue-100 rounded-full mr-3"
                        style="width:32px;height:32px;">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </span>
                    <h3 class="text-lg font-bold text-gray-900">Edit Transaction</h3>
                </div>
            </div>

            <!-- Content -->
            <div class="px-6 py-4">
                <form id="editTransactionForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="edit-description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <input type="text" id="edit-description" name="description" 
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Payment Method</label>
                        <div class="flex items-center mb-2">
                            <input type="radio" name="payment_type" id="edit-select-payment" value="select" checked
                                class="mr-2">
                            <label for="edit-select-payment" class="text-sm">Select Method</label>

                            <input type="radio" name="payment_type" id="edit-custom-payment" value="custom"
                                class="ml-4 mr-2">
                            <label for="edit-custom-payment" class="text-sm">Enter Method</label>
                        </div>

                        <select name="payment_term_id" id="edit-payment-select"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Select a payment method</option>
                            @foreach($paymentTerms as $term)
                                <option value="{{ $term->id }}">{{ $term->name }}</option>
                            @endforeach
                        </select>

                        <input type="text" name="payment_term_name" id="edit-payment-input"
                            placeholder="Enter payment method"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500 mt-2 hidden">
                    </div>

                    <!-- footer with buttons -->
                    <div class="flex justify-end items-center space-x-3 border-t border-gray-100 pt-4 mt-4">
                        <button type="button" id="closeEditModal"
                            class="px-4 py-2 bg-gray-100 text-gray-800 rounded-md hover:bg-gray-200 focus:outline-none transition-colors">
                            Cancel
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 focus:outline-none transition-colors">
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editModal = document.getElementById('transactionEditModal');
    const closeEditBtn = document.getElementById('closeEditModal');
    const editSelectPayment = document.getElementById('edit-select-payment');
    const editCustomPayment = document.getElementById('edit-custom-payment');
    const editPaymentSelect = document.getElementById('edit-payment-select');
    const editPaymentInput = document.getElementById('edit-payment-input');

    if (closeEditBtn && editModal) {
        // close edit modal
        closeEditBtn.addEventListener('click', function() {
            editModal.classList.add('hidden');
        });

        window.addEventListener('click', function(event) {
            if (event.target === editModal) {
                editModal.classList.add('hidden');
            }
        });
    }

    // toggle payment selection/input
    if (editSelectPayment && editCustomPayment && editPaymentSelect && editPaymentInput) {
        editSelectPayment.addEventListener('change', function() {
            if (this.checked) {
                editPaymentSelect.classList.remove('hidden');
                editPaymentInput.classList.add('hidden');
            }
        });

        editCustomPayment.addEventListener('change', function() {
            if (this.checked) {
                editPaymentSelect.classList.add('hidden');
                editPaymentInput.classList.remove('hidden');
            }
        });
    }
});

window.editTransaction = function(id, description, paymentTermName, paymentTermId) {
    // get form elements
    const form = document.getElementById('editTransactionForm');
    const descriptionInput = document.getElementById('edit-description');
    const selectPayment = document.getElementById('edit-select-payment');
    const customPayment = document.getElementById('edit-custom-payment');
    const paymentSelect = document.getElementById('edit-payment-select');
    const paymentInput = document.getElementById('edit-payment-input');

    // set the form action URL
    form.action = `/transactions/${id}`;

    // fill the description
    descriptionInput.value = description;

    // set up payment method
    if (paymentTermId) {
        selectPayment.checked = true;
        customPayment.checked = false;
        paymentSelect.value = paymentTermId;
        paymentSelect.classList.remove('hidden');
        paymentInput.classList.add('hidden');
    } else {
        selectPayment.checked = false;
        customPayment.checked = true;
        paymentInput.value = paymentTermName;
        paymentSelect.classList.add('hidden');
        paymentInput.classList.remove('hidden');
    }

    // show the modal
    document.getElementById('transactionEditModal').classList.remove('hidden');
};
</script>