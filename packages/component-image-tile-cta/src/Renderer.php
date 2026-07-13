<?php
/**
 * Renderer — delegates to the Blade view for Image Tile CTA.
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\ImageTileCta
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ImageTileCta;

class Renderer {

	/**
	 * Render the component from the given props.
	 *
	 * When called from the block render callback, $wrapper_attributes is
	 * the get_block_wrapper_attributes() string; it becomes the view's
	 * $attributes bag so anchor/spacing/className supports reach the markup.
	 *
	 * @param array  $props              Component props (matches Blade @props).
	 * @param string $wrapper_attributes Optional block wrapper attribute string.
	 * @return string HTML output.
	 */
	public static function render( array $props, string $wrapper_attributes = '' ): string {
		$props = self::defaults( $props );

		if ( $wrapper_attributes !== '' ) {
			$bag = \BalefireInc\Sage\Support\BlockAttributes::bag( $wrapper_attributes );
			if ( $bag !== null ) {
				$props['attributes'] = $bag;
			}
		}

		// Sage 10 / Acorn — use the Roots\view() helper.
		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.image-tile-cta', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.image-tile-cta', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/image-tile-cta: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}

	/**
	 * Merge props with defaults (mirrors the Blade @props defaults).
	 *
	 * @param array $props Raw props.
	 * @return array Resolved props.
	 */
	private static function defaults( array $props ): array {
		return wp_parse_args( $props, [
			'heroImageId' => 0,
			'heroImageUrl' => '',
			'heroImageAlt' => '',
			'image1Id' => 0,
			'image1Url' => '',
			'image1Alt' => '',
			'image2Id' => 0,
			'image2Url' => '',
			'image2Alt' => '',
			'image3Id' => 0,
			'image3Url' => '',
			'image3Alt' => '',
			'content' => '',
		] );
	}
}
