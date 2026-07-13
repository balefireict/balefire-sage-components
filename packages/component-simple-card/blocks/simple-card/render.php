<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SimpleCard
 */

declare( strict_types=1 );

use BalefireInc\Sage\SimpleCard\Renderer;

echo Renderer::render( [
	'showBorder' => $attributes['showBorder'] ?? true,
	'borderRadius' => $attributes['borderRadius'] ?? 'rounded-lg',
	'paddingSize' => $attributes['paddingSize'] ?? 'md',
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'imageClass' => $attributes['imageClass'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
