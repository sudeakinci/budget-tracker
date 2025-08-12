<x-layout :title="'Transactions'" :hideNotifications="true">
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

    <!-- 3-month summary cards -->
    <div class="grid grid-cols-3 gap-2 mb-4">
        @for ($i = 0; $i < 3; $i++)
            @php
                $monthKey = 'm' . $i;
                $monthName = $monthNames[$monthKey] ?? '';
                $expense = $stats['expense'][$monthKey] ?? 0;
                $income = $stats['income'][$monthKey] ?? 0;
                $netAmount = $income - $expense;

                if ($netAmount == 0) {
                    $type = 'default';
                    $color = 'gray';
                    $icon = 'minus';
                } elseif ($netAmount > 0) {
                    $type = 'credit'; // yeşil
                    $color = 'green';
                    $icon = 'credit';
                } else {
                    $type = 'debt'; // kırmızı
                    $color = 'red';
                    $icon = 'debt';
                }
            @endphp
            <x-info-cards :title="$monthName . ' Overview'" :type="$type" :amount="$netAmount" :period="$monthName"
                :icon="$icon" :color="$color" subtitle="Net (Income - Expense)" />
        @endfor
    </div>

    <!-- transaction modal -->
    <x-transaction-modal :users="$users" :paymentTerms="$paymentTerms" />

    <!-- transaction details modal -->
    <x-transaction-details-modal />

    <!-- transaction edit modal -->
    <x-transaction-edit-modal :paymentTerms="$paymentTerms" />

    <div class="p-4 pl-0 pr-0 mb-4">
        @if($transactions->isNotEmpty())
            <x-transactions-table :transactions="$transactions" :show-receiver="true" :row-count="20" />
            <div class="mt-2">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-12">
                <p class="text-gray-500">No transactions found for the selected criteria.</p>
            </div>
        @endif
    </div>

    <!-- total summary cards -->
     <h1 class="text-2xl font-bold m-3 text-gray-800">Summary</h1>
    <div class="grid grid-cols-2 gap-4">
        <!-- Overall total summary card -->
        @php
            $totalExpense = is_array($stats['expense']) ? array_sum($stats['expense']) : 0;
            $totalIncome = is_array($stats['income']) ? array_sum($stats['income']) : 0;
            $netAmount = $totalIncome - $totalExpense;

            if ($netAmount == 0) {
                $type = 'default';
                $color = 'gray';
                $icon = 'minus';
            } elseif ($netAmount > 0) {
                $type = 'credit'; // yeşil
                $color = 'green';
                $icon = 'credit';
            } else {
                $type = 'debt'; // kırmızı
                $color = 'red';
                $icon = 'debt';
            }
        @endphp
        <x-info-cards title="Overall Total" :type="$type" :amount="$netAmount" period="All Time" :icon="$icon" :color="$color"
            subtitle="Net (Income - Expense)" />
            
        <!-- Filtered transactions summary card -->
        @php
            $filteredIncome = 0;
            $filteredExpense = 0;
            
            if($transactions->isNotEmpty()) {
                foreach($transactions as $transaction) {
                    if($transaction->display_amount > 0) {
                        $filteredExpense += $transaction->display_amount;
                    } else {
                        $filteredIncome += abs($transaction->display_amount);
                    }
                }
            }
            
            $filteredNetAmount = $filteredIncome - $filteredExpense;
            
            if ($filteredNetAmount == 0) {
                $filteredType = 'default';
                $filteredColor = 'gray';
                $filteredIcon = 'minus';
            } elseif ($filteredNetAmount > 0) {
                $filteredType = 'credit'; // yeşil
                $filteredColor = 'green';
                $filteredIcon = 'credit';
            } else {
                $filteredType = 'debt'; // kırmızı
                $filteredColor = 'red';
                $filteredIcon = 'debt';
            }
        @endphp
        <x-info-cards title="Filtered Total" :type="$filteredType" :amount="$filteredNetAmount" period="Filtered Data" :icon="$filteredIcon" :color="$filteredColor"
            subtitle="Current View Summary" />
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const openBtn = document.getElementById('openTransactionModal');
            const modal = document.getElementById('transactionModal');

            // transaction form modal logic
            if (openBtn && modal) {
                openBtn.addEventListener('click', function () {
                    modal.classList.remove('hidden');
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