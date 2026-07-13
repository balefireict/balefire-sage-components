<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\Split3565
 */

declare( strict_types=1 );

use BalefireInc\Sage\Split3565\Renderer;

echo Renderer::render( [
	'heading' => $attributes['heading'] ?? '',
	'content' => $attributes['content'] ?? '',
	'buttonLabel' => $attributes['buttonLabel'] ?? '',
	'buttonUrl' => isset( $attributes['buttonUrl'] ) ? esc_url( $attributes['buttonUrl'] ) : '',
	'mediaType' => $attributes['mediaType'] ?? 'image',
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'videoUrl' => isset( $attributes['videoUrl'] ) ? esc_url( $attributes['videoUrl'] ) : '',
	'gap' => $attributes['gap'] ?? '8',
	'reverse' => $attributes['reverse'] ?? false,
	'iconId' => $attributes['iconId'] ?? 0,
	'iconUrl' => isset( $attributes['iconUrl'] ) ? esc_url( $attributes['iconUrl'] ) : '',
	'iconAlt' => $attributes['iconAlt'] ?? '',
] );
