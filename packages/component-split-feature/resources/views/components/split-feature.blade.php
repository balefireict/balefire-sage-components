@props([
    'tone' => 'white',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
    'mediaType' => 'content',
    'mediaSide' => 'right',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'statValue' => '',
    'statLabel' => '',
    'statNote' => '',
    'mediaContent' => '',
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'white';
$mediaSide = $mediaSide === 'left' ? 'left' : 'right';
$mediaType = in_array($mediaType, ['image', 'stat', 'content'], true) ? $mediaType : 'content';

$imageId = absint($imageId);
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'large');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}

$primaryUrl = $primaryUrl !== '' ? esc_url(str_starts_with($primaryUrl, '/') ? home_url($primaryUrl) : $primaryUrl) : '';
$secondaryUrl = $secondaryUrl !== '' ? esc_url(str_starts_with($secondaryUrl, '/') ? home_url($secondaryUrl) : $secondaryUrl) : '';
$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');

$buttonBase = 'inline-flex items-center justify-center gap-2 rounded-semi px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide transition-colors';

// Nested content (tables) gets the wider column, per the repair-rates comp.
$gridCols = $mediaType === 'content' ? 'lg:grid-cols-[0.9fr_1.1fr]' : 'lg:grid-cols-2';
$alignment = $mediaType === 'content' ? 'items-start' : 'items-center';
@endphp

<section {{ $attributes->class(['bma-split-feature', 'py-16 lg:py-24', $tones[$tone]]) }}>
    <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
        <div class="grid gap-12 {{ $alignment }} {{ $gridCols }}">
            <div @class(['order-1 lg:order-2' => $mediaSide === 'left'])>
                @if ($eyebrow !== '')
                    <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                @endif

                @if ($title !== '')
                    <h2 class="font-heading text-[clamp(2rem,4vw,2.6rem)] font-bold uppercase leading-tight text-grey-900">{{ $title }}</h2>
                @endif

                @if ($content !== '')
                    <div class="bma-prose mt-5">{!! wp_kses_post(wpautop($content)) !!}</div>
                @endif

                @if ($hasButtons)
                    <div class="mt-8 flex flex-wrap gap-4">
                        @if ($primaryLabel !== '' && $primaryUrl !== '')
                            <a href="{{ $primaryUrl }}" class="{{ $buttonBase }} bg-primary text-white hover:bg-primary-dark">{{ $primaryLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                        @endif
                        @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                            <a href="{{ $secondaryUrl }}" class="{{ $buttonBase }} border border-grey-200 text-grey-800 hover:border-grey-800 hover:bg-grey-25">{{ $secondaryLabel }}</a>
                        @endif
                    </div>
                @endif
            </div>

            <div @class(['order-2 lg:order-1' => $mediaSide === 'left'])>
                @if ($mediaType === 'image' && $imageUrl !== '')
                    <div class="relative aspect-[4/3] overflow-hidden rounded-card ring-1 ring-white/10">
                        <img src="{{ esc_url($imageUrl) }}" alt="{{ $imageAlt }}" class="absolute inset-0 size-full object-cover" loading="lazy" decoding="async" />
                    </div>
                @elseif ($mediaType === 'stat' && ($statValue !== '' || $statLabel !== ''))
                    <div class="rounded-card border border-grey-50 bg-grey-900 p-8 text-center shadow-card">
                        @if ($statLabel !== '' && $statValue === '')
                            <p class="font-mono text-label-m font-bold uppercase tracking-[0.2em] text-grey-300">{{ $statLabel }}</p>
                        @endif
                        @if ($statValue !== '')
                            <p class="font-mono text-5xl font-bold tracking-wide text-white">{{ $statValue }}</p>
                        @endif
                        @if ($statLabel !== '' && $statValue !== '')
                            <p class="mt-2 font-mono text-label-m font-bold uppercase tracking-[0.2em] text-grey-300">{{ $statLabel }}</p>
                        @endif
                        @if ($statNote !== '')
                            <div class="mx-auto my-6 h-px w-16 bg-primary"></div>
                            <p class="text-body-s text-grey-300">{{ $statNote }}</p>
                        @endif
                    </div>
                @elseif ($mediaType === 'content' && $mediaContent !== '')
                    <div class="bma-split-feature__content overflow-hidden rounded-card border border-grey-50 bg-white shadow-card">{!! $mediaContent !!}</div>
                @endif
            </div>
        </div>
    </div>
</section>
