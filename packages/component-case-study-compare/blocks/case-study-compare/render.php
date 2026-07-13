<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CaseStudyCompare
 */

declare( strict_types=1 );

use BalefireInc\Sage\CaseStudyCompare\Renderer;

echo Renderer::render( [
	'leftIconId' => $attributes['leftIconId'] ?? 0,
	'leftIconUrl' => isset( $attributes['leftIconUrl'] ) ? esc_url( $attributes['leftIconUrl'] ) : '',
	'leftIconAlt' => $attributes['leftIconAlt'] ?? '',
	'leftTitle' => $attributes['leftTitle'] ?? '',
	'leftBody' => $attributes['leftBody'] ?? '',
	'rightIconId' => $attributes['rightIconId'] ?? 0,
	'rightIconUrl' => isset( $attributes['rightIconUrl'] ) ? esc_url( $attributes['rightIconUrl'] ) : '',
	'rightIconAlt' => $attributes['rightIconAlt'] ?? '',
	'rightTitle' => $attributes['rightTitle'] ?? '',
	'rightBody' => $attributes['rightBody'] ?? '',
] );
