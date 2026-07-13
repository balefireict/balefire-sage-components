<?php
/**
 * balefireict/component-posts-grid — bootstrap.
 *
 * Registers the Gutenberg block and [bma_posts_grid] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\PostsGrid
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_posts_grid_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'posts-grid' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-posts-grid-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/posts-grid/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-posts-grid-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-posts-grid', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/posts-grid/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-posts-grid', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/posts-grid' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_posts_grid' ) ) {
		add_shortcode( 'bma_posts_grid', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'intro'           => '',
					'postsperpage'    => 0,
					'columns'         => 3,
					'showexcerpt'     => true,
					'showdate'        => true,
					'heading'         => '',
					'maxwidth'        => 'wide',
					'showauthor'      => true,
					'backgroundtone'  => 'transparent',
				],
				$atts,
				'bma_posts_grid'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\PostsGrid\Renderer::render( [
				'intro'          => $atts['intro'],
				'postsPerPage'   => $atts['postsperpage'],
				'columns'        => $atts['columns'],
				'showExcerpt'    => $atts['showexcerpt'],
				'showDate'       => $atts['showdate'],
				'heading'        => $atts['heading'],
				'maxWidth'       => $atts['maxwidth'],
				'showAuthor'     => $atts['showauthor'],
				'backgroundTone' => $atts['backgroundtone'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_posts_grid_boot();
	} else {
		add_action( 'init', $bma_posts_grid_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_posts_grid_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_posts_grid_boot );
