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

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_team_member_centered_boot = static function (): void {

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

if ( did_action( 'init' ) ) {
	$bma_team_member_centered_boot();
} else {
	add_action( 'init', $bma_team_member_centered_boot, 20 );
}

unset( $bma_team_member_centered_boot );
