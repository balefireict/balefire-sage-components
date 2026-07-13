@props([
    'gapSize' => 'gap-4',
    'gapCustom' => '',
    'alternateEvenRows' => false,
    'content' => '',
])

@php
$gapSize = sanitize_key($gapSize);
$allowedGapSizes = ['gap-4', 'gap-6', 'gap-8', 'gap-10', 'gap-12', 'gap-16', 'gap-20', 'gap-24', 'custom'];

if (! in_array($gapSize, $allowedGapSizes, true)) {
    $gapSize = 'gap-4';
}

$gapClass = ($gapSize === 'custom' && $gapCustom !== '') ? $gapCustom : $gapSize;

$rowsListClasses = array_filter(['rows-list', 'flex', 'flex-col', $gapClass]);
@endphp

{{-- tailwind-safelist: gap-4 gap-6 gap-8 gap-10 gap-12 gap-16 gap-20 gap-24 --}}
<div {{ $attributes->class(array_filter([
    'image-text-rows',
    'row-gap-' . ($gapSize === 'custom' ? 'custom' : str_replace('gap-', '', $gapSize)),
    $alternateEvenRows ? 'rows-alternate-even' : null,
])) }}>
    <div class="{{ implode(' ', $rowsListClasses) }}">
        {!! $content !!}
    </div>
</div>
