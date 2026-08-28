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

/**
 * Tab labels, in the order they should appear.
 *
 * An explicit `tabs` attribute wins, so an editor can order the bar however
 * they like. With none set the labels are discovered from the children in the
 * order they first appear, which is the sensible default and means a new
 * category on an item is enough to get a tab.
 */
$tabs = array_values( array_filter( array_map( 'trim', (array) ( $attributes['tabs'] ?? [] ) ) ) );

if ( $tabs === [] ) {
	foreach ( $block->parsed_block['innerBlocks'] ?? [] as $inner ) {
		foreach ( (array) ( $inner['attrs']['categories'] ?? [] ) as $category ) {
			$category = trim( (string) $category );

			if ( $category !== '' && ! in_array( $category, $tabs, true ) ) {
				$tabs[] = $category;
			}
		}
	}
}

echo Renderer::render( [
	'content' => $content,
	'tabs'    => $tabs,
], get_block_wrapper_attributes() );
