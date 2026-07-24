@props([
    'items' => [],
    'headingLevel' => 'h2',
])

@php
use BalefireInc\Sage\ProductHighlightBar\Icons;

// Only real heading levels (plus a non-heading escape hatch): the level is an
// editor-supplied string and lands straight in a tag name.
$allowedLevels = ['h2', 'h3', 'h4', 'h5', 'h6', 'p'];
$tag = in_array($headingLevel, $allowedLevels, true) ? $headingLevel : 'h2';

// Drop rows with nothing to show — an empty repeater row would otherwise
// render a bare icon.
$items = array_values(array_filter(
    is_array($items) ? $items : [],
    fn ($item) => is_array($item) && trim((string) ($item['heading'] ?? '')) !== ''
));
@endphp

@if ($items !== [])
    <section {{ $attributes->class([
        'bma-product-highlight-bar',
        'bg-grey-800 px-6 py-12 lg:px-20 lg:py-20',
    ]) }}>
        <div class="mx-auto flex w-full max-w-[1280px] flex-col gap-8 sm:grid sm:grid-cols-2 lg:flex lg:flex-row lg:items-start lg:justify-center">
            @foreach ($items as $item)
                @php
                    $heading = trim((string) ($item['heading'] ?? ''));
                    $linkText = trim((string) ($item['linkText'] ?? ''));
                    $linkUrl = trim((string) ($item['linkUrl'] ?? ''));
                    // home_url(), not site_url(): on Bedrock site_url() points at
                    // the /wp core directory, so a root-relative "/support" would
                    // resolve to "/wp/support".
                    $linkUrl = $linkUrl !== ''
                        ? esc_url(str_starts_with($linkUrl, '/') ? home_url($linkUrl) : $linkUrl)
                        : '';
                @endphp

                <div class="flex flex-1 items-start gap-3 rounded-semi">
                    {{-- Icon markup is either a bundled default or editor-pasted
                         SVG that has been through Svg::sanitize().

                         The sizing variant is [&_svg] (descendant), NOT [&>svg]
                         (child): a literal ">" inside a class attribute breaks
                         WordPress's the_content filters, which split HTML on a
                         naive <...> and would end this tag early — wptexturize
                         then curls the quotes in the rest of the attributes and
                         the markup falls apart. Never put ">" in an attribute
                         value in block output. --}}
                    <span class="bma-icon-breathe block size-8 shrink-0 text-white [&_svg]:size-full" aria-hidden="true">
                        {!! Icons::markup($item) !!}
                    </span>

                    <div class="flex flex-col items-start gap-[5px]">
                        <{{ $tag }} class="font-heading text-base font-bold uppercase leading-4 text-white">
                            {{ $heading }}
                        </{{ $tag }}>

                        @if ($linkText !== '')
                            @if ($linkUrl !== '')
                                <a
                                    href="{{ $linkUrl }}"
                                    class="group inline-flex items-center gap-[5px] font-mono text-label-m font-bold leading-3 text-white no-underline transition hover:text-primary"
                                >
                                    <span>{{ $linkText }}</span>
                                    <span class="block size-3 shrink-0 transition-transform group-hover:translate-x-0.5 [&_svg]:size-full">
                                        {!! Icons::arrow() !!}
                                    </span>
                                </a>
                            @else
                                {{-- No URL: the caption still shows, but without an
                                     arrow, which would promise a link that isn't there. --}}
                                <span class="font-mono text-label-m font-bold leading-3 text-white/70">
                                    {{ $linkText }}
                                </span>
                            @endif
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endif
