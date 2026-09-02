<?php
/**
 * Renderer — delegates to the Blade view for Process Steps.
 *
 * Bridge between the block entry point and the Blade component. Requires a
 * Sage or Acorn-powered theme — Blade is the only render path, keeping
 * markup in a single source of truth.
 *
 * @package BalefireInc\Sage\ProcessSteps
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ProcessSteps;

class Renderer {

	/**
	 * Render the process steps from the given props.
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

		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.process-steps', $props )->render();
		}

		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.process-steps', $props )->render();
		}

		return '<!-- balefire/process-steps: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}

	/**
	 * Keep a user-supplied CSS colour, or drop it.
	 *
	 * Accepts hex, named colours, the colour functions, and var()/color-mix()
	 * so a theme token can be referenced. Anything that could break out of a
	 * style attribute (semicolons, braces, quotes, url()) is rejected.
	 *
	 * @param string $color Raw colour string.
	 * @return string Sanitised colour, or '' when invalid.
	 */
	public static function cssColor( string $color ): string {
		$color = trim( $color );

		if ( $color === '' || strlen( $color ) > 160 ) {
			return '';
		}

		$pattern = '/^(#[0-9a-f]{3,8}|[a-z]+|(rgba?|hsla?|oklch|oklab|lab|lch|hwb|color|color-mix|var)\([^;{}<>"\'\\\\]{1,140}\))$/i';

		return preg_match( $pattern, $color ) === 1 ? $color : '';
	}

	/**
	 * Merge props with defaults (mirrors the Blade @props defaults).
	 *
	 * @param array $props Raw props.
	 * @return array Resolved props.
	 */
	private static function defaults( array $props ): array {
		return wp_parse_args( $props, [
			'tone'           => 'white',
			'layout'         => 'stack',
			'direction'      => 'vertical',
			'eyebrow'        => '',
			'eyebrowVariant' => 'marks',
			'title'          => '',
			'content'        => '',
			'items'          => [],
			'palette'        => '',
			'numberStyle'    => 'disc',
			'primaryLabel'   => '',
			'primaryUrl'     => '',
			'secondaryLabel' => '',
			'secondaryUrl'   => '',
			'innerContent'   => '',
		] );
	}
}
