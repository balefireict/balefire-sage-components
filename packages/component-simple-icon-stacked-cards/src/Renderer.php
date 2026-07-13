<?php
/**
 * Renderer — delegates to the Blade view for Simple Icon Stacked Cards.
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\SimpleIconStackedCards
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\SimpleIconStackedCards;

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
			return \Roots\view( 'bma::components.simple-icon-stacked-cards', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.simple-icon-stacked-cards', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/simple-icon-stacked-cards: Sage/Acorn Blade runtime not found. '
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
			'iconId' => 0,
			'iconUrl' => '',
			'iconAlt' => '',
			'iconSvg' => '',
			'url' => '',
			'linkType' => 'none',
			'pageId' => 0,
			'openInNewTab' => false,
			'content' => '',
		] );
	}
}
