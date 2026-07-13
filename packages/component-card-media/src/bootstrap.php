<?php
/**
 * balefireict/component-card-media — bootstrap.
 *
 * Registers the Gutenberg block and [bma_card_media] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CardMedia
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_card_media_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-card-media-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/card-media/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-card-media-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the CSS is inlined against a
	// src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-card-media', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/card-media/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-card-media', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/card-media' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_card_media' ) ) {
		add_shortcode( 'bma_card_media', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'logotype'        => 'image',
					'logosvgcode'     => '',
					'mediatype'       => 'image',
					'svgcode'         => '',
					'logoid'          => 0,
					'logourl'         => '',
					'logoalt'         => '',
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'title'           => '',
					'text'            => '',
					'linktext'        => '',
					'url'             => '',
					'linktype'        => 'none',
					'pageid'          => 0,
					'openinnewtab'    => false,
				],
				$atts,
				'bma_card_media'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CardMedia\Renderer::render( [
				'logoType'       => $atts['logotype'],
				'logoSvgCode'    => $atts['logosvgcode'],
				'mediaType'      => $atts['mediatype'],
				'svgCode'        => $atts['svgcode'],
				'logoId'         => $atts['logoid'],
				'logoUrl'        => $atts['logourl'],
				'logoAlt'        => $atts['logoalt'],
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'title'          => $atts['title'],
				'text'           => $atts['text'],
				'linkText'       => $atts['linktext'],
				'url'            => $atts['url'],
				'linkType'       => $atts['linktype'],
				'pageId'         => $atts['pageid'],
				'openInNewTab'   => $atts['openinnewtab'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_card_media_boot();
	} else {
		add_action( 'init', $bma_card_media_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_card_media_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_card_media_boot );
