<?php
/**
 * balefireict/component-faq-no-borders — bootstrap.
 *
 * Registers the Gutenberg block and [bma_faq_no_borders] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\FaqNoBorders
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_faq_no_borders_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/faq-no-borders' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_faq_no_borders' ) ) {
		add_shortcode( 'bma_faq_no_borders', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'question'        => '',
					'answer'          => '',
					'openbydefault'   => false,
				],
				$atts,
				'bma_faq_no_borders'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\FaqNoBorders\Renderer::render( [
				'question'       => $atts['question'],
				'answer'         => $atts['answer'],
				'openByDefault'  => $atts['openbydefault'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_faq_no_borders_boot();
} else {
	add_action( 'init', $bma_faq_no_borders_boot, 20 );
}

unset( $bma_faq_no_borders_boot );
