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

$bma_variant = $attributes['variant'] ?? 'tint';
$bma_wrapper = get_block_wrapper_attributes();

// The inline callout lives inside another block's column; the block-level
// "align" default (full) must not bleed it out of that column.
if ( $bma_variant === 'inline' ) {
	$bma_wrapper = (string) preg_replace( '/\balign(?:full|wide)\b\s*/', '', $bma_wrapper );
}

echo Renderer::render( [
	'tone' => $attributes['tone'] ?? 'white',
	'variant' => $bma_variant,
	'intent' => $attributes['intent'] ?? 'info',
	'title' => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'ctaLabel' => $attributes['ctaLabel'] ?? '',
	'ctaUrl' => isset( $attributes['ctaUrl'] ) ? esc_url( $attributes['ctaUrl'] ) : '',
	// Raw SVG: sanitized on output in the view (wp_kses allowlist).
	'iconSvg' => $attributes['iconSvg'] ?? '',
	'iconId' => (int) ( $attributes['iconId'] ?? 0 ),
], $bma_wrapper );
