@props([
    'transactions',
    'showReceiver' => true,
    'showPaymentMethod' => false,
    'rowCount' => 20,
])

<div class="overflow-x-visible">
    <table class="min-w-full bg-white rounded-lg shadow text-sm">
        <thead>
            <tr class="bg-blue-50 border-b border-blue-200">
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Date</th>
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-60">Description</th>
                <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Amount</th>
                @if($showReceiver)
                    <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Receiver</th>
                @endif
                @if($showPaymentMethod)
                    <th class="py-3 px-4 text-left font-semibold text-blue-700 w-48">Payment Method</th>
                @endif
                <th class="w-10"></th> <!-- empty title for icon -->
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
                <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }}">
                    <td class="py-2 px-4 border-b border-gray-200 w-40">
                        {{ $transaction->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="py-2 px-4 border-b border-gray-200 w-64">{{ $transaction->description }}</td>
                    <td
                        class="py-2 px-4 border-b border-gray-200 font-semibold w-32 {{ $transaction->amount < 0 ? 'text-red-600' : 'text-green-600' }}">
                        {{ number_format(abs($transaction->amount), 2) }}
                        ₺
                    </td>
                    @if($showReceiver)
                        <td class="py-2 px-4 border-b border-gray-200 w-48">
                            {{ optional($transaction->user)->name ?: '-' }}
                        </td>
                    @endif
                    @if($showPaymentMethod)
                        <td class="py-2 px-4 border-b border-gray-200 w-48">
                            {{ $transaction->payment_term_name }}
                        </td>
                    @endif
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
                                '{{ number_format($transaction->amount, 2) }} ₺', 
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

            @for($i = $transactions->count(); $i < $rowCount; $i++)
                    <tr class="{{ $i % 2 == 0 ? 'bg-gray-50' : '' }} hidden">
                    <td class="py-2 px-4 border-b border-gray-200 w-48">&nbsp;</td>
                    <td class="py-2 px-4 border-b border-gray-200 w-60"></td>
                    <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                    @if($showReceiver)
                        <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                    @endif
                    @if($showPaymentMethod)
                        <td class="py-2 px-4 border-b border-gray-200 w-48"></td>
                    @endif
                    <td class="px-2 border-b border-gray-200 w-10"></td>
                </tr>
            @endfor
        </tbody>
    </table>
</div>
