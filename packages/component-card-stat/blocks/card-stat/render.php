<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CardStat
 */

declare( strict_types=1 );

use BalefireInc\Sage\CardStat\Renderer;

echo Renderer::render( [
	'title' => $attributes['title'] ?? '',
	'iconType' => $attributes['iconType'] ?? 'svg',
	'iconSvgCode' => $attributes['iconSvgCode'] ?? '',
	'iconId' => $attributes['iconId'] ?? 0,
	'iconUrl' => isset( $attributes['iconUrl'] ) ? esc_url( $attributes['iconUrl'] ) : '',
	'iconAlt' => $attributes['iconAlt'] ?? '',
	'statLeftValue' => $attributes['statLeftValue'] ?? '',
	'statLeftLabel' => $attributes['statLeftLabel'] ?? '',
	'statRightValue' => $attributes['statRightValue'] ?? '',
	'statRightLabel' => $attributes['statRightLabel'] ?? '',
], get_block_wrapper_attributes() );
