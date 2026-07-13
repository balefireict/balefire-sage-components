<?php
/**
 * Renderer — delegates to the Blade view for Simple Card.
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\SimpleCard
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\SimpleCard;

class Renderer {

	/**
	 * Render the component from the given props.
	 *
	 * @param array $props Component props (matches Blade @props).
	 * @return string HTML output.
	 */
	public static function render( array $props ): string {
		$props = self::defaults( $props );

		// Sage 10 / Acorn — use the Roots\view() helper.
		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.simple-card', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.simple-card', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/simple-card: Sage/Acorn Blade runtime not found. '
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
			'showBorder' => true,
			'borderRadius' => 'rounded-lg',
			'paddingSize' => 'md',
			'imageId' => 0,
			'imageUrl' => '',
			'imageAlt' => '',
			'imageClass' => '',
			'content' => '',
		] );
	}
}
