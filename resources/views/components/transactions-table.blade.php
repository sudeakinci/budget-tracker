@props([
    'transactions',
    'showReceiver' => true,
    'showPaymentMethod' => false,
])

<div class="overflow-x-visible">
    <!-- filter Badges -->
    <div id="active-filters" class="mb-3 flex flex-wrap items-center gap-2 hidden">
        <span class="text-sm font-medium text-gray-600">Filtered by:</span>
        <div id="date-filter-badge" class="filter-badge hidden">
            <span class="filter-text"></span>
            <button type="button" onclick="clearFilter('date')" class="ml-1 text-gray-500 hover:text-gray-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="receiver-filter-badge" class="filter-badge hidden">
            <span class="filter-text"></span>
            <button type="button" onclick="clearFilter('receiver')" class="ml-1 text-gray-500 hover:text-gray-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="amount-filter-badge" class="filter-badge hidden">
            <span class="filter-text"></span>
            <button type="button" onclick="clearFilter('amount')" class="ml-1 text-gray-500 hover:text-gray-700">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <button type="button" onclick="clearAllFilters()" class="text-xs px-2 py-1 rounded text-blue-600 hover:bg-blue-50 font-medium ml-auto">
            Clear all filters
        </button>
    </div>

    <table class="min-w-full bg-white rounded-lg shadow text-sm">
        <thead>
            <tr class="bg-blue-50 border-b border-blue-200">
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48 cursor-pointer relative group" onclick="showFilterInput('date')">
                    <span class="flex items-center">
                        Date
                        <span id="date-filter-icon" class="ml-1 transition-transform">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block align-middle">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </span>
                    <div id="filter-date" class="absolute left-0 top-full mt-2 bg-white border border-blue-200 rounded-xl shadow-2xl p-4 z-50 hidden min-w-[180px] animate-fade-in">
                        <input type="date" class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2" oninput="filterTable()" onblur="hideFilterInput('date')">
                        <div class="flex justify-end">
                            <button type="button" class="text-xs px-2 py-1 rounded bg-blue-100 hover:bg-blue-200 text-blue-700" onclick="clearFilter('date')">Clear</button>
                        </div>
                    </div>
                </th>
                @if($showReceiver)
                    <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48 cursor-pointer relative group" onclick="showFilterInput('receiver')">
                        <span class="flex items-center">
                            Receiver
                            <span id="receiver-filter-icon" class="ml-1 transition-transform">
                                <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block align-middle">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </span>
                        </span>
                        <div id="filter-receiver" class="absolute left-0 top-full mt-2 bg-white border border-blue-200 rounded-xl shadow-2xl p-4 z-50 hidden min-w-[180px] animate-fade-in">
                            <input type="text" placeholder="Filter by receiver" class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2" oninput="filterTable()" onblur="hideFilterInput('receiver')">
                            <div class="flex justify-end">
                                <button type="button" class="text-xs px-2 py-1 rounded bg-blue-100 hover:bg-blue-200 text-blue-700" onclick="clearFilter('receiver')">Clear</button>
                            </div>
                        </div>
                    </th>
                @else
                    <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48 cursor-pointer relative group" onclick="showFilterInput('date')">
                    <span class="flex items-center">
                        Date
                        <span id="date-arrow" class="ml-1 transition-transform" onclick="toggleSort('date', event)">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block align-middle">
                                <path id="date-arrow-path" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </span>
                    <div id="filter-date" class="absolute left-0 top-full mt-2 bg-white border border-blue-200 rounded-xl shadow-2xl p-4 z-50 hidden min-w-[180px] animate-fade-in">
                        <input type="date" class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2" oninput="filterTable()" onblur="hideFilterInput('date')">
                        <div class="flex justify-end">
                            <button type="button" class="text-xs px-2 py-1 rounded bg-blue-100 hover:bg-blue-200 text-blue-700" onclick="clearFilter('date')">Clear</button>
                        </div>
                    </div>
                </th>
                @endif
                @if($showPaymentMethod)
                    <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Payment Method</th>
                @endif
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-60">Description</th>
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-32 cursor-pointer relative group" onclick="showFilterInput('amount')">
                    <span class="flex items-center">
                        Amount
                        <span id="amount-filter-icon" class="ml-1 transition-transform">
                            <svg width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" class="inline-block align-middle">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </span>
                    </span>
                    <div id="filter-amount" class="absolute left-0 top-full mt-2 bg-white border border-blue-200 rounded-xl shadow-2xl p-4 z-50 hidden min-w-[220px] animate-fade-in">
                        <div class="mb-3">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Transaction Type</label>
                            <div class="grid grid-cols-3 gap-1">
                                <button type="button" class="filter-btn active" data-value="all" onclick="setAmountFilter(this, 'all')">
                                    <svg class="w-4 h-4 mx-auto mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                    <span>All</span>
                                </button>
                                <button type="button" class="filter-btn" data-value="income" onclick="setAmountFilter(this, 'income')">
                                    <svg class="w-4 h-4 mx-auto mb-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
                                    </svg>
                                    <span>Income</span>
                                </button>
                                <button type="button" class="filter-btn" data-value="expense" onclick="setAmountFilter(this, 'expense')">
                                    <svg class="w-4 h-4 mx-auto mb-1 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                    </svg>
                                    <span>Expense</span>
                                </button>
                            </div>
                        </div>
                        <div class="hidden">
                            <select id="amount-filter-value" class="hidden" onchange="filterTable()">
                                <option value="all">All transactions</option>
                                <option value="income">Income</option>
                                <option value="expense">Expenses</option>
                            </select>
                        </div>
                        <div class="flex justify-end">
                            <button type="button" class="text-xs px-3 py-1.5 rounded bg-blue-100 hover:bg-blue-200 text-blue-700 font-medium transition-colors" onclick="clearFilter('amount')">Reset Filter</button>
                        </div>
                    </div>
                </th>
                <th class="w-10"></th> <!-- empty title for icon -->
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }} transaction-row"
                    data-payment-method="{{ $transaction->payment_term_name ?: '-' }}"
                    data-payment-term-id="{{ $transaction->payment_term_id }}"
                    data-receiver="{{ optional($transaction->user)->name ?: '-' }}"
                    data-date="{{ $transaction->created_at->format('Y-m-d') }}"
                    data-amount="{{ $transaction->display_amount }}">
                    <td class="py-2 px-4 border-b border-gray-200 w-40">
                        {{ $transaction->created_at->format('d.m.Y H:i') }}
                    </td>
                    @if($showReceiver)
                        <td class="py-2 px-4 border-b border-gray-200 w-48">
                            {{ optional($transaction->user)->name ?: '-' }}
                        </td>
                    @endif
                    @if($showPaymentMethod)
                        <td class="py-2 px-4 border-b border-gray-200 w-48">
                            {{ $transaction->payment_term_name ?: '-' }}
                        </td>
                    @endif
                    <td class="py-2 px-4 border-b border-gray-200 w-64">{{ $transaction->description ?: '-' }}</td>
                    <td
                        class="py-2 px-4 border-b border-gray-200 font-semibold w-32 {{ $transaction->display_amount < 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ number_format(abs($transaction->display_amount), 2) }}
                        ₺
                    </td>

                    <td class="px-2 border-b border-gray-200 w-10 text-center align-middle">
                        <div class="relative dropdown">
                            <button onclick="toggleDropdown({{ $transaction->id }})" class="group focus:outline-none"
                                title="Options">
                                <svg class="w-4 h-4 text-gray-400 group-hover:text-blue-500" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z">
                                    </path>
                                </svg>
                            </button>
                            <div id="dropdown-{{ $transaction->id }}"
                                class="dropdown-menu hidden absolute w-32 bg-white bottom-0 ml-5 rounded-md shadow-lg z-40 overflow-visible max-h-48 overflow-y-auto">
                                <div class="py-1">
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); showTransactionDetails({{ $transaction->id }}, 
                                '{{ $transaction->created_at->format('d.m.Y H:i') }}', 
                                '{{ $transaction->description ?: 'No description' }}', 
                                '{{ number_format($transaction->display_amount, 2) }} ₺', 
                                '{{ optional($transaction->user)->name ?: '-' }}',
                                '{{ $transaction->payment_term_name }}')">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                                </path>
                                            </svg>
                                            Info
                                        </span>
                                    </a>
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); editTransaction({{ $transaction->id }}, 
                                '{{ $transaction->description }}', 
                                '{{ $transaction->payment_term_name }}',
                                {{ $transaction->payment_term_id ?? 'null' }})">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                            Edit
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
 </div>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fade-in 0.2s ease; }

