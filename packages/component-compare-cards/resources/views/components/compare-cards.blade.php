@props([
    'tone' => 'grey',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'leftLabel' => 'Authentic',
    'rightLabel' => 'Counterfeit',
    'items' => [],
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'grey';

$items = array_values(array_filter(
    is_array($items) ? $items : [],
    static fn ($item): bool => is_array($item) && ($item['title'] ?? '') !== ''
));
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-compare-cards', 'bma-band', $tones[$tone]]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            @if ($eyebrow !== '' || $title !== '' || $content !== '')
                <div class="max-w-3xl">
                    @if ($eyebrow !== '')
                        <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                    @endif

                    @if ($title !== '')
                        <h2 class="font-heading text-[clamp(2rem,4vw,2.75rem)] font-bold uppercase leading-[1.03] text-grey-900">{{ $title }}</h2>
                    @endif

                    @if ($content !== '')
                        <p class="mt-5 max-w-2xl text-body-m text-grey-800">{!! wp_kses_post($content) !!}</p>
                    @endif
                </div>
            @endif

            <div class="mt-10 grid gap-6 lg:grid-cols-2">
                @foreach ($items as $item)
                    <div class="flex flex-col overflow-hidden rounded-card border border-grey-50 bg-white shadow-card">
                        <div class="border-b border-grey-50 p-5"><h3 class="font-heading text-lg font-bold uppercase text-grey-900">{{ $item['title'] }}</h3></div>
                        <div class="grid flex-1 grid-cols-1 divide-y divide-grey-50 sm:grid-cols-2 sm:divide-x sm:divide-y-0">
                            <div class="p-5">
                                <p class="mb-2 inline-flex items-center gap-1.5 font-mono text-label-m font-bold uppercase tracking-wide text-[#2E7D5B]"><span class="size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg></span>{{ $leftLabel }}</p>
                                <p class="text-body-s text-grey-800">{{ $item['left'] ?? '' }}</p>
                            </div>
                            <div class="bg-grey-25 p-5">
                                <p class="mb-2 inline-flex items-center gap-1.5 font-mono text-label-m font-bold uppercase tracking-wide text-primary"><span class="size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M7 7l10 10M17 7 7 17"/></svg></span>{{ $rightLabel }}</p>
                                <p class="text-body-s text-grey-800">{{ $item['right'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endif
