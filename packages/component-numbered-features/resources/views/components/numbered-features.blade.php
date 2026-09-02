@props([
    'eyebrow' => 'The B&T Difference',
    'eyebrowVariant' => 'marks',
    'title' => '',
    'titleAccent' => '',
    'content' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
    'items' => [],
    'layout' => 'stack',
    'tone' => 'white',
])

@php
use BalefireInc\Sage\NumberedFeatures\Icons;
use BalefireInc\Sage\Support\SectionStyles;

// Drop rows with nothing to say — an empty repeater row would render a bare number.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item) && trim((string) ($item['title'] ?? '')) !== ''
));

// "stack" is the original layout and the default; "split" is the editorial
// index (intro column beside a list of rows).
$layout = $layout === 'split' ? 'split' : 'stack';

$tone = array_key_exists((string) $tone, SectionStyles::tones()) ? (string) $tone : 'white';
$surface = SectionStyles::surface($tone);
// The stack layout on a white tone must keep the exact classes it shipped
// with; every other combination reads its colors from the tone map.
$isLegacySurface = $tone === 'white';

$eyebrow = trim((string) $eyebrow);
$eyebrowVariant = in_array($eyebrowVariant, ['marks', 'bar', 'plain'], true) ? $eyebrowVariant : 'marks';
$titleAccent = trim((string) $titleAccent);
$content = trim((string) $content);

// home_url(), not site_url(): Bedrock puts core in /wp.
$resolveUrl = static function ($url): string {
    $url = trim((string) $url);
    if ($url === '') {
        return '';
    }
    return esc_url(str_starts_with($url, '/') ? home_url($url) : $url);
};

// The single legacy CTA is the primary button until the new attributes are set.
$primaryLabel = trim((string) $primaryLabel) !== '' ? trim((string) $primaryLabel) : trim((string) $ctaLabel);
$primaryUrl = $resolveUrl(trim((string) $primaryUrl) !== '' ? $primaryUrl : $ctaUrl);
$secondaryLabel = trim((string) $secondaryLabel);
$secondaryUrl = $resolveUrl($secondaryUrl);
$hasPrimary = $primaryLabel !== '' && $primaryUrl !== '';
$hasSecondary = $secondaryLabel !== '' && $secondaryUrl !== '';

// Per-row accent: a CSS color or var(--token). Anything else is dropped so
// the style attribute can never carry a second declaration.
$accentStyle = static function ($color): string {
    $color = trim((string) $color);
    if ($color === '' || ! preg_match('/^[A-Za-z0-9#(),.%\s\/-]+$/', $color)) {
        return '';
    }
    return '--item-accent: ' . $color . ';';
};

$arrow = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M5 12h14M13 6l6 6-6 6"/></svg>';
@endphp

