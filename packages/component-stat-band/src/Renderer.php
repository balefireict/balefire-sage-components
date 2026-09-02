<?php
/**
 * Renderer — delegates to the Blade view for the Stat Band.
 *
 * Bridge between the block entry point and the Blade component. Requires a
 * Sage or Acorn-powered theme — Blade is the only render path, keeping
 * markup in a single source of truth.
 *
 * @package BalefireInc\Sage\StatBand
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\StatBand;

class Renderer {

	/**
	 * Render the stat band from the given props.
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
			return \Roots\view( 'bma::components.stat-band', $props )->render();
		}

		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.stat-band', $props )->render();
		}

		return '<!-- balefire/stat-band: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}

	/**
	 * Strict allowlist for pasted inline SVG icons.
	 *
	 * @return array<string, array<string, bool>>
	 */
	public static function svgAllowlist(): array {
		$shape = [
			'fill' => true,
			'stroke' => true,
			'stroke-width' => true,
			'stroke-linecap' => true,
			'stroke-linejoin' => true,
			'class' => true,
			'aria-hidden' => true,
		];

		return [
			'svg' => $shape + [ 'xmlns' => true, 'viewbox' => true, 'width' => true, 'height' => true ],
			'g' => $shape,
			'path' => $shape + [ 'd' => true ],
			'circle' => $shape + [ 'cx' => true, 'cy' => true, 'r' => true ],
			'rect' => $shape + [ 'x' => true, 'y' => true, 'width' => true, 'height' => true ],
			'line' => $shape + [ 'x' => true, 'y' => true, 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ],
			'polyline' => $shape + [ 'points' => true ],
			'polygon' => $shape + [ 'points' => true ],
		];
	}

	/**
	 * Merge props with defaults (mirrors the Blade @props defaults).
	 *
	 * @param array $props Raw props.
	 * @return array Resolved props.
	 */
	private static function defaults( array $props ): array {
		return wp_parse_args( $props, [
			'tone'           => 'primary',
			'lead'           => '',
			'items'          => [],
			'columns'        => 4,
			'radius'         => 'none',
			'primaryLabel'   => '',
			'primaryUrl'     => '',
			'secondaryLabel' => '',
			'secondaryUrl'   => '',
		] );
	}
}
