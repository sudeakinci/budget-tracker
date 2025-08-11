@props([
    'transactions',
    'showReceiver' => true,
    'showPaymentMethod' => false,
])

<div class="overflow-x-visible">
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
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-32">Amount</th>
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
</style>
<script>
let sortState = {
    date: 'desc', // default sort: newest first
    amount: 'desc' // default sort: high to low
};

function showFilterInput(type) {
    document.querySelectorAll('[id^="filter-"]').forEach(el => el.classList.add('hidden'));
    document.getElementById('filter-' + type).classList.remove('hidden');
    const input = document.querySelector('#filter-' + type + ' input');
    if (input) input.focus();
}

function hideFilterInput(type) {
    setTimeout(() => {
        document.getElementById('filter-' + type).classList.add('hidden');
    }, 200);
}

function clearFilter(type) {
    const popup = document.getElementById('filter-' + type);
    if (popup) {
        popup.querySelectorAll('input').forEach(el => {
            el.value = '';
        });
    }
    filterTable();
}

function filterTable() {
    // receiver
    const receiverInput = document.querySelector('#filter-receiver input');
    const receiverValue = receiverInput ? receiverInput.value.toLowerCase() : '';
    // date
    const dateInput = document.querySelector('#filter-date input');
    const dateValue = dateInput ? dateInput.value : '';
    // amount
    const amountSelect = document.querySelector('#filter-amount select');
    const amountInput = document.querySelector('#filter-amount input[type="number"]');
    const amountOperator = amountSelect ? amountSelect.value : 'gt';
    const amountValue = amountInput && amountInput.value !== '' ? parseFloat(amountInput.value) : null;

    // rows
    const rows = Array.from(document.querySelectorAll('.transaction-row'));
    rows.forEach(row => {
        let visible = true;
        // receiver filter
        if (receiverValue && !row.getAttribute('data-receiver').toLowerCase().includes(receiverValue)) visible = false;
        // date filter
        if (dateValue && row.getAttribute('data-date') !== dateValue) visible = false;
        // amount filter
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
</script>