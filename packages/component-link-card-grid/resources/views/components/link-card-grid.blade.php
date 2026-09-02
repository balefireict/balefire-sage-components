@props([
    'tone' => 'grey',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'ctaLabel' => 'Read the guide',
    'columns' => 3,
    'cardStyle' => 'linked',
    'textAlign' => 'left',
    'iconStyle' => 'disc',
    'items' => [],
])

@php
use BalefireInc\Sage\Support\SectionStyles;

// --- Tone -------------------------------------------------------------
// 'grey' and 'white' are the original (accu-shot) tones and keep their exact
// classes. Every other value goes through the shared SectionStyles map.
$legacyTones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = is_string($tone) ? $tone : 'grey';
if (! array_key_exists($tone, $legacyTones) && ! array_key_exists($tone, SectionStyles::tones())) {
    $tone = 'grey';
}
if (array_key_exists($tone, $legacyTones)) {
    $sectionClass = $legacyTones[$tone];
    $headingClass = 'text-grey-900';
    $bodyClass    = 'text-grey-800';
} else {
    $surface      = SectionStyles::surface($tone);
    $sectionClass = $surface['section'];
    $headingClass = $surface['heading'];
    $bodyClass    = $surface['bodyStrong'];
}

// --- Block-level options ----------------------------------------------
$columns   = in_array((int) $columns, [2, 3, 4], true) ? (int) $columns : 3;
$gridCols  = match ($columns) {
    2 => 'sm:grid-cols-2',
    4 => 'sm:grid-cols-2 lg:grid-cols-4',
    default => 'sm:grid-cols-2 lg:grid-cols-3',
};
$cardStyle = in_array($cardStyle, ['linked', 'plain'], true) ? $cardStyle : 'linked';
$textAlign = in_array($textAlign, ['left', 'center'], true) ? $textAlign : 'left';
$iconStyle = in_array($iconStyle, ['disc', 'plain'], true) ? $iconStyle : 'disc';
$center    = $textAlign === 'center';
$hasHeader = $eyebrow !== '' || $title !== '' || $content !== '';

// --- Inline SVG allowlist (iconSvg) ------------------------------------
// Strict: shapes and their geometry/paint attributes only. wp_kses matches
// attribute names case-insensitively, so lowercase keys cover viewBox.
$paint = ['fill', 'stroke', 'stroke-width', 'stroke-linecap', 'stroke-linejoin', 'fill-rule', 'clip-rule', 'opacity', 'transform', 'class'];
$svgAllowed = [
    'svg'      => array_fill_keys(array_merge($paint, ['xmlns', 'viewbox', 'width', 'height', 'aria-hidden', 'role', 'focusable']), true),
    'g'        => array_fill_keys($paint, true),
    'path'     => array_fill_keys(array_merge($paint, ['d']), true),
    'circle'   => array_fill_keys(array_merge($paint, ['cx', 'cy', 'r']), true),
    'rect'     => array_fill_keys(array_merge($paint, ['x', 'y', 'width', 'height', 'rx', 'ry']), true),
    'line'     => array_fill_keys(array_merge($paint, ['x1', 'y1', 'x2', 'y2']), true),
    'polyline' => array_fill_keys(array_merge($paint, ['points']), true),
    'polygon'  => array_fill_keys(array_merge($paint, ['points']), true),
];

// --- Items ------------------------------------------------------------
// Normalise every item to the full field set. A card needs a label, or an
// image (label-less image items render as a card-fill image tile).
$items = array_values(array_filter(array_map(static function ($item) {
    if (! is_array($item)) {
        return null;
    }

    // Any CSS colour expression: hex, rgb()/hsl(), a named colour, or a
    // var(--token). Strip anything that could break out of the style attr.
    $accent = trim((string) ($item['accentColor'] ?? ''));
    $accent = substr((string) preg_replace('/[^A-Za-z0-9#(),.%\s\/-]/', '', $accent), 0, 64);

    $normalised = [
        'label'       => trim((string) ($item['label'] ?? '')),
        'url'         => trim((string) ($item['url'] ?? '')),
        'description' => trim((string) ($item['description'] ?? '')),
        'meta'        => trim((string) ($item['meta'] ?? '')),
        'linkLabel'   => trim((string) ($item['linkLabel'] ?? '')),
        'iconSvg'     => trim((string) ($item['iconSvg'] ?? '')),
        'iconId'      => max(0, (int) ($item['iconId'] ?? 0)),
        'imageId'     => max(0, (int) ($item['imageId'] ?? 0)),
        'accentColor' => trim($accent),
    ];

    return ($normalised['label'] !== '' || $normalised['imageId'] > 0) ? $normalised : null;
}, is_array($items) ? $items : []), static fn ($item): bool => $item !== null));

// Any card with a description (or any of the newer fields, or no URL)
// switches the whole grid to the richer "chooser" card (title, blurb, CTA
// footer) per the mount-hub comps. Plain style and centred text always use it.
$rich = $cardStyle === 'plain' || $center;
foreach ($items as $item) {
    if (
        $item['description'] !== '' || $item['meta'] !== '' || $item['iconSvg'] !== ''
        || $item['iconId'] > 0 || $item['imageId'] > 0 || $item['accentColor'] !== '' || $item['url'] === ''
    ) {
        $rich = true;
        break;
    }
}

$resolveUrl = static function (string $url): string {
    if ($url === '') {
        return '';
    }
    return esc_url(str_starts_with($url, '/') ? home_url($url) : $url);
};

