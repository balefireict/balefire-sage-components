@props([
    'colSpan' => 6,
    'colSpanTablet' => '',
    'colSpanMobile' => 12,
    'rowSpan' => '',
    'vAlign' => '',
    'content' => '',
])

@php
$allowedSpan = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];

$colSpan = in_array((int) $colSpan, $allowedSpan, true) ? (int) $colSpan : 6;
$colSpanTablet = ($colSpanTablet !== '' && in_array((int) $colSpanTablet, $allowedSpan, true)) ? (int) $colSpanTablet : null;
$colSpanMobile = in_array((int) $colSpanMobile, $allowedSpan, true) ? (int) $colSpanMobile : 12;
$rowSpan = ($rowSpan !== '' && in_array((int) $rowSpan, $allowedSpan, true)) ? (int) $rowSpan : null;

$vAlignMap = [
    'start'   => 'self-start',
    'center'  => 'self-center',
    'end'     => 'self-end',
    'stretch' => 'self-stretch',
];

$classes = ['bma-grid-cell', 'col-span-' . $colSpanMobile];

if ($colSpanTablet !== null) {
    $classes[] = 'md:col-span-' . $colSpanTablet;
}
if ($colSpan !== $colSpanMobile && ($colSpanTablet === null || $colSpan !== $colSpanTablet)) {
    $classes[] = 'lg:col-span-' . $colSpan;
}
if ($rowSpan !== null) {
    $classes[] = 'row-span-' . $rowSpan;
}
if (isset($vAlignMap[$vAlign])) {
    $classes[] = $vAlignMap[$vAlign];
}
@endphp

{{-- tailwind-safelist: col-span-1 col-span-2 col-span-3 col-span-4 col-span-5 col-span-6 col-span-7 col-span-8 col-span-9 col-span-10 col-span-11 col-span-12 md:col-span-1 md:col-span-2 md:col-span-3 md:col-span-4 md:col-span-5 md:col-span-6 md:col-span-7 md:col-span-8 md:col-span-9 md:col-span-10 md:col-span-11 md:col-span-12 lg:col-span-1 lg:col-span-2 lg:col-span-3 lg:col-span-4 lg:col-span-5 lg:col-span-6 lg:col-span-7 lg:col-span-8 lg:col-span-9 lg:col-span-10 lg:col-span-11 lg:col-span-12 row-span-1 row-span-2 row-span-3 row-span-4 row-span-5 row-span-6 row-span-7 row-span-8 row-span-9 row-span-10 row-span-11 row-span-12 self-start self-center self-end self-stretch --}}
<div {{ $attributes->class($classes) }}>
    {!! $content !!}
</div>
