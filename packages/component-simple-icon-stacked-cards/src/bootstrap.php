<?php
/**
 * balefireict/component-simple-icon-stacked-cards — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\SimpleIconStackedCards
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_simple_icon_stacked_cards_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/simple-icon-stacked-cards' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_simple_icon_stacked_cards_boot();
} else {
	add_action( 'init', $bma_simple_icon_stacked_cards_boot, 20 );
}

unset( $bma_simple_icon_stacked_cards_boot );
