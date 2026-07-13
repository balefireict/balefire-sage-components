@props([
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'name' => '',
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
    {{ $attributes->class(['bma-team-member-centered', 'flex', 'flex-col', 'items-center', 'gap-2.5']) }}
    @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
>
    @if ($imageUrl !== '')
        <div class="w-full overflow-hidden rounded-full">
            <img class="block w-full h-auto object-cover object-center" src="{{ esc_url($imageUrl) }}"
                alt="{{ $imageAlt }}" title="{{ $imageTitle }}" />
        </div>
    @endif

    @if ($name !== '' || $title !== '')
        <div class="flex flex-col items-center gap-0.5 text-center">
            @if ($name !== '')
                <h3 class="mt-2 font-semibold text-black">{{ $name }}</h3>
            @endif
            @if ($title !== '')
                <p class="m-0 text-[15px]">{{ $title }}</p>
            @endif
        </div>
    @endif
</{{ $tag }}>
