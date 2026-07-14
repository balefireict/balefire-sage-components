@props([
    'eyebrow' => 'Find Your Fit',
    'title' => '',
    'content' => '',
    'termIds' => [],
    'limit' => 3,
])

@php
use BalefireInc\Sage\MissionCards\Missions;

$cards = Missions::cards(is_array($termIds) ? $termIds : [], (int) $limit);
@endphp

@if ($cards !== [])
    <section {{ $attributes->class([
        'bma-mission-cards',
        'bg-grey-25 px-6 py-12 lg:px-20 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-16">
            <div class="flex flex-col gap-2.5">
                {{-- Shared lockup — balefireict/component-eyebrow. --}}
                <x-bma::eyebrow :text="$eyebrow" />

                @if ($title !== '' || $content !== '')
                    <div class="flex flex-col gap-4">
                        @if ($title !== '')
                            <h2 class="max-w-[539px] font-heading text-3xl font-semibold uppercase leading-tight text-grey-800 lg:text-5xl lg:leading-[56px] lg:tracking-[-1.5px]">
                                {{ $title }}
                            </h2>
                        @endif

                        @if ($content !== '')
                            <p class="max-w-[599px] text-base leading-6 text-grey-400">
                                {{ $content }}
                            </p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 gap-8 sm:grid-cols-2 lg:grid-cols-3">
                @foreach ($cards as $card)
                    @php
                        // home_url(), not site_url(): Bedrock puts core in /wp, so a
                        // root-relative "/blog/x" would resolve to "/wp/blog/x".
                        $ctaUrl = $card['ctaUrl'] !== ''
                            ? esc_url(str_starts_with($card['ctaUrl'], '/') ? home_url($card['ctaUrl']) : $card['ctaUrl'])
                            : '';
                    @endphp

                    <article class="flex flex-col overflow-hidden rounded-semi bg-white">
                        @if ($card['imageId'] > 0)
                            {!! wp_get_attachment_image($card['imageId'], 'large', false, [
                                'class' => 'h-[229px] w-full object-cover',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            ]) !!}
                        @endif

                        <div class="flex flex-col items-start gap-2 p-8">
                            @if ($card['audience'] !== '')
                                <p class="font-mono text-label-m font-bold leading-4 text-primary">
                                    {{ $card['audience'] }}
                                </p>
                            @endif

                            <h3 class="font-heading text-lg font-bold uppercase leading-7 text-grey-800">
                                {{ $card['title'] }}
                            </h3>

                            @if ($card['blurb'] !== '')
                                <p class="text-body-xs text-grey-400">
                                    {{ $card['blurb'] }}
                                </p>
                            @endif

                            @if ($card['ctaLabel'] !== '' && $ctaUrl !== '')
                                <a
                                    href="{{ $ctaUrl }}"
                                    class="group mt-2 inline-flex items-center gap-2.5 font-mono text-label-m font-bold uppercase leading-3 text-primary no-underline transition hover:text-primary-dark"
                                >
                                    <span>{{ $card['ctaLabel'] }}</span>
                                    {{-- [&_svg], never [&>svg]: a literal ">" in a class
                                         attribute breaks WordPress's the_content filters. --}}
                                    <span class="block size-3.5 shrink-0 transition-transform group-hover:translate-x-0.5 [&_svg]:size-full">
                                        <svg viewBox="0 0 12 12" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true" focusable="false">
                                            <path d="M2.5 6.5H8.085L5.645 8.94C5.45 9.135 5.45 9.455 5.645 9.65C5.84 9.845 6.155 9.845 6.35 9.65L9.645 6.355C9.84 6.16 9.84 5.845 9.645 5.65L6.355 2.35C6.16 2.155 5.845 2.155 5.65 2.35C5.455 2.545 5.455 2.86 5.65 3.055L8.085 5.5H2.5C2.225 5.5 2 5.725 2 6C2 6.275 2.225 6.5 2.5 6.5Z" fill="currentColor" />
                                        </svg>
                                    </span>
                                </a>
                            @endif
                        </div>
                    </article>
                @endforeach
            </div>
        </div>
    </section>
@endif
