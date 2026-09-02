@props([
    'tone' => 'primary',
    'title' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'primaryStyle' => 'cta',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
    'tertiaryLabel' => '',
    'tertiaryUrl' => '',
    'tertiaryIconSvg' => '',
    'showMotif' => false,
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$tone = in_array($tone, ['primary', 'dark', 'accent', 'secondary'], true) ? $tone : 'primary';
$s = SectionStyles::surface($tone);

// "primary" keeps the original accu-shot utility strings verbatim; the other
// tones read the shared surface() palette.
$sectionBg = $tone === 'primary' ? 'bg-primary' : $s['section'];
$headingColor = $tone === 'primary' ? 'text-white' : $s['heading'];
$bodyColor = $tone === 'primary' ? 'text-white/90' : $s['body'];

$primaryStyle = $primaryStyle === 'dark' ? 'dark' : 'cta';
$primaryBtn = $primaryStyle === 'dark' ? 'bma-btn bma-btn--dark' : 'bma-btn bma-btn--primary';

// Block attributes and shortcode atts both arrive as strings ("1", "false", "").
$showMotif = filter_var($showMotif, FILTER_VALIDATE_BOOL);

$normalizeUrl = static fn(string $u): string => $u !== '' ? esc_url(str_starts_with($u, '/') ? home_url($u) : $u) : '';
$primaryUrl = $normalizeUrl((string) $primaryUrl);
$secondaryUrl = $normalizeUrl((string) $secondaryUrl);
$tertiaryUrl = $normalizeUrl((string) $tertiaryUrl);
$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');
$hasTertiary = $tertiaryLabel !== '' && $tertiaryUrl !== '';

// Title may carry an <em> accent clause (.bma-heading styles it; on dark
// tones the layer turns the em white).
$titleAllow = ['em' => [], 'br' => [], 'span' => ['class' => true]];
$titleHtml = $title !== '' ? wp_kses($title, $titleAllow) : '';

// Pasted SVG for the pill link, sanitized on output.
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
$tertiaryIconHtml = (is_string($tertiaryIconSvg) && trim($tertiaryIconSvg) !== '') ? wp_kses($tertiaryIconSvg, $svgAllow) : '';
@endphp

<section {{ $attributes->class(['bma-cta-band', 'bma-band', $sectionBg, 'relative overflow-hidden' => $showMotif]) }} data-bma-tone="{{ $tone }}">
    @if ($showMotif)
        {{-- Decorative motif: soft rings and a wave, drawn in the band's text
             colour at low opacity (blocks/cta-band/style.css). --}}
        <svg class="bma-cta-band__motif" viewBox="0 0 320 320" fill="none" stroke="currentColor" stroke-width="6" stroke-linecap="round" aria-hidden="true" focusable="false">
            <circle cx="150" cy="150" r="140" />
            <circle cx="150" cy="150" r="96" />
            <circle cx="150" cy="150" r="52" />
            <path d="M-10 236c40-38 80-38 120 0s80 38 120 0 80-38 120 0" />
        </svg>
    @endif

    <div @class(['mx-auto max-w-content px-6 text-center md:px-10 xl:px-16', 'relative' => $showMotif])>
        @if ($titleHtml !== '')
            <h2 class="bma-heading mx-auto max-w-3xl font-heading text-[clamp(1.8rem,3.6vw,2.5rem)] leading-tight {{ $headingColor }}">{!! $titleHtml !!}</h2>
        @endif

        @if ($content !== '')
            <p class="mx-auto mt-4 max-w-2xl text-body-m {{ $bodyColor }}">{!! wp_kses_post($content) !!}</p>
        @endif

        @if ($hasButtons)
            <div class="mt-8 flex flex-wrap justify-center gap-4">
                @if ($primaryLabel !== '' && $primaryUrl !== '')
                    <a href="{{ $primaryUrl }}" class="{{ $primaryBtn }}">{{ $primaryLabel }}</a>
                @endif
                @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                    <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}<span class="bma-btn__icon"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                @endif
            </div>
        @endif

        @if ($hasTertiary)
            <div class="mt-6">
                <a href="{{ $tertiaryUrl }}" class="bma-cta-band__chip">
                    @if ($tertiaryIconHtml !== '')
                        <span class="bma-cta-band__chip-icon" aria-hidden="true">{!! $tertiaryIconHtml !!}</span>
                    @endif
                    <span>{{ $tertiaryLabel }}</span>
                </a>
            </div>
        @endif
    </div>
</section>
