@props([
    'columns' => 3,
    'columnsTablet' => '',
    'gap' => 6,
    'content' => '',
])

@php
$allowedColumns = [1, 2, 3, 4, 6];

$columns = in_array((int) $columns, $allowedColumns, true) ? (int) $columns : 3;
$columnsTablet = ($columnsTablet !== '' && in_array((int) $columnsTablet, $allowedColumns, true)) ? (int) $columnsTablet : null;
$gap = in_array((int) $gap, [0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16], true) ? (int) $gap : 6;

$classes = ['bma-layout-grid', 'grid', 'grid-cols-1', 'gap-' . $gap];

if ($columnsTablet !== null) {
    $classes[] = 'md:grid-cols-' . $columnsTablet;
}
if ($columns > 1) {
    $classes[] = 'lg:grid-cols-' . $columns;
}
@endphp

{{-- tailwind-safelist: gap-0 gap-1 gap-2 gap-3 gap-4 gap-5 gap-6 gap-8 gap-10 gap-12 gap-16 md:grid-cols-1 md:grid-cols-2 md:grid-cols-3 md:grid-cols-4 md:grid-cols-6 lg:grid-cols-2 lg:grid-cols-3 lg:grid-cols-4 lg:grid-cols-6 --}}
<div {{ $attributes->class($classes) }}>
    {!! $content !!}
</div>
