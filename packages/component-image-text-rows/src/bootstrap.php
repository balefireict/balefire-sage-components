<?php
/**
 * balefireict/component-image-text-rows — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ImageTextRows
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_image_text_rows_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/image-text-rows' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_image_text_rows_boot();
} else {
	add_action( 'init', $bma_image_text_rows_boot, 20 );
}

unset( $bma_image_text_rows_boot );
