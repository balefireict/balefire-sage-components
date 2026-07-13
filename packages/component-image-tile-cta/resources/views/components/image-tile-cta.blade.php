@props([
    'heroImageId' => 0,
    'heroImageUrl' => '',
    'heroImageAlt' => '',
    'image1Id' => 0,
    'image1Url' => '',
    'image1Alt' => '',
    'image2Id' => 0,
    'image2Url' => '',
    'image2Alt' => '',
    'image3Id' => 0,
    'image3Url' => '',
    'image3Alt' => '',
    'content' => '',
])

@php
$resolveImage = static function (int $id, string $url, string $alt): array {
    if ($id > 0 && $url === '') {
        $url = (string) wp_get_attachment_image_url($id, 'full');
    }
    if ($alt === '' && $id > 0) {
        $alt = (string) get_post_meta($id, '_wp_attachment_image_alt', true);
    }
    return [$url, $alt];
};

[$heroUrl, $heroAlt] = $resolveImage(absint($heroImageId), $heroImageUrl, $heroImageAlt);
[$img1Url, $img1Alt] = $resolveImage(absint($image1Id), $image1Url, $image1Alt);
[$img2Url, $img2Alt] = $resolveImage(absint($image2Id), $image2Url, $image2Alt);
[$img3Url, $img3Alt] = $resolveImage(absint($image3Id), $image3Url, $image3Alt);
@endphp

<section {{ $attributes->class(['bma-image-tile-cta']) }}>
    <div class="bma-image-tile-cta__container mx-auto max-w-screen-2xl">
        <div class="bma-image-tile-cta__top grid grid-cols-1 gap-x-12 gap-y-12 lg:grid-cols-2 lg:items-center">
            <div class="bma-image-tile-cta__content">
                {!! $content !!}
            </div>

            @if ($heroUrl !== '')
                <div class="bma-image-tile-cta__hero">
                    <img src="{{ esc_url($heroUrl) }}"
                         alt="{{ $heroAlt }}"
                         class="aspect-6/5 w-full max-w-none rounded-2xl object-cover" />
                </div>
            @endif
        </div>

        @if ($img1Url !== '' || $img2Url !== '' || $img3Url !== '')
            <div class="bma-image-tile-cta__gallery mt-8 flex flex-wrap items-end justify-start gap-6 sm:gap-8 lg:flex-nowrap">
                @if ($img1Url !== '')
                    <div class="bma-image-tile-cta__tile bma-image-tile-cta__tile--1 w-full sm:w-64 lg:w-auto lg:flex-[2] lg:min-w-0">
                        <img src="{{ esc_url($img1Url) }}"
                             alt="{{ $img1Alt }}"
                             class="aspect-4/3 w-full max-w-none rounded-2xl object-cover" />
                    </div>
                @endif

                @if ($img2Url !== '')
                    <div class="bma-image-tile-cta__tile bma-image-tile-cta__tile--2 w-full sm:flex-auto lg:w-auto lg:flex-[3] lg:min-w-0">
                        <img src="{{ esc_url($img2Url) }}"
                             alt="{{ $img2Alt }}"
                             class="aspect-7/5 w-full max-w-none rounded-2xl object-cover" />
                    </div>
                @endif

                @if ($img3Url !== '')
                    <div class="bma-image-tile-cta__tile bma-image-tile-cta__tile--3 hidden self-start sm:block sm:w-64 lg:w-auto lg:flex-[2] lg:min-w-0">
                        <img src="{{ esc_url($img3Url) }}"
                             alt="{{ $img3Alt }}"
                             class="aspect-4/3 w-full max-w-none rounded-2xl object-cover" />
                    </div>
                @endif
            </div>
        @endif
    </div>
</section>
