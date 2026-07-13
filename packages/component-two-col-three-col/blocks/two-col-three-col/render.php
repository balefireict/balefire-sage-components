<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\TwoColThreeCol
 */

declare( strict_types=1 );

use BalefireInc\Sage\TwoColThreeCol\Renderer;

echo Renderer::render( [
	'cards' => $attributes['cards'] ?? [],
], get_block_wrapper_attributes() );
