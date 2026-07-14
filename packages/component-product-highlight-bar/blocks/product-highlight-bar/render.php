<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * Item fields are passed through as-is; the view escapes the text and runs
 * pasted SVG through Svg::sanitize(). Escaping lives in one place rather than
 * being split across the block and shortcode entry points.
 *
 * @package BalefireInc\Sage\ProductHighlightBar
 */

declare( strict_types=1 );

use BalefireInc\Sage\ProductHighlightBar\Icons;
use BalefireInc\Sage\ProductHighlightBar\Renderer;

$items = $attributes['items'] ?? Icons::defaultItems();

echo Renderer::render( [
	'items'        => is_array( $items ) ? $items : [],
	'headingLevel' => $attributes['headingLevel'] ?? 'h2',
], get_block_wrapper_attributes() );
