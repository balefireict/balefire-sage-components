@props([
    'tone' => 'white',
    'layout' => 'stack',
    'direction' => 'vertical',
    'eyebrow' => '',
    'eyebrowVariant' => 'marks',
    'title' => '',
    'content' => '',
    'items' => [],
    'palette' => '',
    'numberStyle' => 'disc',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
    'innerContent' => '',
])

@php
use BalefireInc\Sage\ProcessSteps\Renderer;
use BalefireInc\Sage\Support\SectionStyles;

$tone = array_key_exists($tone, SectionStyles::tones()) ? $tone : 'white';
$surface = SectionStyles::surface($tone);
$layout = $layout === 'split' ? 'split' : 'stack';
$direction = $direction === 'horizontal' ? 'horizontal' : 'vertical';
$numberStyle = $numberStyle === 'plain' ? 'plain' : 'disc';
$eyebrowVariant = in_array($eyebrowVariant, ['marks', 'bar', 'plain'], true) ? $eyebrowVariant : 'marks';

// Drop rows with nothing to say — an empty repeater row would render a bare number.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item)
        && (trim((string) ($item['title'] ?? '')) !== '' || trim((string) ($item['text'] ?? '')) !== '')
));

// Palette: comma-separated CSS colours cycled over items that carry no colour
// of their own. Empty palette = every disc falls back to the accent token.
$palette = array_values(array_filter(array_map(
    fn ($c) => Renderer::cssColor((string) $c),
    explode(',', (string) $palette)
)));

$titleAllowed = ['em' => [], 'br' => [], 'span' => ['class' => true]];

// home_url(), not site_url(): Bedrock puts core in /wp.
$resolveUrl = fn (string $url) => $url !== ''
    ? esc_url(str_starts_with($url, '/') ? home_url($url) : $url)
    : '';
$primaryUrl = $resolveUrl(trim((string) $primaryUrl));
$secondaryUrl = $resolveUrl(trim((string) $secondaryUrl));
$hasPrimary = $primaryLabel !== '' && $primaryUrl !== '';
$hasSecondary = $secondaryLabel !== '' && $secondaryUrl !== '';
$hasButtons = $hasPrimary || $hasSecondary;

$innerContent = trim((string) $innerContent);
$hasIntro = $eyebrow !== '' || $title !== '' || $content !== '' || $innerContent !== '' || $hasButtons;

$headingSize = $layout === 'split'
    ? 'text-[clamp(1.75rem,3.2vw,2.5rem)]'
    : 'text-[clamp(1.9rem,3.6vw,2.75rem)]';
@endphp

@if ($items !== [] || $hasIntro)
    <section {{ $attributes->class([
        'bma-process-steps',
        'bma-band',
        'bma-process-steps--' . $layout,
        'bma-process-steps--' . $direction,
        'bma-process-steps--' . $numberStyle,
        $surface['section'],
    ]) }} data-bma-tone="{{ $tone }}">
        <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
            <div @class([
                'grid gap-10 lg:grid-cols-12 lg:gap-12' => $layout === 'split',
                'flex flex-col gap-12' => $layout === 'stack',
            ])>
                @if ($hasIntro)
                    <div @class([
                        'bma-process-steps__intro',
                        'lg:col-span-5' => $layout === 'split',
                        'mx-auto w-full max-w-3xl text-center' => $layout === 'stack',
                    ])>
                        @if ($eyebrow !== '')
                            <x-bma::eyebrow :text="$eyebrow" :variant="$eyebrowVariant" :class="$layout === 'stack' ? 'justify-center' : ''" />
                        @endif

                        @if ($title !== '')
                            <h2 class="bma-heading mt-3 font-headline {{ $headingSize }} {{ $surface['heading'] }}">{!! wp_kses($title, $titleAllowed) !!}</h2>
                        @endif

                        @if ($content !== '')
                            <div class="mt-4 text-base leading-7 {{ $surface['body'] }} [&_p]:m-0 [&_p+p]:mt-3">
                                {!! wp_kses_post(wpautop($content)) !!}
                            </div>
                        @endif

                        @if ($innerContent !== '')
                            {{-- InnerBlocks slot: a callout dropped into the intro column. --}}
                            <div class="bma-process-steps__inner mt-6">
                                {!! $innerContent !!}
                            </div>
                        @endif

                        @if ($hasButtons)
                            <div @class([
                                'mt-8 flex flex-wrap gap-3',
                                'justify-center' => $layout === 'stack',
                            ])>
                                @if ($hasPrimary)
                                    <a href="{{ $primaryUrl }}" class="bma-btn bma-btn--primary">{{ $primaryLabel }}</a>
                                @endif
                                @if ($hasSecondary)
                                    <a href="{{ $secondaryUrl }}" class="bma-btn bma-btn--outline">{{ $secondaryLabel }}</a>
                                @endif
                            </div>
                        @endif
                    </div>
                @endif

                @if ($items !== [])
                    <ol @class([
                        'bma-process-steps__list',
                        'bma-process-steps__list--' . $direction,
                        'lg:col-span-7' => $layout === 'split',
                        'mx-auto w-full' => $layout === 'stack',
                    ]) style="--bma-steps-count: {{ count($items) }}">
                        @foreach ($items as $i => $item)
                            @php
                                $color = Renderer::cssColor((string) ($item['color'] ?? ''));
                                if ($color === '' && $palette !== []) {
                                    $color = $palette[$i % count($palette)];
                                }
                                $itemTitle = trim((string) ($item['title'] ?? ''));
                                $itemText = trim((string) ($item['text'] ?? ''));
                            @endphp
                            <li class="bma-process-steps__step" @if ($color !== '') style="--item-accent: {{ esc_attr($color) }}" @endif>
                                <span class="bma-process-steps__num font-headline" aria-hidden="true">{{ $i + 1 }}</span>
                                <div class="bma-process-steps__body">
                                    @if ($itemTitle !== '')
                                        <h3 class="bma-process-steps__title font-headline text-xl font-semibold leading-snug {{ $surface['heading'] }}">{{ $itemTitle }}</h3>
                                    @endif
                                    @if ($itemText !== '')
                                        <p class="bma-process-steps__text text-base leading-relaxed {{ $surface['body'] }}">{!! wp_kses_post($itemText) !!}</p>
                                    @endif
                                </div>
                            </li>
                        @endforeach
                    </ol>
                @endif
            </div>
        </div>
    </section>
@endif
