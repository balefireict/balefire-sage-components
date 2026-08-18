@props([
    'tone' => 'white',
    'align' => 'left',
    'eyebrow' => '',
    'title' => '',
    'content' => '',
])

@php
$tones = [
    'white' => 'bg-white',
    'grey'  => 'bg-grey-25',
];
$tone = array_key_exists($tone, $tones) ? $tone : 'white';
$align = $align === 'center' ? 'center' : 'left';
@endphp

<section {{ $attributes->class(['bma-prose-section', 'bma-band', $tones[$tone]]) }} data-bma-tone="{{ $tone }}">
    <div class="mx-auto max-w-content px-6 md:px-10 xl:px-16">
        <div class="max-w-3xl {{ $align === 'center' ? 'mx-auto' : '' }}">
            {{-- Center alignment applies to the header only: body content
                 (prose, FAQ items) keeps its natural start alignment. --}}
            @if ($eyebrow !== '' || $title !== '')
                <div @class(['text-center' => $align === 'center'])>
                    @if ($eyebrow !== '')
                        {{-- Shared lockup — balefireict/component-eyebrow, the home-page eyebrow style. --}}
                        <x-bma::eyebrow :text="$eyebrow" @class(['mb-4', 'justify-center' => $align === 'center']) />
                    @endif

                    @if ($title !== '')
                        <h2 class="font-heading text-[clamp(1.8rem,3.6vw,2.5rem)] font-bold uppercase leading-[1.05] text-grey-900">{{ $title }}</h2>
                    @endif
                </div>
            @endif

            @if ($content !== '')
                <div @class(['bma-prose', 'mt-5' => $title !== '' || $eyebrow !== ''])>{!! $content !!}</div>
            @endif
        </div>
    </div>
</section>
