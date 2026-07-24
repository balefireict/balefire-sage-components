@props([
    'layout' => 'band',
    'tone' => 'dark',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'imageIds' => [],
    'rows' => 3,
    'duration' => 90,
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
    'dark'  => 'bg-grey-900',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'dark';
$isDark = $tone === 'dark';
$isSplit = $layout === 'split';

$ids = array_values(array_filter(array_map('absint', is_array($imageIds) ? $imageIds : [])));
$rows = max(1, min(4, (int) $rows));
$chunks = $ids !== [] ? array_chunk($ids, (int) ceil(count($ids) / $rows)) : [];
$duration = max(20, (int) $duration);

$primaryUrl = (string) $primaryUrl;
if ($primaryUrl !== '' && str_starts_with($primaryUrl, '/')) {
    $primaryUrl = home_url($primaryUrl);
}

$imgClass = $isSplit ? 'h-16 w-auto max-w-none md:h-20' : 'h-24 w-auto max-w-none md:h-28';
@endphp

@if ($chunks !== [])
    <section {{ $attributes->class(['bma-image-marquee', 'bma-band', $tones[$tone]]) }} data-bma-tone="{{ $tone }}">
        @if ($isSplit)
            {{-- Split layout: mirrors the split-feature section — copy on the
                 right, media column on the left holding the scrolling rows in
                 a white card (JPG patches carry white backgrounds, so the
                 card keeps them seamless). --}}
            <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
                <div class="grid items-center gap-12 lg:grid-cols-2">
                    <div class="order-1 lg:order-2">
                        @if ($eyebrow !== '')
                            <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                        @endif

                        @if ($title !== '')
                            <h2 class="font-heading text-[clamp(2rem,4vw,2.6rem)] font-bold uppercase leading-tight text-grey-900">{{ $title }}</h2>
                        @endif

                        @if ($content !== '')
                            <div class="bma-prose mt-5">{!! wp_kses_post(wpautop($content)) !!}</div>
                        @endif

                        @if ($primaryLabel !== '' && $primaryUrl !== '')
                            <div class="mt-8 flex flex-wrap gap-4">
                                <a href="{{ esc_url($primaryUrl) }}" class="inline-flex items-center justify-center gap-2 rounded-semi bg-primary px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide text-white transition-colors hover:bg-primary-dark">{{ $primaryLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                            </div>
                        @endif
                    </div>

                    <div class="order-2 lg:order-1">
                        <div class="flex flex-col gap-5 overflow-hidden rounded-card bg-white py-6 ring-1 ring-grey-50">
                            @foreach ($chunks as $i => $chunk)
                                <div class="bma-marquee__row {{ $i % 2 === 1 ? 'bma-marquee__row--rtl' : '' }}" style="--marquee-duration: {{ $duration + $i * 9 }}s">
                                    @for ($half = 0; $half < 2; $half++)
                                        <div class="bma-marquee__group" @if ($half === 1) aria-hidden="true" @endif>
                                            @foreach ($chunk as $id)
                                                <div class="bma-marquee__item">
                                                    {!! wp_get_attachment_image($id, 'medium', false, [
                                                        'class' => $imgClass,
                                                        'loading' => 'lazy',
                                                        'decoding' => 'async',
                                                    ]) !!}
                                                </div>
                                            @endforeach
                                        </div>
                                    @endfor
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @else
            @if ($eyebrow !== '' || $title !== '' || $content !== '')
                <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
                    <div class="max-w-3xl">
                        @if ($eyebrow !== '')
                            <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                        @endif

                        @if ($title !== '')
                            <h2 class="font-heading text-[clamp(2rem,4vw,2.6rem)] font-bold uppercase leading-tight {{ $isDark ? 'text-white' : 'text-grey-900' }}">{{ $title }}</h2>
                        @endif

                        @if ($content !== '')
                            <div class="bma-prose mt-5 {{ $isDark ? 'text-grey-300' : '' }}">{!! wp_kses_post(wpautop($content)) !!}</div>
                        @endif

                        @if ($primaryLabel !== '' && $primaryUrl !== '')
                            <div class="mt-8">
                                <a href="{{ esc_url($primaryUrl) }}" class="inline-flex items-center justify-center gap-2 rounded-semi bg-primary px-7 py-3.5 font-heading text-body-m font-bold uppercase tracking-wide text-white transition-colors hover:bg-primary-dark">{{ $primaryLabel }}<span class="size-[18px] shrink-0"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Full-bleed rows; direction alternates LTR / RTL / LTR. --}}
            <div class="mt-10 flex flex-col gap-6 overflow-hidden">
                @foreach ($chunks as $i => $chunk)
                    <div class="bma-marquee__row {{ $i % 2 === 1 ? 'bma-marquee__row--rtl' : '' }}" style="--marquee-duration: {{ $duration + $i * 9 }}s">
                        @for ($half = 0; $half < 2; $half++)
                            <div class="bma-marquee__group" @if ($half === 1) aria-hidden="true" @endif>
                                @foreach ($chunk as $id)
                                    <div class="bma-marquee__item">
                                        {!! wp_get_attachment_image($id, 'medium', false, [
                                            'class' => $imgClass,
                                            'loading' => 'lazy',
                                            'decoding' => 'async',
                                        ]) !!}
                                    </div>
                                @endforeach
                            </div>
                        @endfor
                    </div>
                @endforeach
            </div>
        @endif
    </section>
@endif
