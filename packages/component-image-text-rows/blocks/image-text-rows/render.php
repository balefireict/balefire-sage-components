<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\ImageTextRows
 */

declare( strict_types=1 );

use BalefireInc\Sage\ImageTextRows\Renderer;

echo Renderer::render( [
	'gapSize' => $attributes['gapSize'] ?? 'gap-4',
	'gapCustom' => $attributes['gapCustom'] ?? '',
	'alternateEvenRows' => $attributes['alternateEvenRows'] ?? false,
	'content' => $content,
], get_block_wrapper_attributes() );
