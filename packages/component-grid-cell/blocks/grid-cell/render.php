<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\GridCell
 */

declare( strict_types=1 );

use BalefireInc\Sage\GridCell\Renderer;

echo Renderer::render( [
	'colSpan' => $attributes['colSpan'] ?? '6',
	'colSpanTablet' => $attributes['colSpanTablet'] ?? '',
	'colSpanMobile' => $attributes['colSpanMobile'] ?? '12',
	'rowSpan' => $attributes['rowSpan'] ?? '',
	'vAlign' => $attributes['vAlign'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
