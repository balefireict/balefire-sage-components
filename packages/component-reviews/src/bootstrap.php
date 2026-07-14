<?php
/**
 * balefireict/component-reviews — bootstrap.
 *
 * Registers the Gutenberg block, the [bma_product_switcher] shortcode, the
 * editor script, and the front-end switching script.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\Reviews
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block, scripts and shortcode.
 */
$bma_product_switcher_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'reviews' ) ) {
		return;
	}

	// --- Front-end script --------------------------------------------------
	// Registered (not enqueued) here; render.php enqueues it only on pages that
	// actually contain the block. Inline on an src-less handle because vendor/
	// may sit outside the webroot on Bedrock, so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( function_exists( 'wp_register_script' ) ) {
		wp_register_script( 'balefire-reviews-view', false, [ 'balefire-lightbox' ], null, true );

		$view_js = file_get_contents( __DIR__ . '/../blocks/reviews/view.js' );
		if ( $view_js !== false ) {
			wp_add_inline_script( 'balefire-reviews-view', $view_js );
		}
	}

	// --- Editor script -----------------------------------------------------
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-reviews-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/reviews/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-reviews-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/reviews' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	// Items are structural, so they arrive as a JSON array rather than being
	// flattened into shortcode atts.
	if ( ! shortcode_exists( 'bma_reviews' ) ) {
		add_shortcode( 'bma_reviews', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'eyebrow' => 'Reviews From The Field',
					'title'   => '',
					'count'   => 9,
					'orderby' => 'date',
				],
				$atts,
				'bma_reviews'
			);

			if ( wp_script_is( 'balefire-reviews-view', 'registered' ) ) {
				wp_enqueue_script( 'balefire-reviews-view' );
			}

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\Reviews\Renderer::render( [
				'eyebrow' => $atts['eyebrow'],
				'title'   => $atts['title'],
				'count'   => (int) $atts['count'],
				'orderby' => $atts['orderby'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_product_switcher_boot();
	} else {
		add_action( 'init', $bma_product_switcher_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php loads.
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_product_switcher_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_product_switcher_boot );
