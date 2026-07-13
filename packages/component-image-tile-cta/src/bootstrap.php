<?php
/**
 * balefireict/component-image-tile-cta — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ImageTileCta
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_image_tile_cta_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/image-tile-cta' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_image_tile_cta_boot();
} else {
	add_action( 'init', $bma_image_tile_cta_boot, 20 );
}

unset( $bma_image_tile_cta_boot );
