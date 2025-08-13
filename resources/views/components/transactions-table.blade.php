@props([
    'transactions',
    'showReceiver' => true,
    'showPaymentMethod' => false,
])

<div class="overflow-x-visible">
    <!-- filter badges -->
    <div id="active-filters" class="my-3 px-4 flex flex-wrap items-center gap-1 hidden">
        <span class="text-sm font-semibold text-gray-700">Filtered by:</span>
        <div id="date-filter-badge" class="filter-badge hidden">
            <span class="filter-text"></span>
            <button type="button" onclick="clearFilter('date')" class="ml-1 text-gray-500 hover:text-gray-700">
                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
        <div id="receiver-filter-container" class="hidden flex items-center">
            <div id="receiver-filter-badges" class="flex flex-wrap gap-1 items-center ml-1"></div>
        </div>
        <div id="payment-term-filter-container" class="hidden flex items-center">
            <div id="payment-term-filter-badges" class="flex flex-wrap gap-1 items-center ml-1"></div>
        </div>
        <div id="amount-filter-container" class="hidden flex items-center">
            <div id="amount-filter-badges" class="flex flex-wrap gap-1 items-center ml-1"></div>
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
                        <input type="date" class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2" oninput="filterTable()">
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
                        <div id="filter-receiver" class="absolute left-0 top-full mt-2 bg-white border border-blue-200 rounded-xl shadow-2xl p-4 z-50 hidden min-w-[300px] animate-fade-in">
                            <div class="mb-3">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Select Receivers</label>
                                <div class="relative">
                                    <input type="text" id="receiver-search" placeholder="Search receivers..." 
                                        class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2"
                                        oninput="filterReceiverOptions()">
                                    <div id="receiver-options" class="max-h-48 overflow-y-auto border border-blue-100 rounded-md bg-white mb-3"></div>
                                </div>
                            </div>
                            <div id="selected-receivers" class="flex flex-wrap gap-1 mb-3 empty:hidden"></div>
                            <div class="flex justify-between">
                                <button type="button" class="text-xs px-2 py-1 rounded bg-gray-100 hover:bg-gray-200 text-gray-700" onclick="selectAllReceivers()">Select All</button>
                                <button type="button" class="text-xs px-2 py-1 rounded bg-blue-100 hover:bg-blue-200 text-blue-700" onclick="applyReceiverFilter()">Apply</button>
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
                        <input type="date" class="border border-blue-300 rounded-lg px-3 py-2 text-sm w-full focus:ring-2 focus:ring-blue-400 focus:outline-none mb-2" oninput="filterTable()">
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
            @if($transactions->isEmpty())
                <tr class="empty-row">
                    <td colspan="{{ $showReceiver ? '5' : '4' }}" class="py-4 text-center text-gray-500">
                        Transactions not found
                    </td>
                </tr>
            @endif
            @foreach($transactions as $transaction)
                <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }} transaction-row"
                    data-payment-method="{{ $transaction->payment_term_name ?: '-' }}"
                    data-payment-term-id="{{ $transaction->payment_term_id }}"
                    data-receiver="{{ optional($transaction->user)->name ?: '-' }}"
                    data-date="{{ $transaction->created_at->format('Y-m-d') }}"
                    data-amount="{{ $transaction->display_amount }}">
                    <td class="py-2 px-4 border-b border-gray-200 w-40">
                        <div class="flex items-center">
                            <input type="checkbox" class="transaction-include mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   {{ ($transaction->is_included ?? true) ? 'checked' : '' }}
                                   onchange="updateTransactionIncluded({{ $transaction->id }}, this.checked)">
                            {{ $transaction->created_at->format('d.m.Y H:i') }}
                        </div>
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
                                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100" onclick="event.preventDefault(); editTransaction({{ $transaction->id }}, 
                                '{{ $transaction->description }}', 
                                '{{ $transaction->payment_term_name }}',
                                {{ $transaction->payment_term_id ?? 'null' }},
                                {{ $transaction->is_included ?? 1 }})">
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
                                    <a href="#" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100" onclick="event.preventDefault(); deleteTransaction({{ $transaction->id }})">
                                        <span class="flex items-center">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Delete
                                        </span>
                                    </a>
                                </div>
                                <form id="deleteTransactionForm-{{ $transaction->id }}" action="{{ route('transactions.destroy', $transaction->id) }}" method="POST" class="hidden">
                                    @csrf
                                    @method('DELETE')
                                </form>
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
    @apply flex items-center bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded-md border border-blue-100;
}

