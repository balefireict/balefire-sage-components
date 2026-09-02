@props([
    'tone' => 'white',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'items' => [],
    'listColumns' => 1,
    'ratio' => 'even',
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
    'panelIcon' => '',
    'panelTitle' => '',
    'panelText' => '',
    'panelCtaLabel' => '',
    'panelCtaUrl' => '',
    'mediaContent' => '',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

// Legacy tones (accu-shot) keep their exact utility strings so existing pages
// render byte-for-byte; the SectionStyles tones map through the shared
// surface() palette.
$legacyTones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = is_string($tone) ? $tone : 'white';
$isLegacy = array_key_exists($tone, $legacyTones);
if (!$isLegacy && !array_key_exists($tone, SectionStyles::tones())) {
    $tone = 'white';
    $isLegacy = true;
}
$s = SectionStyles::surface($isLegacy ? 'white' : $tone);
$isDarkTone = in_array($tone, ['primary', 'dark', 'accent'], true);

$sectionBg = $isLegacy ? $legacyTones[$tone] : $s['section'];
$headingColor = $isLegacy ? 'text-grey-900' : $s['heading'];
$bodyColor = $isLegacy ? '' : $s['body'];
$eyebrowColor = $isLegacy ? '' : $s['eyebrow'];

$mediaSide = $mediaSide === 'left' ? 'left' : 'right';
$mediaType = in_array($mediaType, ['image', 'stat', 'content', 'panel'], true) ? $mediaType : 'content';
$ratio = in_array($ratio, ['even', '60-40', '40-60'], true) ? $ratio : 'even';
$listColumns = (int) $listColumns === 2 ? 2 : 1;

// Checklist items: plain strings; light inline markup only.
$items = is_array($items) ? array_values(array_filter(array_map(static fn($i) => is_string($i) ? trim($i) : '', $items), static fn($i) => $i !== '')) : [];
$itemAllow = ['b' => [], 'strong' => [], 'em' => [], 'br' => [], 'a' => ['href' => true, 'target' => true, 'rel' => true]];

// Title may carry an <em> accent clause (.bma-heading styles it).
$titleAllow = ['em' => [], 'br' => [], 'span' => ['class' => true]];
$titleHtml = $title !== '' ? wp_kses($title, $titleAllow) : '';

$imageId = absint($imageId);
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'large');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}

$normalizeUrl = static fn(string $u): string => $u !== '' ? esc_url(str_starts_with($u, '/') ? home_url($u) : $u) : '';
$primaryUrl = $normalizeUrl((string) $primaryUrl);
$secondaryUrl = $normalizeUrl((string) $secondaryUrl);
$panelCtaUrl = $normalizeUrl((string) $panelCtaUrl);
$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');
$hasPanel = $mediaType === 'panel' && ($panelTitle !== '' || $panelText !== '' || $panelIcon !== '');
$hasPanelCta = $panelCtaLabel !== '' && $panelCtaUrl !== '';

// Existing (accu-shot) buttons keep their utility strings; the panel CTA is
// new and uses the shared .bma-btn layer.
$buttonBase = 'inline-flex items-center justify-center gap-2 rounded-semi px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide transition-colors';

