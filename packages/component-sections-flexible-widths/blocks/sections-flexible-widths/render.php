<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SectionsFlexibleWidths
 */

declare( strict_types=1 );

use BalefireInc\Sage\SectionsFlexibleWidths\Renderer;

echo Renderer::render( [
	'containerWidth' => $attributes['containerWidth'] ?? 'max-w-7xl',
	'backgroundColor' => $attributes['backgroundColor'] ?? 'transparent',
	'htmlId' => $attributes['htmlId'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
