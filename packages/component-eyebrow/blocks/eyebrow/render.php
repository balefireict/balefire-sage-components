<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\Eyebrow
 */

declare( strict_types=1 );

use BalefireInc\Sage\Eyebrow\Renderer;

echo Renderer::render( [
	'text'          => $attributes['text'] ?? '',
	'showLeftMark'  => $attributes['showLeftMark'] ?? true,
	'showRightMark' => $attributes['showRightMark'] ?? true,
], get_block_wrapper_attributes() );
