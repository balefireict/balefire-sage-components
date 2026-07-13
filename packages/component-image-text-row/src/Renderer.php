<?php
/**
 * Renderer — delegates to the Blade view for Image Text Row.
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\ImageTextRow
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ImageTextRow;

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
			return \Roots\view( 'bma::components.image-text-row', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.image-text-row', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/image-text-row: Sage/Acorn Blade runtime not found. '
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
			'heading' => '',
			'body' => '',
			'mediaId' => 0,
			'mediaUrl' => '',
			'mediaAlt' => '',
			'layout' => 'inherit',
			'preheader' => '',
			'subhead' => '',
			'showArrow' => false,
			'imageCrop' => 'default',
			'imageAspectRatio' => 'default',
			'imageRounded' => false,
			'imagePosition' => 'object-center',
			'columnGap' => 'gap-4',
			'columnGapCustom' => '',
			'imageMode' => 'single',
			'images' => [],
			'imageStackGap' => 'gap-4',
			'imageStackGapCustom' => '',
			'content' => '',
		] );
	}
}
