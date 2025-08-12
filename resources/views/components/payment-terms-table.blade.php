@props(['paymentTerms'])

<div class="overflow-x-visible">
    <table class="min-w-full bg-white rounded-lg shadow text-sm mb-6">
        <thead>
            <tr class="bg-blue-50 border-b border-blue-200">
                <th class="py-3 px-4 text-left font-semibold text-blue-700">Name</th>
                <th class="py-3 px-4 text-left font-semibold text-blue-700">Transactions Count</th>
                <th class="py-3 px-4 text-left font-semibold text-blue-700">Created Date</th>
                <th class="py-3 px-4 text-right font-semibold text-blue-700">Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach($paymentTerms as $paymentTerm)
                <tr class="hover:bg-blue-50 {{ $loop->even ? 'bg-gray-50' : '' }} payment-term-row"
                    data-payment-term-id="{{ $paymentTerm->id }}">
                    <td class="py-2 px-4 border-b border-gray-200">
                        {{ $paymentTerm->name }}
                    </td>
                    <td class="py-2 px-4 border-b border-gray-200">
                        {{ $paymentTerm->transactions_count }}
                    </td>
                    <td class="py-2 px-4 border-b border-gray-200">
                        {{ $paymentTerm->created_at->format('d.m.Y H:i') }}
                    </td>
                    <td class="py-2 px-4 border-b border-gray-200 text-right">
                        <button type="button" 
                            class="text-blue-600 hover:text-blue-800 focus:outline-none filter-button"
                            onclick="togglePaymentTermFilter({{ $paymentTerm->id }}, '{{ $paymentTerm->name }}')">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                    </td>
                </tr>
            @endforeach
            
            @if($paymentTerms->isEmpty())
                <tr>
                    <td colspan="4" class="py-4 text-center text-gray-500">No payment terms found</td>
                </tr>
            @endif
        </tbody>
    </table>
</div>