@if ($items !== [])
    @if ($layout === 'split')
        {{-- ============================================================
             Split: intro column (5/12) beside an index of rows (7/12).
             Row geometry lives in blocks/numbered-features/style.css;
             colors come from the tone map and each row's --item-accent.
             ============================================================ --}}
        @php
            $titleHtml = wp_kses((string) $title, ['em' => [], 'strong' => [], 'br' => []]);
        @endphp
        <section {{ $attributes->class([
            'bma-numbered-features bma-numbered-features--split bma-band',
            $surface['section'],
            'px-6 md:px-10 xl:px-16',
        ]) }} data-bma-tone="{{ $tone }}">
            <div class="bma-numbered-features__grid mx-auto w-full max-w-content">
                <div class="bma-numbered-features__intro">
                    @if ($eyebrow !== '')
                        <x-bma::eyebrow :text="$eyebrow" :variant="$eyebrowVariant" :color="$surface['eyebrow']" />
                    @endif

                    @if ($titleHtml !== '' || $titleAccent !== '')
                        <h2 class="bma-heading bma-numbered-features__title mt-4 text-[clamp(2rem,3.6vw,2.8rem)] {{ $surface['heading'] }}">{!! $titleHtml !!}@if ($titleAccent !== '') <em>{{ $titleAccent }}</em>@endif</h2>
                    @endif

                    @if ($content !== '')
                        <p class="bma-numbered-features__lead mt-4 text-[1.2rem] leading-[1.55] {{ $surface['bodyStrong'] }}">{!! wp_kses_post($content) !!}</p>
                    @endif

                    @if ($hasPrimary || $hasSecondary)
                        <div class="bma-numbered-features__actions mt-6 flex flex-wrap gap-3">
                            @if ($hasPrimary)
                                <a href="{{ $primaryUrl }}" class="bma-btn bma-btn--primary">{{ $primaryLabel }}<span class="bma-btn__icon">{!! $arrow !!}</span></a>
                            @endif
                            @if ($hasSecondary)
                                <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}</a>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="bma-numbered-features__rows border-t {{ $surface['border'] }}">
                    @foreach ($items as $i => $item)
                        @php
                            $number = sprintf('%02d', $i + 1);
                            $iconSvg = Icons::forItem($item);
                            $url = $resolveUrl($item['url'] ?? '');
                            $text = trim((string) ($item['text'] ?? ''));
                            $style = $accentStyle($item['accentColor'] ?? '');
                            $tag = $url !== '' ? 'a' : 'div';
                        @endphp
                        <{{ $tag }}
                            @if ($url !== '') href="{{ $url }}" @endif
                            class="bma-numbered-features__row border-b {{ $surface['border'] }}"
                            @if ($style !== '') style="{{ $style }}" @endif
                        >
                            <span class="bma-numbered-features__num" aria-hidden="true">{{ $number }}</span>

                            @if ($iconSvg !== '')
                                <span class="bma-numbered-features__icon" aria-hidden="true">{!! $iconSvg !!}</span>
                            @endif

                            <div class="bma-numbered-features__body">
                                <h3 class="bma-numbered-features__name {{ $surface['heading'] }}">{{ $item['title'] }}</h3>
                                @if ($text !== '')
                                    <p class="bma-numbered-features__desc {{ $surface['body'] }}">{{ $text }}</p>
                                @endif
                            </div>

                            @if ($url !== '')
                                <span class="bma-numbered-features__arrow" aria-hidden="true">{!! $arrow !!}</span>
                            @endif
                        </{{ $tag }}>
                    @endforeach
                </div>
            </div>
        </section>
    @else
        {{-- ============================================================
             Stack: header above a 2-up hover grid (the original layout).
             ============================================================ --}}
        @php
            $headingClass = $isLegacySurface ? 'text-grey-800' : $surface['heading'];
            $bodyClass = $isLegacySurface ? 'text-grey-400' : $surface['body'];
            $itemTextClass = $isLegacySurface ? 'text-grey-400' : $surface['body'];
        @endphp
        <section {{ $attributes->class([
            'bma-numbered-features bma-numbered-features--stack',
            $surface['section'],
            'px-6 py-12 lg:px-30 lg:py-20',
        ]) }} data-bma-tone="{{ $tone }}">
            <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-16">
                {{-- Header: copy left, CTA right --}}
                <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-16">
                    <div class="flex flex-1 flex-col items-start gap-2.5">
                        <x-bma::eyebrow :text="$eyebrow" :variant="$eyebrowVariant" :color="$isLegacySurface ? 'text-primary' : $surface['eyebrow']" />

                        @if ($title !== '')
                            <h2 class="font-heading text-3xl font-semibold uppercase leading-tight {{ $headingClass }} lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
                                {{ $title }}
                            </h2>
                        @endif

                        @if ($content !== '')
                            <p class="text-base leading-6 {{ $bodyClass }}">
                                {{ $content }}
                            </p>
                        @endif
                    </div>

                    {{-- A lone primary keeps the markup it always had; the wrapper
                         only appears once a secondary button needs to sit beside it. --}}
                    @if ($hasSecondary)
                        <div class="flex shrink-0 flex-wrap items-center gap-3 self-start lg:self-auto">
                    @endif
                        @if ($hasPrimary)
                            <a
                                href="{{ $primaryUrl }}"
                                @class([
                                    'group inline-flex h-14 shrink-0 items-center justify-center gap-2 rounded bg-grey-400 px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-grey-800',
                                    'self-start lg:self-auto' => ! $hasSecondary,
                                ])
                            >
                                {{ $primaryLabel }}
                                {{-- [&_svg], never [&>svg]: a literal ">" in a class attribute
                                     breaks WordPress's the_content filters. --}}
                                <span class="block size-6 shrink-0 transition-transform group-hover:translate-x-0.5 [&_svg]:size-full">
                                    <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z" />
                                    </svg>
                                </span>
                            </a>
                        @endif
                        @if ($hasSecondary)
                            <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}</a>
                        </div>
                    @endif
                </div>

                {{-- 2-up grid. No gap: the hover background IS the card, so the cards
                     sit flush and the grey panel appears under whichever one you point at. --}}
                <div class="grid grid-cols-1 lg:grid-cols-2">
                    @foreach ($items as $i => $item)
                        @php
                            $number = sprintf('%02d', $i + 1);
                            $imageId = absint($item['imageId'] ?? 0);
                            $url = $resolveUrl($item['url'] ?? '');

                            // An item with no icon keeps its numeral, so existing
                            // content is unchanged by the picker arriving. A custom
                            // inline SVG wins over the bundled slug.
                            $iconSvg = Icons::forItem($item);
                        @endphp

                        {{-- `group` drives the reveal, desktop only (lg:): touch
                             screens rest on the number and stay there. focus-within
                             mirrors hover for keyboard users. --}}
                        <div class="group relative flex gap-8 rounded-semi p-8 transition-colors duration-300 lg:hover:bg-grey-25 lg:focus-within:bg-grey-25 motion-reduce:transition-none">
                            {{-- Media slot. At rest it is just wide enough for "02";
                                 on hover (lg+, when an image is set) it grows to the
                                 comp's 142px and the image cross-fades in. --}}
                            <div @class([
                                'relative flex w-10 shrink-0 items-stretch transition-[width] duration-300 ease-out motion-reduce:transition-none',
                                'lg:group-hover:w-[142px] lg:group-focus-within:w-[142px]' => $imageId > 0,
                            ])>
                                <span
                                    aria-hidden="true"
                                    @class([
                                        'transition-opacity duration-200 motion-reduce:transition-none',
                                        $headingClass,
                                        'font-mono text-2xl font-bold leading-8' => $iconSvg === '',
                                        'block size-8 shrink-0' => $iconSvg !== '',
                                        'lg:group-hover:opacity-0 lg:group-focus-within:opacity-0' => $imageId > 0,
                                    ])
                                >@if ($iconSvg !== ''){!! $iconSvg !!}@else{{ $number }}@endif</span>

                                @if ($imageId > 0)
                                    {{-- Decorative: the heading beside it already carries the
                                         meaning, and the image only exists on hover. --}}
                                    <span
                                        aria-hidden="true"
                                        class="pointer-events-none absolute inset-0 scale-95 opacity-0 transition-all duration-300 ease-out lg:group-hover:scale-100 lg:group-hover:opacity-100 lg:group-focus-within:scale-100 lg:group-focus-within:opacity-100 motion-reduce:transition-none"
                                    >
                                        {!! wp_get_attachment_image($imageId, 'medium', false, [
                                            'class' => 'size-full rounded-semi object-cover',
                                            'alt' => '',
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    </span>
                                @endif
                            </div>

                            <div class="flex min-w-0 flex-1 flex-col gap-4">
                                <h3 class="font-heading text-2xl font-bold leading-8 {{ $headingClass }}">
                                    @if ($url !== '')
                                        <a href="{{ $url }}" class="no-underline hover:underline {{ $headingClass }}">{{ $item['title'] }}</a>
                                    @else
                                        {{ $item['title'] }}
                                    @endif
                                </h3>

                                @if (trim((string) ($item['text'] ?? '')) !== '')
                                    <p class="text-body-xs {{ $itemTextClass }}">
                                        {{ $item['text'] }}
                                    </p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>
    @endif
@endif
