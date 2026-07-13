<?php
/**
 * balefireict/component-features-section — bootstrap.
 *
 * Registers the Gutenberg block and [bma_features_section] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\FeaturesSection
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_features_section_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/features-section' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_features_section' ) ) {
		add_shortcode( 'bma_features_section', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'heading'         => '',
					'intro'           => '',
					'maxwidth'        => 'wide',
					'backgroundtone'  => 'white',
				],
				$atts,
				'bma_features_section'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\FeaturesSection\Renderer::render( [
				'heading'        => $atts['heading'],
				'intro'          => $atts['intro'],
				'maxWidth'       => $atts['maxwidth'],
				'backgroundTone' => $atts['backgroundtone'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_features_section_boot();
} else {
	add_action( 'init', $bma_features_section_boot, 20 );
}

unset( $bma_features_section_boot );
