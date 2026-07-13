<?php
/**
 * balefireict/component-preheader-and-title — bootstrap.
 *
 * Registers the Gutenberg block and [bma_preheader_and_title] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\PreheaderAndTitle
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_preheader_and_title_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/preheader-and-title' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_preheader_and_title' ) ) {
		add_shortcode( 'bma_preheader_and_title', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'preheader'       => 'Preheader',
					'title'           => 'Title',
					'textalign'       => 'center',
				],
				$atts,
				'bma_preheader_and_title'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\PreheaderAndTitle\Renderer::render( [
				'preheader'      => $atts['preheader'],
				'title'          => $atts['title'],
				'textAlign'      => $atts['textalign'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_preheader_and_title_boot();
} else {
	add_action( 'init', $bma_preheader_and_title_boot, 20 );
}

unset( $bma_preheader_and_title_boot );
