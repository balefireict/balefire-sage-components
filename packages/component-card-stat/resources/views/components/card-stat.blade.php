@props([
    'title' => '',
    'iconType' => 'svg',
    'iconSvgCode' => '',
    'iconId' => 0,
    'iconUrl' => '',
    'iconAlt' => '',
    'statLeftValue' => '',
    'statLeftLabel' => '',
    'statRightValue' => '',
    'statRightLabel' => '',
])

@php
$iconId = absint($iconId);

// Resolve icon from the media library when no URL is stored.
if ($iconId > 0 && $iconUrl === '') {
    $iconUrl = (string) wp_get_attachment_image_url($iconId, 'full');
}
if ($iconAlt === '' && $iconId > 0) {
    $iconAlt = (string) get_post_meta($iconId, '_wp_attachment_image_alt', true);
}
$iconTitle = ($iconId > 0) ? get_the_title($iconId) : $iconAlt;

$hasIcon = ($iconType === 'image' && $iconUrl !== '')
    || ($iconType === 'svg' && $iconSvgCode !== '');

// SVG allowlist for wp_kses.
$svgAllowlist = [
    'svg' => [
        'xmlns' => true, 'width' => true, 'height' => true, 'viewbox' => true, 'viewBox' => true,
        'fill' => true, 'stroke' => true, 'class' => true, 'aria-hidden' => true, 'aria-label' => true,
        'role' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true,
        'fill-rule' => true, 'clip-rule' => true, 'style' => true, 'id' => true,
    ],
    'path' => [
        'd' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true,
        'stroke-linecap' => true, 'stroke-linejoin' => true, 'transform' => true, 'fill-rule' => true,
        'clip-rule' => true, 'opacity' => true, 'id' => true, 'data-name' => true,
    ],
    'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true],
    'rect' => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true, 'transform' => true],
    'line' => ['x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'stroke' => true, 'class' => true, 'stroke-width' => true],
    'polygon' => ['points' => true, 'fill' => true, 'stroke' => true, 'class' => true],
    'polyline' => ['points' => true, 'fill' => true, 'stroke' => true, 'class' => true],
    'g' => ['fill' => true, 'stroke' => true, 'class' => true, 'transform' => true, 'opacity' => true, 'id' => true, 'data-name' => true],
    'defs' => true,
    'clippath' => ['id' => true],
    'lineargradient' => ['id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true],
    'stop' => ['offset' => true, 'stop-color' => true, 'stop-opacity' => true],
    'radialgradient' => ['id' => true, 'cx' => true, 'cy' => true, 'r' => true],
];
@endphp

<div {{ $attributes->class(['bma-card-stat', 'relative', 'flex', 'flex-col', 'overflow-hidden', 'rounded-[var(--radius-card,0.5rem)]', 'bg-white', 'h-full']) }}>
    @if ($title !== '' || $hasIcon)
        <div class="bma-card-stat__header flex items-end justify-between gap-4 mb-6">
            @if ($title !== '')
                <h3 class="bma-card-stat__title m-0">{{ $title }}</h3>
            @endif

            @if ($hasIcon)
                <div class="bma-card-stat__icon flex-shrink-0">
                    @if ($iconType === 'image' && $iconUrl !== '')
                        <img src="{{ esc_url($iconUrl) }}" alt="{{ $iconAlt }}" title="{{ $iconTitle }}" class="h-8 w-auto object-contain" />
                    @elseif ($iconType === 'svg' && $iconSvgCode !== '')
                        <div class="h-8 w-auto">
                            {!! wp_kses($iconSvgCode, $svgAllowlist) !!}
                        </div>
                    @endif
                </div>
            @endif
        </div>
    @endif

    <div class="bma-card-stat__stats grid grid-cols-2 gap-4 mt-auto">
        @if ($statLeftValue !== '' || $statLeftLabel !== '')
            <div class="bma-card-stat__stat">
                @if ($statLeftValue !== '')
                    <div class="bma-card-stat__value">{{ $statLeftValue }}</div>
                @endif
                @if ($statLeftLabel !== '')
                    <div class="bma-card-stat__label">{{ $statLeftLabel }}</div>
                @endif
            </div>
        @endif

        @if ($statRightValue !== '' || $statRightLabel !== '')
            <div class="bma-card-stat__stat">
                @if ($statRightValue !== '')
                    <div class="bma-card-stat__value">{{ $statRightValue }}</div>
                @endif
                @if ($statRightLabel !== '')
                    <div class="bma-card-stat__label">{{ $statRightLabel }}</div>
                @endif
            </div>
        @endif
    </div>
</div>
