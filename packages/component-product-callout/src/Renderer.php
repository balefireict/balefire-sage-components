<?php
/**
 * Renderer — resolves the products attribute and delegates to Blade.
 *
 * Bridge between the block entry point and the Blade component. Requires a
 * Sage or Acorn-powered theme — Blade is the only render path, keeping
 * markup in a single source of truth.
 *
 * @package BalefireInc\Sage\ProductCallout
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ProductCallout;

class Renderer {

	/**
	 * Render the product callout from the given props.
	 *
	 * @param array  $props              Component props (matches Blade @props).
	 * @param string $wrapper_attributes Optional block wrapper attribute string.
	 * @return string HTML output.
	 */
	public static function render( array $props, string $wrapper_attributes = '' ): string {
		$props = wp_parse_args( $props, [
			'title'    => '',
			'text'     => '',
			'products' => '',
		] );

		$props['products'] = self::resolveProducts( (string) $props['products'] );

		if ( $wrapper_attributes !== '' ) {
			$bag = \BalefireInc\Sage\Support\BlockAttributes::bag( $wrapper_attributes );
			if ( $bag !== null ) {
				$props['attributes'] = $bag;
			}
		}

		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.product-callout', $props )->render();
		}

		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.product-callout', $props )->render();
		}

		return '<!-- balefire/product-callout: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}

	/**
	 * Resolve a comma-separated list of SKUs and/or product IDs into
	 * published WC_Product objects, preserving order. SKUs are the
	 * editor-friendly form ("BT05-QK"); bare integers are treated as IDs
	 * first, then retried as a SKU (some SKUs are numeric). Unresolvable
	 * or unpublished entries are silently dropped — live catalog state
	 * decides what renders.
	 *
	 * @param string $list Comma-separated SKUs / IDs.
	 * @return array<int, \WC_Product>
	 */
	private static function resolveProducts( string $list ): array {
		if ( ! function_exists( 'wc_get_product' ) ) {
			return [];
		}

		$products = [];

		foreach ( array_filter( array_map( 'trim', explode( ',', $list ) ) ) as $ref ) {
			$product = null;

			if ( ctype_digit( $ref ) ) {
				$product = wc_get_product( (int) $ref );
			}

			if ( ! $product && function_exists( 'wc_get_product_id_by_sku' ) ) {
				$id = wc_get_product_id_by_sku( $ref );
				if ( $id ) {
					$product = wc_get_product( $id );
				}
			}

			if ( $product instanceof \WC_Product && $product->get_status() === 'publish' ) {
				$products[] = $product;
			}
		}

		return $products;
	}
}
