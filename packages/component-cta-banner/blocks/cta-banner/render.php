<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CtaBanner
 */

declare( strict_types=1 );

use BalefireInc\Sage\CtaBanner\Renderer;

echo Renderer::render( [
	'eyebrow'        => $attributes['eyebrow'] ?? '',
	'title'          => $attributes['title'] ?? '',
	'content'        => $attributes['content'] ?? '',
	'tone'           => $attributes['tone'] ?? 'primary',
	'primaryLabel'   => $attributes['primaryLabel'] ?? '',
	'primaryUrl'     => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'primaryStyle'   => $attributes['primaryStyle'] ?? 'solid',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'   => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
] );
