<?php
/**
 * balefireict/component-feature-row — bootstrap.
 *
 * Registers the Gutenberg block and [bma_feature_row] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\FeatureRow
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_feature_row_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/feature-row' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_feature_row' ) ) {
		add_shortcode( 'bma_feature_row', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'heading'         => '',
					'body'            => '',
					'mediaid'         => 0,
					'mediaurl'        => '',
					'mediaalt'        => '',
					'imageclass'      => '',
					'linktype'        => 'none',
					'pageid'          => 0,
					'url'             => '',
					'linktext'        => '',
				],
				$atts,
				'bma_feature_row'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\FeatureRow\Renderer::render( [
				'heading'        => $atts['heading'],
				'body'           => $atts['body'],
				'mediaId'        => $atts['mediaid'],
				'mediaUrl'       => $atts['mediaurl'],
				'mediaAlt'       => $atts['mediaalt'],
				'imageClass'     => $atts['imageclass'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
				'url'            => $atts['url'],
				'linkText'       => $atts['linktext'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_feature_row_boot();
} else {
	add_action( 'init', $bma_feature_row_boot, 20 );
}

unset( $bma_feature_row_boot );
