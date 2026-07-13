@props([
    'maxWidth' => 'wide',
    'paddingInline' => 'md',
    'content' => '',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$maxWidth = in_array($maxWidth, ['narrow', 'content', 'medium', 'large', 'wide', 'full'], true)
    ? $maxWidth
    : 'wide';
$paddingInline = in_array($paddingInline, ['none', 'sm', 'md', 'lg'], true)
    ? $paddingInline
    : 'md';
@endphp

<div {{ $attributes->class([
    'bma-container',
    SectionStyles::containerClass($maxWidth),
    SectionStyles::paddingInline($paddingInline),
    'mx-auto',
    'w-full',
]) }}>
    {!! $content !!}
</div>
