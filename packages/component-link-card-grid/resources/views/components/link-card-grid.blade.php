@props([
    'tone' => 'grey',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'ctaLabel' => 'Read the guide',
    'columns' => 3,
    'items' => [],
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'grey';

$columns = in_array((int) $columns, [2, 3], true) ? (int) $columns : 3;
$gridCols = $columns === 2 ? 'sm:grid-cols-2' : 'sm:grid-cols-2 lg:grid-cols-3';

$items = array_values(array_filter(
    is_array($items) ? $items : [],
    static fn ($item): bool => is_array($item) && ($item['label'] ?? '') !== '' && ($item['url'] ?? '') !== ''
));

// Any card with a description switches the whole grid to the richer
// "chooser" card (title, blurb, CTA footer) per the mount-hub comps.
$chooser = array_filter($items, static fn ($item): bool => trim((string) ($item['description'] ?? '')) !== '') !== [];
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-link-card-grid', 'bma-band', $tones[$tone]]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            @if ($eyebrow !== '' || $title !== '' || $content !== '')
                <div class="max-w-3xl">
                    @if ($eyebrow !== '')
                        <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                    @endif

                    @if ($title !== '')
                        <h2 class="font-heading text-[clamp(1.8rem,3.6vw,2.5rem)] font-bold uppercase leading-[1.05] text-grey-900">{{ $title }}</h2>
                    @endif

                    @if ($content !== '')
                        <p class="mt-5 max-w-3xl text-body-m text-grey-800">{!! wp_kses_post($content) !!}</p>
                    @endif
                </div>
            @endif

            <div @class(['mt-8' => $eyebrow !== '' || $title !== '' || $content !== ''])>
                <div class="grid {{ $chooser ? 'gap-6' : 'gap-4' }} {{ $gridCols }}">
                    @foreach ($items as $item)
                        @php
                            $url = (string) $item['url'];
                            $url = esc_url(str_starts_with($url, '/') ? home_url($url) : $url);
                            $description = trim((string) ($item['description'] ?? ''));
                        @endphp

                        @if ($chooser)
                            <a href="{{ $url }}" class="group flex flex-col rounded-card border border-grey-50 bg-white p-6 shadow-card transition-all duration-200 hover:-translate-y-1 hover:border-primary/40">
                                <h3 class="font-heading text-xl font-bold uppercase text-grey-900 transition-colors group-hover:text-primary">{{ $item['label'] }}</h3>
                                @if ($description !== '')
                                    <p class="mt-2 flex-1 text-body-s text-grey-800">{{ $description }}</p>
                                @endif
                                <span class="mt-4 inline-flex items-center gap-2 font-heading text-body-s font-bold uppercase text-primary">{{ $ctaLabel }} <span class="size-[18px]"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span></span>
                            </a>
                        @else
                            <a href="{{ $url }}" class="group flex items-center justify-between gap-3 rounded-card border border-grey-50 bg-white p-5 transition-colors hover:border-primary/40">
                                <span class="font-heading text-body-s font-bold uppercase text-grey-800 transition-colors group-hover:text-primary">{{ $item['label'] }}</span>
                                <span class="size-4 shrink-0 text-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
