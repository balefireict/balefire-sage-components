@props([
    'containerWidth' => 'max-w-7xl',
    'backgroundColor' => 'transparent',
    'htmlId' => '',
    'content' => '',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$allowedWidths = [
    'max-w-none',
    'max-w-xs',
    'max-w-sm',
    'max-w-md',
    'max-w-lg',
    'max-w-xl',
    'max-w-2xl',
    'max-w-3xl',
    'max-w-4xl',
    'max-w-5xl',
    'max-w-6xl',
    'max-w-7xl',
    'max-w-screen-sm',
    'max-w-screen-md',
    'max-w-screen-lg',
    'max-w-screen-xl',
    'max-w-screen-2xl',
    'max-w-prose',
];

$containerWidth = in_array($containerWidth, $allowedWidths, true) ? $containerWidth : 'max-w-7xl';
$surface = SectionStyles::surface(sanitize_key($backgroundColor));
$htmlId = sanitize_key($htmlId);
@endphp

{{-- tailwind-safelist: max-w-none max-w-xs max-w-sm max-w-md max-w-lg max-w-xl max-w-2xl max-w-3xl max-w-4xl max-w-5xl max-w-6xl max-w-7xl max-w-screen-sm max-w-screen-md max-w-screen-lg max-w-screen-xl max-w-screen-2xl max-w-prose --}}
<section
    {{ $attributes->class(['bma-section', 'w-full', 'mx-auto', $surface['section']]) }}
    @if ($htmlId !== '') id="{{ $htmlId }}" @endif
>
    <div class="{{ $containerWidth }} mx-auto container px-4">
        {!! $content !!}
    </div>
</section>
