<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CardIconBreak
 */

declare( strict_types=1 );

use BalefireInc\Sage\CardIconBreak\Renderer;

echo Renderer::render( [
	'iconId' => $attributes['iconId'] ?? 0,
	'iconUrl' => isset( $attributes['iconUrl'] ) ? esc_url( $attributes['iconUrl'] ) : '',
	'iconAlt' => $attributes['iconAlt'] ?? '',
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'openInNewTab' => $attributes['openInNewTab'] ?? false,
	'content' => $content,
], get_block_wrapper_attributes() );
