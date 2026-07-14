@props([
    'eyebrow' => 'Complete Your Setup',
    'title' => '',
    'content' => '',
    'source' => 'products',
    'categoryId' => 0,
    'attribute' => '',
    'items' => [],
    'ctaLabel' => '',
    'ctaUrl' => '',
])

@php
use BalefireInc\Sage\ProductSwitcher\Products;

$panels = Products::resolve([
    'source' => $source,
    'categoryId' => $categoryId,
    'attribute' => $attribute,
    'items' => $items,
]);

// home_url(), not site_url(): Bedrock puts core in /wp.
$ctaUrl = trim((string) $ctaUrl);
$ctaUrl = $ctaUrl !== ''
    ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : $ctaUrl)
    : '';

// One id per block instance so several switchers can share a page without
// their aria-controls / aria-labelledby wires crossing.
$uid = 'bma-ps-' . wp_unique_id();
@endphp

@if ($panels !== [])
    <section {{ $attributes->class([
        'bma-product-switcher',
        'bg-white px-6 py-12 lg:px-20 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col items-center gap-12 lg:flex-row lg:gap-[50px]">
            {{-- Media --}}
            <div class="relative w-full shrink-0 lg:w-[694px]">
                @foreach ($panels as $i => $panel)
                    <div
                        role="tabpanel"
                        id="{{ $uid }}-panel-{{ $i }}"
                        aria-labelledby="{{ $uid }}-tab-{{ $i }}"
                        tabindex="0"
                        @if ($i !== 0) hidden @endif
                    >
                        @if ($panel['imageId'] > 0)
                            {{-- The 694/586 ratio sets the height (586px at the comp's
                                 width), so no max-height is needed on top of it.

                                 Every panel loads eagerly, including the hidden ones.
                                 loading="lazy" inside a [hidden] panel means the browser
                                 defers the fetch until the tab is revealed, so the first
                                 click on each tab flashes an empty frame while the image
                                 downloads. Three product shots is a cheap price for tab
                                 switching that is instant. --}}
                            {!! wp_get_attachment_image($panel['imageId'], 'large', false, [
                                'class' => 'aspect-[694/586] w-full rounded-semi object-cover object-bottom',
                                'loading' => 'eager',
                                'fetchpriority' => $i === 0 ? 'high' : 'low',
                                'decoding' => 'async',
                            ]) !!}
                        @else
                            <div class="aspect-[694/586] w-full rounded-semi bg-grey-25"></div>
                        @endif
                    </div>
                @endforeach

                {{-- Position indicator. Decorative: the tabs above are the real
                     control, so these are not focusable and are hidden from AT. --}}
                @if (count($panels) > 1)
                    <div class="absolute right-8 top-6 flex gap-2" aria-hidden="true">
                        @foreach ($panels as $i => $panel)
                            <span
                                data-dot
                                data-active="{{ $i === 0 ? 'true' : 'false' }}"
                                class="block size-2 rounded-full bg-grey-50 data-[active=true]:bg-primary"
                            ></span>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Copy --}}
            <div class="flex min-w-0 flex-1 flex-col items-start gap-8">
                <div class="flex flex-col items-start gap-2.5">
                    <x-bma::eyebrow :text="$eyebrow" />

                    @if ($title !== '')
                        <h2 class="font-heading text-3xl font-semibold uppercase leading-tight text-grey-800 lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
                            {{ $title }}
                        </h2>
                    @endif

                    @if ($content !== '')
                        <p class="max-w-[491px] text-base leading-6 text-grey-400">
                            {{ $content }}
                        </p>
                    @endif
                </div>

                <div
                    role="tablist"
                    aria-label="{{ $title !== '' ? $title : __('Product options', 'balefire') }}"
                    class="flex flex-wrap gap-4"
                >
                    @foreach ($panels as $i => $panel)
                        <button
                            type="button"
                            role="tab"
                            id="{{ $uid }}-tab-{{ $i }}"
                            aria-controls="{{ $uid }}-panel-{{ $i }}"
                            aria-selected="{{ $i === 0 ? 'true' : 'false' }}"
                            tabindex="{{ $i === 0 ? '0' : '-1' }}"
                            class="rounded border border-grey-200 px-4 py-3 font-mono text-xs font-bold uppercase leading-3 text-grey-200 transition
                                   hover:border-grey-400 hover:text-grey-400
                                   aria-selected:border-primary-800 aria-selected:bg-primary-800 aria-selected:text-white
                                   aria-selected:hover:border-primary-800 aria-selected:hover:text-white"
                        >
                            {{ $panel['label'] }}
                        </button>
                    @endforeach
                </div>

                @if ($ctaLabel !== '' && $ctaUrl !== '')
                    <a
                        href="{{ $ctaUrl }}"
                        class="group inline-flex h-14 items-center justify-center gap-2 rounded bg-primary px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-primary-dark"
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
        </div>
    </section>
@endif
