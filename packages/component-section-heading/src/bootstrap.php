<?php
/**
 * balefireict/component-section-heading — bootstrap.
 *
 * Registers the Gutenberg block and [bma_section_heading] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\SectionHeading
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_section_heading_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/section-heading' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_section_heading' ) ) {
		add_shortcode( 'bma_section_heading', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'eyebrow'         => '',
					'title'           => '',
					'content'         => '',
					'contentalign'    => 'left',
					'maxwidth'        => '',
					'backgroundtone'  => 'transparent',
				],
				$atts,
				'bma_section_heading'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\SectionHeading\Renderer::render( [
				'eyebrow'        => $atts['eyebrow'],
				'title'          => $atts['title'],
				'content'        => $atts['content'],
				'contentAlign'   => $atts['contentalign'],
				'maxWidth'       => $atts['maxwidth'],
				'backgroundTone' => $atts['backgroundtone'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_section_heading_boot();
} else {
	add_action( 'init', $bma_section_heading_boot, 20 );
}

unset( $bma_section_heading_boot );
