<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\FeatureRow
 */

declare( strict_types=1 );

use BalefireInc\Sage\FeatureRow\Renderer;

echo Renderer::render( [
	'heading' => $attributes['heading'] ?? '',
	'body' => $attributes['body'] ?? '',
	'mediaId' => $attributes['mediaId'] ?? 0,
	'mediaUrl' => isset( $attributes['mediaUrl'] ) ? esc_url( $attributes['mediaUrl'] ) : '',
	'mediaAlt' => $attributes['mediaAlt'] ?? '',
	'imageClass' => $attributes['imageClass'] ?? '',
	'linkType' => $attributes['linkType'] ?? 'none',
	'pageId' => $attributes['pageId'] ?? 0,
	'url' => isset( $attributes['url'] ) ? esc_url( $attributes['url'] ) : '',
	'linkText' => $attributes['linkText'] ?? '',
], get_block_wrapper_attributes() );
