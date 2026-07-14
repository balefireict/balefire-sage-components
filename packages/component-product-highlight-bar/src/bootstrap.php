<?php
/**
 * balefireict/component-product-highlight-bar — bootstrap.
 *
 * Registers the Gutenberg block and [bma_product_highlight_bar] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ProductHighlightBar
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_product_highlight_bar_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'product-highlight-bar' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-product-highlight-bar-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/product-highlight-bar/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-product-highlight-bar-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/product-highlight-bar' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	// Items are structural, so the shortcode takes them as a JSON array
	// rather than trying to flatten a repeater into shortcode atts.
	if ( ! shortcode_exists( 'bma_product_highlight_bar' ) ) {
		add_shortcode( 'bma_product_highlight_bar', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'items'        => '',
					'headinglevel' => 'h2',
				],
				$atts,
				'bma_product_highlight_bar'
			);

			$items = [];

			if ( $atts['items'] !== '' ) {
				$decoded = json_decode( (string) $atts['items'], true );
				$items   = is_array( $decoded ) ? $decoded : [];
			}

			if ( $items === [] ) {
				$items = \BalefireInc\Sage\ProductHighlightBar\Icons::defaultItems();
			}

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\ProductHighlightBar\Renderer::render( [
				'items'        => $items,
				'headingLevel' => $atts['headinglevel'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_product_highlight_bar_boot();
	} else {
		add_action( 'init', $bma_product_highlight_bar_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_product_highlight_bar_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_product_highlight_bar_boot );
