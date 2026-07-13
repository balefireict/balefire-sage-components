<?php
/**
 * balefireict/component-featured-image-header — bootstrap.
 *
 * Registers the Gutenberg block and [bma_featured_image_header] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\FeaturedImageHeader
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_featured_image_header_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'featured-image-header' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-featured-image-header-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/featured-image-header/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-featured-image-header-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/featured-image-header' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_featured_image_header' ) ) {
		add_shortcode( 'bma_featured_image_header', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'intro'           => '',
					'showonfrontpage' => false,
				],
				$atts,
				'bma_featured_image_header'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\FeaturedImageHeader\Renderer::render( [
				'intro'          => $atts['intro'],
				'showOnFrontPage' => $atts['showonfrontpage'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_featured_image_header_boot();
	} else {
		add_action( 'init', $bma_featured_image_header_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_featured_image_header_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_featured_image_header_boot );