.filter-btn {
    @apply px-2 py-2 text-xs rounded-md transition-all duration-200 text-gray-600 border border-transparent flex flex-col items-center justify-center;
}

.filter-btn:hover {
    @apply bg-gray-50 text-gray-800;
}

.filter-btn.active {
    @apply bg-blue-50 text-blue-600 border-blue-200 font-medium;
}

#filter-amount {
    width: 260px;
}

.dropdown-menu {
    transform-origin: top right;
    animation: fade-in 0.2s ease;
}

/* Show an indicator when filter is active */
#amount-filter-icon.text-blue-500 svg {
    @apply text-blue-600 stroke-2;
}

/* Filter badges styles */
.filter-badge {
    @apply flex items-center bg-blue-50 text-blue-700 text-xs font-medium px-2.5 py-1.5 rounded-full;
}

#active-filters {
    animation: fade-in 0.3s ease;
}
</style>
<script>
let sortState = {
    date: 'desc', // default sort: newest first
    amount: 'desc' // default sort: high to low
};

// Track current open filter
let currentOpenFilter = null;

function showFilterInput(type) {
    // If clicking the same filter, toggle it
    if (currentOpenFilter === type) {
        document.getElementById('filter-' + type).classList.add('hidden');
        currentOpenFilter = null;
        return;
    }
    
    // Hide all other filters
    document.querySelectorAll('[id^="filter-"]').forEach(el => el.classList.add('hidden'));
    
    // Show the selected filter
    document.getElementById('filter-' + type).classList.remove('hidden');
    currentOpenFilter = type;
    
    // Focus the input if exists
    const input = document.querySelector('#filter-' + type + ' input');
    if (input) input.focus();
}

