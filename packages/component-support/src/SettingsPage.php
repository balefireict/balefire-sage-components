<?php
/**
 * SettingsPage — the "Balefire Blocks" screen in wp-admin.
 *
 * Ported from balefire-blocks. A wp.components admin app (toggle grid,
 * type filter, search, global defaults) saving through the REST settings
 * endpoint. Search/filtering is client-side — the catalog is already in
 * the page — replacing the original's htmx + admin-ajax round-trip.
 * Assets are inlined against src-less handles: vendor/ may live outside
 * the webroot, so no asset URL is ever assumed.
 *
 * @package BalefireInc\Sage\Support
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Support;

final class SettingsPage {

	private const PAGE_SLUG = 'balefire-blocks';

	public static function registerPage(): void {
		add_menu_page(
			__( 'Balefire Blocks', 'balefire' ),
			__( 'Balefire Blocks', 'balefire' ),
			'manage_options',
			self::PAGE_SLUG,
			[ self::class, 'renderPage' ],
			'dashicons-screenoptions',
			61
		);
	}

	public static function renderPage(): void {
		echo '<div class="wrap"><div id="balefire-blocks-admin"></div></div>';
	}

	public static function enqueueAssets( string $hook_suffix ): void {
		if ( $hook_suffix !== 'toplevel_page_' . self::PAGE_SLUG ) {
			return;
		}

		wp_register_script(
			'balefire-blocks-admin',
			false,
			[ 'wp-element', 'wp-components', 'wp-api-fetch', 'wp-i18n' ],
			null,
			true
		);
		wp_enqueue_script( 'balefire-blocks-admin' );

		wp_add_inline_script(
			'balefire-blocks-admin',
			'window.balefireBlocksAdmin = ' . wp_json_encode( [
				'blocks'    => array_values( Catalog::active() ),
				'settings'  => Settings::getSettings(),
				'optionKey' => Settings::OPTION_KEY,
				'restPath'  => '/wp/v2/settings',
			] ) . ';',
			'before'
		);

		$admin_js = file_get_contents( __DIR__ . '/../resources/admin/admin.js' );
		if ( $admin_js !== false ) {
			wp_add_inline_script( 'balefire-blocks-admin', $admin_js );
		}

		wp_register_style( 'balefire-blocks-admin', false, [ 'wp-components' ], null );
		wp_enqueue_style( 'balefire-blocks-admin' );

		$admin_css = file_get_contents( __DIR__ . '/../resources/admin/admin.css' );
		if ( $admin_css !== false ) {
			wp_add_inline_style( 'balefire-blocks-admin', $admin_css );
		}
	}
}
