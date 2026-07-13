<?php
/**
 * balefireict/component-hero-video-header — bootstrap.
 *
 * Registers the Gutenberg block and [bma_hero_video_header] shortcode.
 * The block uses a PHP render callback that delegates to a Blade view.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\HeroVideoHeader
 */

declare( strict_types=1 );

defined( 'ABSPATH' ) || exit;

/**
 * Register the block and shortcode.
 */
$bma_hero_video_header_boot = static function (): void {

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/hero-video-header' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_hero_video_header' ) ) {
		add_shortcode( 'bma_hero_video_header', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'videourl'        => '',
					'fallbackimage'   => '',
					'subtitle'        => '',
					'primarylabel'    => '',
					'primaryurl'      => '',
					'secondarylabel'  => '',
					'secondaryurl'    => '',
				],
				$atts,
				'bma_hero_video_header'
			);

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\HeroVideoHeader\Renderer::render( [
				'videoUrl'       => $atts['videourl'],
				'fallbackImage'  => $atts['fallbackimage'],
				'subtitle'       => $atts['subtitle'],
				'primaryLabel'   => $atts['primarylabel'],
				'primaryUrl'     => $atts['primaryurl'],
				'secondaryLabel' => $atts['secondarylabel'],
				'secondaryUrl'   => $atts['secondaryurl'],
			] );
		} );
	}
};

if ( did_action( 'init' ) ) {
	$bma_hero_video_header_boot();
} else {
	add_action( 'init', $bma_hero_video_header_boot, 20 );
}

unset( $bma_hero_video_header_boot );
