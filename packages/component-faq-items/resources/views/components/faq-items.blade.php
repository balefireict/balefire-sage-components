@props([
    'content' => '',
])

<div {{ $attributes->class(['faq-section-items']) }}>
    <h2 class="wp-block-heading has-text-align-center" id="faq-heading">Frequently Asked Questions</h2>
    {!! $content !!}
</div>
