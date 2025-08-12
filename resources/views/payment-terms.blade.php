<x-layout :title="'Payment Terms'" :hideNotifications="true">
    @if(session('status'))
        <x-success-toast />
    @endif

    <x-error-toast :errors="$errors" />

    <script>
        // hide the toasts automatically
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

    <div class="container mx-auto py-3 flex justify-between items-center">
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

        <h1 class="text-2xl font-bold text-gray-800">Payment Terms</h1>
    </div>

    <!-- payment terms Section -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Your Payment Terms</h2>
        </div>

        <x-payment-terms-table :paymentTerms="$paymentTerms" />
    </div>

    <!-- transactions section -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Transactions by Payment Term</h2>
        </div>

        <div id="filter-badge" class="hidden mb-4 mt-2">
            <div class="inline-flex items-center bg-blue-100 text-blue-800 rounded-full px-3 py-1 text-sm">
                <span id="filter-text"></span>
                <button type="button" class="ml-2 text-blue-600 hover:text-blue-800" onclick="clearPaymentTermFilter()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>

        <div class="p-4 pl-0 pr-0 mb-4">
            @if($transactions->isEmpty())
                <p class="mt-4 text-gray-600">No transaction found.</p>
            @else
                <x-transactions-table :transactions="$transactions" :showReceiver="true" :showPaymentMethod="true"
                    :row-count="20" />
                <div class="mt-2">
                    {{ $transactions->appends(request()->query())->links() }}
                </div>
            @endif
        </div>

        <x-transaction-details-modal />

        <x-transaction-edit-modal :paymentTerms="$paymentTerms" />

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Dropdown toggle functionality
            window.toggleDropdown = function (id) {
                const dropdown = document.getElementById(`dropdown-${id}`);
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== `dropdown-${id}`) {
                        menu.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden');
            }

            // Close dropdowns when clicking outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });
        });

        function filterTransactionsByPaymentTerm(paymentTermId, paymentTermName) {
            // Show loading indicator
            showLoadingIndicator();
            
            // Build the URL with filter parameters
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            
            // Set payment_term_id filter parameter
            params.set('payment_term_id', paymentTermId);
            
            // Update filter badge
            const filterBadge = document.getElementById('filter-badge');
            const filterText = document.getElementById('filter-text');
            filterBadge.classList.remove('hidden');
            filterText.textContent = `Filtered by: ${paymentTermName}`;
            
            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        }
        
        // Show loading indicator when filter is applied
        function showLoadingIndicator() {
            // Create loading overlay if it doesn't exist
            if (!document.getElementById('loading-overlay')) {
                const overlay = document.createElement('div');
                overlay.id = 'loading-overlay';
                overlay.className = 'fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center';
                overlay.innerHTML = `
                    <div class="bg-white p-4 rounded-lg shadow-lg flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Loading...</span>
                    </div>
                `;
                document.body.appendChild(overlay);
            }
        }

        function clearPaymentTermFilter() {
            // Show loading indicator
            showLoadingIndicator();
            
            // Remove the payment_term_id parameter and reload
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            
            // Remove payment_term_id filter
            params.delete('payment_term_id');
            
            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        }
    </script>

</x-layout>