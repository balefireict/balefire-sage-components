<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block). $content is the
 * rendered InnerBlocks HTML (the intro column's callout slot).
 *
 * @package BalefireInc\Sage\ProcessSteps
 */

declare( strict_types=1 );

use BalefireInc\Sage\ProcessSteps\Renderer;

$items = $attributes['items'] ?? [];

echo Renderer::render( [
	'tone'           => $attributes['tone'] ?? 'white',
	'layout'         => $attributes['layout'] ?? 'stack',
	'direction'      => $attributes['direction'] ?? 'vertical',
	'eyebrow'        => $attributes['eyebrow'] ?? '',
	'eyebrowVariant' => $attributes['eyebrowVariant'] ?? 'marks',
	'title'          => $attributes['title'] ?? '',
	'content'        => $attributes['content'] ?? '',
	'items'          => is_array( $items ) ? $items : [],
	'palette'        => $attributes['palette'] ?? '',
	'numberStyle'    => $attributes['numberStyle'] ?? 'disc',
	'primaryLabel'   => $attributes['primaryLabel'] ?? '',
	'primaryUrl'     => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'   => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
	'innerContent'   => is_string( $content ) ? $content : '',
], get_block_wrapper_attributes() );
