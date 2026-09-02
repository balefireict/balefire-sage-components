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

use BalefireInc\Sage\FaqItems\Items;
use BalefireInc\Sage\FaqItems\Renderer;

$source = ( $attributes['source'] ?? 'inner' ) === 'query' ? 'query' : 'inner';

/**
 * Tab labels, in the order they should appear.
 *
 * An explicit `tabs` attribute wins, so an editor can order the bar however
 * they like. With none set the labels are discovered from the children in the
 * order they first appear, which is the sensible default and means a new
 * category on an item is enough to get a tab. Query-mode items carry no
 * categories, so discovery only runs for inner blocks.
 */
$tabs = array_values( array_filter( array_map( 'trim', (array) ( $attributes['tabs'] ?? [] ) ) ) );

/**
 * Question/answer pairs feeding the FAQPage JSON-LD. In query mode these are
 * the queried posts; in inner mode they are read off the child blocks'
 * attributes, which is the same data the children render from.
 */
$items       = [];
$schemaItems = [];

if ( $source === 'query' ) {
	$items       = Items::query( [
		'postType' => (string) ( $attributes['postType'] ?? 'faq' ),
		'taxonomy' => (string) ( $attributes['taxonomy'] ?? 'faq_topic' ),
		'termIds'  => (array) ( $attributes['termIds'] ?? [] ),
		'limit'    => (int) ( $attributes['limit'] ?? 8 ),
		'orderBy'  => (string) ( $attributes['orderBy'] ?? 'menu_order' ),
	] );
	$schemaItems = $items;
} else {
	$discover_tabs = $tabs === [];

	foreach ( $block->parsed_block['innerBlocks'] ?? [] as $inner ) {
		$attrs = (array) ( $inner['attrs'] ?? [] );

		if ( $discover_tabs ) {
			foreach ( (array) ( $attrs['categories'] ?? [] ) as $category ) {
				$category = trim( (string) $category );

				if ( $category !== '' && ! in_array( $category, $tabs, true ) ) {
					$tabs[] = $category;
				}
			}
		}

		if ( ( $inner['blockName'] ?? '' ) === 'balefire/faq-no-borders' ) {
			$schemaItems[] = [
				'question'   => (string) ( $attrs['question'] ?? '' ),
				'answerHtml' => (string) ( $attrs['answer'] ?? '' ),
			];
		}
	}
}

// Structured data is for crawlers: never print it into an editor preview
// (ServerSideRender runs through the REST API) or an admin screen.
$is_editor_preview = is_admin() || ( defined( 'REST_REQUEST' ) && REST_REQUEST );

echo Renderer::render( [
	'content'        => $source === 'query' ? '' : $content,
	'tabs'           => $tabs,
	'eyebrow'        => (string) ( $attributes['eyebrow'] ?? '' ),
	'eyebrowVariant' => (string) ( $attributes['eyebrowVariant'] ?? '' ),
	'title'          => (string) ( $attributes['title'] ?? '' ),
	// The block attribute is `content` (intro copy); the Blade prop has to be
	// `intro` because `content` is already the inner-blocks HTML there.
	'intro'          => (string) ( $attributes['content'] ?? '' ),
	'ctaLabel'       => (string) ( $attributes['ctaLabel'] ?? '' ),
	'ctaUrl'         => isset( $attributes['ctaUrl'] ) ? esc_url_raw( (string) $attributes['ctaUrl'] ) : '',
	'layout'         => (string) ( $attributes['layout'] ?? 'stack' ),
	'tone'           => (string) ( $attributes['tone'] ?? '' ),
	'source'         => $source,
	'items'          => $items,
	'exclusive'      => ! empty( $attributes['exclusive'] ),
	'emitSchema'     => ! empty( $attributes['emitSchema'] ) && ! $is_editor_preview,
	'schemaItems'    => $schemaItems,
], get_block_wrapper_attributes() );
