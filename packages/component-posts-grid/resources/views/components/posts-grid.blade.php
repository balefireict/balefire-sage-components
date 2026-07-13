@props([
    'postsPerPage' => 3,
    'columns' => 3,
    'showExcerpt' => true,
    'showDate' => true,
    'showAuthor' => true,
    'heading' => '',
    'intro' => '',
    'maxWidth' => 'wide',
    'backgroundTone' => 'transparent',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$postsPerPage = (int) $postsPerPage > 0 ? (int) $postsPerPage : 3;
$columns = max(1, min(3, (int) $columns));

$query = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'ignore_sticky_posts' => true,
    'posts_per_page'      => $postsPerPage,
]);

$gridClasses = [
    1 => 'grid-cols-1',
    2 => 'grid-cols-1 lg:grid-cols-2',
    3 => 'grid-cols-1 lg:grid-cols-3',
];
$surface = SectionStyles::surface(sanitize_key($backgroundTone));
$innerStyle = SectionStyles::innerStyle(sanitize_key($maxWidth));
@endphp

<div {{ $attributes->class(['bma-posts-grid', 'lg:px-12 xl:px-16', $surface['section']]) }}>
    <div class="mx-auto w-full" style="{{ $innerStyle }}">
        @if ($heading !== '' || $intro !== '')
            <div class="mx-auto max-w-2xl text-center">
                @if ($heading !== '')
                    <h2 class="h1 font-headline {{ $surface['heading'] }}">
                        {{ $heading }}
                    </h2>
                @endif

                @if ($intro !== '')
                    <p class="mt-2 text-lg/8 {{ $surface['metaSoft'] }}">
                        {{ $intro }}
                    </p>
                @endif
            </div>
        @endif

        @if ($query->have_posts())
            <div class="mx-auto my-8 md:my-16 grid max-w-2xl grid-cols-1 gap-x-8 gap-y-20 lg:mx-0 lg:max-w-none {{ $gridClasses[$columns] ?? $gridClasses[3] }}">
                @while ($query->have_posts())
                    @php
                    $query->the_post();

                    $authorId = (int) get_the_author_meta('ID');
                    $authorName = get_the_author();
                    $authorRole = '';
                    $author = $authorId > 0 ? get_userdata($authorId) : null;

                    if ($author instanceof WP_User && ! empty($author->roles)) {
                        $roleKey = (string) reset($author->roles);
                        $authorRole = translate_user_role(ucwords(str_replace('_', ' ', $roleKey)));
                    }

                    $categories = get_the_category();
                    $category = $categories[0] ?? null;
                    @endphp
                    <article <?php post_class('group flex min-w-full flex-col overflow-hidden rounded-md border border-[2.5px] border-neutral-400/20 bg-white transition duration-200 hover:-translate-y-1 hover:shadow-[var(--shadow-card)]'); ?>>
                        <div class="relative aspect-video w-full overflow-hidden bg-[var(--color-surface-muted)]">
                            @if (has_post_thumbnail())
                                <a href="{{ get_permalink() }}" title="{{ the_title_attribute(['echo' => false]) }}" aria-hidden="true" tabindex="-1" class="block h-full w-full">
                                    {!! get_the_post_thumbnail(null, 'large', ['class' => 'h-full w-full object-cover transition duration-300 group-hover:scale-105']) !!}
                                </a>
                            @else
                                <a class="block h-full w-full bg-[var(--color-surface-muted)]" href="{{ get_permalink() }}" title="{{ the_title_attribute(['echo' => false]) }}" aria-hidden="true" tabindex="-1"></a>
                            @endif

                            <div class="pointer-events-none absolute inset-0 ring-1 ring-inset {{ $surface['ring'] }}"></div>
                        </div>

                        <div class="flex max-w-xl grow flex-1 flex-col justify-between py-6 px-9">
                            @if ($showDate)
                                <div class="mt-2 flex items-center gap-x-4 text-xs">
                                    <time datetime="{{ get_the_date('c') }}" class="{{ $surface['meta'] }}">
                                        {{ get_the_date('M j, Y') }}
                                    </time>

                                    @if ($category)
                                        <a href="{{ get_category_link($category->term_id) }}" title="{{ $category->name }}" class="relative z-10 px-3 py-1.5 font-medium no-underline rounded-full transition {{ $surface['chipBg'] }} {{ $surface['chipText'] }} {{ $surface['chipBgHover'] }}">
                                            {{ $category->name }}
                                        </a>
                                    @endif
                                </div>
                            @endif

                            <div class="relative group grow">
                                <h3 class="mt-3 font-bold transition color-inherit leading-tight text-[20px] group-hover:{{ $surface['metaSoft'] }}">
                                    <a class="no-underline text-inherit" href="{{ get_permalink() }}" title="{{ the_title_attribute(['echo' => false]) }}">
                                        <span class="absolute inset-0"></span>
                                        {{ get_the_title() }}
                                    </a>
                                </h3>

                                @if ($showExcerpt)
                                    <p class="mt-2 line-clamp-3 text-base/6 {{ $surface['body'] }}">
                                        {{ wp_trim_words(get_the_excerpt(), 28, '...') }}
                                    </p>
                                @endif

                                <div class="mt-6 md:mt-8">
                                    <span class="bma-post-card__link arrow-link flex items-center py-1 text-sm gap-2">
                                        {{ __('Learn More', 'balefire') }}
                                        <svg class="w-[1.375rem] h-[0.75rem] shrink-0" xmlns="http://www.w3.org/2000/svg" width="22.62" height="12.5" viewBox="0 0 22.62 12.5" aria-hidden="true">
                                            <path d="M12.87,16.808l5.358-5.362a.643.643,0,0,0,0-.908L12.87,5.178a.63.63,0,0,0-.449-.186.643.643,0,0,0-.459,1.094l4.264,4.264H-3.063a.643.643,0,0,0-.642.642.643.643,0,0,0,.642.642H16.226L11.961,15.9a.644.644,0,0,0,.462,1.093.632.632,0,0,0,.447-.184Z" transform="translate(3.955 -4.742)" fill="currentColor" stroke="currentColor" stroke-width="0.5"></path>
                                        </svg>
                                    </span>
                                </div>
                            </div>

                            @if ($showAuthor)
                                <div class="flex relative gap-x-4 items-center self-start mt-8">
                                    {!! get_avatar($authorId, 40, '', $authorName, ['class' => 'size-10 rounded-full bg-light']) !!}
                                    <div class="text-sm/6">
                                        <p class="font-semibold {{ $surface['heading'] }}">
                                            <a class="no-underline text-inherit" href="{{ get_author_posts_url($authorId) }}" title="{{ $authorName }}">
                                                {{ $authorName }}
                                            </a>
                                        </p>
                                        @if ($authorRole !== '')
                                            <p class="{{ $surface['meta'] }}">{{ $authorRole }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </article>
                @endwhile
            </div>
        @else
            <p class="px-5 py-6 text-base rounded-2xl border {{ $surface['border'] }} {{ $surface['metaSoft'] }}">
                {{ __('No posts found.', 'balefire') }}
            </p>
        @endif

        @php(wp_reset_postdata())
    </div>
</div>