$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-link-card-grid', 'bma-band', $sectionClass]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            @if ($hasHeader)
                <div @class(['max-w-3xl', 'mx-auto text-center' => $center])>
                    @if ($eyebrow !== '')
                        {{-- Shared lockup — balefireict/component-eyebrow, the home-page eyebrow style. --}}
                        <x-bma::eyebrow :text="$eyebrow" :class="$center ? 'mb-4 justify-center' : 'mb-4'" />
                    @endif

                    @if ($title !== '')
                        {{-- .bma-heading owns font/weight/transform via theme tokens; an <em> is the accent clause. --}}
                        <h2 class="bma-heading text-[clamp(1.8rem,3.6vw,2.5rem)] leading-[1.05] {{ $headingClass }}">{!! wp_kses($title, ['em' => [], 'strong' => [], 'br' => []]) !!}</h2>
                    @endif

                    @if ($content !== '')
                        <p class="mt-5 max-w-3xl text-body-m {{ $bodyClass }}">{!! wp_kses_post($content) !!}</p>
                    @endif
                </div>
            @endif

            <div @class(['mt-8' => $hasHeader])>
                <div class="grid {{ $rich ? 'gap-6' : 'gap-4' }} {{ $gridCols }}">
                    @foreach ($items as $item)
                        @php
                            $href   = $resolveUrl($item['url']);
                            $isLink = $href !== '';
                            $isTel  = str_starts_with(strtolower($item['url']), 'tel:');
                        @endphp

                        @if ($rich)
                            @php
                                $tag  = $isLink ? 'a' : 'div';
                                $fill = $item['imageId'] > 0 && $item['label'] === '';

                                // Icon: pasted SVG wins over a media-library image.
                                $iconHtml = '';
                                if ($item['iconSvg'] !== '') {
                                    $iconHtml = wp_kses($item['iconSvg'], $svgAllowed);
                                } elseif ($item['iconId'] > 0) {
                                    $iconSrc = wp_get_attachment_image_url($item['iconId'], 'thumbnail');
                                    if ($iconSrc) {
                                        $iconHtml = '<img src="' . esc_url($iconSrc) . '" alt="" loading="lazy" decoding="async">';
                                    }
                                }

                                // Image: card-fill when it is the whole card, card-top otherwise.
                                $imageHtml = '';
                                if ($item['imageId'] > 0) {
                                    $imageHtml = (string) wp_get_attachment_image($item['imageId'], $fill ? 'large' : 'medium_large', false, [
                                        'class'   => $fill ? 'bma-link-card-grid__image bma-link-card-grid__image--fill' : 'bma-link-card-grid__image bma-link-card-grid__image--top',
                                        'loading' => 'lazy',
                                    ]);
                                }

                                $showCta = $cardStyle === 'linked' && $isLink && ! $fill;
                                $ctaText = $item['linkLabel'] !== '' ? $item['linkLabel'] : ($isTel ? trim(substr($item['url'], 4)) : $ctaLabel);

                                $open = '<' . $tag;
                                if ($isLink) {
                                    $open .= ' href="' . $href . '"';
                                }
                                if ($item['accentColor'] !== '') {
                                    $open .= ' style="' . esc_attr('--card-accent: ' . $item['accentColor']) . '"';
                                }
                            @endphp

                            {!! $open !!} @class([
                                'bma-link-card-grid__card group flex flex-col rounded-card border border-grey-50 bg-white shadow-card transition-all duration-200 hover:-translate-y-1 hover:border-primary/40',
                                'p-6' => ! $fill,
                                'overflow-hidden' => $imageHtml !== '',
                                'items-center text-center bma-link-card-grid__card--center' => $center,
                                'bma-link-card-grid__card--fill' => $fill,
                                'bma-link-card-grid__card--accent' => $item['accentColor'] !== '',
                            ])>
                                @if ($imageHtml !== '')
                                    {!! $imageHtml !!}
                                @endif

                                @if (! $fill)
                                    @if ($iconHtml !== '')
                                        <span class="bma-link-card-grid__icon bma-link-card-grid__icon--{{ $iconStyle }}" aria-hidden="true">{!! $iconHtml !!}</span>
                                    @endif

                                    <h3 @class(['bma-heading text-xl text-grey-900', 'transition-colors group-hover:text-primary' => $isLink])>{{ $item['label'] }}</h3>

                                    @if ($item['description'] !== '')
                                        <p class="mt-2 flex-1 text-body-s text-grey-800">{{ $item['description'] }}</p>
                                    @endif

                                    @if ($item['meta'] !== '')
                                        <p class="bma-link-card-grid__meta mt-2 text-sm text-muted">{{ $item['meta'] }}</p>
                                    @endif

                                    @if ($showCta)
                                        <span @class(['bma-link-card-grid__cta mt-4 inline-flex items-center gap-2 text-body-s', 'bma-link-card-grid__cta--tel' => $isTel])>{{ $ctaText }}@if (! $isTel) <span class="size-[18px]">{!! $arrow !!}</span>@endif</span>
                                    @endif
                                @endif
                            </{{ $tag }}>
                        @else
                            <a href="{{ $href }}" class="group flex items-center justify-between gap-3 rounded-card border border-grey-50 bg-white p-5 transition-colors hover:border-primary/40">
                                <span class="font-heading text-body-s font-bold uppercase text-grey-800 transition-colors group-hover:text-primary">{{ $item['label'] }}</span>
                                <span class="size-4 shrink-0 text-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
