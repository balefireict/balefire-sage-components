<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\HeroVideoHeader
 */

declare( strict_types=1 );

use BalefireInc\Sage\HeroVideoHeader\Renderer;

echo Renderer::render( [
	'videoUrl' => isset( $attributes['videoUrl'] ) ? esc_url( $attributes['videoUrl'] ) : '',
	'fallbackImage' => $attributes['fallbackImage'] ?? '',
	'subtitle' => $attributes['subtitle'] ?? '',
	'primaryLabel' => $attributes['primaryLabel'] ?? '',
	'primaryUrl' => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl' => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
], get_block_wrapper_attributes() );
