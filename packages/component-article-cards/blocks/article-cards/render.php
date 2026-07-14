<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * @package BalefireInc\Sage\ArticleCards
 */

declare( strict_types=1 );

use BalefireInc\Sage\ArticleCards\Renderer;

$post_ids = $attributes['postIds'] ?? [];

echo Renderer::render( [
	'eyebrow'         => $attributes['eyebrow'] ?? '',
	'title'           => $attributes['title'] ?? '',
	'content'         => $attributes['content'] ?? '',
	'ctaLabel'        => $attributes['ctaLabel'] ?? '',
	'ctaUrl'          => $attributes['ctaUrl'] ?? '',
	'source'          => $attributes['source'] ?? 'filter',
	'taxonomy'        => $attributes['taxonomy'] ?? 'category',
	'termId'          => $attributes['termId'] ?? 0,
	'postIds'         => is_array( $post_ids ) ? $post_ids : [],
	'count'           => $attributes['count'] ?? 4,
	'columns'         => $attributes['columns'] ?? 4,
	'fallbackImageId' => $attributes['fallbackImageId'] ?? 0,
], get_block_wrapper_attributes() );
