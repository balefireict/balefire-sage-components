<?php
/**
 * balefireict/component-support — bootstrap.
 *
 * Registers the shared "Balefire" block category that every component
 * package's block.json points at. Runs once regardless of how many
 * component packages are installed.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\Support
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

if ( ! defined( 'BALEFIRE_SAGE_COMPONENTS_CATEGORY' ) ) {
	define( 'BALEFIRE_SAGE_COMPONENTS_CATEGORY', 'balefire' );

	$balefire_category_filter = static function ( array $categories ): array {
		foreach ( $categories as $category ) {
			if ( ( $category['slug'] ?? '' ) === 'balefire' ) {
				return $categories;
			}
		}

		array_unshift( $categories, [
			'slug'  => 'balefire',
			'title' => __( 'Balefire', 'balefire' ),
			'icon'  => null,
		] );

		return $categories;
	};

	if ( function_exists( 'add_filter' ) ) {
		add_filter( 'block_categories_all', $balefire_category_filter );
	} else {
		// Autoloaded before WordPress's plugin API exists (Bedrock requires
		// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
		// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php
		// loads, making this equivalent to add_filter().
		$GLOBALS['wp_filter']['block_categories_all'][10][] = [
			'function'      => $balefire_category_filter,
			'accepted_args' => 1,
		];
	}

	unset( $balefire_category_filter );
}
