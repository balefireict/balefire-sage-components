<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\TeamMemberCentered
 */

declare( strict_types=1 );

use BalefireInc\Sage\TeamMemberCentered\Renderer;

echo Renderer::render( [
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'name' => $attributes['name'] ?? '',
	'title' => $attributes['title'] ?? '',
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'linkType' => $attributes['linkType'] ?? 'none',
	'pageId' => $attributes['pageId'] ?? 0,
] );