#active-filters {
    animation: fade-in 0.3s ease;
}

/* Receiver filter styles */
#receiver-options {
    border: 1px solid #e2e8f0;
    border-radius: 0.375rem;
    max-height: 200px;
    overflow-y: auto;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, 0.05);
}

#receiver-options div {
    padding: 0.5rem 0.75rem;
    cursor: pointer;
    transition: background-color 0.15s ease;
    user-select: none;
}

#receiver-options div:hover {
    background-color: #f1f5f9;
}

#receiver-options div:not(:last-child) {
    border-bottom: 1px solid #e2e8f0;
}

#selected-receivers {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
}

#receiver-filter-badges span {
    animation: fade-in 0.2s ease;
}

/* Style the checkboxes */
#receiver-options input[type="checkbox"] {
    accent-color: #2563eb;
    width: 1rem;
    height: 1rem;
    margin-right: 0.5rem;
}

/* Add some spacing between filter badges */
#receiver-filter-container {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    animation: fade-in 0.3s ease;
}

/* Loading indicator styles */
@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}

#loading-overlay {
    animation: fade-in 0.2s ease;
    z-index: 9999;
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
    // Prevent event propagation to avoid immediate closure by document click listener
    event.stopPropagation();
    
    // If clicking the same filter that's already open, do nothing (let the outside click handler close it)
    if (currentOpenFilter === type) {
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
    // Remove this function as we'll handle filter hiding through the document click handler
}

// Close filter when clicking outside
document.addEventListener('click', function(event) {
    // Check if click was outside filter elements and filter headers
    if (!event.target.closest('[id^="filter-"]') && 
        !event.target.closest('[onclick*="showFilterInput"]') &&
        !event.target.closest('[onclick*="applyReceiverFilter"]') && 
        !event.target.closest('[onclick*="setAmountFilter"]')) {
        // Close all filter popups
        document.querySelectorAll('[id^="filter-"]').forEach(el => el.classList.add('hidden'));
        currentOpenFilter = null;
    }
});

