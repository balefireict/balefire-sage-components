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
