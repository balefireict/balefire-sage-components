@props([
    'showBorder' => true,
    'borderRadius' => 'rounded-lg',
    'paddingSize' => 'md',
    'imageId' => 0,
    'imageUrl' => '',
    'imageAlt' => '',
    'imageClass' => '',
    'content' => '',
])

@php
$borderRadius = sanitize_key($borderRadius);
$imageId = absint($imageId);

$paddingMap = [
    'none' => 'p-0',
    'sm'   => 'p-[var(--spacing-card,1.5rem)]',
    'md'   => 'p-8',
    'lg'   => 'p-16',
    'xl'   => 'p-20',
];
$paddingSize = isset($paddingMap[$paddingSize]) ? $paddingSize : 'md';

$classes = array_filter([
    'bma-simple-card',
    'flex',
    'flex-col',
    $borderRadius,
    $paddingMap[$paddingSize],
    $showBorder ? 'border border-gray-200 min-h-[325px] my-auto' : null,
]);

$imgClassString = trim('w-full h-auto object-cover ' . $imageClass);

$imageHtml = '';
if ($imageId > 0) {
    $imageHtml = wp_get_attachment_image($imageId, 'full', false, [
        'class' => $imgClassString,
        'alt'   => $imageAlt,
        'title' => get_the_title($imageId),
    ]);
}
if ($imageHtml === '' && $imageUrl !== '') {
    $imageHtml = sprintf(
        '<img src="%1$s" alt="%2$s" title="%2$s" class="%3$s" />',
        esc_url($imageUrl),
        esc_attr($imageAlt),
        esc_attr($imgClassString)
    );
}
@endphp

{{-- tailwind-safelist: rounded-none rounded-sm rounded-md rounded-lg rounded-xl rounded-2xl rounded-full p-0 p-8 p-16 p-20 --}}
<div {{ $attributes->class($classes) }}>
    <div class="flex flex-1 flex-col items-center justify-center gap-2">
        @if ($imageHtml !== '')
            {!! $imageHtml !!}
        @endif
        @if ($content !== '')
            {!! $content !!}
        @endif
    </div>
</div>
