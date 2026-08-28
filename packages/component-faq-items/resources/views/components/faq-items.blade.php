@props([
    'content' => '',
    'tabs' => [],
])

@php
$tabs = array_values(array_filter(array_map('trim', (array) $tabs)));

// One id per instance so two FAQ blocks on a page cannot cross their
// aria-controls wiring.
$uid = 'bma-faq-' . wp_unique_id();
@endphp

<div {{ $attributes->class(['faq-section-items']) }} @if ($tabs !== []) data-faq-root @endif>
    <h2 class="wp-block-heading has-text-align-center" id="faq-heading">Frequently Asked Questions</h2>

    @if ($tabs !== [])
        {{-- "All" is always first and always the resting state, so the page
             still shows every question with JavaScript unavailable — the
             filter only ever hides things once it runs. --}}
        <div class="faq-section-tabs" role="tablist" aria-label="{{ __('Filter questions by topic', 'balefire') }}">
            <button
                type="button"
                role="tab"
                class="faq-section-tab"
                id="{{ $uid }}-tab-all"
                aria-selected="true"
                data-faq-tab="">{{ __('All', 'balefire') }}</button>

            @foreach ($tabs as $i => $tab)
                <button
                    type="button"
                    role="tab"
                    class="faq-section-tab"
                    id="{{ $uid }}-tab-{{ $i }}"
                    aria-selected="false"
                    data-faq-tab="{{ $tab }}">{{ $tab }}</button>
            @endforeach
        </div>

        {{-- Announced when a filter changes; the questions themselves are not
             a live region, so a screen reader would otherwise get no feedback
             from pressing a tab. --}}
        <p class="faq-section-count" role="status" aria-live="polite" data-faq-count></p>
    @endif

    <div class="faq-section-list" data-faq-list>
        {!! $content !!}
    </div>
</div>
