@props([
    'iconId' => 0,
    'iconUrl' => '',
    'iconAlt' => '',
    'url' => '',
    'openInNewTab' => false,
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

// Never link-wrap in the editor context.
$isEditor = defined('REST_REQUEST') && REST_REQUEST;
$hasLink = ! $isEditor && $url !== '';
$href = $hasLink ? esc_url(str_starts_with($url, '/') ? home_url($url) : $url) : '';
$tag = $hasLink ? 'a' : 'div';
@endphp

<{{ $tag }}
    {{ $attributes->class(['bma-card-icon-break']) }}
    @if ($hasLink) href="{{ $href }}" @endif
    @if ($hasLink && $openInNewTab) target="_blank" rel="noopener noreferrer" @endif
>
    @if ($iconUrl !== '')
        <div class="bma-card-icon-break__icon">
            <div class="icon-bg rounded-full h-24 w-24 flex items-center justify-center">
                <img src="{{ esc_url($iconUrl) }}" alt="{{ $iconAlt }}" title="{{ $iconTitle }}" class="h-16 w-16 object-contain" />
            </div>
        </div>
    @endif

    <div class="bma-card-icon-break__content">
        {!! $content !!}
    </div>
</{{ $tag }}>
