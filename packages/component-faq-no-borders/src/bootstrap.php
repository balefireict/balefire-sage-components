<?php
/**
 * balefireict/component-faq-no-borders — bootstrap.
 *
 * Registers the Gutenberg block and [bma_faq_no_borders] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\FaqNoBorders
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_faq_no_borders_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'faq-no-borders' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-faq-no-borders-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/faq-no-borders/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-faq-no-borders-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/faq-no-borders' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_faq_no_borders' ) ) {
		add_shortcode( 'bma_faq_no_borders', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'question'        => '',
					'answer'          => '',
					'openbydefault'   => false,
				],
				$atts,
				'bma_faq_no_borders'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\FaqNoBorders\Renderer::render( [
				'question'       => $atts['question'],
				'answer'         => $atts['answer'],
				'openByDefault'  => $atts['openbydefault'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_faq_no_borders_boot();
	} else {
		add_action( 'init', $bma_faq_no_borders_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_faq_no_borders_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_faq_no_borders_boot );
