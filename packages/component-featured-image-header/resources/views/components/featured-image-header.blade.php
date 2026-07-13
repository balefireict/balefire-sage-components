@props([
    'intro' => '',
    'showOnFrontPage' => false,
])

@php
// Skip on the front page unless explicitly enabled.
$skip = is_front_page() && ! $showOnFrontPage;

// Resolve background image from the featured image (singular pages only).
// On archives/search/blog index, get_the_ID() inside the loop would return
// the first post's ID and pick up its thumbnail — that's not what we want.
$backgroundImage = '';
if (! $skip && is_singular() && has_post_thumbnail()) {
    $backgroundImage = (string) get_the_post_thumbnail_url(null, 'full');
}

// Fallback to the theme default for routes/posts without a featured image
// (archives, search, 404, blog index, singular without thumbnail).
if (! $skip && $backgroundImage === '') {
    $backgroundImage = get_template_directory_uri() . '/resources/img/rockerbox-page-heading-default.webp';
}

// Resolve contextual title.
// Order of checks matters: WordPress sets the global $post to the first
// query result on archive/search/home, so get_the_ID() returns truthy
// even on those pages. Check search/404/archive/home FIRST, fall back
// to post title last (which covers single posts AND the editor's
// REST block-renderer preview where only get_the_ID() works).
$title = '';
if (! $skip) {
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
    } elseif (get_the_ID()) {
        // REST block-renderer fallback for the editor preview.
        $title = get_the_title();
    }
}
@endphp

@unless ($skip || ($title === '' && $backgroundImage === '' && $intro === ''))
    <header {{ $attributes->class(['bma-featured-image-header', 'flex', 'flex-col', 'items-center', 'justify-center', 'isolate', 'overflow-hidden', 'relative']) }}>
        @if ($backgroundImage !== '')
            <img
                src="{{ esc_url($backgroundImage) }}"
                alt=""
                title=""
                class="absolute inset-0 -z-20 object-cover object-center size-full"
            />
        @endif

        <div aria-hidden="true" class="absolute inset-0 -z-10 overlay"></div>

        <div class="mx-auto w-full max-w-5xl text-center px-[var(--spacing-gutter)] py-[var(--spacing-section)]">
            @if ($title !== '')
                <h1 class="text-5xl font-medium tracking-tight text-white text-balance pb-0 text-shadow-lg">
                    {{ $title }}
                </h1>
            @endif

            @if ($intro !== '')
                <div class="bma-featured-header-intro mx-auto mt-4 max-w-2xl text-[20px] leading-[1.5] text-white">
                    {!! wp_kses_post(wpautop($intro)) !!}
                </div>
            @endif
        </div>
    </header>
@endunless
