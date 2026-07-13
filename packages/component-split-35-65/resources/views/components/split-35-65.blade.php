@props([
    'heading' => '',
    'content' => '',
    'buttonLabel' => '',
    'buttonUrl' => '',
    'mediaType' => 'image',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'videoUrl' => '',
    'gap' => 8,
    'reverse' => false,
    'iconId' => 0,
    'iconUrl' => '',
    'iconAlt' => '',
])

@php
$mediaType = sanitize_key($mediaType);
$imageId = absint($imageId);
$iconId = absint($iconId);

// Resolve images from the media library when no URL is stored.
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'full');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}
$imageTitle = ($imageId > 0) ? get_the_title($imageId) : $imageAlt;

if ($iconId > 0 && $iconUrl === '') {
    $iconUrl = (string) wp_get_attachment_image_url($iconId, 'full');
}
if ($iconAlt === '' && $iconId > 0) {
    $iconAlt = (string) get_post_meta($iconId, '_wp_attachment_image_alt', true);
}
$iconTitle = ($iconId > 0) ? get_the_title($iconId) : $iconAlt;

$gap = in_array((int) $gap, [0, 1, 2, 3, 4, 5, 6, 8, 10, 12, 16], true) ? (int) $gap : 8;

// Detect whether the video URL is an oEmbed provider (YouTube, Vimeo, etc.).
$videoEmbed = '';
$videoIsDirect = false;
if ($videoUrl !== '') {
    $oembed = wp_oembed_get($videoUrl, ['width' => 960]);
    if ($oembed !== false) {
        // Strip width/height/style attrs, then inject our own fill styling.
        $iframe = preg_replace('/\s(width|height|style)="[^"]*"/i', '', $oembed);
        $iframe = preg_replace(
            '/<iframe\b/i',
            '<iframe style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);width:100%;height:100%;"',
            $iframe,
            1
        );
        $videoEmbed = $iframe;
    } else {
        $videoIsDirect = true;
    }
}

$hasLeftContent = ($heading !== '' || $content !== '' || ($buttonLabel !== '' && $buttonUrl !== '') || $iconUrl !== '');
$hasRightContent = ($mediaType === 'image' && $imageUrl !== '')
    || ($mediaType === 'video' && $videoUrl !== '');

// Reverse: on desktop the text column goes right, media goes left.
// On mobile, media always stacks first and text second.
$wrapperClasses = array_filter([
    'bf-split-35-65',
    'flex',
    'flex-col',
    'md:flex-row',
    'my-3',
    'md:my-6',
    'gap-' . $gap,
    $reverse ? 'md:flex-row-reverse' : null,
]);
@endphp

{{-- tailwind-safelist: gap-0 gap-1 gap-2 gap-3 gap-4 gap-5 gap-6 gap-8 gap-10 gap-12 gap-16 --}}
<div {{ $attributes->class($wrapperClasses) }}>
    @if ($hasLeftContent)
        <div class="w-full md:w-[35%] flex items-center order-2 md:order-none">
            <div class="flex flex-col">
                @if ($iconUrl !== '')
                    <img
                        class="w-full h-auto object-contain max-w-3xs"
                        src="{{ esc_url($iconUrl) }}"
                        alt="{{ $iconAlt }}"
                        title="{{ $iconTitle }}"
                    />
                @endif

                @if ($heading !== '')
                    <h2 class="text-4xl md:text-5xl font-headline leading-[1.05]">
                        {{ $heading }}
                    </h2>
                @endif

                @if ($content !== '')
                    <div class="max-w-[62ch] text-base leading-7 mt-3">
                        {!! wp_kses_post($content) !!}
                    </div>
                @endif

                @if ($buttonLabel !== '' && $buttonUrl !== '')
                    <div class="wp-block-button mt-8">
                        <a
                            class="wp-block-button__link has-text-align-center wp-element-button"
                            href="{{ esc_url($buttonUrl) }}"
                            title="{{ $buttonLabel }}"
                        >
                            {{ $buttonLabel }}
                        </a>
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($hasRightContent)
        <div class="w-full md:w-[65%] order-1 md:order-none">
            @if ($mediaType === 'video' && $videoEmbed !== '')
                <div class="bf-split-media relative w-full overflow-hidden rounded-lg" style="aspect-ratio:16/9;">
                    {!! $videoEmbed !!}
                </div>
            @elseif ($mediaType === 'video' && $videoIsDirect)
                <video autoplay muted loop playsinline class="w-full h-auto">
                    <source src="{{ esc_url($videoUrl) }}" type="video/mp4">
                </video>
            @elseif ($mediaType === 'image' && $imageUrl !== '')
                <img
                    class="w-full h-auto object-cover rounded-lg"
                    src="{{ esc_url($imageUrl) }}"
                    alt="{{ $imageAlt }}"
                    title="{{ $imageTitle }}"
                />
            @endif
        </div>
    @endif
</div>
