<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\FeaturesSection
 */

declare( strict_types=1 );

use BalefireInc\Sage\FeaturesSection\Renderer;

echo Renderer::render( [
	'heading' => $attributes['heading'] ?? '',
	'intro' => $attributes['intro'] ?? '',
	'maxWidth' => $attributes['maxWidth'] ?? 'wide',
	'backgroundTone' => $attributes['backgroundTone'] ?? 'white',
] );