function hideFilterInput(type) {
    setTimeout(() => {
        document.getElementById('filter-' + type).classList.add('hidden');
        if (currentOpenFilter === type) {
            currentOpenFilter = null;
        }
    }, 200);
}

// Close filter when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('[id^="filter-"]') && !event.target.closest('[onclick*="showFilterInput"]')) {
        document.querySelectorAll('[id^="filter-"]').forEach(el => el.classList.add('hidden'));
        currentOpenFilter = null;
    }
});

function clearFilter(type) {
    const popup = document.getElementById('filter-' + type);
    if (popup) {
        popup.querySelectorAll('input').forEach(el => {
            el.value = '';
        });
        popup.querySelectorAll('select').forEach(el => {
            el.selectedIndex = 0;
        });
        
        // If it's the amount filter, also update the button states
        if (type === 'amount') {
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
                if (btn.dataset.value === 'all') {
                    btn.classList.add('active');
                }
            });
            
            // Reset the filter icon state
            const amountFilterIcon = document.querySelector('#amount-filter-icon');
            amountFilterIcon.classList.remove('text-blue-500', 'font-bold');
        }
        
        // Hide filter badge for this type
        const badge = document.getElementById(type + '-filter-badge');
        if (badge) {
            badge.classList.add('hidden');
        }
    }
    
    // Close popup
    document.getElementById('filter-' + type).classList.add('hidden');
    if (currentOpenFilter === type) {
        currentOpenFilter = null;
    }
    
    filterTable();
    updateActiveFiltersVisibility();
}

function setAmountFilter(element, value) {
    // Update visual state for all buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
    
    // Update hidden select value
    document.querySelector('#amount-filter-value').value = value;
    
    // Apply filter
    filterTable();
    
    // Show active filter indicator
    const amountFilterIcon = document.querySelector('#amount-filter-icon');
    if (value !== 'all') {
        amountFilterIcon.classList.add('text-blue-500', 'font-bold');
        
        // Update filter badge
        const badge = document.getElementById('amount-filter-badge');
        badge.querySelector('.filter-text').textContent = value === 'income' ? 'Income' : 'Expenses';
        badge.classList.remove('hidden');
    } else {
        amountFilterIcon.classList.remove('text-blue-500', 'font-bold');
        document.getElementById('amount-filter-badge').classList.add('hidden');
    }
    
    // Hide the filter popup after selection
    setTimeout(() => {
        document.getElementById('filter-amount').classList.add('hidden');
        currentOpenFilter = null;
    }, 200);
    
    updateActiveFiltersVisibility();
}

function updateActiveFiltersVisibility() {
    // Check if any filter is active
    const hasDateFilter = document.querySelector('#filter-date input')?.value;
    const hasReceiverFilter = document.querySelector('#filter-receiver input')?.value;
    const hasAmountFilter = document.querySelector('#amount-filter-value')?.value !== 'all';
    
    const activeFiltersContainer = document.getElementById('active-filters');
    
    if (hasDateFilter || hasReceiverFilter || hasAmountFilter) {
        activeFiltersContainer.classList.remove('hidden');
    } else {
        activeFiltersContainer.classList.add('hidden');
    }
}

function clearAllFilters() {
    // Clear all filters
    clearFilter('date');
    clearFilter('receiver');
    clearFilter('amount');
    
    // Hide the active filters container
    document.getElementById('active-filters').classList.add('hidden');
}

