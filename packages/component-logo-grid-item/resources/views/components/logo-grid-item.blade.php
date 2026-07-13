@props([
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'linkType' => 'none',
    'pageId' => 0,
    'url' => '',
])

@php
$imageId = absint($imageId);
$imageAlt = sanitize_text_field($imageAlt);
$imageTitle = ($imageId > 0) ? get_the_title($imageId) : $imageAlt;
$pageId = absint($pageId);

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
@endphp

@if ($imageUrl !== '')
    <{{ $tag }}
        {{ $attributes->class(['bma-logo-grid-item']) }}
        @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
        @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
    >
        <img
            src="{{ esc_url($imageUrl) }}"
            alt="{{ $imageAlt }}"
            title="{{ $imageTitle }}"
            class="bma-logo-grid-item__img"
            loading="lazy"
            width="160"
            height="80"
        />
    </{{ $tag }}>
@endif
