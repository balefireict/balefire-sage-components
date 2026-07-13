@props([
    'question' => '',
    'answer' => '',
    'openByDefault' => false,
])

@php
$question = trim($question);
$answer = trim($answer);
@endphp

@if ($question !== '' || $answer !== '')
    <details
        {{ $attributes->class(['bma-faq-no-borders', 'faq-section-item']) }}
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
