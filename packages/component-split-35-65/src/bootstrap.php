<?php
/**
 * balefireict/component-split-35-65 — bootstrap.
 *
 * Registers the Gutenberg block and [bma_split_35_65] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\Split3565
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_split_35_65_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-split-35-65-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/split-35-65/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-split-35-65-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/split-35-65' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_split_35_65' ) ) {
		add_shortcode( 'bma_split_35_65', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'heading'         => '',
					'content'         => '',
					'buttonlabel'     => '',
					'buttonurl'       => '',
					'mediatype'       => 'image',
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'videourl'        => '',
					'gap'             => '8',
					'reverse'         => false,
					'iconid'          => 0,
					'iconurl'         => '',
					'iconalt'         => '',
				],
				$atts,
				'bma_split_35_65'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\Split3565\Renderer::render( [
				'heading'        => $atts['heading'],
				'content'        => $atts['content'],
				'buttonLabel'    => $atts['buttonlabel'],
				'buttonUrl'      => $atts['buttonurl'],
				'mediaType'      => $atts['mediatype'],
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'videoUrl'       => $atts['videourl'],
				'gap'            => $atts['gap'],
				'reverse'        => $atts['reverse'],
				'iconId'         => $atts['iconid'],
				'iconUrl'        => $atts['iconurl'],
				'iconAlt'        => $atts['iconalt'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_split_35_65_boot();
} else {
	add_action( 'init', $bma_split_35_65_boot, 20 );
}

unset( $bma_split_35_65_boot );
