@props([
    'eyebrow' => 'Reviews From The Field',
    'title' => '',
    'count' => 9,
    'orderby' => 'date',
])

@php
use BalefireInc\Sage\Reviews\Reviews;

$cards = Reviews::cards((int) $count, (string) $orderby);

$uid = 'bma-reviews-' . wp_unique_id();
@endphp

@if ($cards !== [])
    {{-- .bma-reviews also carries `overflow-x: clip` (plain CSS, component-support):
         a carousel must never be able to scroll the page sideways. --}}
    <section {{ $attributes->class([
        'bma-reviews',
        'bg-grey-25 px-6 py-12 lg:px-16 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-16">
            <div class="flex flex-col items-center gap-2.5 text-center">
                <x-bma::eyebrow :text="$eyebrow" />

                @if ($title !== '')
                    <h2 class="max-w-[900px] font-heading text-3xl font-semibold uppercase leading-tight text-grey-800 lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
                        {{ $title }}
                    </h2>
                @endif
            </div>

            <div class="flex flex-col gap-8">
                {{-- Scroll-snap track: this scrolls and swipes with no JS at all.
                     The buttons below are an enhancement layered on top. --}}
                {{-- Structural classes only (bma-reviews__*), defined as plain CSS in
                     component-support's view.css. NOT Tailwind utilities: the scroll
                     container is load-bearing, and a utility that the content scanner
                     misses silently turns this into page-wide horizontal scroll. --}}
                <ul
                    data-track
                    class="bma-reviews__track"
                    tabindex="0"
                    aria-label="{{ __('Customer reviews', 'balefire') }}"
                >
                    @foreach ($cards as $card)
                        <li
                            data-review
                            data-name="{{ $card['name'] }}"
                            data-location="{{ $card['location'] }}"
                            data-body="{{ $card['body'] }}"
                            {{-- lg width is derived, not the comp's literal 416px: the
                                 comp's content box is 1312px (1440 frame - 64px padding),
                                 ours is 1280. Three 416px cards + two 32px gaps = 1312,
                                 so the third card always overhung the track by 32px and
                                 had its rounded corners clipped. (100% - 2 gaps) / 3
                                 makes three cards + gaps fill the track exactly. --}}
                            class="bma-reviews__card flex w-[calc(100%-1rem)] flex-col gap-8 rounded-semi bg-white p-8 sm:w-[calc(50%-1rem)] lg:w-[calc((100%-4rem)/3)]"
                        >
                            {{-- Quote mark --}}
                            <span class="block size-[30px] shrink-0 text-primary [&_svg]:size-full" aria-hidden="true">
                                <svg viewBox="0 0 30 30" fill="currentColor" xmlns="http://www.w3.org/2000/svg" focusable="false">
                                    <path d="M5.25 17.5h4.375l-2.5 5h3.75l2.5-5V10h-8.125v7.5Zm11.25 0h4.375l-2.5 5h3.75l2.5-5V10H16.5v7.5Z" />
                                </svg>
                            </span>

                            <div class="flex flex-1 flex-col gap-2">
                                <p data-review-body class="line-clamp-3 text-base leading-6 text-grey-800">
                                    {{ $card['body'] }}
                                </p>

                                {{-- Rendered for every card but hidden by default; view.js
                                     unhides it only where the text ACTUALLY overflows three
                                     clamped lines. PHP cannot know that — it depends on how
                                     the text wraps at the card's width, which is why a
                                     character-count guess got it wrong. Hidden by default
                                     also means it never appears without the JS that opens
                                     the lightbox. --}}
                                <button
                                    type="button"
                                    data-review-more
                                    hidden
                                    class="self-start font-mono text-label-m font-bold uppercase leading-4 text-primary transition hover:text-primary-dark"
                                >
                                    <span aria-hidden="true">&hellip;</span>
                                    <span class="sr-only">
                                        {{ sprintf(__('Read the full review from %s', 'balefire'), $card['name']) }}
                                    </span>
                                    <span aria-hidden="true">{{ __('Read more', 'balefire') }}</span>
                                </button>
                            </div>

                            <div class="flex items-center gap-4">
                                @if ($card['avatarId'] > 0)
                                    {!! wp_get_attachment_image($card['avatarId'], 'thumbnail', false, [
                                        'class' => 'size-14 shrink-0 rounded-full object-cover',
                                        'alt' => '',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]) !!}
                                @endif

                                <div class="flex min-w-0 flex-col text-grey-800">
                                    <p class="text-base font-semibold leading-6">{{ $card['name'] }}</p>

                                    @if ($card['location'] !== '')
                                        <p class="text-body-xs italic leading-5">{{ $card['location'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>

                {{-- Dots + arrows. One dot per page; view.js hides the surplus once
                     it knows how many cards actually fit. --}}
                @if (count($cards) > 1)
                    <div class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            @foreach ($cards as $i => $card)
                                <button
                                    type="button"
                                    data-dot
                                    data-active="{{ $i === 0 ? 'true' : 'false' }}"
                                    class="size-2 rounded-full bg-grey-200 transition data-[active=true]:bg-primary"
                                    aria-label="{{ sprintf(__('Go to slide %d', 'balefire'), $i + 1) }}"
                                ></button>
                            @endforeach
                        </div>

                        <div class="flex items-center gap-4">
                            <button
                                type="button"
                                data-prev
                                class="grid size-[50px] place-items-center rounded-full bg-white text-grey-800 transition hover:bg-grey-800 hover:text-white disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-grey-800"
                                aria-label="{{ __('Previous reviews', 'balefire') }}"
                            >
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M15 4 7 12l8 8" /></svg>
                            </button>

                            <button
                                type="button"
                                data-next
                                class="grid size-[50px] place-items-center rounded-full bg-white text-grey-800 transition hover:bg-grey-800 hover:text-white disabled:opacity-40 disabled:hover:bg-white disabled:hover:text-grey-800"
                                aria-label="{{ __('More reviews', 'balefire') }}"
                            >
                                <svg viewBox="0 0 24 24" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="M9 4l8 8-8 8" /></svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endif
