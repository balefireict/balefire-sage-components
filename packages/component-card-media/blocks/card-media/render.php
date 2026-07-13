<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CardMedia
 */

declare( strict_types=1 );

use BalefireInc\Sage\CardMedia\Renderer;

echo Renderer::render( [
	'logoType' => $attributes['logoType'] ?? 'image',
	'logoSvgCode' => $attributes['logoSvgCode'] ?? '',
	'mediaType' => $attributes['mediaType'] ?? 'image',
	'svgCode' => $attributes['svgCode'] ?? '',
	'logoId' => $attributes['logoId'] ?? 0,
	'logoUrl' => isset( $attributes['logoUrl'] ) ? esc_url( $attributes['logoUrl'] ) : '',
	'logoAlt' => $attributes['logoAlt'] ?? '',
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'title' => $attributes['title'] ?? '',
	'text' => $attributes['text'] ?? '',
	'linkText' => $attributes['linkText'] ?? '',
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'linkType' => $attributes['linkType'] ?? 'none',
	'pageId' => $attributes['pageId'] ?? 0,
	'openInNewTab' => $attributes['openInNewTab'] ?? false,
] );
