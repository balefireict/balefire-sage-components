<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\FaqNoBorders
 */

declare( strict_types=1 );

use BalefireInc\Sage\FaqNoBorders\Renderer;

echo Renderer::render( [
	'question' => $attributes['question'] ?? '',
	'answer' => $attributes['answer'] ?? '',
	'openByDefault' => $attributes['openByDefault'] ?? false,
] );
