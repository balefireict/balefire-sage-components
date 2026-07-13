@props([
    'preheader' => '',
    'title' => '',
    'textAlign' => 'center',
])

@php
$alignClass = [
    'left'   => 'text-left lg:max-w-1/2',
    'center' => 'text-center mx-auto max-w-3xl',
    'right'  => 'text-right lg:max-w-1/2',
];
$textAlign = sanitize_key($textAlign);
@endphp

<div {{ $attributes->class(array_filter([
    'bma-preheader-and-title',
    'block',
    $alignClass[$textAlign] ?? null,
])) }}>
    @if ($preheader !== '')
        <p class="bma-preheader font-semibold mb-1.5">{{ $preheader }}</p>
    @endif
    @if ($title !== '')
        <h2 class="bma-preheader-and-title__heading leading-[1]">
            {{ $title }}
        </h2>
    @endif
</div>
