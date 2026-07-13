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
			'primaryStyle'   => '',
			'secondaryLabel' => '',
			'secondaryUrl'   => '',
		] );
	}
}
