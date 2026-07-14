<?php
/**
 * Renderer — delegates to the Blade view for Reviews.
 *
 * @package BalefireInc\Sage\Reviews
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Reviews;

class Renderer {

	/**
	 * Render the component from the given props.
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
			return \Roots\view( 'bma::components.reviews', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.reviews', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/reviews: Sage/Acorn Blade runtime not found. '
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
			'eyebrow' => 'Reviews From The Field',
			'title'   => '',
			'count'   => 9,
			'orderby' => 'date',
		] );
	}
}
