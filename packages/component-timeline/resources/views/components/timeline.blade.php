@props([
    'tone' => 'white',
    'eyebrow' => '',
    'eyebrowVariant' => 'marks',
    'title' => '',
    'content' => '',
    'items' => [],
    'imageId' => 0,
    'imageSide' => 'right',
])

@php
use BalefireInc\Sage\Support\SectionStyles;

$tone = array_key_exists($tone, SectionStyles::tones()) ? $tone : 'white';
$surface = SectionStyles::surface($tone);
$eyebrowVariant = in_array($eyebrowVariant, ['marks', 'bar', 'plain'], true) ? $eyebrowVariant : 'marks';
$imageSide = $imageSide === 'left' ? 'left' : 'right';
$imageId = absint($imageId);
$hasImage = $imageId > 0 && wp_attachment_is_image($imageId);

// Drop rows with nothing to say.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item)
        && (trim((string) ($item['label'] ?? '')) !== '' || trim((string) ($item['text'] ?? '')) !== '')
));

$titleAllowed = ['em' => [], 'br' => [], 'span' => ['class' => true]];
$hasHeader = $eyebrow !== '' || $title !== '' || $content !== '';
@endphp

@if ($items !== [] || $hasHeader)
    <section {{ $attributes->class([
        'bma-timeline',
        'bma-band',
        'bma-timeline--image-' . $imageSide => $hasImage,
        $surface['section'],
    ]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            @if ($hasHeader)
                <div class="bma-timeline__header mb-10 max-w-3xl">
                    @if ($eyebrow !== '')
                        <x-bma::eyebrow :text="$eyebrow" :variant="$eyebrowVariant" />
                    @endif

                    @if ($title !== '')
                        <h2 class="bma-heading mt-3 font-headline text-[clamp(1.75rem,3.2vw,2.5rem)] {{ $surface['heading'] }}">{!! wp_kses($title, $titleAllowed) !!}</h2>
                    @endif

                    @if ($content !== '')
                        <div class="mt-4 text-base leading-7 {{ $surface['body'] }} [&_p]:m-0 [&_p+p]:mt-3">
                            {!! wp_kses_post(wpautop($content)) !!}
                        </div>
                    @endif
                </div>
            @endif

            @if ($items !== [])
                <div @class([
                    'grid gap-10 lg:grid-cols-5 lg:gap-14 lg:items-start' => $hasImage,
                ])>
                    <ul @class([
                        'bma-timeline__list',
                        'lg:col-span-3' => $hasImage,
                        'lg:order-2' => $hasImage && $imageSide === 'left',
                        'max-w-3xl' => ! $hasImage,
                    ])>
                        @foreach ($items as $item)
                            @php
                                $label = trim((string) ($item['label'] ?? ''));
                                $text = trim((string) ($item['text'] ?? ''));
                            @endphp
                            <li class="bma-timeline__item">
                                @if ($label !== '')
                                    <span class="bma-timeline__label font-headline {{ $surface['highlight'] }}">{{ $label }}</span>
                                @endif
                                @if ($text !== '')
                                    <div class="bma-timeline__body {{ $surface['body'] }}">{!! wp_kses_post($text) !!}</div>
                                @endif
                            </li>
                        @endforeach
                    </ul>

                    @if ($hasImage)
                        <figure @class([
                            'bma-timeline__media m-0 lg:col-span-2 lg:sticky lg:top-24',
                            'lg:order-1' => $imageSide === 'left',
                        ])>
                            {!! wp_get_attachment_image($imageId, 'large', false, [
                                'class' => 'block h-auto w-full rounded-card object-cover shadow-card',
                                'loading' => 'lazy',
                                'decoding' => 'async',
                            ]) !!}
                        </figure>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endif
