@props([
    'title' => '',
    'text' => '',
    'products' => [],
])

@php
// Mirrors the product-page mounting panel (theme partial
// partials/product/sections/mounting.blade.php, Figma node 2057:2593):
// white bordered panel, heading, supporting copy, grey product mini-cards.
$products = array_values(array_filter(
    is_array($products) ? $products : [],
    static fn ($p): bool => $p instanceof \WC_Product
));
@endphp

@if ($title !== '' || $text !== '' || $products !== [])
    <div {{ $attributes->class(['bma-product-callout', 'not-prose', 'rounded-semi', 'border', 'border-grey-50', 'bg-white', 'p-6', 'lg:p-10']) }}>
        <div class="flex flex-col gap-5">
            @if ($title !== '')
                <h3 class="font-heading text-4xl font-bold uppercase leading-none text-grey-800">{{ $title }}</h3>
            @endif

            @if ($text !== '')
                <p class="text-body-xs text-grey-400">{{ $text }}</p>
            @endif

            @foreach ($products as $product)
                @php($adds = $product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock())
                <div class="flex gap-5 rounded-semi bg-grey-25 p-6 lg:p-8">
                    <a href="{{ get_permalink($product->get_id()) }}" tabindex="-1" aria-hidden="true" class="block size-[116px] shrink-0 overflow-hidden rounded-semi bg-white">
                        {!! wp_get_attachment_image($product->get_image_id() ?: 0, 'woocommerce_gallery_thumbnail', false, [
                            'class' => 'size-full! object-contain',
                            'loading' => 'lazy',
                            'decoding' => 'async',
                            'alt' => '',
                        ]) !!}
                    </a>
                    <div class="flex min-w-0 flex-1 flex-col items-start gap-2.5">
                        <a href="{{ get_permalink($product->get_id()) }}" class="font-heading text-xl font-bold uppercase leading-7 text-grey-800 hover:text-primary">
                            {{ $product->get_name() }}
                        </a>
                        @if ($product->get_sku() !== '')
                            <span class="text-body-xs text-grey-400">{{ $product->get_sku() }}</span>
                        @endif
                        <span class="text-body-m font-bold text-primary [&_.amount]:text-primary">{!! $product->get_price_html() !!}</span>
                        <a
                            href="{{ $adds ? esc_url($product->add_to_cart_url()) : get_permalink($product->get_id()) }}"
                            @if ($adds) rel="nofollow" @endif
                            class="inline-flex rounded bg-primary px-4 py-2.5 font-mono text-body-xs font-bold uppercase text-white no-underline transition-colors hover:bg-primary-dark"
                        >
                            {{ $adds ? __('Add', 'balefire') : __('View', 'balefire') }}
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif
