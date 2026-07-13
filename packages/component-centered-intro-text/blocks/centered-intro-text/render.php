<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\CenteredIntroText
 */

declare( strict_types=1 );

use BalefireInc\Sage\CenteredIntroText\Renderer;

echo Renderer::render( [
	'content' => $attributes['content'] ?? '',
	'maxWidth' => $attributes['maxWidth'] ?? 'narrow',
	'backgroundTone' => $attributes['backgroundTone'] ?? 'light',
] );
