<?php
/**
 * balefireict/component-page-header — bootstrap.
 *
 * Registers the Gutenberg block and [bma_page_header] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\PageHeader
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_page_header_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/page-header' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_page_header' ) ) {
		add_shortcode( 'bma_page_header', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'backgroundimage' => '',
					'minheight'       => 'auto',
					'subtitle'        => '',
					'primarylabel'    => '',
					'primaryurl'      => '',
					'secondarylabel'  => '',
					'secondaryurl'    => '',
				],
				$atts,
				'bma_page_header'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\PageHeader\Renderer::render( [
				'backgroundImage' => $atts['backgroundimage'],
				'minHeight'      => $atts['minheight'],
				'subtitle'       => $atts['subtitle'],
				'primaryLabel'   => $atts['primarylabel'],
				'primaryUrl'     => $atts['primaryurl'],
				'secondaryLabel' => $atts['secondarylabel'],
				'secondaryUrl'   => $atts['secondaryurl'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_page_header_boot();
} else {
	add_action( 'init', $bma_page_header_boot, 20 );
}

unset( $bma_page_header_boot );
