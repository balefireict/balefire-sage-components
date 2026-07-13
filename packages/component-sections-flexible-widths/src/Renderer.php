<?php
/**
 * Renderer — delegates to the Blade view for Section (Flexible Width).
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\SectionsFlexibleWidths
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\SectionsFlexibleWidths;

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
			return \Roots\view( 'bma::components.sections-flexible-widths', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.sections-flexible-widths', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/sections-flexible-widths: Sage/Acorn Blade runtime not found. '
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
			'containerWidth' => 'max-w-7xl',
			'backgroundColor' => 'transparent',
			'htmlId' => '',
			'content' => '',
		] );
	}
}
