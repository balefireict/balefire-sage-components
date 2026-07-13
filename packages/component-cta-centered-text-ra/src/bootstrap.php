<?php
/**
 * balefireict/component-cta-centered-text-ra — bootstrap.
 *
 * Registers the Gutenberg block and [bma_cta_centered_text_ra] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CtaCenteredTextRa
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_cta_centered_text_ra_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/cta-centered-text-ra' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_cta_centered_text_ra' ) ) {
		add_shortcode( 'bma_cta_centered_text_ra', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'preheader'       => '',
					'title'           => '',
					'ctatext'         => '',
					'content'         => '',
					'primarylabel'    => '',
					'primaryurl'      => '',
					'secondarylabel'  => '',
					'secondaryurl'    => '',
				],
				$atts,
				'bma_cta_centered_text_ra'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CtaCenteredTextRa\Renderer::render( [
				'preheader'      => $atts['preheader'],
				'title'          => $atts['title'],
				'ctaText'        => $atts['ctatext'],
				'content'        => $atts['content'],
				'primaryLabel'   => $atts['primarylabel'],
				'primaryUrl'     => $atts['primaryurl'],
				'secondaryLabel' => $atts['secondarylabel'],
				'secondaryUrl'   => $atts['secondaryurl'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_cta_centered_text_ra_boot();
} else {
	add_action( 'init', $bma_cta_centered_text_ra_boot, 20 );
}

unset( $bma_cta_centered_text_ra_boot );
