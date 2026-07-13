<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\ImageTileCta
 */

declare( strict_types=1 );

use BalefireInc\Sage\ImageTileCta\Renderer;

echo Renderer::render( [
	'heroImageId' => $attributes['heroImageId'] ?? 0,
	'heroImageUrl' => isset( $attributes['heroImageUrl'] ) ? esc_url( $attributes['heroImageUrl'] ) : '',
	'heroImageAlt' => $attributes['heroImageAlt'] ?? '',
	'image1Id' => $attributes['image1Id'] ?? 0,
	'image1Url' => isset( $attributes['image1Url'] ) ? esc_url( $attributes['image1Url'] ) : '',
	'image1Alt' => $attributes['image1Alt'] ?? '',
	'image2Id' => $attributes['image2Id'] ?? 0,
	'image2Url' => isset( $attributes['image2Url'] ) ? esc_url( $attributes['image2Url'] ) : '',
	'image2Alt' => $attributes['image2Alt'] ?? '',
	'image3Id' => $attributes['image3Id'] ?? 0,
	'image3Url' => isset( $attributes['image3Url'] ) ? esc_url( $attributes['image3Url'] ) : '',
	'image3Alt' => $attributes['image3Alt'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
