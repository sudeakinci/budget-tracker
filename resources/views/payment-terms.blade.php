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

            /* Filter badge animations */
            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            #payment-term-filter-badges>div {
                animation: fade-in 0.2s ease;
            }

            /* Active payment term in table */
            .payment-term-row.active {
                background-color: rgba(59, 130, 246, 0.05);
            }

            .filter-button.active {
                @apply text-blue-800 font-medium;
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
        <div id="active-filters" class="flex flex-wrap items-center gap-1 mb-4 mt-2">
            <span class="text-sm font-semibold text-gray-700">Filtered by:</span>
            <div id="payment-term-filter-badges" class="flex flex-wrap gap-1 ml-1"></div>
            <button id="clear-all-filters" type="button" onclick="clearAllPaymentTermFilters()"
                class="text-xs px-2 py-1 rounded text-blue-600 hover:bg-blue-50 font-medium ml-auto hidden">
                Clear all filters
            </button>
        </div>

    </div>

    <!-- transactions section -->
    <div>
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-semibold text-gray-700">Transactions by Payment Term</h2>
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
            // Initialize selected payment terms from URL
            const urlParams = new URLSearchParams(window.location.search);

            // Support both old and new parameter names for backward compatibility
            if (urlParams.has('payment_term_ids')) {
                const paymentTermIds = urlParams.get('payment_term_ids').split(',');
                const paymentTermRows = document.querySelectorAll('.payment-term-row');

                // Clear existing selected terms
                selectedPaymentTerms = [];

                // For each ID in the URL, find the corresponding row and add to selected terms
                paymentTermIds.forEach(id => {
                    for (const row of paymentTermRows) {
                        if (row.dataset.paymentTermId === id) {
                            const paymentTermName = row.querySelector('td:first-child').textContent.trim();
                            selectedPaymentTerms.push({
                                id: parseInt(id),
                                name: paymentTermName
                            });
                            break;
                        }
                    }
                });

                // Update the UI
                updateFilterBadges();
                highlightActivePaymentTerms();

                // Make sure the active-filters in transactions-table is visible
                const activeFilters = document.getElementById('active-filters');
                if (activeFilters) activeFilters.classList.remove('hidden');
            }
            // Backwards compatibility with old single payment_term_id parameter
            else if (urlParams.has('payment_term_id')) {
                const paymentTermId = urlParams.get('payment_term_id');
                const paymentTermRows = document.querySelectorAll('.payment-term-row');

                for (const row of paymentTermRows) {
                    if (row.dataset.paymentTermId === paymentTermId) {
                        const paymentTermName = row.querySelector('td:first-child').textContent.trim();

                        // Add to selected terms
                        selectedPaymentTerms.push({
                            id: parseInt(paymentTermId),
                            name: paymentTermName
                        });

                        // Update the UI
                        updateFilterBadges();
                        highlightActivePaymentTerms();

                        // Make sure the active-filters in transactions-table is visible
                        const activeFilters = document.getElementById('active-filters');
                        if (activeFilters) activeFilters.classList.remove('hidden');
                        break;
                    }
                }
            }

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

        // Array to track selected payment term IDs and names
        let selectedPaymentTerms = [];

        function togglePaymentTermFilter(paymentTermId, paymentTermName) {
            // Check if this payment term is already selected
            const index = selectedPaymentTerms.findIndex(term => term.id === paymentTermId);

            if (index === -1) {
                // Add to selected terms
                selectedPaymentTerms.push({
                    id: paymentTermId,
                    name: paymentTermName
                });
            } else {
                // Remove from selected terms
                selectedPaymentTerms.splice(index, 1);
            }

            // Apply the filters
            applyPaymentTermFilters();
        }

        function applyPaymentTermFilters() {
            // Show loading indicator
            showLoadingIndicator();

            // Build the URL with filter parameters
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);

            if (selectedPaymentTerms.length > 0) {
                // Set payment_term_ids filter parameter with comma-separated IDs
                const ids = selectedPaymentTerms.map(term => term.id).join(',');
                params.set('payment_term_ids', ids);
            } else {
                // If no terms selected, remove the parameter
                params.delete('payment_term_ids');
            }

            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        }

        function removePaymentTermFilter(paymentTermId) {
            // Remove the payment term from selected terms
            selectedPaymentTerms = selectedPaymentTerms.filter(term => term.id !== paymentTermId);

            // Re-apply the filters
            applyPaymentTermFilters();
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

        function clearAllPaymentTermFilters() {
            // Show loading indicator
            showLoadingIndicator();

            // Clear all selected payment terms
            selectedPaymentTerms = [];

            // Remove the payment_term_ids parameter and reload
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);

            // Remove payment_term_ids filter
            params.delete('payment_term_ids');
            // For backward compatibility
            params.delete('payment_term_id');

            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        }

        function updateFilterBadges() {
            const container = document.getElementById('payment-term-filter-badges');
            if (!container) return;

            container.innerHTML = '';

            selectedPaymentTerms.forEach(term => {
                const badge = document.createElement('span');
                badge.className = 'inline-flex items-center bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded-md border border-blue-100';
                badge.innerHTML = `
                    <span>${term.name}</span>
                    <span class="ml-1 text-blue-400 font-medium"></span>
                    <button type="button" class="ml-1 text-gray-500 hover:text-gray-700" onclick="removePaymentTermFilter(${term.id})">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                `;
                container.appendChild(badge);
            });

            // Get the transaction table's filter container
            const activeFilters = document.getElementById('active-filters');
            const paymentTermFilterContainer = document.getElementById('payment-term-filter-container');

            if (selectedPaymentTerms.length > 0) {
                if (activeFilters) activeFilters.classList.remove('hidden');
                if (paymentTermFilterContainer) paymentTermFilterContainer.classList.remove('hidden');
            } else {
                if (paymentTermFilterContainer) paymentTermFilterContainer.classList.add('hidden');

                // Only hide active filters if no other filters are active
                if (activeFilters &&
                    document.querySelectorAll('#date-filter-badge:not(.hidden), #receiver-filter-container:not(.hidden), #amount-filter-badge:not(.hidden)').length === 0) {
                    activeFilters.classList.add('hidden');
                }

                // Check if there's a clear all button
                const clearAllBtn = document.querySelector('#active-filters button[onclick="clearAllFilters()"]');
                if (clearAllBtn) clearAllBtn.classList.remove('hidden');
            }

            // Call highlight function after updating badges
            highlightActivePaymentTerms();
        }

        function highlightActivePaymentTerms() {
            // Clear all active states first
            document.querySelectorAll('.payment-term-row').forEach(row => {
                row.classList.remove('active');
                const filterButton = row.querySelector('.filter-button');
                if (filterButton) {
                    filterButton.classList.remove('active');
                }
            });

            // Highlight selected payment terms
            selectedPaymentTerms.forEach(term => {
                const row = document.querySelector(`.payment-term-row[data-payment-term-id="${term.id}"]`);
                if (row) {
                    row.classList.add('active');
                    const filterButton = row.querySelector('.filter-button');
                    if (filterButton) {
                        filterButton.classList.add('active');
                    }
                }
            });
        }

        // Function that can be called from the transactions-table component to clear payment terms without reloading
        function clearPaymentTermsWithoutReload() {
            selectedPaymentTerms = [];
            highlightActivePaymentTerms();
        }
    </script>

</x-layout>