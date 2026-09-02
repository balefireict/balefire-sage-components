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
	'eyebrow'        => $attributes['eyebrow'] ?? '',
	'eyebrowVariant' => $attributes['eyebrowVariant'] ?? 'marks',
	'title'          => $attributes['title'] ?? '',
	'titleAccent'    => $attributes['titleAccent'] ?? '',
	'content'        => $attributes['content'] ?? '',
	'ctaLabel'       => $attributes['ctaLabel'] ?? '',
	'ctaUrl'         => $attributes['ctaUrl'] ?? '',
	'primaryLabel'   => $attributes['primaryLabel'] ?? '',
	'primaryUrl'     => $attributes['primaryUrl'] ?? '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'   => $attributes['secondaryUrl'] ?? '',
	'items'          => is_array( $items ) ? $items : [],
	'layout'         => $attributes['layout'] ?? 'stack',
	'tone'           => $attributes['tone'] ?? 'white',
], get_block_wrapper_attributes() );
