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

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_posts_grid_boot = static function (): void {

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

if ( did_action( 'init' ) ) {
	$bma_posts_grid_boot();
} else {
	add_action( 'init', $bma_posts_grid_boot, 20 );
}

unset( $bma_posts_grid_boot );