// Pasted SVG allowlist (same vocabulary as component-card-stat).
$svgAllow = [
    'svg' => ['xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'viewBox' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'aria-hidden' => true, 'role' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'fill-rule' => true, 'clip-rule' => true],
    'path' => ['d' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true, 'transform' => true, 'fill-rule' => true, 'clip-rule' => true, 'opacity' => true],
    'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true],
    'ellipse' => ['cx' => true, 'cy' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true],
    'rect' => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true, 'transform' => true],
    'line' => ['x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true],
    'polygon' => ['points' => true, 'fill' => true, 'stroke' => true, 'class' => true],
    'polyline' => ['points' => true, 'fill' => true, 'stroke' => true, 'class' => true],
    'g' => ['fill' => true, 'stroke' => true, 'class' => true, 'transform' => true, 'opacity' => true],
];
$panelIconHtml = $panelIcon !== '' ? wp_kses($panelIcon, $svgAllow) : '';

// Column tracks are visual left/right (mediaSide swaps with `order`, so the
// tracks stay put). "even" keeps the original behaviour: nested content
// (tables) gets the wider column, everything else splits 50/50.
$gridCols = match ($ratio) {
    '60-40' => 'lg:grid-cols-[1.1fr_0.9fr]',
    '40-60' => 'lg:grid-cols-[0.9fr_1.1fr]',
    default => $mediaType === 'content' ? 'lg:grid-cols-[0.9fr_1.1fr]' : 'lg:grid-cols-2',
};
$alignment = $mediaType === 'content' ? 'items-start' : 'items-center';

// Panel tint lives in blocks/split-feature/style.css (.bma-split-feature__panel,
// tone-aware via [data-bma-tone]); only the border reads the tone map here.
$panelIconDisc = $isDarkTone ? 'bg-white text-dark' : 'bg-primary text-white';
@endphp

<section {{ $attributes->class(['bma-split-feature', 'bma-band', $sectionBg]) }} data-bma-tone="{{ $tone }}">
    <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
        <div class="grid gap-12 {{ $alignment }} {{ $gridCols }}">
            <div @class(['order-1 lg:order-2' => $mediaSide === 'left'])>
                @if ($eyebrow !== '')
                    {{-- Shared lockup — balefireict/component-eyebrow, the home-page eyebrow style. --}}
                    <x-bma::eyebrow :text="$eyebrow" :class="trim('mb-4 ' . $eyebrowColor)" />
                @endif

                @if ($titleHtml !== '')
                    <h2 class="bma-heading font-heading text-[clamp(2rem,4vw,2.6rem)] font-bold uppercase leading-tight {{ $headingColor }}">{!! $titleHtml !!}</h2>
                @endif

                @if ($content !== '')
                    <div class="bma-prose mt-5 {{ $bodyColor }}">{!! wp_kses_post(wpautop($content)) !!}</div>
                @endif

                @if ($items !== [])
                    <ul @class(['bma-checklist', 'bma-checklist--cols' => $listColumns === 2, 'mt-6', $isLegacy ? '' : $s['bodyStrong']])>
                        @foreach ($items as $item)
                            <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg><span>{!! wp_kses($item, $itemAllow) !!}</span></li>
                        @endforeach
                    </ul>
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
                @elseif ($hasPanel)
                    {{-- Soft panel: tinted card with icon disc, title, copy and one CTA. --}}
                    <div class="bma-split-feature__panel self-start rounded-card border p-7 lg:p-8 {{ $s['border'] }}">
                        @if ($panelIconHtml !== '')
                            <span class="bma-split-feature__panel-icon mb-4 grid size-[52px] place-items-center rounded-full {{ $panelIconDisc }}" aria-hidden="true">{!! $panelIconHtml !!}</span>
                        @endif
                        @if ($panelTitle !== '')
                            <h3 class="font-headline text-xl font-semibold leading-snug {{ $s['heading'] }}">{{ $panelTitle }}</h3>
                        @endif
                        @if ($panelText !== '')
                            <div class="mt-2 text-body-m leading-relaxed {{ $s['body'] }}">{!! wp_kses_post(wpautop($panelText)) !!}</div>
                        @endif
                        @if ($hasPanelCta)
                            <div class="mt-5"><a href="{{ $panelCtaUrl }}" class="bma-btn bma-btn--primary">{{ $panelCtaLabel }}<span class="bma-btn__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a></div>
                        @endif
                    </div>
                @elseif ($mediaType === 'content' && $mediaContent !== '')
                    <div class="bma-split-feature__content overflow-hidden rounded-card border border-grey-50 bg-white shadow-card">{!! $mediaContent !!}</div>
                @endif
            </div>
        </div>
    </div>
</section>