function filterTable() {
    // receiver
    const receiverInput = document.querySelector('#filter-receiver input');
    const receiverValue = receiverInput ? receiverInput.value.toLowerCase() : '';
    // date
    const dateInput = document.querySelector('#filter-date input');
    const dateValue = dateInput ? dateInput.value : '';
    // amount type filter (income/expense)
    const amountTypeSelect = document.querySelector('#amount-filter-value');
    const amountType = amountTypeSelect ? amountTypeSelect.value : 'all';
    
    // amount value filter (if present)
    const amountInput = document.querySelector('#filter-amount input[type="number"]');
    const amountOperator = document.querySelector('#filter-amount select[data-operator]') ? 
                          document.querySelector('#filter-amount select[data-operator]').value : 'gt';
    const amountValue = amountInput && amountInput.value !== '' ? parseFloat(amountInput.value) : null;
    
    // Update filter badges
    if (dateValue) {
        const formattedDate = new Date(dateValue).toLocaleDateString();
        document.querySelector('#date-filter-badge .filter-text').textContent = `Date: ${formattedDate}`;
        document.getElementById('date-filter-badge').classList.remove('hidden');
    } else {
        document.getElementById('date-filter-badge').classList.add('hidden');
    }
    
    if (receiverValue) {
        document.querySelector('#receiver-filter-badge .filter-text').textContent = `Receiver: ${receiverValue}`;
        document.getElementById('receiver-filter-badge').classList.remove('hidden');
    } else {
        document.getElementById('receiver-filter-badge').classList.add('hidden');
    }
    
    updateActiveFiltersVisibility();

    // rows
    const rows = Array.from(document.querySelectorAll('.transaction-row'));
    rows.forEach(row => {
        let visible = true;
        // receiver filter
        if (receiverValue && !row.getAttribute('data-receiver').toLowerCase().includes(receiverValue)) visible = false;
        // date filter
        if (dateValue && row.getAttribute('data-date') !== dateValue) visible = false;
        
        // amount type filter (income/expense)
        if (amountType !== 'all') {
            const rowAmount = parseFloat(row.getAttribute('data-amount'));
            if (amountType === 'income' && rowAmount <= 0) visible = false;
            if (amountType === 'expense' && rowAmount >= 0) visible = false;
        }
        
        // amount value filter (if present)
        if (amountValue !== null) {
            const rowAmount = parseFloat(row.getAttribute('data-amount'));
            if (amountOperator === 'gt' && !(rowAmount > amountValue)) visible = false;
            if (amountOperator === 'lt' && !(rowAmount < amountValue)) visible = false;
            if (amountOperator === 'eq' && Math.abs(rowAmount - amountValue) > 0.01) visible = false;
        }
        
        if (visible) {
            row.classList.remove('hidden');
        } else {
            row.classList.add('hidden');
        }
    });
    updateEmptyRows();
}

function toggleSort(type, event) {
    event.stopPropagation();
    sortState[type] = sortState[type] === 'asc' ? 'desc' : 'asc';
    document.getElementById(type + '-arrow-path').setAttribute('d', sortState[type] === 'asc' ? 'M19 15l-7-7-7 7' : 'M19 9l-7 7-7-7');
    sortTable(type, sortState[type]);
}

function sortTable(type, direction) {
    const tbody = document.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('.transaction-row'));
    let compareFn;
    if (type === 'date') {
        compareFn = (a, b) => {
            const dateA = a.getAttribute('data-date');
            const dateB = b.getAttribute('data-date');
            return direction === 'asc' ? dateA.localeCompare(dateB) : dateB.localeCompare(dateA);
        };
    } else if (type === 'amount') {
        compareFn = (a, b) => {
            const amountA = parseFloat(a.getAttribute('data-amount'));
            const amountB = parseFloat(b.getAttribute('data-amount'));
            return direction === 'asc' ? amountA - amountB : amountB - amountA;
        };
    } else {
        return;
    }
    rows.sort(compareFn);
    rows.forEach(row => tbody.appendChild(row));
    updateEmptyRows();
}

function updateEmptyRows() {
    const visibleRows = document.querySelectorAll('tbody tr:not(.hidden):not(.empty-row)').length;
    const emptyRow = document.querySelector('.empty-row');
    
    if (visibleRows === 0) {
        if (!emptyRow) {
            const tbody = document.querySelector('tbody');
            const colSpan = document.querySelector('thead tr').children.length;
            const tr = document.createElement('tr');
            tr.className = 'empty-row';
            const td = document.createElement('td');
            td.colSpan = colSpan;
            td.className = 'py-4 text-center text-gray-500';
            td.innerText = 'No transactions found matching your filters';
            tr.appendChild(td);
            tbody.appendChild(tr);
        } else {
            emptyRow.classList.remove('hidden');
        }
    } else if (emptyRow) {
        emptyRow.classList.add('hidden');
    }
}
</script>