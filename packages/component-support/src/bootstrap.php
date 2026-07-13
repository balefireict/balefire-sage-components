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

defined( 'ABSPATH' ) || exit;

if ( ! defined( 'BALEFIRE_SAGE_COMPONENTS_CATEGORY' ) ) {
	define( 'BALEFIRE_SAGE_COMPONENTS_CATEGORY', 'balefire' );

	add_filter( 'block_categories_all', static function ( array $categories ): array {
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
	} );
}
