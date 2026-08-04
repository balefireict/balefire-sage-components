<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\ProductCallout
 */

declare( strict_types=1 );

use BalefireInc\Sage\ProductCallout\Renderer;

echo Renderer::render( [
	'title'    => $attributes['title'] ?? '',
	'text'     => $attributes['text'] ?? '',
	'products' => $attributes['products'] ?? '',
], get_block_wrapper_attributes() );
