<?php
/**
 * Renderer — delegates to the Blade view for the CTA banner.
 *
 * This is the bridge between the block/shortcode entry points and the
 * Blade component. In a Sage theme, it uses Sage's Blade integration.
 * In a non-Sage theme (or standalone), it falls back to a raw PHP view.
 *
 * @package BalefireInc\Sage\CtaBanner
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\CtaBanner;

class Renderer {

	/**
	 * Render the CTA banner from the given props.
	 *
	 * Uses Sage's Blade runtime. The component REQUIRES a Sage or
	 * Acorn-powered theme — this is a Sage component library, not a
	 * generic PHP library. No fallback view is shipped; Blade is the
	 * only render path, keeping markup in a single source of truth.
	 *
	 * @param array $props Component props (matches Blade @props).
	 * @return string HTML output.
	 */
	public static function render( array $props ): string {
		$props = self::defaults( $props );

		// Sage 10 / Acorn — use the Roots\view() helper.
		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.cta-banner', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.cta-banner', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- bma_cta_banner: Sage/Acorn Blade runtime not found. '
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
			'eyebrow'        => '',
			'title'          => '',
			'content'        => '',
			'tone'           => 'primary',
			'primaryLabel'   => '',
			'primaryUrl'     => '',
			'primaryStyle'   => 'solid',
			'secondaryLabel' => '',
			'secondaryUrl'   => '',
		] );
	}
}
