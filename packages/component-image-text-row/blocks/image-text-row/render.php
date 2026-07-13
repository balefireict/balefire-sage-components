<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\ImageTextRow
 */

declare( strict_types=1 );

use BalefireInc\Sage\ImageTextRow\Renderer;

echo Renderer::render( [
	'heading' => $attributes['heading'] ?? '',
	'body' => $attributes['body'] ?? '',
	'mediaId' => $attributes['mediaId'] ?? 0,
	'mediaUrl' => isset( $attributes['mediaUrl'] ) ? esc_url( $attributes['mediaUrl'] ) : '',
	'mediaAlt' => $attributes['mediaAlt'] ?? '',
	'layout' => $attributes['layout'] ?? 'inherit',
	'preheader' => $attributes['preheader'] ?? '',
	'subhead' => $attributes['subhead'] ?? '',
	'showArrow' => $attributes['showArrow'] ?? false,
	'imageCrop' => $attributes['imageCrop'] ?? 'default',
	'imageAspectRatio' => $attributes['imageAspectRatio'] ?? 'default',
	'imageRounded' => $attributes['imageRounded'] ?? false,
	'imagePosition' => $attributes['imagePosition'] ?? 'object-center',
	'columnGap' => $attributes['columnGap'] ?? 'gap-4',
	'columnGapCustom' => $attributes['columnGapCustom'] ?? '',
	'imageMode' => $attributes['imageMode'] ?? 'single',
	'images' => $attributes['images'] ?? [],
	'imageStackGap' => $attributes['imageStackGap'] ?? 'gap-4',
	'imageStackGapCustom' => $attributes['imageStackGapCustom'] ?? '',
	'content' => $content,
], get_block_wrapper_attributes() );
