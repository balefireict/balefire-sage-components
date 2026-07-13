<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\PreheaderAndTitle
 */

declare( strict_types=1 );

use BalefireInc\Sage\PreheaderAndTitle\Renderer;

echo Renderer::render( [
	'preheader' => $attributes['preheader'] ?? 'Preheader',
	'title' => $attributes['title'] ?? 'Title',
	'textAlign' => $attributes['textAlign'] ?? 'center',
], get_block_wrapper_attributes() );
