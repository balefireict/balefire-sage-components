@props([
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'imageRatio' => 'fill',
    'imageFrame' => 'card',
    'imageFit' => 'cover',
    'showBreadcrumb' => true,
])

@php
$showBreadcrumb = filter_var($showBreadcrumb, FILTER_VALIDATE_BOOL);

if ($title === '' && is_singular()) {
    $title = get_the_title();
}

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

// Breadcrumb trail: Home / [ancestors…] / current title, from the page tree.
$trail = [];
if ($showBreadcrumb && is_singular()) {
    $trail[] = ['label' => __('Home', 'balefire'), 'url' => home_url('/')];
    foreach (array_reverse(get_post_ancestors(get_the_ID())) as $ancestorId) {
        $trail[] = ['label' => get_the_title($ancestorId), 'url' => (string) get_permalink($ancestorId)];
    }
}

$buttonBase = 'inline-flex items-center justify-center gap-2 rounded-semi px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide transition-colors';

// Image framing. 'fill' stretches the frame to the text column's height on
// desktop; below lg the columns stack, so it falls back to the comp's 5:4.
// Complete static strings so the JIT scanner picks them up.
$imageRatios = [
    'fill' => 'aspect-[5/4] lg:aspect-auto lg:self-stretch',
    '5/4'  => 'aspect-[5/4]',
    '4/3'  => 'aspect-[4/3]',
    '1/1'  => 'aspect-square',
    '16/9' => 'aspect-video',
];
$imageRatio = array_key_exists($imageRatio, $imageRatios) ? $imageRatio : 'fill';

// "card" is the framed treatment the comps use for photography. "none" drops
// the rounding and hairline ring so a logo or cut-out graphic sits directly
// on the hero background instead of looking like a pasted-in tile.
$imageFrames = [
    'card' => 'overflow-hidden rounded-card ring-1 ring-white/10',
    'none' => '',
];
$imageFrame = array_key_exists($imageFrame, $imageFrames) ? $imageFrame : 'card';

// Photography fills the frame; logos need the whole mark visible.
$imageFits = [
    'cover'   => 'object-cover',
    'contain' => 'object-contain',
];
$imageFit = array_key_exists($imageFit, $imageFits) ? $imageFit : 'cover';
@endphp

<section {{ $attributes->class(['bma-guide-hero', 'relative overflow-hidden bg-grey-900']) }} data-bma-tone="dark">
    {{-- Diagonal-line texture: the same drifting tile the homepage hero
         (bma-hero-headline) uses, at the same opacity. --}}
    <div aria-hidden="true" class="bma-hero-headline__texture pointer-events-none absolute inset-0 opacity-40"></div>
    <div aria-hidden="true" class="pointer-events-none absolute inset-y-0 left-0 w-1.5 bg-primary"></div>

    @if ($trail !== [])
        <div class="relative mx-auto max-w-content px-6 py-6 md:px-10 xl:px-16">
            <nav aria-label="{{ __('Breadcrumb', 'balefire') }}" class="font-mono text-label-m uppercase tracking-wide">
                @foreach ($trail as $crumb)
                    <a href="{{ esc_url($crumb['url']) }}" class="text-grey-300 transition-colors hover:text-white">{{ $crumb['label'] }}</a><span class="px-2 text-white/30">/</span>
                @endforeach
                <span class="text-white/90" aria-current="page">{{ $title }}</span>
            </nav>
        </div>
    @endif

    <div class="relative mx-auto grid max-w-content items-center gap-12 px-6 pb-14 pt-6 md:px-10 lg:pb-20 lg:pt-8 xl:px-16 {{ $imageUrl !== '' ? 'lg:grid-cols-[1.1fr_0.9fr]' : '' }}">
        <div class="max-w-2xl">
            @if ($eyebrow !== '')
                <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
            @endif

            @if ($title !== '')
                <h1 class="font-heading text-[clamp(2.1rem,4.6vw,3.5rem)] font-bold uppercase leading-[1.0] text-white">{{ $title }}</h1>
            @endif

            @if ($content !== '')
                <p class="mt-6 max-w-xl text-body-m text-grey-300">{!! wp_kses_post($content) !!}</p>
            @endif

            @if ($hasButtons)
                <div class="mt-8 flex flex-wrap gap-4">
                    @if ($primaryLabel !== '' && $primaryUrl !== '')
                        <a href="{{ $primaryUrl }}" class="{{ $buttonBase }} bg-primary text-white hover:bg-primary-dark">{{ $primaryLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                    @endif
                    @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                        <a href="{{ $secondaryUrl }}" class="{{ $buttonBase }} border border-white/30 text-white hover:bg-white hover:text-grey-900">{{ $secondaryLabel }}</a>
                    @endif
                </div>
            @endif
        </div>

        @if ($imageUrl !== '')
            <div class="{{ implode(' ', array_filter(['relative', $imageFrames[$imageFrame], $imageRatios[$imageRatio]])) }}">
                <img src="{{ esc_url($imageUrl) }}" alt="{{ $imageAlt }}" class="absolute inset-0 size-full {{ $imageFits[$imageFit] }}" fetchpriority="high" decoding="async" />
            </div>
        @endif
    </div>
</section>
