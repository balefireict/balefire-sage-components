<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * @package BalefireInc\Sage\ProductSwitcher
 */

declare( strict_types=1 );

use BalefireInc\Sage\ProductSwitcher\Renderer;

// Only load the switching script on pages that actually use the block.
if ( wp_script_is( 'balefire-product-switcher-view', 'registered' ) ) {
	wp_enqueue_script( 'balefire-product-switcher-view' );
}

$items = $attributes['items'] ?? [];

echo Renderer::render( [
	'eyebrow'  => $attributes['eyebrow'] ?? '',
	'title'    => $attributes['title'] ?? '',
	'content'  => $attributes['content'] ?? '',
	'source'     => $attributes['source'] ?? 'products',
	'categoryId' => $attributes['categoryId'] ?? 0,
	'attribute'  => $attributes['attribute'] ?? '',
	'items'    => is_array( $items ) ? $items : [],
	'ctaLabel' => $attributes['ctaLabel'] ?? '',
	'ctaUrl'   => $attributes['ctaUrl'] ?? '',
], get_block_wrapper_attributes() );
