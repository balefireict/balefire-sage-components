<?php
/**
 * balefireict/component-auto-grid — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\AutoGrid
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_auto_grid_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-auto-grid-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/auto-grid/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-auto-grid-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/auto-grid' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_auto_grid_boot();
} else {
	add_action( 'init', $bma_auto_grid_boot, 20 );
}

unset( $bma_auto_grid_boot );
