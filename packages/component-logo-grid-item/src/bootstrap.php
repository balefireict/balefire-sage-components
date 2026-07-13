<?php
/**
 * balefireict/component-logo-grid-item — bootstrap.
 *
 * Registers the Gutenberg block and [bma_logo_grid_item] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\LogoGridItem
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_logo_grid_item_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/logo-grid-item' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_logo_grid_item' ) ) {
		add_shortcode( 'bma_logo_grid_item', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'linktype'        => 'none',
					'pageid'          => 0,
					'url'             => '',
				],
				$atts,
				'bma_logo_grid_item'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\LogoGridItem\Renderer::render( [
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
				'url'            => $atts['url'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_logo_grid_item_boot();
} else {
	add_action( 'init', $bma_logo_grid_item_boot, 20 );
}

unset( $bma_logo_grid_item_boot );
