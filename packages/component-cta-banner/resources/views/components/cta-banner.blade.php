@props([
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'tone' => 'primary',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'primaryStyle' => 'solid',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
// Tone → Tailwind class bundles.
// Written as complete static strings so the JIT scanner picks them up.
$toneClasses = [
    'primary'   => 'bg-primary text-white',
    'secondary' => 'bg-secondary text-white',
    'dark'      => 'bg-dark text-white',
    'light'     => 'bg-surface text-dark',
];

$tone = array_key_exists($tone, $toneClasses) ? $tone : 'primary';

$primaryBtnClass = ($primaryStyle === 'outline')
    ? 'border border-white bg-transparent text-white hover:bg-white/10'
    : 'bg-white text-dark hover:bg-white/90';
@endphp

<section {{ $attributes->class([
    'bma-cta-banner',
    'rounded-card px-6 py-8 md:px-10 md:py-12',
    $toneClasses[$tone],
]) }}>
    <div class="mx-auto flex max-w-[72rem] flex-col gap-8 md:flex-row md:items-end md:justify-between">
        <div class="max-w-[44rem] space-y-4">
            @unless(empty($eyebrow))
                <p class="text-sm font-semibold uppercase tracking-[0.2em] text-current/80">
                    {{ $eyebrow }}
                </p>
            @endunless

            @unless(empty($title))
                <h2 class="text-3xl font-headline leading-[1.05] text-current md:text-5xl">
                    {{ $title }}
                </h2>
            @endunless

            @unless(empty($content))
                <div class="max-w-[62ch] text-base leading-7 text-current/85">
                    {!! $content !!}
                </div>
            @endunless
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:justify-end">
            @unless(empty($primaryLabel) || empty($primaryUrl))
                <a
                    class="inline-flex items-center justify-center rounded-full px-6 py-3 font-semibold no-underline transition hover:-translate-y-px {{ $primaryBtnClass }}"
                    href="{{ $primaryUrl }}"
                >
                    {{ $primaryLabel }}
                </a>
            @endunless

            @unless(empty($secondaryLabel) || empty($secondaryUrl))
                <a
                    class="inline-flex items-center justify-center rounded-full border border-current px-6 py-3 font-semibold text-current no-underline transition hover:-translate-y-px"
                    href="{{ $secondaryUrl }}"
                >
                    {{ $secondaryLabel }}
                </a>
            @endunless
        </div>
    </div>
</section>
