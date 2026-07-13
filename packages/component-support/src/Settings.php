<?php
/**
 * Settings — site-wide component settings, ported from balefire-blocks.
 *
 * Same option key as the original plugin, so a site migrating from
 * balefire-blocks keeps its saved toggles and defaults. Blocks read the
 * "defaults" bucket as fallbacks for unset attributes; package bootstraps
 * consult isBlockEnabled() before registering anything.
 *
 * @package BalefireInc\Sage\Support
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Support;

final class Settings {

	public const OPTION_KEY = 'balefire_blocks_settings';

	private const LEGACY_OPTION_KEY = 'bma_tw_blocks_settings';

	public static function registerSettings(): void {
		register_setting(
			'balefire_blocks',
			self::OPTION_KEY,
			[
				'type'              => 'object',
				'show_in_rest'      => [
					'schema' => [
						'type'       => 'object',
						'properties' => [
							'enabledBlocks' => [
								'type'                 => 'object',
								'additionalProperties' => [
									'type' => 'boolean',
								],
							],
							'defaults' => [
								'type'       => 'object',
								'properties' => [
									'sectionMaxWidth' => [ 'type' => 'string' ],
									'buttonStyle'     => [ 'type' => 'string' ],
									'postsPerPage'    => [ 'type' => 'integer' ],
								],
							],
						],
					],
				],
				'sanitize_callback' => [ self::class, 'sanitizeSettings' ],
				'default'           => self::defaults(),
			]
		);
	}

	public static function getSettings(): array {
		$settings = get_option( self::OPTION_KEY, [] );

		if ( ! is_array( $settings ) || $settings === [] ) {
			$settings = get_option( self::LEGACY_OPTION_KEY, [] );
		}

		$settings = is_array( $settings ) ? $settings : [];

		return wp_parse_args( $settings, self::defaults() );
	}

	/**
	 * A default value from the "defaults" bucket.
	 *
	 * @param string $key     defaults key (sectionMaxWidth, buttonStyle, postsPerPage).
	 * @param mixed  $fallback Returned when the key is missing.
	 */
	public static function defaultFor( string $key, $fallback = null ) {
		return self::getSettings()['defaults'][ $key ] ?? $fallback;
	}

	/**
	 * Whether a block is enabled on this site (dependency-aware).
	 */
	public static function isBlockEnabled( string $slug ): bool {
		// Before WP is fully loaded there is no option store; stay enabled.
		if ( ! function_exists( 'get_option' ) ) {
			return true;
		}

		$enabled = self::getSettings()['enabledBlocks'] ?? [];

		return Catalog::isEnabled( $slug, is_array( $enabled ) ? $enabled : [] );
	}

	public static function defaults(): array {
		$enabled_blocks = [];

		foreach ( Catalog::all() as $block ) {
			$enabled_blocks[ $block['slug'] ] = (bool) $block['active'];
		}

		return [
			'enabledBlocks' => $enabled_blocks,
			'defaults'      => [
				'sectionMaxWidth' => 'wide',
				'buttonStyle'     => 'solid',
				'postsPerPage'    => 3,
			],
		];
	}

	public static function sanitizeSettings( $value ): array {
		$defaults = self::defaults();
		$value    = is_array( $value ) ? $value : [];

		$enabled_blocks  = [];
		$default_enabled = $defaults['enabledBlocks'];

		foreach ( $default_enabled as $slug => $is_enabled ) {
			$enabled_blocks[ $slug ] = ! empty( $value['enabledBlocks'][ $slug ] );
		}

		// A block whose dependency is disabled is disabled too.
		foreach ( Catalog::all() as $block ) {
			foreach ( Catalog::dependenciesFor( $block ) as $dependency_slug ) {
				if ( empty( $enabled_blocks[ $dependency_slug ] ) ) {
					$enabled_blocks[ $block['slug'] ] = false;
					break;
				}
			}
		}

		$section_max_width = isset( $value['defaults']['sectionMaxWidth'] )
			? sanitize_key( (string) $value['defaults']['sectionMaxWidth'] )
			: $defaults['defaults']['sectionMaxWidth'];

		if ( ! in_array( $section_max_width, [ 'content', 'wide', 'full' ], true ) ) {
			$section_max_width = $defaults['defaults']['sectionMaxWidth'];
		}

		$button_style = isset( $value['defaults']['buttonStyle'] )
			? sanitize_key( (string) $value['defaults']['buttonStyle'] )
			: $defaults['defaults']['buttonStyle'];

		if ( ! in_array( $button_style, [ 'solid', 'outline' ], true ) ) {
			$button_style = $defaults['defaults']['buttonStyle'];
		}

		$posts_per_page = isset( $value['defaults']['postsPerPage'] )
			? max( 1, min( 12, absint( $value['defaults']['postsPerPage'] ) ) )
			: $defaults['defaults']['postsPerPage'];

		return [
			'enabledBlocks' => $enabled_blocks,
			'defaults'      => [
				'sectionMaxWidth' => $section_max_width,
				'buttonStyle'     => $button_style,
				'postsPerPage'    => $posts_per_page,
			],
		];
	}
}
