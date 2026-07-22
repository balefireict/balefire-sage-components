@props([
    'tone' => 'white',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
    'items' => [],
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'white';

$items = array_values(array_filter(
    is_array($items) ? $items : [],
    static fn ($item): bool => is_array($item) && ($item['title'] ?? '') !== ''
));
@endphp

@if ($items !== [])
    <section {{ $attributes->class(['bma-video-cards', 'py-16 lg:py-24', $tones[$tone]]) }}>
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

            <div class="mt-10 grid gap-6 md:grid-cols-2">
                @foreach ($items as $item)
                    @php
                        $imageId = absint($item['imageId'] ?? 0);
                        $imageUrl = (string) ($item['imageUrl'] ?? '');
                        if ($imageId > 0 && $imageUrl === '') {
                            $imageUrl = (string) wp_get_attachment_image_url($imageId, 'large');
                        }
                        $url = (string) ($item['url'] ?? '');
                        $url = $url !== '' ? esc_url(str_starts_with($url, '/') ? home_url($url) : $url) : '';
                        $tag = $url !== '' ? 'a' : 'div';
                    @endphp

                    <{{ $tag }} @if ($url !== '') href="{{ $url }}" @endif class="group overflow-hidden rounded-card border border-grey-50 bg-white shadow-card">
                        <div class="relative">
                            <div class="relative aspect-video overflow-hidden bg-grey-900">
                                @if ($imageUrl !== '')
                                    <img src="{{ esc_url($imageUrl) }}" alt="" class="absolute inset-0 size-full object-cover" loading="lazy" decoding="async" />
                                @endif
                            </div>
                            <div class="absolute inset-0 grid place-items-center"><span class="grid size-16 place-items-center rounded-full bg-primary/90 text-white ring-4 ring-white/20 transition-transform group-hover:scale-105"><span class="size-8"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M10 8.5v7l6-3.5z"/></svg></span></span></div>
                            @if (($item['badge'] ?? '') !== '')
                                <span class="absolute left-3 top-3 rounded-full bg-grey-900/85 px-3 py-1 font-mono text-label-m font-bold uppercase tracking-wide text-white">{{ $item['badge'] }}</span>
                            @endif
                        </div>
                        <div class="p-6 lg:p-7">
                            <h3 class="font-heading text-xl font-bold uppercase text-grey-900">{{ $item['title'] }}</h3>
                            @if (($item['text'] ?? '') !== '')
                                <p class="mt-2 text-body-s text-grey-800">{{ $item['text'] }}</p>
                            @endif
                        </div>
                    </{{ $tag }}>
                @endforeach
            </div>
        </div>
    </section>
@endif
