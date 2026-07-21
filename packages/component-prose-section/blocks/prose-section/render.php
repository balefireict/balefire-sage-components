<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\ProseSection
 */

declare( strict_types=1 );

use BalefireInc\Sage\ProseSection\Renderer;

echo Renderer::render( [
	'tone'    => $attributes['tone'] ?? 'white',
	'align'   => $attributes['contentAlign'] ?? 'left',
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title'   => $attributes['title'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
