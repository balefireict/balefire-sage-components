<?php
/**
 * balefireict/component-card-media — bootstrap.
 *
 * Registers the Gutenberg block and [bma_card_media] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CardMedia
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_card_media_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/card-media' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_card_media' ) ) {
		add_shortcode( 'bma_card_media', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'logotype'        => 'image',
					'logosvgcode'     => '',
					'mediatype'       => 'image',
					'svgcode'         => '',
					'logoid'          => 0,
					'logourl'         => '',
					'logoalt'         => '',
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'title'           => '',
					'text'            => '',
					'linktext'        => '',
					'url'             => '',
					'linktype'        => 'none',
					'pageid'          => 0,
					'openinnewtab'    => false,
				],
				$atts,
				'bma_card_media'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CardMedia\Renderer::render( [
				'logoType'       => $atts['logotype'],
				'logoSvgCode'    => $atts['logosvgcode'],
				'mediaType'      => $atts['mediatype'],
				'svgCode'        => $atts['svgcode'],
				'logoId'         => $atts['logoid'],
				'logoUrl'        => $atts['logourl'],
				'logoAlt'        => $atts['logoalt'],
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'title'          => $atts['title'],
				'text'           => $atts['text'],
				'linkText'       => $atts['linktext'],
				'url'            => $atts['url'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
				'openInNewTab'   => $atts['openinnewtab'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_card_media_boot();
} else {
	add_action( 'init', $bma_card_media_boot, 20 );
}

unset( $bma_card_media_boot );
