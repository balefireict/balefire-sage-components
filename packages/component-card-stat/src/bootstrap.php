<?php
/**
 * balefireict/component-card-stat — bootstrap.
 *
 * Registers the Gutenberg block and [bma_card_stat] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CardStat
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_card_stat_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-card-stat-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/card-stat/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-card-stat-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-card-stat', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/card-stat/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-card-stat', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/card-stat' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_card_stat' ) ) {
		add_shortcode( 'bma_card_stat', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'title'           => '',
					'icontype'        => 'svg',
					'iconsvgcode'     => '',
					'iconid'          => 0,
					'iconurl'         => '',
					'iconalt'         => '',
					'statleftvalue'   => '',
					'statleftlabel'   => '',
					'statrightvalue'  => '',
					'statrightlabel'  => '',
				],
				$atts,
				'bma_card_stat'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CardStat\Renderer::render( [
				'title'          => $atts['title'],
				'iconType'       => $atts['icontype'],
				'iconSvgCode'    => $atts['iconsvgcode'],
				'iconId'         => $atts['iconid'],
				'iconUrl'        => $atts['iconurl'],
				'iconAlt'        => $atts['iconalt'],
				'statLeftValue'  => $atts['statleftvalue'],
				'statLeftLabel'  => $atts['statleftlabel'],
				'statRightValue' => $atts['statrightvalue'],
				'statRightLabel' => $atts['statrightlabel'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_card_stat_boot();
} else {
	add_action( 'init', $bma_card_stat_boot, 20 );
}

unset( $bma_card_stat_boot );
