@props([
    'columnsMobile' => 1,
    'columnsTablet' => '',
    'columnsDesktop' => 3,
    'gap' => 6,
    'verticalAlign' => 'start',
    'tagName' => 'div',
    'content' => '',
])

@php
$allowedColumns = [1, 2, 3, 4, 5, 6];
$allowedGap = [0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16];

$columnsMobile = in_array((int) $columnsMobile, $allowedColumns, true) ? (int) $columnsMobile : 1;
$columnsTablet = ($columnsTablet !== '' && in_array((int) $columnsTablet, $allowedColumns, true)) ? (int) $columnsTablet : null;
$columnsDesktop = in_array((int) $columnsDesktop, $allowedColumns, true) ? (int) $columnsDesktop : 3;
$gap = in_array((int) $gap, $allowedGap, true) ? (int) $gap : 6;

$vAlignMap = [
    'start'   => 'items-start',
    'center'  => 'items-center',
    'end'     => 'items-end',
    'stretch' => 'items-stretch',
];
$verticalAlign = isset($vAlignMap[$verticalAlign]) ? $verticalAlign : 'start';
$tagName = in_array($tagName, ['div', 'section'], true) ? $tagName : 'div';

$classes = [
    'bma-auto-grid',
    'flex',
    'flex-wrap',
    'justify-center',
    'gap-' . $gap,
    'auto-grid-gap-' . $gap,
    'auto-grid-cols-' . $columnsMobile,
    $vAlignMap[$verticalAlign],
];

// Responsive column classes: mobile below md, tablet at md (when set and
// different), desktop at lg (when different from the md/active value).
if ($columnsTablet !== null) {
    if ($columnsTablet !== $columnsMobile) {
        $classes[] = 'md:auto-grid-cols-' . $columnsTablet;
    }
    if ($columnsDesktop !== $columnsTablet) {
        $classes[] = 'lg:auto-grid-cols-' . $columnsDesktop;
    }
} elseif ($columnsDesktop !== $columnsMobile) {
    $classes[] = 'lg:auto-grid-cols-' . $columnsDesktop;
}

$classes = array_unique($classes);
@endphp

{{-- tailwind-safelist: auto-grid-gap-0 auto-grid-gap-1 auto-grid-gap-2 auto-grid-gap-3 auto-grid-gap-4 auto-grid-gap-5 auto-grid-gap-6 auto-grid-gap-8 auto-grid-gap-10 auto-grid-gap-12 auto-grid-gap-16 auto-grid-cols-1 auto-grid-cols-2 auto-grid-cols-3 auto-grid-cols-4 auto-grid-cols-5 auto-grid-cols-6 md:auto-grid-cols-1 md:auto-grid-cols-2 md:auto-grid-cols-3 md:auto-grid-cols-4 md:auto-grid-cols-5 md:auto-grid-cols-6 lg:auto-grid-cols-1 lg:auto-grid-cols-2 lg:auto-grid-cols-3 lg:auto-grid-cols-4 lg:auto-grid-cols-5 lg:auto-grid-cols-6 gap-0 gap-1 gap-2 gap-3 gap-4 gap-5 gap-6 gap-8 gap-10 gap-12 gap-16 items-start items-center items-end items-stretch --}}
<{{ $tagName }} {{ $attributes->class($classes) }}>
    {!! $content !!}
</{{ $tagName }}>
