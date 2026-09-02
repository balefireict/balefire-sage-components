@props([
    'tone' => 'white',
    'variant' => 'tint',
    'intent' => 'info',
    'title' => '',
    'content' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
    'iconSvg' => '',
    'iconId' => 0,
])

@php
use BalefireInc\Sage\Support\SectionStyles;

// Legacy tones (accu-shot) keep their exact utility strings; the
// SectionStyles tones map through the shared surface() palette.
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

$variant = in_array($variant, ['tint', 'card', 'inline'], true) ? $variant : 'tint';
$intent = $intent === 'alert' ? 'alert' : 'info';

$ctaUrl = $ctaUrl !== '' ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : $ctaUrl) : '';
$hasCta = $ctaLabel !== '' && $ctaUrl !== '';

// Title may carry an <em> accent clause (.bma-heading styles it).
$titleAllow = ['em' => [], 'br' => [], 'span' => ['class' => true]];
$titleHtml = $title !== '' ? wp_kses($title, $titleAllow) : '';

// Icon precedence: pasted SVG, then a media-library attachment, then the
// built-in tag glyph. Pasted SVG is sanitized on output.
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
$iconId = absint($iconId);
$iconHtml = '';
if (is_string($iconSvg) && trim($iconSvg) !== '') {
    $iconHtml = wp_kses($iconSvg, $svgAllow);
}
if ($iconHtml === '' && $iconId > 0) {
    $iconHtml = (string) wp_get_attachment_image($iconId, 'thumbnail', false, ['class' => 'bma-highlight-banner__icon-img size-full object-contain', 'alt' => '', 'loading' => 'lazy']);
}
if ($iconHtml === '') {
    $iconHtml = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12V4h8l9 9-8 8z"/><circle cx="7.5" cy="7.5" r="1.5"/></svg>';
}

// tint: soft primary-tinted band (warranty exclusions). card: white card with
// a solid primary icon tile (Wright Project credit banner). On dark tones the
// tint band reads the tone map instead of primary/5.
$bandClass = $variant === 'card'
    ? 'border-grey-50 bg-white shadow-card'
    : ($isDarkTone ? $s['badge'] . ' ' . $s['border'] : 'border-primary/20 bg-primary/5');
$iconClass = $variant === 'card'
    ? 'bg-primary text-white'
    : ($isDarkTone ? 'bg-white/15 text-white' : 'bg-primary/15 text-primary');

// Card text is always on white; tint text follows the section tone.
$headingColor = ($variant === 'card' || $isLegacy) ? 'text-grey-900' : $s['heading'];
$bodyColor = ($variant === 'card' || $isLegacy) ? 'text-grey-800' : $s['body'];

$rootClasses = ['bma-highlight-banner'];
if ($intent === 'alert') {
    $rootClasses[] = 'bma-highlight-banner--alert';
}
@endphp

@if ($title !== '' || $content !== '')
    @if ($variant === 'inline')
        {{-- Compact callout row: no outer section, no band padding. Sits inside
             another block's column (steps + cost note, "not a crisis line"). --}}
        <div {{ $attributes->class(array_merge($rootClasses, ['bma-highlight-banner--inline'])) }} data-bma-intent="{{ $intent }}">
            <span class="bma-highlight-banner__icon" aria-hidden="true">{!! $iconHtml !!}</span>
            <div class="bma-highlight-banner__body">
                <p class="bma-highlight-banner__text">@if ($titleHtml !== '')<b class="bma-highlight-banner__lead">{!! $titleHtml !!}</b> @endif{!! wp_kses_post($content) !!}</p>
                @if ($hasCta)
                    <a href="{{ $ctaUrl }}" class="bma-btn bma-btn--primary bma-highlight-banner__cta">{{ $ctaLabel }}<span class="bma-btn__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                @endif
            </div>
        </div>
    @else
        <section {{ $attributes->class(array_merge($rootClasses, ['bma-band', $sectionBg])) }} data-bma-tone="{{ $tone }}">
            <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
                <div class="bma-highlight-banner__band relative overflow-hidden rounded-card border p-8 lg:p-10 {{ $bandClass }}">
                    <div class="grid items-center gap-8 lg:grid-cols-[auto_1fr_auto]">
                        <span class="bma-highlight-banner__icon grid size-14 place-items-center rounded-semi {{ $iconClass }}" aria-hidden="true"><span class="size-8">{!! $iconHtml !!}</span></span>

                        <div>
                            @if ($titleHtml !== '')
                                <h2 class="bma-heading font-heading text-2xl font-bold uppercase {{ $headingColor }}">{!! $titleHtml !!}</h2>
                            @endif
                            @if ($content !== '')
                                <p class="mt-2 max-w-2xl text-body-s {{ $bodyColor }}">{!! wp_kses_post($content) !!}</p>
                            @endif
                        </div>

                        @if ($hasCta)
                            <div class="shrink-0">
                                <a href="{{ $ctaUrl }}" class="bma-btn bma-btn--primary bma-highlight-banner__cta">{{ $ctaLabel }}<span class="bma-btn__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    @endif
@endif
