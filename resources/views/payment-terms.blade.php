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

        <x-transactions-table :transactions="$transactions" :showReceiver="true" :showPaymentMethod="true" />
        
        <x-transaction-details-modal />

        <x-transaction-edit-modal :paymentTerms="$paymentTerms"/>

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
            const rows = document.querySelectorAll('.transaction-row');
            let anyVisible = false;

            rows.forEach(row => {
                const isVisible = row.dataset.paymentTermId == paymentTermId;
                row.style.display = isVisible ? '' : 'none';
                if (isVisible) anyVisible = true;
            });

            // filter badge
            const filterBadge = document.getElementById('filter-badge');
            const filterText = document.getElementById('filter-text');
            filterBadge.classList.remove('hidden');
            filterText.textContent = `Filtered by: ${paymentTermName}`;

            // message if no transactions found
            const tbody = document.querySelector('tbody');
            let noResultsRow = document.getElementById('no-transactions-found');

            if (!anyVisible) {
                if (!noResultsRow) {
                    noResultsRow = document.createElement('tr');
                    noResultsRow.id = 'no-transactions-found';
                    noResultsRow.innerHTML = `<td colspan="5" class="py-4 text-center text-gray-500">No transactions found for this payment term</td>`;
                    tbody.appendChild(noResultsRow);
                }
            } else if (noResultsRow) {
                noResultsRow.remove();
            }
        }

        function clearPaymentTermFilter() {
            const rows = document.querySelectorAll('.transaction-row');

            rows.forEach(row => {
                row.style.display = '';
            });

            document.getElementById('filter-badge').classList.add('hidden');

            const noResultsRow = document.getElementById('no-transactions-found');
            if (noResultsRow) {
                noResultsRow.remove();
            }
        }
    </script>

</x-layout>