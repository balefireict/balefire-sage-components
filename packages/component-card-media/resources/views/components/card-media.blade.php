@props([
    'logoId' => 0,
    'logoUrl' => '',
    'logoAlt' => '',
    'logoType' => 'image',
    'logoSvgCode' => '',
    'mediaType' => 'image',
    'svgCode' => '',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'title' => '',
    'text' => '',
    'linkText' => 'Learn More',
    'url' => '',
    'linkType' => 'none',
    'pageId' => 0,
    'openInNewTab' => false,
])

@php
$logoId = absint($logoId);
$imageId = absint($imageId);
$pageId = absint($pageId);

// Resolve images from the media library when no URL is stored.
if ($logoId > 0 && $logoUrl === '') {
    $logoUrl = (string) wp_get_attachment_image_url($logoId, 'full');
}
if ($logoAlt === '' && $logoId > 0) {
    $logoAlt = (string) get_post_meta($logoId, '_wp_attachment_image_alt', true);
}
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'full');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}
$logoTitle = ($logoId > 0) ? get_the_title($logoId) : $logoAlt;
$imageTitle = ($imageId > 0) ? get_the_title($imageId) : $imageAlt;

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

$hasMedia = ($mediaType === 'svg' && $svgCode !== '') || ($imageUrl !== '');

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
    {{ $attributes->class(['bma-card-media', 'relative', 'flex', 'flex-col', 'overflow-hidden', 'rounded-[var(--radius-card,0.5rem)]', 'py-6', 'md:pb-12', 'px-6', 'min-h-80']) }}
    @if ($hasLink) href="{{ esc_url($linkHref) }}" @endif
    @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
>
    <div class="flex h-8 w-full items-start justify-end pl-4 mt-1.5">
        @if ($logoType === 'image' && $logoUrl !== '')
            <img class="top-logo flex w-full items-center justify-end" src="{{ esc_url($logoUrl) }}"
                alt="{{ $logoAlt }}" title="{{ $logoTitle }}" />
        @elseif ($logoType === 'svg' && $logoSvgCode !== '')
            <div class="top-logo flex w-full items-center justify-end">
                {!! wp_kses($logoSvgCode, $svgAllowlist) !!}
            </div>
        @endif
    </div>

    @if ($hasMedia)
        <div class="w-full px-4 align-center">
            @if ($mediaType === 'svg' && $svgCode !== '')
                <div class="min-h-8 my-auto card-media-svg flex">
                    {!! wp_kses($svgCode, $svgAllowlist) !!}
                </div>
            @elseif ($imageUrl !== '')
                <img class="block w-full h-auto object-cover" src="{{ esc_url($imageUrl) }}"
                    alt="{{ $imageAlt }}" title="{{ $imageTitle }}" />
            @endif
        </div>
    @endif

    <div class="mt-auto px-4 pt-6 pb-1.5">
        @if ($title !== '')
            <h3 class="bma-media-card-title">{{ $title }}</h3>
        @endif

        @if ($text !== '')
            <p class="mt-1 mb-2 text-black !no-underline">{{ $text }}</p>
        @endif

        @if ($hasLink && $linkText !== '')
            <span class="my-3 inline-flex items-center gap-2 font-semibold arrow-link">
                {{ $linkText }}
                <svg class="w-[1.375rem] h-[0.75rem] shrink-0 arrow-link-icon" xmlns="http://www.w3.org/2000/svg" width="22.62"
                    height="12.5" viewBox="0 0 22.62 12.5" aria-hidden="true">
                    <path
                        d="M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z"
                        transform="translate(3.955 -4.742)" fill="currentColor" stroke="currentColor" stroke-width="0.5" />
                </svg>
            </span>
        @endif
    </div>
</{{ $tag }}>
