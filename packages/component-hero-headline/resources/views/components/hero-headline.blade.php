@props([
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
$imageId = absint($imageId);

// Resolve the background from the media library when no URL is stored.
if ($imageId > 0 && $imageUrl === '') {
    $imageUrl = (string) wp_get_attachment_image_url($imageId, 'full');
}
if ($imageAlt === '' && $imageId > 0) {
    $imageAlt = (string) get_post_meta($imageId, '_wp_attachment_image_alt', true);
}

$primaryUrl = $primaryUrl !== '' ? esc_url(str_starts_with($primaryUrl, '/') ? site_url($primaryUrl) : $primaryUrl) : '';
$secondaryUrl = $secondaryUrl !== '' ? esc_url(str_starts_with($secondaryUrl, '/') ? site_url($secondaryUrl) : $secondaryUrl) : '';

// *word* in the title renders as a brand-highlighted span. Escape first,
// then swap the markers, so only our span survives as markup.
$titleHtml = preg_replace(
    '/\*([^*]+)\*/',
    '<span class="text-primary">$1</span>',
    esc_html($title)
);

$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');
@endphp

<section {{ $attributes->class([
    'bma-hero-headline',
    'relative isolate flex items-center overflow-hidden bg-dark',
    'min-h-[480px] lg:min-h-[724px]',
]) }}>
    @if ($imageUrl !== '')
        <img
            src="{{ esc_url($imageUrl) }}"
            alt="{{ $imageAlt }}"
            class="absolute inset-0 -z-30 size-full object-cover"
            fetchpriority="high"
            decoding="async"
        />
    @endif

    {{-- Scrim + crosshatch texture, per the comp (black/70 + 10px hatch at 40%). --}}
    <div aria-hidden="true" class="absolute inset-0 -z-20 bg-black/70"></div>
    <div
        aria-hidden="true"
        class="bma-hero-headline__texture absolute inset-0 -z-10 opacity-40"
        style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.06) 0 1px, transparent 1px 10px), repeating-linear-gradient(-45deg, rgba(255,255,255,0.06) 0 1px, transparent 1px 10px);"
    ></div>

    <div class="flex w-full max-w-[980px] flex-col items-start gap-8 px-6 py-16 lg:px-20">
        <div class="flex flex-col gap-5">
            @if ($eyebrow !== '')
                <p class="flex items-center gap-2 font-heading text-base font-bold uppercase text-primary">
                    <svg class="h-3 w-[21px] shrink-0" viewBox="0 0 21 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <rect x="0" y="5" width="12" height="2" />
                        <path d="M14 0l7 6-7 6V0z" />
                    </svg>
                    <span>{{ $eyebrow }}</span>
                    <svg class="h-3 w-[28px] shrink-0" viewBox="0 0 28 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M0 0l7 6-7 6V0z" />
                        <path d="M10 0l7 6-7 6V0z" opacity="0.6" />
                        <path d="M20 0l7 6-7 6V0z" opacity="0.3" />
                    </svg>
                </p>
            @endif

            @if ($title !== '')
                <h1 class="max-w-[894px] font-heading text-4xl font-semibold uppercase leading-none text-white sm:text-5xl lg:text-[64px] lg:leading-[64px] lg:tracking-[-2px]">
                    {!! $titleHtml !!}
                </h1>
            @endif

            @if ($content !== '')
                <div class="max-w-[500px] text-lg leading-7 text-white/70">
                    {!! wp_kses_post(wpautop($content)) !!}
                </div>
            @endif
        </div>

        @if ($hasButtons)
            <div class="flex flex-wrap gap-4">
                @if ($primaryLabel !== '' && $primaryUrl !== '')
                    <a
                        href="{{ $primaryUrl }}"
                        title="{{ $primaryLabel }}"
                        class="inline-flex h-14 items-center justify-center gap-2 rounded bg-primary px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-primary-dark"
                    >
                        {{ $primaryLabel }}
                        <svg class="size-6 shrink-0" viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                            <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z" />
                        </svg>
                    </a>
                @endif

                @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                    <a
                        href="{{ $secondaryUrl }}"
                        title="{{ $secondaryLabel }}"
                        class="inline-flex h-14 items-center justify-center rounded border border-white/80 px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-white/10"
                    >
                        {{ $secondaryLabel }}
                    </a>
                @endif
            </div>
        @endif
    </div>
</section>
