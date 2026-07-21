@props([
    'tone' => 'grey',
    'eyebrow' => '',
    'title' => '',
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
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-link-card-grid', 'py-14 lg:py-20', $tones[$tone]]) }}>
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            @if ($eyebrow !== '' || $title !== '')
                <div class="max-w-3xl">
                    @if ($eyebrow !== '')
                        <span class="mb-4 inline-flex items-center gap-3 font-mono text-label-m font-bold uppercase tracking-[0.16em] text-primary"><span class="h-px w-8 bg-primary"></span>{{ $eyebrow }}</span>
                    @endif

                    @if ($title !== '')
                        <h2 class="font-heading text-[clamp(1.8rem,3.6vw,2.5rem)] font-bold uppercase leading-[1.05] text-grey-900">{{ $title }}</h2>
                    @endif
                </div>
            @endif

            <div @class(['mt-8' => $eyebrow !== '' || $title !== ''])>
                <div class="grid gap-4 {{ $gridCols }}">
                    @foreach ($items as $item)
                        @php
                            $url = (string) $item['url'];
                            $url = esc_url(str_starts_with($url, '/') ? home_url($url) : $url);
                        @endphp
                        <a href="{{ $url }}" class="group flex items-center justify-between gap-3 rounded-card border border-grey-50 bg-white p-5 transition-colors hover:border-primary/40">
                            <span class="font-heading text-body-s font-bold uppercase text-grey-800 transition-colors group-hover:text-primary">{{ $item['label'] }}</span>
                            <span class="size-4 shrink-0 text-primary"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12h14M13 6l6 6-6 6"/></svg></span>
                        </a>
                    @endforeach
                </div>
            </div>
        </div>
    </section>
@endif
