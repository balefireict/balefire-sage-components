<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SplitFeature
 */

declare( strict_types=1 );

use BalefireInc\Sage\SplitFeature\Renderer;

echo Renderer::render( [
	'tone' => $attributes['tone'] ?? 'white',
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title' => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'primaryLabel' => $attributes['primaryLabel'] ?? '',
	'primaryUrl' => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl' => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
	'mediaType' => $attributes['mediaType'] ?? 'content',
	'mediaSide' => $attributes['mediaSide'] ?? 'right',
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'statValue' => $attributes['statValue'] ?? '',
	'statLabel' => $attributes['statLabel'] ?? '',
	'statNote' => $attributes['statNote'] ?? '',
	'mediaContent' => $content,
], get_block_wrapper_attributes() );
