<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SimpleIconStackedCards
 */

declare( strict_types=1 );

use BalefireInc\Sage\SimpleIconStackedCards\Renderer;

echo Renderer::render( [
	'iconId' => $attributes['iconId'] ?? 0,
	'iconUrl' => isset( $attributes['iconUrl'] ) ? esc_url( $attributes['iconUrl'] ) : '',
	'iconAlt' => $attributes['iconAlt'] ?? '',
	'iconSvg' => $attributes['iconSvg'] ?? '',
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'linkType' => $attributes['linkType'] ?? 'none',
	'pageId' => $attributes['pageId'] ?? 0,
	'openInNewTab' => $attributes['openInNewTab'] ?? false,
	'content' => $content,
], get_block_wrapper_attributes() );
