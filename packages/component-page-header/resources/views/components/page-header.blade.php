@props([
    'backgroundImage' => '',
    'minHeight' => 'auto',
    'subtitle' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
$minHeight = sanitize_key($minHeight);
$primaryUrl = $primaryUrl !== '' ? esc_url(str_starts_with($primaryUrl, '/') ? site_url($primaryUrl) : $primaryUrl) : '';
$secondaryUrl = $secondaryUrl !== '' ? esc_url(str_starts_with($secondaryUrl, '/') ? site_url($secondaryUrl) : $secondaryUrl) : '';

// Auto-detect background image if none set and the post has a featured image.
if ($backgroundImage === '' && is_singular() && has_post_thumbnail()) {
    $backgroundImage = (string) get_the_post_thumbnail_url(null, 'full');
}

// Resolve the contextual title.
$title = '';
if (is_singular()) {
    $title = get_the_title();
} elseif (is_search()) {
    $title = sprintf(__('Search Results for: %s', 'balefire'), get_search_query());
} elseif (is_404()) {
    $title = __('Page Not Found', 'balefire');
} elseif (is_archive()) {
    ob_start();
    the_archive_title();
    $title = ob_get_clean();
} elseif (is_home()) {
    $postsPage = get_option('page_for_posts');
    $title = $postsPage ? get_the_title($postsPage) : __('Blog', 'balefire');
}

$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');

$minHeightStyle = ($minHeight !== 'auto' && $minHeight !== '')
    ? sprintf('min-height: %s;', $minHeight)
    : null;
@endphp

<header
    {{ $attributes->class(['bma-page-header', 'isolate', 'overflow-hidden', 'relative']) }}
    @if ($minHeightStyle) style="{{ $minHeightStyle }}" @endif
>
    @if ($backgroundImage !== '')
        <img
            src="{{ esc_url($backgroundImage) }}"
            alt=""
            title=""
            class="absolute inset-0 -z-20 object-cover size-full"
        />
    @endif

    <div aria-hidden="true" class="absolute inset-0 -z-10 bma-gradient-overlay"></div>

    <div class="mx-auto max-w-7xl px-[var(--spacing-gutter)]">
        <div class="mx-auto max-w-5xl text-center py-[var(--spacing-section)]">
            @if ($title !== '')
                <h1 class="text-5xl font-medium tracking-tight text-white font-1 text-balance sm:text-7xl pb-[var(--spacing-copy)]">
                    {{ $title }}
                </h1>
            @endif

            @if ($subtitle !== '')
                <div class="bma-hero-subtitle mx-auto mt-[var(--spacing-copy)] max-w-2xl text-lg text-white/80 sm:text-xl">
                    {!! wp_kses_post(wpautop($subtitle)) !!}
                </div>
            @endif

            @if ($hasButtons)
                <div class="bma-hero-buttons mt-[var(--spacing-section)] flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @if ($primaryLabel !== '' && $primaryUrl !== '')
                        <a href="{{ $primaryUrl }}" title="{{ $primaryLabel }}" class="bma-hero-btn-primary btn btn-lg btn-black inline-flex items-center justify-center gap-2.5">
                            {{ $primaryLabel }}
                            <svg class="bma-hero-btn-arrow size-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 20" fill="none" aria-hidden="true">
                                <path d="M10.652 13.735l3.964-3.967a.476.476 0 000-.672L10.652 5.13a.466.466 0 00-.332-.138.476.476 0 00-.34.809l3.155 3.155H4.817a.475.475 0 000 .951h8.318L9.979 13.063a.477.477 0 00.342.809.467.467 0 00.331-.136Z" fill="currentColor"/>
                            </svg>
                        </a>
                    @endif

                    @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                        <a href="{{ $secondaryUrl }}" title="{{ $secondaryLabel }}" class="bma-hero-btn-secondary inline-flex items-center justify-center px-[var(--spacing-card)] py-[var(--spacing-card)] text-base font-semibold text-white no-underline transition hover:text-white/80">
                            {{ $secondaryLabel }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </div>
</header>
