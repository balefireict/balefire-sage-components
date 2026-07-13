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

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-section-heading-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/section-heading/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-section-heading-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-section-heading', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/section-heading/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-section-heading', $style_css );
		}
	}

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
