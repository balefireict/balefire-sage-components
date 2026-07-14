<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * @package BalefireInc\Sage\Reviews
 */

declare( strict_types=1 );

use BalefireInc\Sage\Reviews\Renderer;

// Only load the carousel/lightbox script on pages that use the block.
if ( wp_script_is( 'balefire-reviews-view', 'registered' ) ) {
	wp_enqueue_script( 'balefire-reviews-view' );
}

echo Renderer::render( [
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title'   => $attributes['title'] ?? '',
	'count'   => $attributes['count'] ?? 9,
	'orderby' => $attributes['orderby'] ?? 'date',
], get_block_wrapper_attributes() );
