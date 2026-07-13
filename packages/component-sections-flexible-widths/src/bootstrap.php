<?php
/**
 * balefireict/component-sections-flexible-widths — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\SectionsFlexibleWidths
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_sections_flexible_widths_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/sections-flexible-widths' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_sections_flexible_widths_boot();
} else {
	add_action( 'init', $bma_sections_flexible_widths_boot, 20 );
}

unset( $bma_sections_flexible_widths_boot );
