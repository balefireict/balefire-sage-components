<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SimpleImageCard
 */

declare( strict_types=1 );

use BalefireInc\Sage\SimpleImageCard\Renderer;

echo Renderer::render( [
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'title' => $attributes['title'] ?? '',
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'linkType' => $attributes['linkType'] ?? 'none',
	'pageId' => $attributes['pageId'] ?? 0,
], get_block_wrapper_attributes() );
