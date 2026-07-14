@props([
    'text' => '',
    'showLeftMark' => true,
    'showRightMark' => true,
])

@php
// Block attributes and shortcode atts both arrive as strings ("1", "false", "").
$showLeftMark = filter_var($showLeftMark, FILTER_VALIDATE_BOOL);
$showRightMark = filter_var($showRightMark, FILTER_VALIDATE_BOOL);
@endphp

@if ($text !== '')
    <p {{ $attributes->class([
        'bma-eyebrow',
        'flex items-center gap-2 font-heading text-base font-bold uppercase text-primary',
    ]) }}>
        {{-- Both marks: viewBox min-x/min-y are non-zero so the Figma path data
             drops in unmodified. Each box matches the export's clip rect, which
             is why no <clipPath> is needed — and why no duplicate SVG ids leak
             into the DOM when several eyebrows share a page. currentColor keeps
             the marks locked to the label's text color. --}}
        @if ($showLeftMark)
            <svg class="h-3 w-[21px] shrink-0" viewBox="0 6 21 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 6.56492C21 6.25415 20.7496 6 20.4435 6H13.7678C13.4617 6 13.2113 6.25415 13.2113 6.56492C13.2113 6.87568 13.4617 7.12984 13.7678 7.12984H20.4435C20.7496 7.12984 21 6.87568 21 6.56492Z" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 10.1881C21 9.87735 20.7496 9.6232 20.4435 9.6232H0.556504C0.250367 9.6232 0 9.87735 0 10.1881C0 10.4989 0.250367 10.753 0.556504 10.753H20.4441C20.7502 10.753 21.0006 10.4989 21.0006 10.1881H21Z" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 13.8119C21 13.5011 20.7496 13.247 20.4435 13.247H9.13248C8.82634 13.247 8.57597 13.5011 8.57597 13.8119C8.57597 14.1227 8.82634 14.3768 9.13248 14.3768H20.4435C20.7496 14.3768 21 14.1227 21 13.8119Z" />
                <path fill-rule="evenodd" clip-rule="evenodd" d="M21 17.4351C21 17.1243 20.7496 16.8702 20.4435 16.8702H16.1321C15.8259 16.8702 15.5756 17.1243 15.5756 17.4351C15.5756 17.7459 15.8259 18 16.1321 18H20.4435C20.7496 18 21 17.7459 21 17.4351Z" />
            </svg>
        @endif

        <span>{{ $text }}</span>

        @if ($showRightMark)
            <svg class="h-3 w-[28px] shrink-0" viewBox="158 6 28 12" fill="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                <path d="M186 11.9991C186 9.5208 169.065 6 158 6V18C169.065 18 186 14.4834 186 11.9991ZM159.69 9.38289V7.70499C164.132 7.8387 169.559 8.47468 174.58 9.46299C179.59 10.4489 182.287 11.3938 183.562 12.0003C175.319 10.208 166.245 9.37144 159.69 9.38289Z" />
            </svg>
        @endif
    </p>
@endif
