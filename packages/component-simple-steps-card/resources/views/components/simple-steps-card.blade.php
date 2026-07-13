@props([
    'iconId' => 0,
    'iconUrl' => '',
    'iconAlt' => '',
    'iconSvg' => '',
    'content' => '',
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

$iconHtml = '';
if ($iconSvg !== '') {
    // Raw SVG — sanitize on output.
    $iconHtml = wp_kses($iconSvg, [
        'svg'    => ['xmlns' => true, 'viewbox' => true, 'class' => true, 'fill' => true, 'width' => true, 'height' => true, 'role' => true, 'aria-hidden' => true],
        'path'   => ['d' => true, 'fill' => true, 'fill-rule' => true, 'clip-rule' => true, 'stroke' => true, 'stroke-width' => true, 'stroke-linecap' => true, 'stroke-linejoin' => true],
        'circle' => ['cx' => true, 'cy' => true, 'r' => true, 'fill' => true],
        'rect'   => ['x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true, 'fill' => true],
        'g'      => ['transform' => true, 'fill' => true],
        'use'    => ['href' => true, 'xlink:href' => true],
        'defs'   => [],
        'lineargradient' => ['id' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true, 'gradientunits' => true],
        'stop'   => ['offset' => true, 'stop-color' => true],
    ]);
} elseif ($iconUrl !== '') {
    $iconHtml = sprintf(
        '<img src="%s" alt="%s" title="%s" class="w-24 h-24 object-contain" />',
        esc_url($iconUrl),
        esc_attr($iconAlt),
        esc_attr($iconTitle)
    );
}
@endphp

<div {{ $attributes->class(['simple-steps-card', 'flex', 'flex-col', 'items-center', 'items-stretch', 'text-center', 'rounded-[var(--radius-card,0.5rem)]']) }}>
    @if ($iconHtml !== '')
        <div class="simple-steps-card__icon">
            {!! $iconHtml !!}
        </div>
    @endif
    <div class="simple-steps-card__content">
        {!! $content !!}
    </div>
</div>
