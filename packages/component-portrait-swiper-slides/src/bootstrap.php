<?php
/**
 * balefireict/component-portrait-swiper-slides — bootstrap.
 *
 * Registers the Gutenberg block.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\PortraitSwiperSlides
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block.
 */
$bma_portrait_swiper_slides_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/portrait-swiper-slides' );
	}
};

if ( did_action( 'init' ) ) {
	$bma_portrait_swiper_slides_boot();
} else {
	add_action( 'init', $bma_portrait_swiper_slides_boot, 20 );
}

unset( $bma_portrait_swiper_slides_boot );
