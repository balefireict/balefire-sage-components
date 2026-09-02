@props([
    'tone' => 'primary',
    'lead' => '',
    'items' => [],
    'columns' => 4,
    'radius' => 'none',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
use BalefireInc\Sage\StatBand\Renderer;
use BalefireInc\Sage\Support\SectionStyles;

$tone = array_key_exists($tone, SectionStyles::tones()) ? $tone : 'primary';
$surface = SectionStyles::surface($tone);
$radius = $radius === 'card' ? 'card' : 'none';
$columns = in_array((int) $columns, [2, 3, 4], true) ? (int) $columns : 4;

// Drop rows with nothing to say.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item)
        && (trim((string) ($item['value'] ?? '')) !== '' || trim((string) ($item['label'] ?? '')) !== '')
));

$svgAllowed = Renderer::svgAllowlist();

// home_url(), not site_url(): Bedrock puts core in /wp.
$resolveUrl = fn (string $url) => $url !== ''
    ? esc_url(str_starts_with($url, '/') ? home_url($url) : $url)
    : '';
$primaryUrl = $resolveUrl(trim((string) $primaryUrl));
$secondaryUrl = $resolveUrl(trim((string) $secondaryUrl));
$hasPrimary = $primaryLabel !== '' && $primaryUrl !== '';
$hasSecondary = $secondaryLabel !== '' && $secondaryUrl !== '';
$hasButtons = $hasPrimary || $hasSecondary;

$lead = trim((string) $lead);
$isCard = $radius === 'card';
@endphp

@if ($items !== [] || $lead !== '')
    {{-- radius=none: the section IS the band (tone bg, full bleed).
         radius=card: the section stays transparent and the tone lives on a
         rounded card inside the container; data-bma-tone moves with it so
         the shared button/heading layers still see the right surface. --}}
    <section {{ $attributes->class([
        'bma-stat-band',
        'bma-band',
        'bma-stat-band--' . $radius,
        $surface['section'] => ! $isCard,
    ]) }} @if (! $isCard) data-bma-tone="{{ $tone }}" @endif>
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            <div @class([
                'bma-stat-band__band',
                'overflow-hidden rounded-card shadow-card ' . $surface['section'] => $isCard,
            ]) @if ($isCard) data-bma-tone="{{ $tone }}" @endif>
                <div class="bma-stat-band__grid" style="--bma-stat-columns: {{ $columns }}">
                    @if ($lead !== '')
                        <div class="bma-stat-band__lead text-lg leading-relaxed {{ $surface['bodyStrong'] }}">
                            {!! wp_kses_post(wpautop($lead)) !!}
                        </div>
                    @endif

                    @foreach ($items as $item)
                        @php
                            $value = trim((string) ($item['value'] ?? ''));
                            $unit = trim((string) ($item['unit'] ?? ''));
                            $label = trim((string) ($item['label'] ?? ''));
                            $iconId = absint($item['iconId'] ?? 0);
                            $iconSvg = trim((string) ($item['iconSvg'] ?? ''));
                            $hasIcon = $iconId > 0 || $iconSvg !== '';
                        @endphp
                        <div class="bma-stat-band__cell">
                            @if ($hasIcon)
                                <span class="bma-stat-band__icon {{ $surface['highlight'] }}" aria-hidden="true">
                                    @if ($iconId > 0)
                                        {!! wp_get_attachment_image($iconId, 'thumbnail', false, [
                                            'class' => 'bma-stat-band__icon-img',
                                            'alt' => '',
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    @else
                                        {!! wp_kses($iconSvg, $svgAllowed) !!}
                                    @endif
                                </span>
                            @endif

                            @if ($value !== '')
                                <span class="bma-stat-band__value font-headline {{ $surface['heading'] }}">{{ $value }}@if ($unit !== '')<span class="bma-stat-band__unit {{ $surface['highlight'] }}">{{ $unit }}</span>@endif</span>
                            @endif

                            @if ($label !== '')
                                <span class="bma-stat-band__label {{ $surface['body'] }}">{{ $label }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>

                @if ($hasButtons && ! $isCard)
                    <div class="bma-stat-band__actions mt-8 flex flex-wrap justify-center gap-3">
                        @if ($hasPrimary)
                            <a href="{{ $primaryUrl }}" class="bma-btn bma-btn--primary">{{ $primaryLabel }}</a>
                        @endif
                        @if ($hasSecondary)
                            <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}</a>
                        @endif
                    </div>
                @endif
            </div>

            @if ($hasButtons && $isCard)
                {{-- Card mode: buttons sit on the page surface below the card. --}}
                <div class="bma-stat-band__actions mt-7 flex flex-wrap justify-center gap-3 text-dark">
                    @if ($hasPrimary)
                        <a href="{{ $primaryUrl }}" class="bma-btn bma-btn--primary">{{ $primaryLabel }}</a>
                    @endif
                    @if ($hasSecondary)
                        <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}</a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endif
