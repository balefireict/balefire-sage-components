@props([
    'eyebrow' => 'Get It Right',
    'title' => '',
    'content' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
    'source' => 'filter',
    'taxonomy' => 'category',
    'termId' => 0,
    'postIds' => [],
    'count' => 4,
    'columns' => 4,
    'fallbackImageId' => 0,
])

@php
use BalefireInc\Sage\ArticleCards\Articles;

$cards = Articles::resolve([
    'source' => $source,
    'taxonomy' => $taxonomy,
    'termId' => $termId,
    'postIds' => $postIds,
    'count' => $count,
    'fallbackImageId' => $fallbackImageId,
]);

// home_url(), not site_url(): Bedrock puts core in /wp.
$ctaUrl = trim((string) $ctaUrl);
$ctaUrl = $ctaUrl !== ''
    ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : $ctaUrl)
    : '';

/*
 * Last-resort placeholder for posts with no featured image and no block-level
 * fallback attachment. Supplied as a URL by the theme (see the theme's
 * filters.php), because a "no image" placeholder is theme chrome — it belongs
 * with the theme's assets, not the media library. Keeping it a filter also
 * means this package never has to know a theme path.
 */
$fallbackUrl = (string) apply_filters('balefire/article_cards/fallback_image_url', '');

// Only 3 or 4 — the comp is 4-up and anything wider breaks the card copy.
$columns = (int) $columns === 3 ? 3 : 4;
$gridClass = $columns === 3
    ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
    : 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-4';
@endphp

@if ($cards !== [])
    <section {{ $attributes->class([
        'bma-article-cards',
        'bg-grey-800 px-6 py-12 lg:px-30 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-16">
            {{-- Header: copy left, CTA right --}}
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-16">
                <div class="flex flex-1 flex-col items-start gap-2.5">
                    <x-bma::eyebrow :text="$eyebrow" />

                    @if ($title !== '')
                        <h2 class="font-heading text-3xl font-semibold uppercase leading-tight text-white lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
                            {{ $title }}
                        </h2>
                    @endif

                    @if ($content !== '')
                        <p class="text-base leading-6 text-grey-400">
                            {{ $content }}
                        </p>
                    @endif
                </div>

                @if ($ctaLabel !== '' && $ctaUrl !== '')
                    <a
                        href="{{ $ctaUrl }}"
                        class="group inline-flex h-14 shrink-0 items-center justify-center gap-2 self-start rounded bg-grey-400 px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-grey-800 hover:ring-1 hover:ring-white/40 lg:self-auto"
                    >
                        {{ $ctaLabel }}
                        {{-- [&_svg], never [&>svg]: a literal ">" in a class attribute
                             breaks WordPress's the_content filters. --}}
                        <span class="block size-6 shrink-0 transition-transform group-hover:translate-x-0.5 [&_svg]:size-full">
                            <svg viewBox="0 0 24 24" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8-8-8z" />
                            </svg>
                        </span>
                    </a>
                @endif
            </div>

            {{-- Cards --}}
            <div class="grid gap-8 {{ $gridClass }}">
                @foreach ($cards as $card)
                    <article class="flex flex-col overflow-hidden rounded-semi bg-white">
                        @if ($card['imageId'] > 0)
                            {!! wp_get_attachment_image($card['imageId'], 'medium_large', false, [
                                'class' => 'h-56 w-full object-cover',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            ]) !!}
                        @elseif ($fallbackUrl !== '')
                            {{-- Decorative: the card's heading already names the post,
                                 so an empty alt keeps it out of the a11y tree. --}}
                            <img
                                src="{{ esc_url($fallbackUrl) }}"
                                alt=""
                                class="h-56 w-full object-cover"
                                loading="lazy"
                                decoding="async"
                            />
                        @endif

                        <div class="flex flex-col items-start gap-4 px-8 pb-8 pt-8">
                            <div class="flex flex-col items-start gap-2">
                                @if ($card['terms'] !== '')
                                    <p class="font-mono text-label-m font-bold leading-4 text-primary">
                                        {{ __('CAT:', 'balefire') }} {{ $card['terms'] }}
                                    </p>
                                @endif

                                <h3 class="font-heading text-lg font-semibold uppercase leading-7 text-grey-800">
                                    {{ $card['title'] }}
                                </h3>

                                @if ($card['excerpt'] !== '')
                                    <p class="line-clamp-3 text-body-xs text-grey-400">
                                        {{ $card['excerpt'] }}
                                    </p>
                                @endif
                            </div>

                            {{-- Whole card is not a link: an <a> wrapping the heading
                                 keeps one clear link target per card for screen readers. --}}
                            <a
                                href="{{ esc_url($card['url']) }}"
                                class="group mt-auto inline-flex items-center gap-2.5 font-mono text-label-m font-bold uppercase leading-3 text-primary no-underline transition hover:text-primary-dark"
                            >
                                <span>{{ __('Read more', 'balefire') }}</span>
                                <span class="sr-only">: {{ $card['title'] }}</span>
                                <span class="block size-3.5 shrink-0 transition-transform group-hover:translate-x-0.5 [&_svg]:size-full">
                                    <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                        <path d="M2.5 6.5H8.085L5.645 8.94C5.45 9.135 5.45 9.455 5.645 9.65C5.84 9.845 6.155 9.845 6.35 9.65L9.645 6.355C9.84 6.16 9.84 5.845 9.645 5.65L6.355 2.35C6.16 2.155 5.845 2.155 5.65 2.35C5.455 2.545 5.455 2.86 5.65 3.055L8.085 5.5H2.5C2.225 5.5 2 5.725 2 6C2 6.275 2.225 6.5 2.5 6.5Z" fill="currentColor" />
                                    </svg>
                                </span>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
