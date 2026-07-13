<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SectionHeading
 */

declare( strict_types=1 );

use BalefireInc\Sage\SectionHeading\Renderer;

echo Renderer::render( [
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title' => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'contentAlign' => $attributes['contentAlign'] ?? 'left',
	'maxWidth' => $attributes['maxWidth'] ?? '',
	'backgroundTone' => $attributes['backgroundTone'] ?? 'transparent',
], get_block_wrapper_attributes() );