function clearFilter(type) {
    // Special handling for server-side amount filter
    if (type === 'amount') {
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.has('amount_type')) {
            // Show loading indicator
            showLoadingIndicator();
            
            // Remove the amount_type parameter and reload
            urlParams.delete('amount_type');
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
            return;
        }
    }
    
    // Continue with local filter clearing
    const popup = document.getElementById('filter-' + type);
    if (popup) {
        popup.querySelectorAll('input').forEach(el => {
            el.value = '';
        });
        popup.querySelectorAll('select').forEach(el => {
            el.selectedIndex = 0;
        });
        
        // Special handling for different filter types
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
            
            // Hide filter container for amount
            const container = document.getElementById(type + '-filter-container');
            if (container) {
                container.classList.add('hidden');
            }
            
            // Clear badges
            const badgesContainer = document.getElementById(type + '-filter-badges');
            if (badgesContainer) {
                badgesContainer.innerHTML = '';
            }
        } else if (type === 'receiver') {
            // Show loading indicator
            showLoadingIndicator();
            
            // Remove the receiver parameter and reload
            const urlParams = new URLSearchParams(window.location.search);
            urlParams.delete('receiver');
            window.location.href = `${window.location.pathname}?${urlParams.toString()}`;
            document.getElementById('receiver-filter-container').classList.add('hidden');
        } else if (type === 'date') {
            // Hide filter badge for date
            const badge = document.getElementById(type + '-filter-badge');
            if (badge) {
                badge.classList.add('hidden');
            }
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
    // Show loading indicator
    showLoadingIndicator();
    
    // Update visual state for all buttons
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    element.classList.add('active');
    
    // Update hidden select value
    document.querySelector('#amount-filter-value').value = value;
    
    // Build the URL with filter parameters
    const currentUrl = new URL(window.location.href);
    
    // Keep existing parameters but update amount_type
    const params = new URLSearchParams(currentUrl.search);
    
    if (value === 'all') {
        params.delete('amount_type');
    } else {
        params.set('amount_type', value);
    }
    
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
                <svg class="animate-spin h-5 w-5 text-blue-600 mr-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span class="text-gray-700">Filtering transactions...</span>
            </div>
        `;
        document.body.appendChild(overlay);
    } else {
        document.getElementById('loading-overlay').classList.remove('hidden');
    }
}

function updateActiveFiltersVisibility() {
    // Check if any filter is active
    const hasDateFilter = document.querySelector('#filter-date input')?.value;
    const hasReceiverFilter = selectedReceivers.length > 0;
    
    // Check if the amount filter has an active value
    const amountValue = document.querySelector('#amount-filter-value')?.value;
    const hasAmountFilter = amountValue && amountValue !== 'all';
    
    // Also check URL parameters for server-side filters
    const urlParams = new URLSearchParams(window.location.search);
    const hasAmountTypeParam = urlParams.has('amount_type') && urlParams.get('amount_type') !== 'all';
    
    const activeFiltersContainer = document.getElementById('active-filters');
    
    if (hasDateFilter || hasReceiverFilter || hasAmountFilter || hasAmountTypeParam) {
        activeFiltersContainer.classList.remove('hidden');
        
        // Update filter labels to be consistent
        if (hasDateFilter) {
            document.getElementById('date-filter-badge').classList.remove('hidden');
        } else {
            document.getElementById('date-filter-badge').classList.add('hidden');
        }
        
        // Check if amount filter is active and update its visibility
        if (hasAmountFilter || hasAmountTypeParam) {
            // Get the amount type from URL or select
            const amountType = urlParams.get('amount_type') || 
                               (document.querySelector('#amount-filter-value')?.value !== 'all' ? 
                                document.querySelector('#amount-filter-value').value : null);
            
            if (amountType) {
                // Show and update the amount filter badge
                updateAmountFilterBadge(amountType);
            }
        } else {
            // Hide the amount filter container if no filter is active
            const amountFilterContainer = document.getElementById('amount-filter-container');
            if (amountFilterContainer) {
                amountFilterContainer.classList.add('hidden');
            }
        }
    } else {
        activeFiltersContainer.classList.add('hidden');
    }
}

function clearAllFilters() {
    // Check if we have URL parameters for filters
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.has('amount_type') || urlParams.has('payment_term_ids') || urlParams.has('payment_term_id')) {
        // Show loading indicator
        showLoadingIndicator();
        
        // Create new URL without filter parameters but keep pagination if present
        const newParams = new URLSearchParams();
        if (urlParams.has('page')) {
            newParams.set('page', urlParams.get('page'));
        }
        
        // Redirect to unfiltered URL
        window.location.href = `${window.location.pathname}?${newParams.toString()}`;
        return;
    }

    // Clear all local filters
    clearFilter('date');
    clearFilter('receiver');
    clearFilter('amount');
    
    // Additional cleanup for receiver filter
    selectedReceivers = [];
    updateSelectedReceiversDisplay();
    document.getElementById('receiver-filter-badges').innerHTML = '';
    document.getElementById('receiver-filter-container').classList.add('hidden');
    
    // Clear amount filter
    const amountFilterContainer = document.getElementById('amount-filter-container');
    if (amountFilterContainer) {
        amountFilterContainer.classList.add('hidden');
        const badgesContainer = document.getElementById('amount-filter-badges');
        if (badgesContainer) {
            badgesContainer.innerHTML = '';
        }
    }
    
    // Clear payment term filters if they exist
    if (document.getElementById('payment-term-filter-badges')) {
        document.getElementById('payment-term-filter-badges').innerHTML = '';
        document.getElementById('payment-term-filter-container').classList.add('hidden');
        
        // If clearAllPaymentTermFilters function exists, call it (without reloading page)
        if (typeof clearPaymentTermsWithoutReload === 'function') {
            clearPaymentTermsWithoutReload();
        }
    }
    
    // Hide the active filters container
    document.getElementById('active-filters').classList.add('hidden');
    
    // Make sure all rows are visible
    const rows = document.querySelectorAll('.transaction-row');
    rows.forEach(row => row.classList.remove('hidden'));
    
    // Update empty rows indicator
    updateEmptyRows();
}

// Store selected receivers
let selectedReceivers = [];
let allReceivers = [];

// Initialize receivers on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeReceivers();
    
    // Add click event handlers to filter elements to stop propagation
    document.querySelectorAll('[id^="filter-"]').forEach(filter => {
        filter.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    });
    
    // Initialize filters based on URL parameters
    initializeFiltersFromUrl();
});

// Initialize filters based on URL parameters
function initializeFiltersFromUrl() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Handle amount type filter
    if (urlParams.has('amount_type')) {
        const amountType = urlParams.get('amount_type');
        
        // Update select value
        const selectElement = document.querySelector('#amount-filter-value');
        if (selectElement) {
            selectElement.value = amountType;
        }
        
        // Update filter button active state
        document.querySelectorAll('.filter-btn').forEach(btn => {
            btn.classList.remove('active');
            if (btn.dataset.value === amountType) {
                btn.classList.add('active');
            }
        });
        
        // Update filter icon
        const amountFilterIcon = document.querySelector('#amount-filter-icon');
        if (amountFilterIcon && amountType && amountType !== 'all') {
            amountFilterIcon.classList.add('text-blue-500', 'font-bold');
            
            // Update amount filter badges
            updateAmountFilterBadge(amountType);
            
            // Show the active filters container
            document.getElementById('active-filters').classList.remove('hidden');
        } else {
            // Hide amount filter container
            const container = document.getElementById('amount-filter-container');
            if (container) {
                container.classList.add('hidden');
            }
        }
    }
    
    // Handle receiver filter
    if (urlParams.has('receiver')) {
        const receiverParam = urlParams.get('receiver');
        selectedReceivers = receiverParam.split(',');
        
        // Update receiver filter icon
        const receiverFilterIcon = document.querySelector('#receiver-filter-icon');
        if (receiverFilterIcon && selectedReceivers.length > 0) {
            receiverFilterIcon.classList.add('text-blue-500', 'font-bold');
            
            // Show active filters container and update badges
            document.getElementById('active-filters').classList.remove('hidden');
            updateReceiverFilterBadges();
        }
    }
}

function initializeReceivers() {
    // Get all unique receivers from the table
    allReceivers = Array.from(document.querySelectorAll('.transaction-row'))
        .map(row => row.getAttribute('data-receiver'))
        .filter(receiver => receiver && receiver !== '-')
        .filter((value, index, self) => self.indexOf(value) === index)
        .sort();
    
    updateReceiverOptions();
}

function updateReceiverOptions() {
    const optionsContainer = document.getElementById('receiver-options');
    optionsContainer.innerHTML = '';
    
    const searchTerm = document.getElementById('receiver-search').value.toLowerCase();
    
    const filteredReceivers = allReceivers.filter(
        receiver => receiver.toLowerCase().includes(searchTerm)
    );
    
    if (filteredReceivers.length === 0) {
        const noResults = document.createElement('div');
        noResults.className = 'py-2 px-3 text-sm text-gray-500 text-center';
        noResults.textContent = 'No receivers found';
        optionsContainer.appendChild(noResults);
        return;
    }
    
    filteredReceivers.forEach(receiver => {
        const option = document.createElement('div');
        option.className = 'py-2 px-3 text-sm hover:bg-blue-50 cursor-pointer flex items-center';
        
        const checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.className = 'mr-2 h-4 w-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500';
        checkbox.checked = selectedReceivers.includes(receiver);
        checkbox.addEventListener('change', function() {
            toggleReceiver(receiver, this.checked);
        });
        
        const label = document.createElement('span');
        label.textContent = receiver;
        label.className = 'flex-grow';
        
        option.appendChild(checkbox);
        option.appendChild(label);
        
        // Allow clicking the entire row to toggle
        option.addEventListener('click', function(e) {
            if (e.target !== checkbox) {
                checkbox.checked = !checkbox.checked;
                toggleReceiver(receiver, checkbox.checked);
            }
        });
        
        optionsContainer.appendChild(option);
    });
}

function filterReceiverOptions() {
    updateReceiverOptions();
}

function toggleReceiver(receiver, isSelected) {
    if (isSelected && !selectedReceivers.includes(receiver)) {
        selectedReceivers.push(receiver);
    } else if (!isSelected && selectedReceivers.includes(receiver)) {
        selectedReceivers = selectedReceivers.filter(r => r !== receiver);
    }
    updateSelectedReceiversDisplay();
}

function updateSelectedReceiversDisplay() {
    const container = document.getElementById('selected-receivers');
    container.innerHTML = '';
    
    selectedReceivers.forEach(receiver => {
        const badge = document.createElement('span');
        badge.className = 'inline-flex items-center bg-blue-100 text-blue-800 text-xs font-medium px-2 py-0.5 rounded';
        
        const text = document.createElement('span');
        text.textContent = receiver;
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'ml-1 text-blue-600 hover:text-blue-800';
        removeBtn.innerHTML = '&times;';
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Show loading indicator
            showLoadingIndicator();
            
            // Remove this receiver from the filter and reload
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            
            // Get current receivers and remove this one
            const receivers = params.get('receiver') ? params.get('receiver').split(',') : [];
            const updatedReceivers = receivers.filter(r => r !== receiver);
            
            if (updatedReceivers.length === 0) {
                params.delete('receiver');
            } else {
                params.set('receiver', updatedReceivers.join(','));
            }
            
            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
        });
        
        badge.appendChild(text);
        badge.appendChild(removeBtn);
        container.appendChild(badge);
    });
}

function selectAllReceivers() {
    const checkboxes = document.querySelectorAll('#receiver-options input[type="checkbox"]');
    const isAnyUnchecked = Array.from(checkboxes).some(cb => !cb.checked);
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = isAnyUnchecked;
        const receiverName = checkbox.parentElement.querySelector('span').textContent;
        toggleReceiver(receiverName, isAnyUnchecked);
    });
}

function applyReceiverFilter() {
    // Show loading indicator
    showLoadingIndicator();
    
    // Update UI to show applied filters
    updateReceiverFilterBadges();
    
    // Build the URL with filter parameters
    const currentUrl = new URL(window.location.href);
    
    // Keep existing parameters but update receiver
    const params = new URLSearchParams(currentUrl.search);
    
    if (selectedReceivers.length === 0) {
        params.delete('receiver');
    } else {
        params.set('receiver', selectedReceivers.join(','));
    }
    
    // Redirect to filtered URL
    window.location.href = `${currentUrl.pathname}?${params.toString()}`;
    
    // Stop propagation of click event to prevent immediate closure
    event.stopPropagation();
}

function updateAmountFilterBadge(amountType) {
    const container = document.getElementById('amount-filter-badges');
    if (!container) return;
    
    // Clear existing badges
    container.innerHTML = '';
    
    // Create badge element
    const badge = document.createElement('span');
    badge.className = 'inline-flex items-center bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded-md border border-blue-100';
    
    // Create badge text
    const text = document.createElement('span');
    text.textContent = amountType === 'income' ? 'Income' : 'Expenses';
    
    // Create remove button
    const removeBtn = document.createElement('button');
    removeBtn.className = 'ml-1 text-gray-500 hover:text-gray-700';
    removeBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        
        // Show loading indicator
        showLoadingIndicator();
        
        // Remove the amount filter and reload
        const currentUrl = new URL(window.location.href);
        const params = new URLSearchParams(currentUrl.search);
        params.delete('amount_type');
        
        // Redirect to filtered URL
        window.location.href = `${currentUrl.pathname}?${params.toString()}`;
    });
    
    // Assemble badge
    badge.appendChild(text);
    badge.appendChild(removeBtn);
    container.appendChild(badge);
    
    // Show container
    document.getElementById('amount-filter-container').classList.remove('hidden');
}

function updateReceiverFilterBadges() {
    const container = document.getElementById('receiver-filter-badges');
    container.innerHTML = '';
    
    if (selectedReceivers.length === 0) {
        document.getElementById('receiver-filter-container').classList.add('hidden');
        // Reset the filter icon state
        const receiverFilterIcon = document.querySelector('#receiver-filter-icon');
        if (receiverFilterIcon) {
            receiverFilterIcon.classList.remove('text-blue-500', 'font-bold');
        }
        return;
    }
    
    document.getElementById('receiver-filter-container').classList.remove('hidden');
    
    // Update the filter icon state to show it's active
    const receiverFilterIcon = document.querySelector('#receiver-filter-icon');
    if (receiverFilterIcon) {
        receiverFilterIcon.classList.add('text-blue-500', 'font-bold');
    }
    
    selectedReceivers.forEach(receiver => {
        const badge = document.createElement('span');
        badge.className = 'inline-flex items-center bg-blue-50 text-blue-700 text-xs font-medium px-2 py-1 rounded-md border border-blue-100';
        
        const text = document.createElement('span');
        text.textContent = receiver;
        
        const removeBtn = document.createElement('button');
        removeBtn.className = 'ml-1 text-gray-500 hover:text-gray-700';
        removeBtn.innerHTML = '<svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>';
        removeBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            
            // Show loading indicator
            showLoadingIndicator();
            
            // Remove this receiver from the filter and reload
            const currentUrl = new URL(window.location.href);
            const params = new URLSearchParams(currentUrl.search);
            
            // Get current receivers and remove this one
            const receivers = params.get('receiver') ? params.get('receiver').split(',') : [];
            const updatedReceivers = receivers.filter(r => r !== receiver);
            
            if (updatedReceivers.length === 0) {
                params.delete('receiver');
            } else {
                params.set('receiver', updatedReceivers.join(','));
            }
            
            // Redirect to filtered URL
            window.location.href = `${currentUrl.pathname}?${params.toString()}`;
            filterTable();
        });
        
        badge.appendChild(text);
        badge.appendChild(removeBtn);
        container.appendChild(badge);
    });
}

function filterTable() {
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
    
    updateActiveFiltersVisibility();

    // rows
    const rows = Array.from(document.querySelectorAll('.transaction-row'));
    rows.forEach(row => {
        let visible = true;
        
        // Receiver filter is now handled server-side, no need to filter client-side
        
        // date filter
        if (dateValue && row.getAttribute('data-date') !== dateValue) visible = false;
        
        // amount type filter (income/expense)
        if (amountType !== 'all') {
            const rowAmount = parseFloat(row.getAttribute('data-amount'));
            if (amountType === 'income' && rowAmount >= 0) visible = false;
            if (amountType === 'expense' && rowAmount <= 0) visible = false;
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

    event.preventDefault();

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
    const visibleRows = document.querySelectorAll('tbody tr.transaction-row:not(.hidden)').length;
    let emptyRow = document.querySelector('.empty-row');
    const tbody = document.querySelector('tbody');
    const colSpan = document.querySelector('thead tr').children.length;
    
    if (visibleRows === 0) {
        if (!emptyRow) {
            // Create new empty row if it doesn't exist
            const tr = document.createElement('tr');
            tr.className = 'empty-row';
            const td = document.createElement('td');
            td.colSpan = colSpan;
            td.className = 'py-4 text-center text-gray-500';
            td.innerText = 'Transactions not found';
            tr.appendChild(td);
            tbody.appendChild(tr);
        } else {
            // Show existing empty row and ensure it's at the top
            emptyRow.classList.remove('hidden');
            // Make sure colspan is correct (in case table structure changed)
            emptyRow.querySelector('td').colSpan = colSpan;
        }
    } else if (emptyRow) {
        // Hide the empty row when there are visible transactions
        emptyRow.classList.add('hidden');
    }
}

function deleteTransaction(id) {
    event.preventDefault();
    Swal.fire({
        title: 'Are you sure?',
        text: "This transaction will be permanently deleted. This action cannot be undone!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('deleteTransactionForm-' + id).submit();
        }
    });
}

// Function to update transaction inclusion status
function updateTransactionIncluded(transactionId, isIncluded) {

    event.preventDefault();

    // Show a loading indicator or feedback
    const checkbox = event.target;
    const originalState = checkbox.checked;
    
    // Send an AJAX request to update the transaction
    fetch(`/transactions/${transactionId}/update-inclusion`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            is_included: isIncluded ? 1 : 0
        })
    })
    .then(response => {
        if (!response.ok) {
            // If there was an error, revert the checkbox
            checkbox.checked = !originalState;
            throw new Error('Failed to update transaction');
        }
        return response.json();
    })
    .then(data => {
        // Update the data attribute
        checkbox.closest('tr').setAttribute('data-included', isIncluded ? '1' : '0');
        
        // Show success message if needed
        if (data.success) {
            // Optional: show a success message
        }
    })
    .catch(error => {
        console.error('Error:', error);
        // Show error message
        Swal.fire({
            title: 'Error',
            text: 'Failed to update transaction inclusion status',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    });
}
</script>