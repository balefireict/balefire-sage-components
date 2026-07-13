@props([
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'title' => '',
    'url' => '',
    'linkType' => 'none',
    'pageId' => 0,
])

@php
$imageId = absint($imageId);
$pageId = absint($pageId);

// Resolve image from the media library when no URL is stored.
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'full');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}
$imageTitle = ($imageId > 0) ? get_the_title($imageId) : $imageAlt;

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

<{{ $tag }}
    {{ $attributes->class(['bma-simple-image-card', 'overflow-hidden', 'rounded-[var(--radius-card,0.5rem)]', 'bg-white']) }}
    @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
>
    @if ($imageUrl !== '')
        <div class="w-full">
            <img class="block w-full h-auto object-cover" src="{{ esc_url($imageUrl) }}"
                alt="{{ $imageAlt }}" title="{{ $imageTitle }}" />
        </div>
    @endif

    @if ($title !== '')
        <div class="p-[var(--spacing-card,1.5rem)]">
            <h3 class="text-black">{{ $title }}</h3>
        </div>
    @endif
</{{ $tag }}>
