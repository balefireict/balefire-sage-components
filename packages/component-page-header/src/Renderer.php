<?php
/**
 * Renderer — delegates to the Blade view for Page Header.
 *
 * Bridge between the block/shortcode entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\PageHeader
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\PageHeader;

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
			return \Roots\view( 'bma::components.page-header', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.page-header', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/page-header: Sage/Acorn Blade runtime not found. '
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
			'backgroundImage' => '',
			'minHeight' => 'auto',
			'subtitle' => '',
			'primaryLabel' => '',
			'primaryUrl' => '',
			'secondaryLabel' => '',
			'secondaryUrl' => '',
		] );
	}
}
