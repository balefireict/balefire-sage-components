<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CtaBand
 */

declare( strict_types=1 );

use BalefireInc\Sage\CtaBand\Renderer;

echo Renderer::render( [
	'tone'            => $attributes['tone'] ?? 'primary',
	'title'           => $attributes['title'] ?? '',
	'content'         => $attributes['content'] ?? '',
	'primaryLabel'    => $attributes['primaryLabel'] ?? '',
	'primaryUrl'      => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'primaryStyle'    => $attributes['primaryStyle'] ?? 'cta',
	'secondaryLabel'  => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'    => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
	'tertiaryLabel'   => $attributes['tertiaryLabel'] ?? '',
	'tertiaryUrl'     => isset( $attributes['tertiaryUrl'] ) ? esc_url( $attributes['tertiaryUrl'] ) : '',
	// Raw SVG: sanitized on output in the view (wp_kses allowlist).
	'tertiaryIconSvg' => $attributes['tertiaryIconSvg'] ?? '',
	'showMotif'       => ! empty( $attributes['showMotif'] ),
], get_block_wrapper_attributes() );
