<?php
/**
 * balefireict/component-process-steps — bootstrap.
 *
 * Registers the Gutenberg block. The block uses a PHP render callback that
 * delegates to a Blade view. No shortcode layer: this component postdates
 * the WPBakery era and has no legacy consumer.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ProcessSteps
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block.
 */
$bma_process_steps_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-process-steps-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/process-steps/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-process-steps-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at, so it prints
	// only where the block renders.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-process-steps', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/process-steps/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-process-steps', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/process-steps' );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_process_steps_boot();
	} else {
		add_action( 'init', $bma_process_steps_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_process_steps_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_process_steps_boot );
