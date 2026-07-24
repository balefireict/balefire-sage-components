<?php
/**
 * balefireict/component-image-marquee — bootstrap.
 *
 * Registers the Gutenberg block. The block uses a PHP render callback that
 * delegates to a Blade view. No shortcode layer: this component postdates
 * the WPBakery era and has no legacy consumer.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ImageMarquee
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block.
 */
$bma_image_marquee_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-image-marquee-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/image-marquee/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-image-marquee-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/image-marquee' );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_image_marquee_boot();
	} else {
		add_action( 'init', $bma_image_marquee_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_image_marquee_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_image_marquee_boot );
