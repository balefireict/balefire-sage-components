<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\InfoCards
 */

declare( strict_types=1 );

use BalefireInc\Sage\InfoCards\Renderer;

echo Renderer::render( [
	'tone'    => $attributes['tone'] ?? 'white',
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title'   => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'variant' => $attributes['variant'] ?? 'check',
	'columns' => $attributes['columns'] ?? 3,
	'items'   => is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
], get_block_wrapper_attributes() );
