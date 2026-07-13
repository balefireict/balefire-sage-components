@props([
    'heading' => '',
    'body' => '',
    'preheader' => '',
    'subhead' => '',
    'showArrow' => false,
    'mediaId' => 0,
    'mediaUrl' => '',
    'mediaAlt' => '',
    'layout' => 'inherit',
    'imageCrop' => 'default',
    'imagePosition' => 'object-center',
    'imageAspectRatio' => 'default',
    'imageRounded' => false,
    'columnGap' => 'gap-4',
    'columnGapCustom' => '',
    'imageMode' => 'single',
    'imageStackGap' => 'gap-4',
    'imageStackGapCustom' => '',
    'images' => [],
    'content' => '',
])

@php
$mediaId = (int) $mediaId;
$mediaUrl = $mediaUrl !== '' ? esc_url($mediaUrl) : '';
$layout = sanitize_key($layout);
$showArrow = $showArrow === true;
$imageRounded = $imageRounded === true;

if (! in_array($layout, ['inherit', 'text-image', 'image-text'], true)) {
    $layout = 'inherit';
}

$imageCrop = sanitize_key($imageCrop);
$imageAspectRatio = sanitize_text_field($imageAspectRatio);

$allowedColumnGaps = ['gap-0', 'gap-2', 'gap-4', 'gap-6', 'gap-8', 'gap-10', 'gap-12', 'gap-16', 'custom'];

$columnGap = sanitize_key($columnGap);
if (! in_array($columnGap, $allowedColumnGaps, true)) {
    $columnGap = 'gap-4';
}
$gapClass = ($columnGap === 'custom' && $columnGapCustom !== '') ? $columnGapCustom : $columnGap;
if ($columnGap === 'custom' && $columnGapCustom === '') {
    $gapClass = 'gap-4';
}

$imageMode = sanitize_key($imageMode);
if (! in_array($imageMode, ['single', 'multi'], true)) {
    $imageMode = 'single';
}

$imageStackGap = sanitize_key($imageStackGap);
if (! in_array($imageStackGap, $allowedColumnGaps, true)) {
    $imageStackGap = 'gap-4';
}
$stackGapClass = ($imageStackGap === 'custom' && $imageStackGapCustom !== '') ? $imageStackGapCustom : $imageStackGap;
if ($imageStackGap === 'custom' && $imageStackGapCustom === '') {
    $stackGapClass = 'gap-4';
}

$allowedCrops = ['default', 'object-cover', 'object-contain', 'object-fill', 'object-none'];
$allowedPositions = ['object-top-left', 'object-top', 'object-top-right', 'object-left', 'object-center', 'object-right', 'object-bottom-left', 'object-bottom', 'object-bottom-right'];
$allowedAspectRatios = ['default', 'aspect-auto', 'aspect-square', 'aspect-video', 'aspect-3/4', 'aspect-4/3', 'aspect-16/9', 'aspect-21/9'];

$buildImageClasses = function ($crop, $position, $aspectRatio) use ($allowedPositions) {
    $classes = ['row-image', 'h-full', 'w-full'];
    if ($crop !== 'default') {
        $classes[] = $crop;
        if (in_array($position, $allowedPositions, true)) {
            $classes[] = $position;
        }
    }
    if ($aspectRatio !== 'default') {
        $classes[] = $aspectRatio;
    }
    return implode(' ', $classes);
};

$buildImageHtml = function ($imgId, $imgUrl, $imgAlt, $imgClasses, $imgTitle) {
    $imageAttributes = ['class' => $imgClasses];
    if ($imgAlt !== '') {
        $imageAttributes['alt'] = $imgAlt;
    }
    if ($imgId > 0) {
        $imageAttributes['title'] = $imgTitle;
    }
    $html = $imgId > 0 ? wp_get_attachment_image($imgId, 'full', false, $imageAttributes) : '';
    if ($html === '' && $imgUrl !== '') {
        $html = sprintf(
            '<img src="%1$s" alt="%2$s" title="%2$s" class="%3$s" />',
            esc_url($imgUrl),
            esc_attr($imgAlt),
            esc_attr($imgClasses)
        );
    }
    return $html;
};

$buildFigureClasses = function ($rounded) {
    $classes = ['row-media', 'overflow-hidden'];
    if ($rounded) {
        $classes[] = 'rounded-[var(--radius-card)]';
    }
    return implode(' ', $classes);
};

// ---- Single mode ----
$imagePosition = sanitize_key($imagePosition);
if (! in_array($imagePosition, $allowedPositions, true)) {
    $imagePosition = 'object-center';
}

$imgClasses = ['row-image', 'h-full', 'w-full'];
if ($imageCrop !== 'default') {
    $imgClasses[] = $imageCrop;
    if (in_array($imagePosition, $allowedPositions, true)) {
        $imgClasses[] = $imagePosition;
    }
}
if ($imageAspectRatio !== 'default') {
    $imgClasses[] = $imageAspectRatio;
}

$imageAttributes = ['class' => implode(' ', $imgClasses)];
if ($mediaAlt !== '') {
    $imageAttributes['alt'] = $mediaAlt;
}
if ($mediaId > 0) {
    $imageAttributes['title'] = get_the_title($mediaId);
}
$imageHtml = $mediaId > 0 ? wp_get_attachment_image($mediaId, 'full', false, $imageAttributes) : '';
if ($imageHtml === '' && $mediaUrl !== '') {
    $imageHtml = sprintf(
        '<img src="%1$s" alt="%2$s" title="%2$s" class="%3$s" />',
        esc_url($mediaUrl),
        esc_attr($mediaAlt),
        esc_attr($imageAttributes['class'])
    );
}

