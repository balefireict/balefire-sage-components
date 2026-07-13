<?php
/**
 * balefireict/component-simple-image-card — bootstrap.
 *
 * Registers the Gutenberg block and [bma_simple_image_card] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\SimpleImageCard
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_simple_image_card_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'simple-image-card' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-simple-image-card-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/simple-image-card/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-simple-image-card-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/simple-image-card' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_simple_image_card' ) ) {
		add_shortcode( 'bma_simple_image_card', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'title'           => '',
					'url'             => '',
					'linktype'        => 'none',
					'pageid'          => 0,
				],
				$atts,
				'bma_simple_image_card'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\SimpleImageCard\Renderer::render( [
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'title'          => $atts['title'],
				'url'            => $atts['url'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_simple_image_card_boot();
	} else {
		add_action( 'init', $bma_simple_image_card_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_simple_image_card_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_simple_image_card_boot );
