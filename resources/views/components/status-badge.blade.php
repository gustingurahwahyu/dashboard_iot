@props(['status' => 'ADA'])

@php
    $isAda = strtoupper($status) === 'ADA';
    $classes = $isAda
        ? 'border-emerald-200 bg-emerald-50 text-emerald-800'
        : 'border-rose-200 bg-rose-50 text-rose-800';
    $dot = $isAda ? 'bg-emerald-500' : 'bg-rose-500';
@endphp

<span class="inline-flex items-center gap-2 rounded-full border px-3 py-1 text-xs font-semibold {{ $classes }}">
    <span class="h-2 w-2 rounded-full {{ $dot }}"></span>
    {{ strtoupper($status) }}
</span>
