<?php
/**
 * balefireict/component-team-member-centered — bootstrap.
 *
 * Registers the Gutenberg block and [bma_team_member_centered] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\TeamMemberCentered
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_team_member_centered_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-team-member-centered-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/team-member-centered/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-team-member-centered-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/team-member-centered' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_team_member_centered' ) ) {
		add_shortcode( 'bma_team_member_centered', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'imageid'         => 0,
					'imageurl'        => '',
					'imagealt'        => '',
					'name'            => '',
					'title'           => '',
					'url'             => '',
					'linktype'        => 'none',
					'pageid'          => 0,
				],
				$atts,
				'bma_team_member_centered'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\TeamMemberCentered\Renderer::render( [
				'imageId'        => $atts['imageid'],
				'imageUrl'       => $atts['imageurl'],
				'imageAlt'       => $atts['imagealt'],
				'name'           => $atts['name'],
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
		$bma_team_member_centered_boot();
	} else {
		add_action( 'init', $bma_team_member_centered_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
	// loads, making this equivalent to add_action( 'init', ..., 20 ).
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_team_member_centered_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_team_member_centered_boot );
