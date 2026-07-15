@props([
    'eyebrow' => 'The B&T Difference',
    'title' => '',
    'content' => '',
    'ctaLabel' => '',
    'ctaUrl' => '',
    'items' => [],
])

@php
// Drop rows with nothing to say — an empty repeater row would render a bare number.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item) && trim((string) ($item['title'] ?? '')) !== ''
));

// home_url(), not site_url(): Bedrock puts core in /wp.
$ctaUrl = trim((string) $ctaUrl);
$ctaUrl = $ctaUrl !== ''
    ? esc_url(str_starts_with($ctaUrl, '/') ? home_url($ctaUrl) : $ctaUrl)
    : '';
@endphp

@if ($items !== [])
    <section {{ $attributes->class([
        'bma-numbered-features',
        'bg-white px-6 py-12 lg:px-30 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-16">
            {{-- Header: copy left, CTA right --}}
            <div class="flex flex-col gap-8 lg:flex-row lg:items-center lg:gap-16">
                <div class="flex flex-1 flex-col items-start gap-2.5">
                    <x-bma::eyebrow :text="$eyebrow" />

                    @if ($title !== '')
                        <h2 class="font-heading text-3xl font-semibold uppercase leading-tight text-grey-800 lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
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
                        class="group inline-flex h-14 shrink-0 items-center justify-center gap-2 self-start rounded bg-grey-400 px-4 font-mono text-base font-bold uppercase text-white no-underline transition hover:bg-grey-800 lg:self-auto"
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

            {{-- 2-up grid. No gap: the hover background IS the card, so the cards
                 sit flush and the grey panel appears under whichever one you point at. --}}
            <div class="grid grid-cols-1 lg:grid-cols-2">
                @foreach ($items as $i => $item)
                    @php
                        $number = sprintf('%02d', $i + 1);
                        $imageId = absint($item['imageId'] ?? 0);
                    @endphp

                    {{-- `group` drives the reveal, desktop only (lg:): touch
                         screens rest on the number and stay there. focus-within
                         mirrors hover for keyboard users. --}}
                    <div class="group relative flex gap-8 rounded-semi p-8 transition-colors duration-300 lg:hover:bg-grey-25 lg:focus-within:bg-grey-25 motion-reduce:transition-none">
                        {{-- Media slot. At rest it is just wide enough for "02";
                             on hover (lg+, when an image is set) it grows to the
                             comp's 142px and the image cross-fades in. --}}
                        <div @class([
                            'relative flex w-10 shrink-0 items-stretch transition-[width] duration-300 ease-out motion-reduce:transition-none',
                            'lg:group-hover:w-[142px] lg:group-focus-within:w-[142px]' => $imageId > 0,
                        ])>
                            <span
                                aria-hidden="true"
                                @class([
                                    'font-mono text-2xl font-bold leading-8 text-grey-800 transition-opacity duration-200 motion-reduce:transition-none',
                                    'lg:group-hover:opacity-0 lg:group-focus-within:opacity-0' => $imageId > 0,
                                ])
                            >{{ $number }}</span>

                            @if ($imageId > 0)
                                {{-- Decorative: the heading beside it already carries the
                                     meaning, and the image only exists on hover. --}}
                                <span
                                    aria-hidden="true"
                                    class="pointer-events-none absolute inset-0 scale-95 opacity-0 transition-all duration-300 ease-out lg:group-hover:scale-100 lg:group-hover:opacity-100 lg:group-focus-within:scale-100 lg:group-focus-within:opacity-100 motion-reduce:transition-none"
                                >
                                    {!! wp_get_attachment_image($imageId, 'medium', false, [
                                        'class' => 'size-full rounded-semi object-cover',
                                        'alt' => '',
                                        'loading' => 'lazy',
                                        'decoding' => 'async',
                                    ]) !!}
                                </span>
                            @endif
                        </div>

                        <div class="flex min-w-0 flex-1 flex-col gap-4">
                            <h3 class="font-heading text-2xl font-bold leading-8 text-grey-800">
                                {{ $item['title'] }}
                            </h3>

                            @if (trim((string) ($item['text'] ?? '')) !== '')
                                <p class="text-body-xs text-grey-400">
                                    {{ $item['text'] }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
