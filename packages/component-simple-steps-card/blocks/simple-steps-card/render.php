<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SimpleStepsCard
 */

declare( strict_types=1 );

use BalefireInc\Sage\SimpleStepsCard\Renderer;

echo Renderer::render( [
	'iconId' => $attributes['iconId'] ?? 0,
	'iconUrl' => isset( $attributes['iconUrl'] ) ? esc_url( $attributes['iconUrl'] ) : '',
	'iconAlt' => $attributes['iconAlt'] ?? '',
	'iconSvg' => $attributes['iconSvg'] ?? '',
	'content' => $content,
] );
