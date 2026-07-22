<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\HighlightBanner
 */

declare( strict_types=1 );

use BalefireInc\Sage\HighlightBanner\Renderer;

echo Renderer::render( [
	'tone' => $attributes['tone'] ?? 'white',
	'variant' => $attributes['variant'] ?? 'tint',
	'title' => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'ctaLabel' => $attributes['ctaLabel'] ?? '',
	'ctaUrl' => isset( $attributes['ctaUrl'] ) ? esc_url( $attributes['ctaUrl'] ) : '',
], get_block_wrapper_attributes() );
