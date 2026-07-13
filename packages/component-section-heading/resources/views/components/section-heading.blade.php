@props([
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'contentAlign' => 'left',
    'maxWidth' => 'wide',
    'backgroundTone' => 'transparent',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$contentAlign = sanitize_key($contentAlign);
$maxWidth = $maxWidth !== ''
    ? sanitize_key($maxWidth)
    : sanitize_key((string) \BalefireInc\Sage\Support\Settings::defaultFor('sectionMaxWidth', 'wide'));

$alignmentClasses = [
    'left'   => 'items-start text-left',
    'center' => 'items-center text-center',
    'right'  => 'items-end text-right',
];
$surface = SectionStyles::surface(sanitize_key($backgroundTone));
$innerStyle = SectionStyles::innerStyle($maxWidth);
@endphp

<section {{ $attributes->class(['bma-section-heading', 'py-8 sm:py-12', $surface['section']]) }}>
    <div
        class="mx-auto flex w-full flex-col gap-4 px-4 lg:px-6 {{ $alignmentClasses[$contentAlign] ?? $alignmentClasses['left'] }}"
        style="{{ $innerStyle }}"
    >
        @if ($eyebrow !== '')
            <p class="text-sm font-semibold uppercase tracking-[0.2em] {{ $surface['eyebrow'] }}">
                {{ $eyebrow }}
            </p>
        @endif

        @if ($title !== '')
            <h2 class="text-3xl font-headline leading-[1.05] {{ $surface['heading'] }}">
                {{ $title }}
            </h2>
        @endif

        @if ($content !== '')
            <div class="max-w-[62ch] text-base leading-7 {{ $surface['bodyStrong'] }}">
                {!! wp_kses_post(wpautop($content)) !!}
            </div>
        @endif
    </div>
</section>
