<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 *
 * @package BalefireInc\Sage\MissionCards
 */

declare( strict_types=1 );

use BalefireInc\Sage\MissionCards\Renderer;

$term_ids = $attributes['termIds'] ?? [];

echo Renderer::render( [
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title'   => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'termIds' => is_array( $term_ids ) ? $term_ids : [],
	'limit'   => $attributes['limit'] ?? 3,
], get_block_wrapper_attributes() );
