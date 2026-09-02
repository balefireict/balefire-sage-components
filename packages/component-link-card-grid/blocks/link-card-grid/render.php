<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\LinkCardGrid
 */

declare( strict_types=1 );

use BalefireInc\Sage\LinkCardGrid\Renderer;

echo Renderer::render( [
	'tone'      => $attributes['tone'] ?? 'grey',
	'eyebrow'   => $attributes['eyebrow'] ?? '',
	'title'     => $attributes['title'] ?? '',
	'content'   => $attributes['content'] ?? '',
	'ctaLabel'  => $attributes['ctaLabel'] ?? 'Read the guide',
	'columns'   => $attributes['columns'] ?? 3,
	'cardStyle' => $attributes['cardStyle'] ?? 'linked',
	'textAlign' => $attributes['textAlign'] ?? 'left',
	'iconStyle' => $attributes['iconStyle'] ?? 'disc',
	'items'     => is_array( $attributes['items'] ?? null ) ? $attributes['items'] : [],
], get_block_wrapper_attributes() );
