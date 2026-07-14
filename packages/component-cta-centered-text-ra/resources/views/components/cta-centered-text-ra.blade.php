@props([
    'preheader' => '',
    'title' => '',
    'ctaText' => '',
    'content' => '',
    'primaryLabel' => '',
    'primaryUrl' => '',
    'secondaryLabel' => '',
    'secondaryUrl' => '',
])

@php
$primaryUrl = $primaryUrl !== '' ? esc_url(str_starts_with($primaryUrl, '/') ? home_url($primaryUrl) : $primaryUrl) : '';
$secondaryUrl = $secondaryUrl !== '' ? esc_url(str_starts_with($secondaryUrl, '/') ? home_url($secondaryUrl) : $secondaryUrl) : '';

$hasButtons = ($primaryLabel !== '' && $primaryUrl !== '') || ($secondaryLabel !== '' && $secondaryUrl !== '');
$hasContent = ($preheader !== '' || $title !== '' || $ctaText !== '' || $content !== '' || $hasButtons);
@endphp

@if ($hasContent)
    <section {{ $attributes->class(['bf-cta-centered-text-ra', 'bg-inherit', 'text-center']) }}>
        <div class="mx-auto max-w-4xl">
            @if ($preheader !== '')
                <p class="text-[18px] font-semibold uppercase tracking-[0.2em] prehead-text mb-2 md:pt-9">
                    {{ $preheader }}
                </p>
            @endif

            @if ($title !== '')
                <h2 class="text-balance mx-auto">
                    {{ $title }}
                </h2>
            @endif

            @if ($content !== '')
                <div class="mx-auto mt-2 max-w-[52ch] text-md leading-[1.6] font-light cta-text">
                    {!! wp_kses_post($content) !!}
                </div>
            @endif

            @if ($hasButtons)
                <div class="btn-group mt-4 flex flex-col items-center justify-center gap-4 sm:flex-row">
                    @if ($primaryLabel !== '' && $primaryUrl !== '')
                        <a class="btn btn-white inline-flex items-center justify-center gap-1 align-center"
                           href="{{ $primaryUrl }}"
                           title="{{ $primaryLabel }}">
                            {{ $primaryLabel }}
                            <span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="10.913" height="9.379" viewBox="0 0 10.913 9.379" class="bma-hero-btn-arrow size-3 shrink-0 my-0.5 text-color-3">
                                    <path id="Path_471" data-name="Path 471" d="M10.652,13.735l3.964-3.967a.476.476,0,0,0,0-.672L10.652,5.13a.466.466,0,0,0-.332-.138.476.476,0,0,0-.34.809l3.155,3.155H4.817a.475.475,0,0,0,0,.951h8.318L9.979,13.063a.477.477,0,0,0,.342.809.467.467,0,0,0,.331-.136Z" transform="translate(-4.092 -4.742)" fill="#93a8a4" stroke="currentColor" stroke-width="0.5"/>
                                </svg>
                            </span>
                        </a>
                    @endif

                    @if ($secondaryLabel !== '' && $secondaryUrl !== '')
                        <a class="btn-transparent inline-flex items-center justify-center px-[var(--spacing-card)] py-[var(--spacing-card)] text-base font-semibold text-white no-underline transition hover:text-white/80"
                           href="{{ $secondaryUrl }}"
                           title="{{ $secondaryLabel }}">
                            {{ $secondaryLabel }}
                        </a>
                    @endif
                </div>
            @endif
        </div>
    </section>
@endif
