@props([
    'title' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
$primaryUrl = $primaryUrl !== '' ? esc_url(str_starts_with($primaryUrl, '/') ? home_url($primaryUrl) : $primaryUrl) : '';
$secondaryUrl = $secondaryUrl !== '' ? esc_url(str_starts_with($secondaryUrl, '/') ? home_url($secondaryUrl) : $secondaryUrl) : '';
$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');

$buttonBase = 'inline-flex items-center justify-center gap-2 rounded-semi px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide transition-colors';
@endphp

<section {{ $attributes->class(['bma-cta-band', 'bma-band', 'bg-primary']) }} data-bma-tone="primary">
    <div class="mx-auto max-w-content px-6 text-center md:px-10 xl:px-16">
        @if ($title !== '')
            <h2 class="mx-auto max-w-3xl font-heading text-[clamp(1.8rem,3.6vw,2.5rem)] font-bold uppercase leading-tight text-white">{{ $title }}</h2>
        @endif

        @if ($content !== '')
            <p class="mx-auto mt-4 max-w-2xl text-body-m text-white/90">{!! wp_kses_post($content) !!}</p>
        @endif

        @if ($hasButtons)
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                @if ($primaryLabel !== '' && $primaryUrl !== '')
                    <a href="{{ $primaryUrl }}" class="{{ $buttonBase }} bg-grey-900 text-white hover:bg-black">{{ $primaryLabel }}</a>
                @endif
                @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                    <a href="{{ $secondaryUrl }}" class="{{ $buttonBase }} border border-white/30 text-white hover:bg-white hover:text-grey-900">{{ $secondaryLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                @endif
            </div>
        @endif
    </div>
</section>
