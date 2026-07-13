<?php
/**
 * Catalog — the block catalog for the settings screen and enable gates.
 *
 * Ported from balefire-blocks' BlockRegistry. The catalog data lives in
 * resources/catalog.json (slug, title, description, type, keywords,
 * dependsOn) and drives the wp-admin settings screen plus the per-block
 * enabled checks each package bootstrap performs.
 *
 * @package BalefireInc\Sage\Support
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Support;

final class Catalog {

	private static ?array $blocks = null;

	/**
	 * All catalog entries.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function all(): array {
		if ( self::$blocks !== null ) {
			return self::$blocks;
		}

		$path = __DIR__ . '/../resources/catalog.json';
		$data = is_file( $path ) ? json_decode( (string) file_get_contents( $path ), true ) : [];
		$data = is_array( $data ) ? $data : [];

		self::$blocks = array_values( array_filter(
			$data['blocks'] ?? [],
			static fn ( $block ): bool => is_array( $block ) && ! empty( $block['slug'] )
		) );

		return self::$blocks;
	}

	/**
	 * Catalog entries marked active.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function active(): array {
		return array_values( array_filter(
			self::all(),
			static fn ( array $block ): bool => ! empty( $block['active'] )
		) );
	}

	/**
	 * Whether a block is enabled, honoring dependency chains.
	 *
	 * @param string   $slug           Block slug.
	 * @param array    $enabled_blocks slug => bool map from settings.
	 * @param string[] $seen           Cycle guard.
	 */
	public static function isEnabled( string $slug, array $enabled_blocks, array $seen = [] ): bool {
		if ( in_array( $slug, $seen, true ) ) {
			return false;
		}

		// Unknown to the catalog (or unset in settings): enabled by default.
		if ( array_key_exists( $slug, $enabled_blocks ) && empty( $enabled_blocks[ $slug ] ) ) {
			return false;
		}

		$block = self::find( $slug );

		if ( $block === null ) {
			return true;
		}

		$seen[] = $slug;

		foreach ( self::dependenciesFor( $block ) as $dependency_slug ) {
			if ( ! self::isEnabled( $dependency_slug, $enabled_blocks, $seen ) ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * The dependsOn list for a catalog entry.
	 *
	 * @param array $block Catalog entry.
	 * @return string[]
	 */
	public static function dependenciesFor( array $block ): array {
		return array_values( array_filter(
			(array) ( $block['dependsOn'] ?? [] ),
			'is_string'
		) );
	}

	/**
	 * Find a catalog entry by slug.
	 */
	public static function find( string $slug ): ?array {
		foreach ( self::all() as $block ) {
			if ( ( $block['slug'] ?? '' ) === $slug ) {
				return $block;
			}
		}

		return null;
	}
}
