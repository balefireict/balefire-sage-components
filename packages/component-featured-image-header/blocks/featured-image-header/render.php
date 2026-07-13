<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\FeaturedImageHeader
 */

declare( strict_types=1 );

use BalefireInc\Sage\FeaturedImageHeader\Renderer;

echo Renderer::render( [
	'intro' => $attributes['intro'] ?? '',
	'showOnFrontPage' => $attributes['showOnFrontPage'] ?? false,
], get_block_wrapper_attributes() );
