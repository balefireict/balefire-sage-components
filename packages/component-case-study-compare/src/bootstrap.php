<?php
/**
 * balefireict/component-case-study-compare — bootstrap.
 *
 * Registers the Gutenberg block and [bma_case_study_compare] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\CaseStudyCompare
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_case_study_compare_boot = static function (): void {

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	// See https://roots.io/wordpress-plugins-that-assume-your-directory-structure/
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-case-study-compare-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/case-study-compare/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-case-study-compare-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/case-study-compare' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_case_study_compare' ) ) {
		add_shortcode( 'bma_case_study_compare', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'lefticonid'      => 0,
					'lefticonurl'     => '',
					'lefticonalt'     => '',
					'lefttitle'       => '',
					'leftbody'        => '',
					'righticonid'     => 0,
					'righticonurl'    => '',
					'righticonalt'    => '',
					'righttitle'      => '',
					'rightbody'       => '',
				],
				$atts,
				'bma_case_study_compare'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\CaseStudyCompare\Renderer::render( [
				'leftIconId'     => $atts['lefticonid'],
				'leftIconUrl'    => $atts['lefticonurl'],
				'leftIconAlt'    => $atts['lefticonalt'],
				'leftTitle'      => $atts['lefttitle'],
				'leftBody'       => $atts['leftbody'],
				'rightIconId'    => $atts['righticonid'],
				'rightIconUrl'   => $atts['righticonurl'],
				'rightIconAlt'   => $atts['righticonalt'],
				'rightTitle'     => $atts['righttitle'],
				'rightBody'      => $atts['rightbody'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_case_study_compare_boot();
} else {
	add_action( 'init', $bma_case_study_compare_boot, 20 );
}

unset( $bma_case_study_compare_boot );
