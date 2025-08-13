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

    <div class="mb-4">
        <div class="flex justify-between items-center mb-2">
            <div class="bg-blue-50 border border-blue-200 text-blue-700 rounded px-3 py-1 shadow text-sm flex items-center">
                <i class="fas fa-wallet mr-1"></i>
                <span class="font-semibold">Balance:</span>
                <span class="ml-1">{{ number_format($balance, 2) }}</span>
            </div>

            <button type="button" id="openLedgerModal"
                class="text-white bg-blue-700 hover:bg-blue-800 font-medium rounded-lg text-sm px-5 py-2.5 me-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none ">New
                Transaction</button>
        </div>

    <!-- 3-month summary cards -->
    <div class="grid grid-cols-3 gap-2 mb-4">
        @for ($i = 0; $i < 3; $i++)
            @php
                $monthKey = 'm' . $i;
                $monthName = $monthNames[$monthKey] ?? '';
                $debt = $stats['debt'][$monthKey] ?? 0;
                $credit = $stats['credit'][$monthKey] ?? 0;
                $netAmount = $credit - $debt;
                $icon = $netAmount >= 0 ? 'credit' : 'debt';
                $color = $netAmount >= 0 ? 'green' : 'red';
            @endphp
            <x-info-cards
                title="{{ $monthName }} Overview"
                type="ledger"
                :amount="$netAmount"
                period="{{ $monthName }}"
                :icon="$icon"
                :color="$color"
                subtitle="Net (Credit - Debt)"
            />
        @endfor
    </div>

    <x-ledger-modal :users="$users" />

    <!-- transaction edit modal -->
    <x-transaction-edit-modal :paymentTerms="$paymentTerms" />

        <div class="p-4 pl-0 pr-0 mb-4">
        <x-transactions-table :transactions="$transactions" :show-receiver="true" :row-count="20" :showPaymentMethod="true" :is-ledger="true" />
        @if($transactions->isNotEmpty())
            <div class="mt-2">
                {{ $transactions->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- summary cards -->
    <div class="grid grid-cols-2 gap-4">
        <!-- Overall total summary card -->
        @php
            $totalDebt = is_array($stats['debt']) ? array_sum($stats['debt']) : 0;
            $totalCredit = is_array($stats['credit']) ? array_sum($stats['credit']) : 0;
            $netAmount = $totalCredit - $totalDebt;
            $icon = $netAmount >= 0 ? 'credit' : 'debt';
            $color = $netAmount >= 0 ? 'green' : 'red';
        @endphp
        <x-info-cards
            title="Overall Total"
            type="ledger"
            :amount="$netAmount"
            period="All Time"
            :icon="$icon"
            :color="$color"
            subtitle="Net (Credit - Debt)"
        />
        
        <!-- Filtered transactions summary card -->
        @php
            $filteredCredit = 0;
            $filteredDebt = 0;
            $user = Auth::user();
            
            if($transactions->isNotEmpty()) {
                foreach($transactions as $transaction) {
                    // Exactly match the controller's calculation logic
                    // Credit: (owner = user AND amount > 0) OR (user_id = user AND amount < 0)
                    if (($transaction->owner == $user->id && $transaction->amount > 0) || 
                        ($transaction->user_id == $user->id && $transaction->amount < 0)) {
                        $filteredCredit += abs($transaction->amount);
                    }
                    // Debt: (owner = user AND amount < 0) OR (user_id = user AND amount > 0)
                    else if (($transaction->owner == $user->id && $transaction->amount < 0) || 
                             ($transaction->user_id == $user->id && $transaction->amount > 0)) {
                        $filteredDebt += abs($transaction->amount);
                    }
                }
            }
            
            $filteredNetAmount = $filteredCredit - $filteredDebt;
            $filteredIcon = $filteredNetAmount >= 0 ? 'credit' : 'debt';
            $filteredColor = $filteredNetAmount >= 0 ? 'green' : 'red';
        @endphp
        <x-info-cards
            title="Filtered Total"
            type="ledger"
            :amount="$filteredNetAmount"
            period="Filtered Data"
            :icon="$filteredIcon"
            :color="$filteredColor"
            subtitle="Current View Summary"
        />
    </div>
    

    <script>
        document.addEventListener('DOMContentLoaded', function () {
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