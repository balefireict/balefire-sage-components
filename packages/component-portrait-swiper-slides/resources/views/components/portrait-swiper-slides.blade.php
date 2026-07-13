@props([
    'slides' => [],
    'slidesPerView' => 4,
    'spaceBetween' => 16,
    'showPagination' => false,
    'showNavigation' => false,
])

@php
$slides = is_array($slides) ? $slides : [];
$spaceBetween = absint($spaceBetween);
$isEditor = defined('REST_REQUEST') && REST_REQUEST;

// Signal that this page needs Swiper.js (theme handles deduplicated enqueue).
if (class_exists('\Balefire\Assets') && method_exists('\Balefire\Assets', 'needsSwiper')) {
    \Balefire\Assets::needsSwiper();
}

// Unique ID for this swiper instance.
$uniqueId = 'bma-swiper-' . uniqid();
$escapedId = esc_attr($uniqueId);

// Swiper config. Mobile: 1 slide, tablet (640+): 2, desktop (1024+): 4.
$swiperConfig = [
    'slidesPerView' => 1,
    'spaceBetween'  => $spaceBetween,
    'loop'          => true,
    'breakpoints'   => [
        '640'  => ['slidesPerView' => 2],
        '1024' => ['slidesPerView' => 4],
    ],
];

if ($showPagination) {
    $swiperConfig['pagination'] = [
        'el'        => "#{$escapedId} ~ .swiper-pagination",
        'clickable' => true,
    ];
}

if ($showNavigation) {
    $swiperConfig['navigation'] = [
        'nextEl' => "#{$escapedId} ~ .swiper-button-next",
        'prevEl' => "#{$escapedId} ~ .swiper-button-prev",
    ];
}
@endphp

<section {{ $attributes->class(['bma-portrait-swiper-slides']) }}>
    <div id="{{ $uniqueId }}" class="swiper">
        <div class="swiper-wrapper">
            @foreach ($slides as $slide)
                @php
                $imageUrl = isset($slide['imageUrl']) ? esc_url((string) $slide['imageUrl']) : '';
                $imageAlt = isset($slide['imageAlt']) ? (string) $slide['imageAlt'] : '';
                $imageTitle = isset($slide['imageTitle']) ? (string) $slide['imageTitle'] : $imageAlt;
                $title = isset($slide['title']) ? (string) $slide['title'] : '';
                $url = isset($slide['url']) ? esc_url((string) $slide['url']) : '';
                $hasLink = $url !== '' && ! $isEditor;
                $slideTag = $hasLink ? 'a' : 'div';
                @endphp
                <div class="swiper-slide">
                    <{{ $slideTag }}
                        @if ($hasLink) href="{{ $url }}" @endif
                        class="bma-portrait-slide block relative aspect-[3/4] overflow-hidden rounded-[var(--radius-card,0.5rem)]"
                    >
                        @if ($imageUrl !== '')
                            <img
                                class="absolute inset-0 h-full w-full object-cover"
                                src="{{ $imageUrl }}"
                                alt="{{ $imageAlt }}"
                                title="{{ $imageTitle }}"
                                loading="lazy"
                            />
                        @else
                            <div class="absolute inset-0 bg-gray-300"></div>
                        @endif

                        <div class="bma-portrait-slide__overlay absolute inset-0"></div>

                        <div class="bma-portrait-slide__content absolute inset-0 flex flex-col justify-end p-[var(--spacing-card,1.5rem)]">
                            @if ($title !== '')
                                <h3 class="bma-portrait-slide__title text-white font-headline text-lg font-bold">
                                    {{ $title }}
                                </h3>
                            @endif
                        </div>
                    </{{ $slideTag }}>
                </div>
            @endforeach
        </div>
    </div>

    @if ($showNavigation)
        <button type="button" class="swiper-button-prev" aria-label="Previous slide">
            <svg id="Group_103" class="bma-portrait-swiper-arrow" data-name="Group 103" xmlns="http://www.w3.org/2000/svg" width="13.884" height="24" viewBox="0 0 13.884 24" aria-hidden="true">
                <g id="Group_102" data-name="Group 102">
                    <path id="Path_55" data-name="Path 55" d="M638.554,795.729l9.384-9.3-9.6-9.762,2.239-2.486,11.646,12.272-11.646,11.728Z" transform="translate(-638.336 -774.185)" fill="currentColor"/>
                </g>
            </svg>
        </button>
        <button type="button" class="swiper-button-next" aria-label="Next slide">
            <svg id="Group_103" class="bma-portrait-swiper-arrow" data-name="Group 103" xmlns="http://www.w3.org/2000/svg" width="13.884" height="24" viewBox="0 0 13.884 24" aria-hidden="true">
                <g id="Group_102" data-name="Group 102">
                    <path id="Path_55" data-name="Path 55" d="M638.554,795.729l9.384-9.3-9.6-9.762,2.239-2.486,11.646,12.272-11.646,11.728Z" transform="translate(-638.336 -774.185)" fill="currentColor"/>
                </g>
            </svg>
        </button>
    @endif

    @if ($showPagination)
        <div class="swiper-pagination"></div>
    @endif

    @if (! $isEditor && $slides !== [])
        <script>
            (function() {
                var config = {!! wp_json_encode($swiperConfig) !!};
                var selector = '#{{ $escapedId }}';
                var attempts = 0;
                function init() {
                    if (typeof Swiper !== 'undefined') {
                        new Swiper(selector, config);
                    } else if (attempts < 20) {
                        attempts++;
                        setTimeout(init, 50);
                    }
                }
                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', init);
                } else {
                    init();
                }
            })();
        </script>
    @endif
</section>
