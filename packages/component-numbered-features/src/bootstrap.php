<?php
/**
 * balefireict/component-numbered-features — bootstrap.
 *
 * Registers the Gutenberg block, the [bma_numbered_features] shortcode, the
 * editor script and the block style. The hover reveal is pure CSS (see the
 * Blade view), so this component ships no front-end JavaScript.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\NumberedFeatures
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_numbered_features_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'numbered-features' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-numbered-features-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		// The icon picker's options come from the PHP set, so the editor and
		// the rendered markup can never drift apart.
		wp_add_inline_script(
			'balefire-numbered-features-editor',
			'window.balefireNumberedFeatureIcons = ' . wp_json_encode( \BalefireInc\Sage\NumberedFeatures\Icons::choices() ) . ';',
			'before'
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/numbered-features/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-numbered-features-editor', $editor_js );
		}
	}

	// --- Block style ------------------------------------------------------
	// Same no-URL rule as the editor script: the split layout's row CSS is
	// inlined against a src-less handle that block.json's "style" points at.
	if ( function_exists( 'wp_register_style' ) ) {
		wp_register_style( 'balefire-numbered-features', false, [], null );

		$style_css = file_get_contents( __DIR__ . '/../blocks/numbered-features/style.css' );
		if ( $style_css !== false ) {
			wp_add_inline_style( 'balefire-numbered-features', $style_css );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/numbered-features' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	// Items are structural, so they arrive as a JSON array rather than being
	// flattened into shortcode atts.
	if ( ! shortcode_exists( 'bma_numbered_features' ) ) {
		add_shortcode( 'bma_numbered_features', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'eyebrow'        => 'The B&T Difference',
					'eyebrowvariant' => 'marks',
					'title'          => '',
					'titleaccent'    => '',
					'content'        => '',
					'ctalabel'       => '',
					'ctaurl'         => '',
					'primarylabel'   => '',
					'primaryurl'     => '',
					'secondarylabel' => '',
					'secondaryurl'   => '',
					'items'          => '',
					'layout'         => 'stack',
					'tone'           => 'white',
				],
				$atts,
				'bma_numbered_features'
			);

			$items = [];

			if ( $atts['items'] !== '' ) {
				$decoded = json_decode( (string) $atts['items'], true );
				$items   = is_array( $decoded ) ? $decoded : [];
			}

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\NumberedFeatures\Renderer::render( [
				'eyebrow'        => $atts['eyebrow'],
				'eyebrowVariant' => $atts['eyebrowvariant'],
				'title'          => $atts['title'],
				'titleAccent'    => $atts['titleaccent'],
				'content'        => $atts['content'],
				'ctaLabel'       => $atts['ctalabel'],
				'ctaUrl'         => $atts['ctaurl'],
				'primaryLabel'   => $atts['primarylabel'],
				'primaryUrl'     => $atts['primaryurl'],
				'secondaryLabel' => $atts['secondarylabel'],
				'secondaryUrl'   => $atts['secondaryurl'],
				'items'          => $items,
				'layout'         => $atts['layout'],
				'tone'           => $atts['tone'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_numbered_features_boot();
	} else {
		add_action( 'init', $bma_numbered_features_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php loads.
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_numbered_features_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_numbered_features_boot );
