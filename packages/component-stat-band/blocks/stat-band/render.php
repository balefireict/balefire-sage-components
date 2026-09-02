<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * This file is referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\StatBand
 */

declare( strict_types=1 );

use BalefireInc\Sage\StatBand\Renderer;

$items = $attributes['items'] ?? [];

echo Renderer::render( [
	'tone'           => $attributes['tone'] ?? 'primary',
	'lead'           => $attributes['lead'] ?? '',
	'items'          => is_array( $items ) ? $items : [],
	'columns'        => (int) ( $attributes['columns'] ?? 4 ),
	'radius'         => $attributes['radius'] ?? 'none',
	'primaryLabel'   => $attributes['primaryLabel'] ?? '',
	'primaryUrl'     => isset( $attributes['primaryUrl'] ) ? esc_url( $attributes['primaryUrl'] ) : '',
	'secondaryLabel' => $attributes['secondaryLabel'] ?? '',
	'secondaryUrl'   => isset( $attributes['secondaryUrl'] ) ? esc_url( $attributes['secondaryUrl'] ) : '',
], get_block_wrapper_attributes() );
