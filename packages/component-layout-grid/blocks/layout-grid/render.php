<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\LayoutGrid
 */

declare( strict_types=1 );

use BalefireInc\Sage\LayoutGrid\Renderer;

echo Renderer::render( [
	'columns' => $attributes['columns'] ?? '3',
	'columnsTablet' => $attributes['columnsTablet'] ?? '',
	'gap' => $attributes['gap'] ?? '6',
	'content' => $content,
] );
