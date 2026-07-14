<?php
/**
 * balefireict/component-mission-cards — bootstrap.
 *
 * Registers the Gutenberg block and [bma_mission_cards] shortcode, and exposes
 * the Mission terms to the editor script so the term picker has something to
 * list. The taxonomy itself is registered by ACF from
 * acf-exports/mission-taxonomy.json — this package only reads it.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\MissionCards
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_mission_cards_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'mission-cards' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-mission-cards-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		// The picker needs the Mission terms. Ship them with the script rather
		// than making the editor fetch a taxonomy that may not exist yet.
		wp_add_inline_script(
			'balefire-mission-cards-editor',
			'window.balefireMissionTerms = ' . wp_json_encode( \BalefireInc\Sage\MissionCards\Missions::choices() ) . ';',
			'before'
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/mission-cards/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-mission-cards-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/mission-cards' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_mission_cards' ) ) {
		add_shortcode( 'bma_mission_cards', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'eyebrow' => 'Find Your Fit',
					'title'   => '',
					'content' => '',
					'termids' => '',
					'limit'   => 3,
				],
				$atts,
				'bma_mission_cards'
			);

			$term_ids = $atts['termids'] !== ''
				? array_map( 'absint', explode( ',', (string) $atts['termids'] ) )
				: [];

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\MissionCards\Renderer::render( [
				'eyebrow' => $atts['eyebrow'],
				'title'   => $atts['title'],
				'content' => $atts['content'],
				'termIds' => $term_ids,
				'limit'   => (int) $atts['limit'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_mission_cards_boot();
	} else {
		// Priority 20 — after ACF (priority 5) has registered the taxonomy.
		add_action( 'init', $bma_mission_cards_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php loads.
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_mission_cards_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_mission_cards_boot );
