<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\GridRow
 */

declare( strict_types=1 );

use BalefireInc\Sage\GridRow\Renderer;

echo Renderer::render( [
	'gap' => $attributes['gap'] ?? '6',
	'minColumnWidth' => $attributes['minColumnWidth'] ?? '',
	'content' => $content,
] );
