<?php
/**
 * Bundled icon set for Numbered Features.
 *
 * An item with no icon keeps the numeral it always had, so nothing that
 * already uses this component changes. The set is deliberately small and
 * generic: these mark what a feature *is about*, and a picker with forty
 * entries invites decoration rather than meaning.
 *
 * Paths are Feather (MIT), drawn on a 24x24 grid as strokes so they inherit
 * both color and weight from the surrounding text.
 *
 * Items may also carry their own inline SVG (`iconSvg`), which wins over the
 * bundled slug once it passes the strict allowlist in self::sanitize().
 *
 * @package BalefireInc\Sage\NumberedFeatures
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\NumberedFeatures;

class Icons {

	/**
	 * Icon slug => human label, for the editor's picker.
	 *
	 * @return array<string, string>
	 */
	public static function choices(): array {
		return array(
			''              => __( 'Number', 'balefire' ),
			'crosshair'     => __( 'Crosshair', 'balefire' ),
			'tool'          => __( 'Wrench', 'balefire' ),
			'shield-check'  => __( 'Shield with check', 'balefire' ),
			'award'         => __( 'Award', 'balefire' ),
			'check-circle'  => __( 'Check in a circle', 'balefire' ),
			'settings'      => __( 'Gear', 'balefire' ),
		);
	}

	/**
	 * The inner geometry of each icon, keyed by slug.
	 *
	 * @return array<string, string>
	 */
	private static function paths(): array {
		return array(
			'crosshair'    => '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/>'
				. '<path d="M12 1v4M12 19v4M1 12h4M19 12h4"/>',
			'tool'         => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 '
				. '7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
			'shield-check' => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>',
			'award'        => '<circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>',
			'check-circle' => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="m22 4-10 10.01-3-3"/>',
			'settings'     => '<circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 '
				. '1-2.83 2.83l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-4 0v-.09A1.65 1.65 '
				. '0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 '
				. '1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1 0-4h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 '
				. '2 0 0 1 2.83-2.83l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 4 0v.09a1.65 '
				. '1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 2.83l-.06.06a1.65 1.65 0 0 0-.33 '
				. '1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 0 4h-.09a1.65 1.65 0 0 0-1.51 1z"/>',
		);
	}

	/**
	 * One icon as a sized, color-inheriting <svg>, or '' for an unknown slug.
	 *
	 * Decorative by contract: the heading beside it carries the meaning, so the
	 * markup is hidden from assistive tech rather than given a label.
	 *
	 * @param string $slug Icon slug.
	 * @return string SVG markup, or '' when there is no such icon.
	 */
	public static function svg( string $slug ): string {
		$paths = self::paths();

		if ( $slug === '' || ! isset( $paths[ $slug ] ) ) {
			return '';
		}

		return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" '
			. 'stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false" '
			. 'class="size-full">' . $paths[ $slug ] . '</svg>';
	}

	/**
	 * Allowlist for author-supplied inline SVG.
	 *
	 * Geometry only: no <style>, <script>, <use>, <foreignObject>, event
	 * handlers, ids or hrefs. wp_kses matches attribute names case-insensitively,
	 * so the lowercase `viewbox` covers `viewBox`.
	 *
	 * @return array<string, array<string, bool>>
	 */
	public static function allowlist(): array {
		$paint = array(
			'fill'            => true,
			'stroke'          => true,
			'stroke-width'    => true,
			'stroke-linecap'  => true,
			'stroke-linejoin' => true,
			'fill-rule'       => true,
			'clip-rule'       => true,
			'opacity'         => true,
			'transform'       => true,
			'class'           => true,
		);

		return array(
			'svg'      => $paint + array(
				'xmlns'       => true,
				'viewbox'     => true,
				'width'       => true,
				'height'      => true,
				'aria-hidden' => true,
				'focusable'   => true,
				'role'        => true,
			),
			'g'        => $paint,
			'path'     => $paint + array( 'd' => true ),
			'circle'   => $paint + array( 'cx' => true, 'cy' => true, 'r' => true ),
			'rect'     => $paint + array( 'x' => true, 'y' => true, 'width' => true, 'height' => true, 'rx' => true, 'ry' => true ),
			'line'     => $paint + array( 'x1' => true, 'y1' => true, 'x2' => true, 'y2' => true ),
			'polyline' => $paint + array( 'points' => true ),
			'polygon'  => $paint + array( 'points' => true ),
		);
	}

	/**
	 * Sanitize author-supplied inline SVG, or '' when nothing usable remains.
	 *
	 * @param string $svg Raw markup from the editor.
	 * @return string Sanitized <svg>…</svg>, or ''.
	 */
	public static function sanitize( string $svg ): string {
		$svg = trim( $svg );

		if ( $svg === '' || stripos( $svg, '<svg' ) === false ) {
			return '';
		}

		$clean = trim( wp_kses( $svg, self::allowlist() ) );

		// A stripped-down blob that no longer opens with <svg> is not an icon.
		if ( stripos( $clean, '<svg' ) !== 0 ) {
			return '';
		}

		return $clean;
	}

	/**
	 * Resolve the icon for one item: custom inline SVG first, bundled slug second.
	 *
	 * @param array $item Item as stored in the block's `items` attribute.
	 * @return string SVG markup, or '' when the item has no icon.
	 */
	public static function forItem( array $item ): string {
		$custom = self::sanitize( (string) ( $item['iconSvg'] ?? '' ) );

		if ( $custom !== '' ) {
			return $custom;
		}

		return self::svg( trim( (string) ( $item['icon'] ?? '' ) ) );
	}
}
