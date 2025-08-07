@props(['id' => null, 'date' => null, 'description' => null, 'amount' => null, 'receiver' => null, 'paymentMethod' => null])

<div id="transactionDetailsModal"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const detailsModal = document.getElementById('transactionDetailsModal');
    const closeDetailsBtn = document.getElementById('closeDetailsModal');

    if (closeDetailsBtn && detailsModal) {
        // Close details modal
        closeDetailsBtn.addEventListener('click', function() {
            detailsModal.classList.add('hidden');
        });

        // Close on click outside
        window.addEventListener('click', function(event) {
            if (event.target === detailsModal) {
                detailsModal.classList.add('hidden');
            }
        });
    }
});

window.showTransactionDetails = function(id, date, description, amount, receiver, paymentMethod) {
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
};
</script>