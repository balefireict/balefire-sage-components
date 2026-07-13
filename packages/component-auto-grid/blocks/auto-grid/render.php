<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\AutoGrid
 */

declare( strict_types=1 );

use BalefireInc\Sage\AutoGrid\Renderer;

echo Renderer::render( [
	'tagName' => $attributes['tagName'] ?? 'div',
	'columnsMobile' => $attributes['columnsMobile'] ?? '1',
	'columnsTablet' => $attributes['columnsTablet'] ?? '',
	'columnsDesktop' => $attributes['columnsDesktop'] ?? '3',
	'gap' => $attributes['gap'] ?? '6',
	'verticalAlign' => $attributes['verticalAlign'] ?? 'start',
	'content' => $content,
] );
