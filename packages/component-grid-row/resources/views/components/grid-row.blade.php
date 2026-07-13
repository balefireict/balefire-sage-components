@props([
    'gap' => 6,
    'minColumnWidth' => '',
    'content' => '',
])

@php
$gap = in_array((int) $gap, [0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16], true) ? (int) $gap : 6;

$style = ($minColumnWidth !== '' && is_numeric($minColumnWidth))
    ? '--min-col-width: ' . (int) $minColumnWidth . 'px'
    : null;
@endphp

{{-- tailwind-safelist: gap-0 gap-1 gap-2 gap-3 gap-4 gap-5 gap-6 gap-8 gap-10 gap-12 gap-16 --}}
@php
$rootAttributes = $attributes->class(['bma-grid-row', 'grid', 'grid-cols-12', 'gap-' . $gap]);
if ($style) {
    $rootAttributes = $rootAttributes->style([$style]);
}
@endphp
<div {{ $rootAttributes }}>
    {!! $content !!}
</div>
