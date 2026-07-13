<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\HeroHeadline
 */

declare( strict_types=1 );

use BalefireInc\Sage\HeroHeadline\Renderer;

echo Renderer::render( [
	'eyebrow'        => $attributes['eyebrow'] ?? '',
	'title'          => $attributes['title'] ?? '',
	'content'        => $attributes['content'] ?? '',
	'imageId'        => $attributes['imageId'] ?? 0,
	'imageUrl'       => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt'       => $attributes['imageAlt'] ?? '',
	'primaryLabel'   => $attributes['primaryLabel'] ?? '',
	'primaryUrl'     => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'   => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
], get_block_wrapper_attributes() );
