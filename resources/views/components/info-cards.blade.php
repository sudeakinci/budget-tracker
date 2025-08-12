@props([
    'title' => 'Summary',
    'type' => 'default', // default, credit, debt
    'amount' => 0,
    'period' => 'Current',
    'subtitle' => null,
    'icon' => 'wallet',
    'color' => 'blue',
])

@php
    $gradients = [
        'blue' => 'bg-gradient-to-br from-blue-400 to-blue-600',
        'green' => 'bg-gradient-to-br from-green-400 to-green-600',
        'red' => 'bg-gradient-to-br from-red-400 to-red-600',
        'gray' => 'bg-gradient-to-br from-gray-400 to-gray-500',
    ];

    $textColors = [
        'blue' => 'text-blue-700',
        'green' => 'text-green-700',
        'red' => 'text-red-700',
        'gray' => 'text-gray-700',
    ];

    $icons = [
        'wallet' => 'fas fa-wallet',
        'credit' => 'fas fa-hand-holding-usd',
        'debt' => 'fas fa-file-invoice-dollar',
        'minus' => 'fas fa-minus',
    ];

    // Type'a göre otomatik renk ve ikon
    if ($type === 'credit') {
        $color = 'green';
        $icon = 'credit';
    } elseif ($type === 'debt') {
        $color = 'red';
        $icon = 'debt';
    } elseif ($amount == 0) {
        $color = 'gray';
        $icon = 'minus';
    }
@endphp

<div class="col-xl-3 col-md-6 mb-4">
    <div class="flex justify-between items-center rounded-2xl p-5 border border-white/30 shadow-lg
                backdrop-blur-lg bg-white/60 transition-all duration-300 hover:scale-[1.03] hover:shadow-xl">
        
        <!-- Sol -->
        <div>
            <h2 class="text-2xl font-bold {{ $textColors[$color] }}">
                ₺{{ number_format($amount, 2) }}
            </h2>
            <h4 class="text-sm font-semibold text-gray-800 mt-1">{{ $title }}</h4>
            <p class="text-xs text-gray-500 mt-0.5">{{ $subtitle ?? $period }}</p>
        </div>

        <!-- Sağ ikon -->
        <div class="{{ $gradients[$color] }} p-4 rounded-xl flex items-center justify-center shadow-md">
            <i class="{{ $icons[$icon] ?? $icon }} text-white fa-lg"></i>
        </div>
    </div>
</div>
