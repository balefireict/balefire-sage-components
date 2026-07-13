@props([
    'iconId' => 0,
    'iconUrl' => '',
    'iconAlt' => '',
    'iconSvg' => '',
    'url' => '',
    'linkType' => 'none',
    'pageId' => 0,
    'openInNewTab' => false,
    'content' => '',
])

@php
$iconId = absint($iconId);
$pageId = absint($pageId);

// Resolve icon from the media library when no URL is stored.
if ($iconId > 0 && $iconUrl === '') {
    $iconUrl = (string) wp_get_attachment_image_url($iconId, 'full');
}
if ($iconAlt === '' && $iconId > 0) {
    $iconAlt = (string) get_post_meta($iconId, '_wp_attachment_image_alt', true);
}
$iconTitle = ($iconId > 0) ? get_the_title($iconId) : $iconAlt;

// Resolve link (never link-wrap in the editor context).
$isEditor = defined('REST_REQUEST') && REST_REQUEST;
$linkHref = '';
$isExternal = false;
if (! $isEditor) {
    if ($linkType === 'external' && $url !== '') {
        $linkHref = $url;
        $isExternal = true;
    } elseif ($linkType === 'page' && $pageId > 0) {
        $pageUrl = get_permalink($pageId);
        if ($pageUrl !== false) {
            $linkHref = $pageUrl;
        }
    }
}

$hasLink = $linkHref !== '';
$tag = $hasLink ? 'a' : 'div';

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
        '<img src="%s" alt="%s" title="%s" class="w-10 h-10 object-contain" />',
        esc_url($iconUrl),
        esc_attr($iconAlt),
        esc_attr($iconTitle)
    );
}
@endphp

<{{ $tag }}
    {{ $attributes->class(['bma-simple-icon-stacked-card', 'overflow-hidden', 'rounded-[var(--radius-card,0.5rem)]', 'bg-white', 'border', 'border-gray-200', 'p-[var(--spacing-card,1.5rem)]']) }}
    @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
>
    <div class="bma-simple-icon-stacked-card__content flex flex-col">
        @if ($iconHtml !== '')
            <div class="bma-simple-icon-stacked-card__icon">
                {!! $iconHtml !!}
            </div>
        @endif
        {!! $content !!}
    </div>
</{{ $tag }}>
