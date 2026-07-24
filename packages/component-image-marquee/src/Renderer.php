<?php
/**
 * Renderer — delegates to the Blade view for the image-marquee component.
 *
 * Bridge between the block entry point and the Blade component. Requires a
 * Sage or Acorn-powered theme — Blade is the only render path, keeping
 * markup in a single source of truth.
 *
 * @package BalefireInc\Sage\ImageMarquee
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ImageMarquee;

class Renderer {

	/**
	 * Render the component from the given props.
	 *
	 * @param array  $props              Component props (matches Blade @props).
	 * @param string $wrapper_attributes Optional block wrapper attribute string.
	 * @return string HTML output.
	 */
	public static function render( array $props, string $wrapper_attributes = '' ): string {
		if ( $wrapper_attributes !== '' ) {
			$bag = \BalefireInc\Sage\Support\BlockAttributes::bag( $wrapper_attributes );
			if ( $bag !== null ) {
				$props['attributes'] = $bag;
			}
		}

		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.image-marquee', $props )->render();
		}

		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.image-marquee', $props )->render();
		}

		return '<!-- balefire/image-marquee: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}
}
