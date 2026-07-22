@props([
    'tone' => 'white',
    'variant' => 'tint',
    'title' => '',
    'content' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'white';
$variant = $variant === 'card' ? 'card' : 'tint';

$ctaUrl = $ctaUrl !== '' ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : (str_starts_with($ctaUrl, '#') ? $ctaUrl : $ctaUrl)) : '';
$hasCta = $ctaLabel !== '' && $ctaUrl !== '';

// tint: soft red-tinted band (warranty exclusions). card: white card with a
// solid red icon tile (Wright Project credit banner).
$bandClass = $variant === 'card'
    ? 'border-grey-50 bg-white shadow-card'
    : 'border-primary/20 bg-primary/5';
$iconClass = $variant === 'card'
    ? 'bg-primary text-white'
    : 'bg-primary/15 text-primary';
@endphp

@if ($title !== '' || $content !== '')
    <section {{ $attributes->class(['bma-highlight-banner', 'bma-band', $tones[$tone]]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            <div class="relative overflow-hidden rounded-card border p-8 lg:p-10 {{ $bandClass }}">
                <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto]">
                    <span class="grid size-14 place-items-center rounded-semi {{ $iconClass }}"><span class="size-8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12V4h8l9 9-8 8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg></span></span>

                    <div>
                        @if ($title !== '')
                            <h2 class="font-heading text-2xl font-bold uppercase text-grey-900">{{ $title }}</h2>
                        @endif
                        @if ($content !== '')
                            <p class="mt-2 max-w-2xl text-body-s text-grey-800">{!! wp_kses_post($content) !!}</p>
                        @endif
                    </div>

                    @if ($hasCta)
                        <div class="shrink-0">
                            <a href="{{ $ctaUrl }}" class="inline-flex items-center justify-center gap-2 rounded-semi px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide transition-colors bg-primary text-white hover:bg-primary-dark">{{ $ctaLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>
@endif
