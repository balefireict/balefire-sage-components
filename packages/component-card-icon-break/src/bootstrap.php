<?php
/**
 * balefireict/component-card-icon-break — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CardIconBreak
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_card_icon_break_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/card-icon-break' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_card_icon_break_boot();
} else {
	add_action( 'init', $bma_card_icon_break_boot, 20 );
}

unset( $bma_card_icon_break_boot );
