<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\Timeline
 */

declare( strict_types=1 );

use BalefireInc\Sage\Timeline\Renderer;

$items = $attributes['items'] ?? [];

echo Renderer::render( [
	'tone'           => $attributes['tone'] ?? 'white',
	'eyebrow'        => $attributes['eyebrow'] ?? '',
	'eyebrowVariant' => $attributes['eyebrowVariant'] ?? 'marks',
	'title'          => $attributes['title'] ?? '',
	'content'        => $attributes['content'] ?? '',
	'items'          => is_array( $items ) ? $items : [],
	'imageId'        => absint( $attributes['imageId'] ?? 0 ),
	'imageSide'      => $attributes['imageSide'] ?? 'right',
], get_block_wrapper_attributes() );
