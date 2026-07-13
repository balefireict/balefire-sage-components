@props([
    'heading' => '',
    'body' => '',
    'mediaId' => 0,
    'mediaUrl' => '',
    'mediaAlt' => '',
    'imageClass' => '',
    'linkType' => 'none',
    'pageId' => 0,
    'url' => '',
    'linkText' => 'Learn More',
])

@php
$mediaId = absint($mediaId);
$pageId = absint($pageId);

$imgClassString = trim('w-full h-auto object-cover ' . $imageClass);

$imageHtml = '';
if ($mediaId > 0) {
    $imageHtml = wp_get_attachment_image($mediaId, 'full', false, [
        'class' => $imgClassString,
        'alt'   => $mediaAlt,
        'title' => get_the_title($mediaId),
    ]);
}
if ($imageHtml === '' && $mediaUrl !== '') {
    $imageHtml = sprintf(
        '<img src="%1$s" alt="%2$s" title="%2$s" class="%3$s" />',
        esc_url($mediaUrl),
        esc_attr($mediaAlt),
        esc_attr($imgClassString)
    );
}

$hasImage = $imageHtml !== '';
$hasText = ($heading !== '' || $body !== '');

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
@endphp

@if ($hasImage || $hasText)
    <div {{ $attributes->class(['bma-feature-row', 'flex', 'flex-col', 'md:flex-row', 'items-center', 'gap-8']) }}>
        @if ($hasImage)
            <div class="w-full md:w-[30%] shrink-0 overflow-hidden rounded-md">
                @if ($hasLink)
                    <a
                        href="{{ esc_url($linkHref) }}"
                        @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                        aria-hidden="true"
                        tabindex="-1"
                    >
                        {!! $imageHtml !!}
                    </a>
                @else
                    {!! $imageHtml !!}
                @endif
            </div>
        @endif

        @if ($hasText)
            <div class="w-full md:w-[70%] flex items-center">
                <div class="flex flex-col gap-0.5">
                    @if ($heading !== '')
                        <h3 class="row-heading">
                            {{ $heading }}
                        </h3>
                    @endif

                    @if ($body !== '')
                        <div class="max-w-[62ch] text-base leading-7">
                            {!! wp_kses_post($body) !!}
                        </div>
                    @endif

                    @if ($hasLink && $linkText !== '')
                        <a
                            href="{{ esc_url($linkHref) }}"
                            @if ($isExternal) target="_blank" rel="noopener noreferrer" @endif
                            class="my-3 inline-flex items-center gap-2 font-semibold arrow-link"
                        >
                            {{ $linkText }}
                            <svg class="w-[1.375rem] h-[0.75rem] shrink-0" xmlns="http://www.w3.org/2000/svg" width="22.62" height="12.5" viewBox="0 0 22.62 12.5" aria-hidden="true">
                                <path d="M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z" transform="translate(3.955 -4.742)" fill="currentColor" stroke="currentColor" stroke-width="0.5" />
                            </svg>
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
@endif
