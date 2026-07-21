@props([
    'tone' => 'white',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'variant' => 'check',
    'columns' => 3,
    'items' => [],
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'white';

$variant = $variant === 'numbered' ? 'numbered' : 'check';

// Complete static strings so the JIT scanner picks them up.
$grids = [
    2 => 'sm:grid-cols-2',
    3 => 'sm:grid-cols-3',
    4 => 'sm:grid-cols-2 lg:grid-cols-4',
];
$columns = array_key_exists((int) $columns, $grids) ? (int) $columns : 3;

$items = array_values(array_filter(
    is_array($items) ? $items : [],
    static fn ($item): bool => is_array($item) && (($item['lead'] ?? '') !== '' || ($item['text'] ?? '') !== '')
));
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-info-cards', 'py-14 lg:py-20', $tones[$tone]]) }}>
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

            <div class="mt-8 grid gap-5 {{ $grids[$columns] }}">
                @foreach ($items as $index => $item)
                    @php
                        $lead = trim((string) ($item['lead'] ?? ''));
                        $text = trim((string) ($item['text'] ?? ''));
                    @endphp

                    @if ($variant === 'numbered')
                        <div class="rounded-card border border-grey-50 bg-white p-6 shadow-card">
                            <span class="font-mono text-3xl font-bold text-primary/25">{{ sprintf('%02d', $index + 1) }}</span>
                            <p class="mt-3 text-body-s text-grey-800">
                                @if ($lead !== '')<strong class="font-heading uppercase tracking-wide text-grey-900">{{ $lead }}</strong>@endif
                                {!! wp_kses_post($text) !!}
                            </p>
                        </div>
                    @else
                        <div class="flex gap-4 rounded-card border border-grey-50 bg-white p-6 shadow-card">
                            <span class="mt-0.5 grid size-7 shrink-0 place-items-center rounded-full bg-primary/10 text-primary"><span class="size-4"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l4.5 4.5L19 7"/></svg></span></span>
                            <p class="text-body-s text-grey-800">
                                @if ($lead !== '')<strong class="font-heading uppercase tracking-wide text-grey-900">{{ $lead }}</strong>@endif
                                {!! wp_kses_post($text) !!}
                            </p>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </section>
@endif