// ---- Multi mode ----
if ($imageMode === 'multi' && is_array($images) && count($images) > 0) {
    $multiImageHtml = '';
    foreach ($images as $img) {
        if (! is_array($img) || empty($img['url'])) {
            continue;
        }
        $mId = isset($img['id']) ? (int) $img['id'] : 0;
        $mUrl = esc_url((string) $img['url']);
        $mAlt = isset($img['alt']) ? (string) $img['alt'] : '';
        $mCrop = isset($img['crop']) ? sanitize_key((string) $img['crop']) : 'default';
        if (! in_array($mCrop, $allowedCrops, true)) {
            $mCrop = 'default';
        }
        $mPosition = isset($img['position']) ? sanitize_key((string) $img['position']) : 'object-center';
        if (! in_array($mPosition, $allowedPositions, true)) {
            $mPosition = 'object-center';
        }
        $mAspect = isset($img['aspectRatio']) ? sanitize_text_field((string) $img['aspectRatio']) : 'default';
        if (! in_array($mAspect, $allowedAspectRatios, true)) {
            $mAspect = 'default';
        }
        // Fall back to the block-level crop/position/aspect when a per-image
        // value is left at "default" — otherwise multi-mode images render
        // with bare h-full w-full and stretch to fill the figure box.
        if ($mCrop === 'default' && $imageCrop !== 'default') {
            $mCrop = $imageCrop;
        }
        if ($mPosition === 'object-center' && $imagePosition !== 'object-center') {
            $mPosition = $imagePosition;
        }
        if ($mAspect === 'default' && $imageAspectRatio !== 'default') {
            $mAspect = $imageAspectRatio;
        }
        $mRounded = isset($img['rounded']) && $img['rounded'] === true;

        $mClasses = $buildImageClasses($mCrop, $mPosition, $mAspect);
        $mTitle = $mId > 0 ? get_the_title($mId) : '';
        $mHtml = $buildImageHtml($mId, $mUrl, $mAlt, $mClasses, $mTitle);
        if ($mHtml !== '') {
            $multiImageHtml .= sprintf(
                '<figure class="%s">%s</figure>',
                esc_attr($buildFigureClasses($mRounded)),
                $mHtml
            );
        }
    }
    $hasImage = $multiImageHtml !== '';
} else {
    $multiImageHtml = '';
    $hasImage = $imageHtml !== '';
}

$figureClasses = array_filter([
    'row-media',
    'overflow-hidden',
    $imageRounded ? 'rounded-[var(--radius-card)]' : null,
]);

$layoutClass = match ($layout) {
    'text-image' => 'layout-text-image',
    'image-text' => 'layout-image-text',
    default => 'layout-inherit',
};

$gridClasses = array_filter([
    'row-grid',
    'grid',
    'grid-cols-1',
    'items-center',
    $gapClass,
    $hasImage ? 'lg:grid-cols-2' : null,
]);

$hasLegacyContent = ($heading !== '' || $body !== '' || $preheader !== '' || $subhead !== '');
@endphp

{{-- tailwind-safelist: gap-0 gap-2 gap-4 gap-6 gap-8 gap-10 gap-12 gap-16 object-cover object-contain object-fill object-none object-top-left object-top object-top-right object-left object-center object-right object-bottom-left object-bottom object-bottom-right aspect-auto aspect-square aspect-video aspect-3/4 aspect-4/3 aspect-16/9 aspect-21/9 --}}
<div {{ $attributes->class(array_filter([
    'image-text-row',
    $layoutClass,
    $hasImage ? 'has-image' : 'missing-image',
])) }}>
    <div class="{{ implode(' ', $gridClasses) }}">
        @if ($hasLegacyContent)
            <div class="row-text space-y-5">
                @if ($preheader !== '')
                    <p class="text-sm font-semibold uppercase tracking-widest text-[color-mix(in_srgb,var(--color-dark)_50%,transparent)]">
                        {{ $preheader }}
                    </p>
                @endif

                @if ($heading !== '')
                    <h2 class="text-3xl lg:text-5xl leading-none">
                        {{ $heading }}
                    </h2>
                @endif

                @if ($subhead !== '')
                    <p class="text-lg leading-relaxed text-[color-mix(in_srgb,var(--color-dark)_72%,transparent)]">
                        {{ $subhead }}
                    </p>
                @endif

                @if ($body !== '')
                    <div class="row-copy max-w-[62ch] text-base leading-7 text-[color-mix(in_srgb,var(--color-dark)_72%,transparent)]">
                        {!! wp_kses_post($body) !!}
                    </div>
                @endif

                @if ($showArrow)
                    <span class="arrow-link inline-flex items-center gap-2 font-semibold mt-2">
                        {{ __('Learn More', 'balefire') }}
                        <svg class="w-[1.375rem] h-[0.75rem] shrink-0" xmlns="http://www.w3.org/2000/svg" width="22.62" height="12.5" viewBox="0 0 22.62 12.5" aria-hidden="true">
                            <path d="M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z" transform="translate(3.955 -4.742)" fill="currentColor" stroke="currentColor" stroke-width="0.5"></path>
                        </svg>
                    </span>
                @endif

                @if (trim($content) !== '')
                    <div class="row-buttons">
                        {!! $content !!}
                    </div>
                @endif
            </div>
        @else
            <div class="row-text">
                {!! $content !!}
            </div>
        @endif

        @if ($hasImage)
            @if ($imageMode === 'multi' && $multiImageHtml !== '')
                <div class="row-media flex flex-col {{ $stackGapClass }}">
                    {!! $multiImageHtml !!}
                </div>
            @else
                <figure class="{{ implode(' ', $figureClasses) }}">
                    {!! $imageHtml !!}
                </figure>
            @endif
        @endif
    </div>
</div>
