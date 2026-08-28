@props([
    'question' => '',
    'answer' => '',
    'openByDefault' => false,
    // Tab labels this question belongs to. Empty means it only ever shows
    // under "All"; the wrapper decides whether a tab bar renders at all.
    'categories' => [],
])

@php
$question = trim($question);
$answer = trim($answer);

// Pipe-joined so the filter can substring-match one label without a parser,
// and so a label containing a comma stays intact.
$categoryList = implode('|', array_filter(array_map('trim', (array) $categories)));
@endphp

@if ($question !== '' || $answer !== '')
    <details
        {{ $attributes->class(['bma-faq-no-borders', 'faq-section-item']) }}
        @if ($categoryList !== '') data-faq-categories="{{ $categoryList }}" @endif
        @if ($openByDefault) open @endif
    >
        <summary class="faq-section-question">
            <h3 class="faq-section-question-title">{{ $question }}</h3>
        </summary>

        <div class="faq-section-answer">
            {!! wp_kses_post(wpautop($answer)) !!}
        </div>
    </details>
@endif
