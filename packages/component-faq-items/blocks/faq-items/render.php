<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\FaqItems
 */

declare( strict_types=1 );

use BalefireInc\Sage\FaqItems\Renderer;

echo Renderer::render( [
	'content' => $content,
], get_block_wrapper_attributes() );
