<?php
/**
 * balefireict/component-simple-image-card — bootstrap.
 *
 * Registers the Gutenberg block and [bma_simple_image_card] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\SimpleImageCard
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_simple_image_card_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/simple-image-card' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_simple_image_card' ) ) {
		add_shortcode( 'bma_simple_image_card', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'title'           => '',
					'url'             => '',
					'linktype'        => 'none',
					'pageid'          => 0,
				],
				$atts,
				'bma_simple_image_card'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\SimpleImageCard\Renderer::render( [
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'title'          => $atts['title'],
				'url'            => $atts['url'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_simple_image_card_boot();
} else {
	add_action( 'init', $bma_simple_image_card_boot, 20 );
}

unset( $bma_simple_image_card_boot );
