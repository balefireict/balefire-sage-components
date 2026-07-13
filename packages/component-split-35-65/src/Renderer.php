<?php
/**
 * Renderer — delegates to the Blade view for Split 35/65.
 *
 * Bridge between the block/shortcode entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\Split3565
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Split3565;

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
			return \Roots\view( 'bma::components.split-35-65', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.split-35-65', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/split-35-65: Sage/Acorn Blade runtime not found. '
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
			'content' => '',
			'buttonLabel' => '',
			'buttonUrl' => '',
			'mediaType' => 'image',
			'imageId' => 0,
			'imageUrl' => '',
			'imageAlt' => '',
			'videoUrl' => '',
			'gap' => '8',
			'reverse' => false,
			'iconId' => 0,
			'iconUrl' => '',
			'iconAlt' => '',
		] );
	}
}
