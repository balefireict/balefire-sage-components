@props([
    'content' => '',
    'maxWidth' => 'narrow',
    'backgroundTone' => 'light',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$surface = SectionStyles::surface(sanitize_key($backgroundTone));
$innerStyle = match (sanitize_key($maxWidth)) {
    'content' => SectionStyles::innerStyle('content'),
    'wide' => SectionStyles::innerStyle('wide'),
    'full' => SectionStyles::innerStyle('full'),
    default => 'max-width: 56rem;',
};
@endphp

<section {{ $attributes->class(['bma-centered-intro-text', 'w-full', $surface['section']]) }}>
    <div class="mx-auto px-6 py-6 md:py-12 lg:py-20 text-center mb-4 md:mb-6 [&_p]:text-center [&_p]:text-base md:[&_p]:text-xl [&_p:not(:last-child)]:mb-4" style="{{ $innerStyle }}">
        <div class="{{ $surface['bodyStrong'] }}">
            {!! wp_kses_post(wpautop($content)) !!}
        </div>
    </div>
</section>
