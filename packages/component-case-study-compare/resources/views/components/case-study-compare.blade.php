@props([
    'leftIconId' => 0,
    'leftIconUrl' => '',
    'leftIconAlt' => '',
    'leftTitle' => '',
    'leftBody' => '',
    'rightIconId' => 0,
    'rightIconUrl' => '',
    'rightIconAlt' => '',
    'rightTitle' => '',
    'rightBody' => '',
])

@php
$leftIconId = absint($leftIconId);
$rightIconId = absint($rightIconId);

$hasLeftCard = ($leftIconId > 0 || $leftIconUrl !== '' || $leftTitle !== '' || $leftBody !== '');
$hasRightCard = ($rightIconId > 0 || $rightIconUrl !== '' || $rightTitle !== '' || $rightBody !== '');

$renderCardImage = static function (int $iconId, string $iconUrl, string $iconAlt): string {
    $html = '';
    if ($iconId > 0) {
        $html = wp_get_attachment_image($iconId, 'full', false, [
            'class' => 'h-16 w-16 object-contain',
            'alt'   => $iconAlt,
            'title' => get_the_title($iconId),
        ]);
    }
    if ($html === '' && $iconUrl !== '') {
        $html = sprintf(
            '<img src="%1$s" alt="%2$s" title="%2$s" class="h-16 w-16 object-contain" />',
            esc_url($iconUrl),
            esc_attr($iconAlt)
        );
    }
    return $html;
};

$leftIconHtml = $renderCardImage($leftIconId, $leftIconUrl, $leftIconAlt);
$rightIconHtml = $renderCardImage($rightIconId, $rightIconUrl, $rightIconAlt);
@endphp

@if ($hasLeftCard || $hasRightCard)
    <div {{ $attributes->class(['bma-case-study-compare', 'text-center']) }}>
        <div class="bma-case-study-compare__row flex flex-col md:flex-row items-center justify-center gap-6 md:gap-8 md:my-14">
            @if ($hasLeftCard)
                <div class="bma-case-study-compare__card w-full md:w-1/2 flex flex-col items-center border border-[var(--color-12)] rounded-[var(--radius-card)] px-8 pt-12 pb-14">
                    @if ($leftIconHtml !== '')
                        <div class="bma-case-study-compare__icon -mt-24 mb-8">
                            <div class="icon-bg rounded-full h-[100px] w-[100px] flex items-center justify-center">
                                {!! $leftIconHtml !!}
                            </div>
                        </div>
                    @endif
                    @if ($leftTitle !== '')
                        <h3 class="leading-tight">{{ $leftTitle }}</h3>
                    @endif
                    @if ($leftBody !== '')
                        <div class="bma-case-study-compare__text md:max-w-[44ch] text-base leading-7">{!! wp_kses_post($leftBody) !!}</div>
                    @endif
                </div>
            @endif

            <div class="bma-case-study-compare__arrow rotate-90 md:rotate-0 shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" width="37.224" height="37.225" viewBox="0 0 37.224 37.225" aria-hidden="true">
                    <path d="M20.627,1.995A18.613,18.613,0,1,0,39.238,20.608,18.622,18.622,0,0,0,20.627,1.995Zm0,2.792a15.82,15.82,0,1,1-15.82,15.82,15.827,15.827,0,0,1,15.82-15.82Zm2.845,8.778s2.8,2.8,6.06,6.067a1.4,1.4,0,0,1,0,1.975c-3.263,3.265-6.058,6.065-6.058,6.065a1.382,1.382,0,0,1-.981.4A1.4,1.4,0,0,1,21.5,25.7l3.682-3.68H12.718a1.4,1.4,0,0,1,0-2.792H25.18L21.5,15.54a1.389,1.389,0,0,1,.011-1.962,1.4,1.4,0,0,1,.989-.413A1.373,1.373,0,0,1,23.471,13.565Z" transform="translate(-2.014 -1.995)" fill="#93a8a4"/>
                </svg>
            </div>

            @if ($hasRightCard)
                <div class="bma-case-study-compare__card w-full md:w-1/2 flex flex-col items-center border border-[var(--color-12)] rounded-[var(--radius-card)] px-8 pt-12 pb-14">
                    @if ($rightIconHtml !== '')
                        <div class="bma-case-study-compare__icon -mt-24 mb-8">
                            <div class="icon-bg rounded-full h-[100px] w-[100px] flex items-center justify-center">
                                {!! $rightIconHtml !!}
                            </div>
                        </div>
                    @endif
                    @if ($rightTitle !== '')
                        <h3 class="leading-tight">{{ $rightTitle }}</h3>
                    @endif
                    @if ($rightBody !== '')
                        <div class="bma-case-study-compare__text md:max-w-[44ch] text-base leading-7">{!! wp_kses_post($rightBody) !!}</div>
                    @endif
                </div>
            @endif
        </div>
    </div>
@endif
