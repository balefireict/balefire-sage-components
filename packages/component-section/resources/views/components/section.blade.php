@props([
    'backgroundColor' => 'none',
    'htmlId' => '',
    'content' => '',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$tone = sanitize_key($backgroundColor);
$hasBg = $tone !== 'none' && $tone !== 'transparent' && $tone !== '';
$surface = SectionStyles::surface($tone);
$htmlId = sanitize_key($htmlId);
@endphp

<section
    {{ $attributes->class(['bma-section', $surface['section']]) }}
    data-has-bg="{{ $hasBg ? 'true' : 'false' }}"
    @if ($htmlId !== '' && ! $attributes->has('id')) id="{{ $htmlId }}" @endif
>
    {!! $content !!}
</section>
