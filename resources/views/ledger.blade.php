<x-layout :title="'Ledger'" :hide-notifications="true">
    @if(session('status'))
        <x-success-toast />
    @endif

    <x-error-toast :errors="$errors" />

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

        <h1 class="text-2xl font-bold text-gray-800">Ledger</h1>
    </div>

    <div class="flex items-end mb-4 justify-between">
        <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded px-3 py-1 shadow text-sm flex items-center">
            <i class="fas fa-wallet mr-1"></i>
            <span class="font-semibold">Balance:</span>
            <span class="ml-1">{{ number_format($balance, 2) }}</span>
        </div>

        <button type="button" id="openLedgerModal"
            class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none ">New
            Entry</button>
    </div>

    <x-ledger-modal :users="$users" />

    <!-- transaction details modal -->
    <x-transaction-details-modal />

    <!-- transaction edit modal -->
    <x-transaction-edit-modal :paymentTerms="$paymentTerms" />

    @if($transactions->isEmpty())
        <p class="mt-4 text-gray-600">No ledger entries found.</p>
    @else
        <x-transactions-table :transactions="$transactions" :show-receiver="true" :row-count="20" />
        <div class="mt-2">
            {{ $transactions->links() }}
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openLedgerModal');
            const modal = document.getElementById('ledgerModal');
            const closeBtn = document.getElementById('closeLedgerModal');

            if (openBtn && modal && closeBtn) {
                openBtn.addEventListener('click', function () {
                    modal.classList.remove('hidden');
                });

                closeBtn.addEventListener('click', function () {
                    modal.classList.add('hidden');
                });

                window.addEventListener('click', function (event) {
                    if (event.target === modal) {
                        modal.classList.add('hidden');
                    }
                });
            }

            // dropdown toggle
            window.toggleDropdown = function (id) {
                const dropdown = document.getElementById(`dropdown-${id}`);
                document.querySelectorAll('.dropdown-menu').forEach(menu => {
                    if (menu.id !== `dropdown-${id}`) {
                        menu.classList.add('hidden');
                    }
                });
                dropdown.classList.toggle('hidden');
            }

            // close menu on click outside
            document.addEventListener('click', function (event) {
                if (!event.target.closest('.dropdown')) {
                    document.querySelectorAll('.dropdown-menu').forEach(menu => {
                        menu.classList.add('hidden');
                    });
                }
            });
        });

    </script>
</x-layout>