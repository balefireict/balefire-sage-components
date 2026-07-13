<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\Section
 */

declare( strict_types=1 );

use BalefireInc\Sage\Section\Renderer;

echo Renderer::render( [
	'backgroundColor' => $attributes['backgroundColor'] ?? 'transparent',
	'htmlId' => $attributes['htmlId'] ?? '',
	'content' => $content,
] );
