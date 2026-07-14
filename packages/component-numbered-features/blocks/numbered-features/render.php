<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * @package BalefireInc\Sage\NumberedFeatures
 */

declare( strict_types=1 );

use BalefireInc\Sage\NumberedFeatures\Renderer;

$items = $attributes['items'] ?? [];

echo Renderer::render( [
	'eyebrow'  => $attributes['eyebrow'] ?? '',
	'title'    => $attributes['title'] ?? '',
	'content'  => $attributes['content'] ?? '',
	'ctaLabel' => $attributes['ctaLabel'] ?? '',
	'ctaUrl'   => $attributes['ctaUrl'] ?? '',
	'items'    => is_array( $items ) ? $items : [],
], get_block_wrapper_attributes() );
