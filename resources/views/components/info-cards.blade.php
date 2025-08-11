@props([
    'title' => 'Summary',
    'type' => 'default', // default, transaction, ledger
    'amount' => 0,
    'period' => 'Current', // Current, Monthly, etc.
    'icon' => 'wallet', // wallet, arrow-up, arrow-down, chart, calendar
    'color' => 'blue', // blue, green, red, yellow, purple
    'subtitle' => null,
    'trend' => null, // up, down, stable
    'trendPercentage' => null,
    'stats' => null, // For monthly stats display
    'monthNames' => null, // For month names
    'compareValue' => null, // To show comparison value
    'showFooter' => false,
    'footerLink' => null,
    'footerText' => null
])

@php
    $colors = [
        'blue' => [
            'bg' => 'bg-blue-50',
            'border' => 'border-blue-200',
            'text' => 'text-blue-700',
            'icon' => 'text-blue-600',
            'trend-up' => 'text-green-600',
            'trend-down' => 'text-red-600',
            'trend-stable' => 'text-gray-600',
            'hover' => 'hover:bg-blue-100'
        ],
        'green' => [
            'bg' => 'bg-green-50',
            'border' => 'border-green-200',
            'text' => 'text-green-700',
            'icon' => 'text-green-600',
            'trend-up' => 'text-green-600',
            'trend-down' => 'text-red-600',
            'trend-stable' => 'text-gray-600',
            'hover' => 'hover:bg-green-100'
        ],
        'red' => [
            'bg' => 'bg-red-50',
            'border' => 'border-red-200',
            'text' => 'text-red-700',
            'icon' => 'text-red-600',
            'trend-up' => 'text-green-600',
            'trend-down' => 'text-red-600',
            'trend-stable' => 'text-gray-600',
            'hover' => 'hover:bg-red-100'
        ],
        'yellow' => [
            'bg' => 'bg-yellow-50',
            'border' => 'border-yellow-200',
            'text' => 'text-yellow-700',
            'icon' => 'text-yellow-600',
            'trend-up' => 'text-green-600',
            'trend-down' => 'text-red-600',
            'trend-stable' => 'text-gray-600',
            'hover' => 'hover:bg-yellow-100'
        ],
        'purple' => [
            'bg' => 'bg-purple-50',
            'border' => 'border-purple-200',
            'text' => 'text-purple-700',
            'icon' => 'text-purple-600',
            'trend-up' => 'text-green-600',
            'trend-down' => 'text-red-600',
            'trend-stable' => 'text-gray-600',
            'hover' => 'hover:bg-purple-100'
        ],
    ];

    $icons = [
        'wallet' => 'fas fa-wallet',
        'arrow-up' => 'fas fa-arrow-up',
        'arrow-down' => 'fas fa-arrow-down',
        'chart' => 'fas fa-chart-line',
        'calendar' => 'fas fa-calendar-alt',
        'credit' => 'fas fa-hand-holding-usd',
        'debt' => 'fas fa-file-invoice-dollar',
        'minus' => 'fas fa-minus'
    ];

    // determine if we should format as currency
    $formatAsCurrency = in_array($type, ['transaction', 'ledger', 'balance']);
@endphp

<div class="col-xl-6 col-md-12 mb-4 {{ $attributes->get('class') }}">
    <div class="card rounded-lg shadow-sm border {{ $colors[$color]['border'] }} h-full" style="min-height:70px;">
        <div class="card-body p-2" style="min-height:50px;">
            <div class="d-flex justify-content-between">
                <div class="d-flex flex-row">
                    <div class="align-self-center">
                        <h2 class="h2 mb-0 me-2 font-bold {{ $colors[$color]['text'] }}" style="font-size:1.1rem;">
                            @if($formatAsCurrency)
                                ₺{{ number_format($amount, 2) }}
                            @else
                                {{ $amount }}
                            @endif
                        </h2>
                    </div>
                    <div class="ms-3">
                        <h4 class="text-base font-semibold text-gray-800">{{ $title }}</h4>
                        <p class="mb-0 text-xs text-gray-600">{{ $period }}</p>
                        
                        @if($subtitle)
                            <p class="text-xs text-gray-500" style="font-size:0.7rem;">{{ $subtitle }}</p>
                        @endif
                        
                        @if($trend)
                            <div class="flex items-center mt-1">
                                @if($trend === 'up')
                                    <i class="fas fa-arrow-up {{ $colors[$color]['trend-up'] }} text-xs mr-1"></i>
                                @elseif($trend === 'down')
                                    <i class="fas fa-arrow-down {{ $colors[$color]['trend-down'] }} text-xs mr-1"></i>
                                @else
                                    <i class="fas fa-minus {{ $colors[$color]['trend-stable'] }} text-xs mr-1"></i>
                                @endif
                                
                                @if($trendPercentage)
                                    <span class="text-xs {{ $trend === 'up' ? $colors[$color]['trend-up'] : ($trend === 'down' ? $colors[$color]['trend-down'] : $colors[$color]['trend-stable']) }}">
                                        {{ $trendPercentage }}%
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
                <div class="align-self-center {{ $colors[$color]['bg'] }} p-3 rounded-full">
                    <i class="{{ $icons[$icon] ?? $icon }} {{ $colors[$color]['icon'] }} fa-2x"></i>
                </div>
            </div>
            
            @if(($type === 'ledger' || $type === 'transaction') && $stats && $monthNames)
            <div class="mt-4">
                <div class="text-sm font-semibold mb-2">Last 3 Months</div>
                <div class="grid grid-cols-3 gap-2">
                    @foreach(array_keys($monthNames) as $key)
                        @php
                            $monthName = $monthNames[$key];
                            $value = isset($stats[$key]) ? $stats[$key] : 0;
                            $isPositive = $value >= 0;
                            
                            // set colors based on card type and value
                            if ($type === 'transaction' && $title === 'Total Expenses') {
                                // for expenses, lower is better
                                $bgColor = 'bg-gray-100';
                                $textColor = 'text-gray-700';
                            } elseif ($type === 'transaction' && $title === 'Total Income') {
                                // for income, higher is better
                                $bgColor = 'bg-gray-100';
                                $textColor = 'text-gray-700';
                            } else {
                                // for net values or ledger cards
                                $bgColor = $isPositive ? 'bg-green-100' : 'bg-red-100';
                                $textColor = $isPositive ? 'text-green-700' : 'text-red-700';
                            }
                        @endphp
                        <div class="p-2 {{ $bgColor }} rounded">
                            <div class="text-xs text-gray-600">{{ $monthName }}</div>
                            <div class="font-bold {{ $textColor }}">
                                ₺{{ number_format($value, 2) }}
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
            
            @if($compareValue !== null)
            <div class="mt-3 pt-3 border-t border-gray-200">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Compared to:</span>
                    <span class="font-semibold {{ $compareValue >= 0 ? 'text-green-600' : 'text-red-600' }}">
                        {{ $compareValue >= 0 ? '+' : '' }}₺{{ number_format($compareValue, 2) }}
                    </span>
                </div>
            </div>
            @endif
        </div>
        
        @if($showFooter && $footerLink && $footerText)
        <div class="card-footer p-3 bg-gray-50 border-t border-gray-200">
            <a href="{{ $footerLink }}" class="text-sm font-medium {{ $colors[$color]['text'] }} hover:underline flex items-center justify-end">
                {{ $footerText }}
                <i class="fas fa-chevron-right ml-1 text-xs"></i>
            </a>
        </div>
        @endif
    </div>
</div>