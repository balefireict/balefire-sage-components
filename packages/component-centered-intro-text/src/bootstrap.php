<?php
/**
 * balefireict/component-centered-intro-text — bootstrap.
 *
 * Registers the Gutenberg block and [bma_centered_intro_text] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CenteredIntroText
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_centered_intro_text_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-centered-intro-text-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/centered-intro-text/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-centered-intro-text-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-centered-intro-text', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/centered-intro-text/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-centered-intro-text', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/centered-intro-text' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_centered_intro_text' ) ) {
		add_shortcode( 'bma_centered_intro_text', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'content'         => '',
					'maxwidth'        => 'narrow',
					'backgroundtone'  => 'light',
				],
				$atts,
				'bma_centered_intro_text'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CenteredIntroText\Renderer::render( [
				'content'        => $atts['content'],
				'maxWidth'       => $atts['maxwidth'],
				'backgroundTone' => $atts['backgroundtone'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_centered_intro_text_boot();
} else {
	add_action( 'init', $bma_centered_intro_text_boot, 20 );
}

unset( $bma_centered_intro_text_boot );
