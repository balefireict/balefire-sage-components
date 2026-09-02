<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\SplitFeature
 */

declare( strict_types=1 );

use BalefireInc\Sage\SplitFeature\Renderer;

$bma_items = $attributes['items'] ?? [];
$bma_items = is_array( $bma_items ) ? array_values( array_filter( array_map( 'strval', $bma_items ) ) ) : [];

echo Renderer::render( [
	'tone' => $attributes['tone'] ?? 'white',
	'eyebrow' => $attributes['eyebrow'] ?? '',
	'title' => $attributes['title'] ?? '',
	'content' => $attributes['content'] ?? '',
	'items' => $bma_items,
	'listColumns' => (int) ( $attributes['listColumns'] ?? 1 ),
	'ratio' => $attributes['ratio'] ?? 'even',
	'primaryLabel' => $attributes['primaryLabel'] ?? '',
	'primaryUrl' => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl' => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
	'mediaType' => $attributes['mediaType'] ?? 'content',
	'mediaSide' => $attributes['mediaSide'] ?? 'right',
	'imageId' => $attributes['imageId'] ?? 0,
	'imageUrl' => isset( $attributes['imageUrl'] ) ? esc_url( $attributes['imageUrl'] ) : '',
	'imageAlt' => $attributes['imageAlt'] ?? '',
	'statValue' => $attributes['statValue'] ?? '',
	'statLabel' => $attributes['statLabel'] ?? '',
	'statNote' => $attributes['statNote'] ?? '',
	// Raw SVG: sanitized on output in the view (wp_kses allowlist).
	'panelIcon' => $attributes['panelIcon'] ?? '',
	'panelTitle' => $attributes['panelTitle'] ?? '',
	'panelText' => $attributes['panelText'] ?? '',
	'panelCtaLabel' => $attributes['panelCtaLabel'] ?? '',
	'panelCtaUrl' => isset( $attributes['panelCtaUrl'] ) ? esc_url( $attributes['panelCtaUrl'] ) : '',
	'mediaContent' => $content,
], get_block_wrapper_attributes() );
