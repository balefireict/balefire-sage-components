<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\Container
 */

declare( strict_types=1 );

use BalefireInc\Sage\Container\Renderer;

echo Renderer::render( [
	'maxWidth' => $attributes['maxWidth'] ?? 'wide',
	'paddingInline' => $attributes['paddingInline'] ?? 'md',
	'content' => $content,
] );
