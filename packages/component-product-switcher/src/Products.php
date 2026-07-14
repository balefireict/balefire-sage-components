<?php
/**
 * Resolves the block's items to product data.
 *
 * Each item points at a WooCommerce product; the label, image and link default
 * to that product's name, featured image and permalink, and any of the three can
 * be overridden per item. Nothing here assumes WooCommerce is active — with Woo
 * off, an item with an explicit label/image still renders.
 *
 * @package BalefireInc\Sage\ProductSwitcher
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ProductSwitcher;

class Products {

	/**
	 * Resolve the block's props into render-ready panels.
	 *
	 * Two sources:
	 *
	 * - `attribute` — pick a product category and a product attribute; the tabs
	 *   are the attribute terms actually used within that category. This is the
	 *   one to reach for: a new product tagged with the attribute shows up on
	 *   its own, and nothing about the catalogue has to be restructured.
	 *
	 * - `products` — a hand-picked list of products, one per tab.
	 *
	 * @param array $props Block props.
	 * @return array<int, array<string, mixed>>
	 */
	public static function resolve( array $props ): array {
		$source = (string) ( $props['source'] ?? 'products' );

		if ( $source === 'attribute' ) {
			return self::attributePanels(
				absint( $props['categoryId'] ?? 0 ),
				(string) ( $props['attribute'] ?? '' )
			);
		}

		$items = $props['items'] ?? [];

		return self::panels( is_array( $items ) ? $items : [] );
	}

	/**
	 * Panels from the terms of a product attribute, within one product category.
	 *
	 * A term only becomes a tab if the category actually contains a product
	 * carrying it — so an attribute shared across the catalogue does not drag
	 * irrelevant tabs into a category-specific block.
	 *
	 * @param int    $category_id product_cat term id.
	 * @param string $taxonomy    Attribute taxonomy, e.g. "pa_mount".
	 * @return array<int, array<string, mixed>>
	 */
	public static function attributePanels( int $category_id, string $taxonomy ): array {
		if ( $category_id <= 0 || $taxonomy === '' || ! taxonomy_exists( $taxonomy ) ) {
			return [];
		}

		$terms = get_terms( [
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
		] );

		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return [];
		}

		/*
		 * Order by WooCommerce's own attribute term order — the drag-sort under
		 * Products > Attributes > (attribute) > Configure terms, stored as an
		 * `order_{taxonomy}` term meta. Sorting in PHP rather than via get_terms'
		 * meta_key: that would INNER JOIN termmeta and silently drop every term
		 * that has never been dragged (i.e. all of them, on a fresh attribute).
		 */
		usort(
			$terms,
			static function ( \WP_Term $a, \WP_Term $b ) use ( $taxonomy ): int {
				$key    = 'order_' . $taxonomy;
				$a_pos  = (int) get_term_meta( $a->term_id, $key, true );
				$b_pos  = (int) get_term_meta( $b->term_id, $key, true );

				return $a_pos === $b_pos
					? strcasecmp( $a->name, $b->name )
					: $a_pos <=> $b_pos;
			}
		);

		$category = get_term( $category_id, 'product_cat' );

		if ( ! $category instanceof \WP_Term ) {
			return [];
		}

		$panels = [];

		foreach ( $terms as $term ) {
			$product_ids = self::productsIn( $category_id, $taxonomy, (int) $term->term_id );

			if ( $product_ids === [] ) {
				continue;
			}

			$image_id = (int) self::field( 'mount_image', $term );

			// No term image set? Borrow the first matching product's photo, so
			// the block is useful the moment an attribute is tagged.
			if ( $image_id === 0 && function_exists( 'wc_get_product' ) ) {
				$product  = wc_get_product( $product_ids[0] );
				$image_id = $product ? absint( $product->get_image_id() ) : 0;
			}

			$url = (string) self::field( 'mount_url', $term );

			if ( $url === '' ) {
				// WooCommerce layered nav: /product-category/x/?filter_mount=slug
				$filter_key = 'filter_' . str_replace( 'pa_', '', $taxonomy );
				$url        = add_query_arg( $filter_key, $term->slug, (string) get_term_link( $category ) );
			}

			$panels[] = [
				'label'     => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
				'imageId'   => $image_id,
				'url'       => $url,
				'productId' => $product_ids[0],
			];
		}

		return $panels;
	}

	/**
	 * Product ids in a category that also carry an attribute term.
	 *
	 * @param int    $category_id product_cat term id.
	 * @param string $taxonomy    Attribute taxonomy.
	 * @param int    $term_id     Attribute term id.
	 * @return array<int, int>
	 */
	private static function productsIn( int $category_id, string $taxonomy, int $term_id ): array {
		$query = new \WP_Query( [
			'post_type'              => 'product',
			'post_status'            => 'publish',
			'posts_per_page'         => 20,
			'fields'                 => 'ids',
			// Deterministic, so the borrowed fallback image does not change on a
			// whim. The reliable way to control it is the term's own image field.
			'orderby'                => [ 'menu_order' => 'ASC', 'title' => 'ASC' ],
			'ignore_sticky_posts'    => true,
			'no_found_rows'          => true,
			'update_post_meta_cache' => false,
			'update_post_term_cache' => false,
			'tax_query'              => [
				'relation' => 'AND',
				[
					'taxonomy' => 'product_cat',
					'field'    => 'term_id',
					'terms'    => $category_id,
				],
				[
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_id,
				],
			],
		] );

		return array_map( 'absint', $query->posts );
	}

	/**
	 * One ACF field off a term, or '' when ACF is not present.
	 *
	 * @param string    $name Field name.
	 * @param \WP_Term  $term Term.
	 * @return mixed
	 */
	private static function field( string $name, \WP_Term $term ) {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$value = get_field( $name, $term );

		return $value === null || $value === false ? '' : $value;
	}

	/**
	 * Attribute choices for the editor picker.
	 *
	 * @return array<int, array<string, string>>
	 */
	public static function attributeChoices(): array {
		if ( ! function_exists( 'wc_get_attribute_taxonomies' ) ) {
			return [];
		}

		return array_values( array_map(
			static fn( $a ): array => [
				'taxonomy' => 'pa_' . $a->attribute_name,
				'label'    => $a->attribute_label,
			],
			wc_get_attribute_taxonomies()
		) );
	}

	/**
	 * Product category choices for the editor picker.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function categoryChoices(): array {
		$terms = get_terms( [
			'taxonomy'   => 'product_cat',
			'hide_empty' => false,
			'orderby'    => 'name',
		] );

		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return [];
		}

		return array_map(
			static fn( \WP_Term $t ): array => [
				'id'   => $t->term_id,
				'name' => html_entity_decode( $t->name, ENT_QUOTES, 'UTF-8' ),
			],
			$terms
		);
	}

	/**
	 * Resolve raw block items into render-ready panels.
	 *
	 * @param array $items Raw items (productId, label, imageId, url).
	 * @return array<int, array<string, mixed>>
	 */
	public static function panels( array $items ): array {
		$panels = [];

		foreach ( $items as $item ) {
			if ( ! is_array( $item ) ) {
				continue;
			}

			$panel = self::panel( $item );

			// A panel with no label is unusable — it would render a nameless tab.
			if ( $panel['label'] !== '' ) {
				$panels[] = $panel;
			}
		}

		return $panels;
	}

	/**
	 * Map one item to a panel.
	 *
	 * @param array $item Raw item.
	 * @return array<string, mixed>
	 */
	private static function panel( array $item ): array {
		$product_id = absint( $item['productId'] ?? 0 );
		$label      = trim( (string) ( $item['label'] ?? '' ) );
		$image_id   = absint( $item['imageId'] ?? 0 );
		$url        = trim( (string) ( $item['url'] ?? '' ) );

		$product = $product_id > 0 && function_exists( 'wc_get_product' )
			? wc_get_product( $product_id )
			: null;

		if ( $product ) {
			if ( $label === '' ) {
				// Product titles are stored HTML-encoded; decode so Blade's {{ }}
				// escapes exactly once rather than rendering a literal "&amp;".
				$label = html_entity_decode( $product->get_name(), ENT_QUOTES, 'UTF-8' );
			}

			if ( $image_id === 0 ) {
				$image_id = absint( $product->get_image_id() );
			}

			if ( $url === '' ) {
				$url = (string) get_permalink( $product_id );
			}
		}

		return [
			'label'     => $label,
			'imageId'   => $image_id,
			'url'       => $url,
			'productId' => $product_id,
		];
	}

	/**
	 * Product choices for the editor's picker.
	 *
	 * @param int $limit Maximum products to offer.
	 * @return array<int, array<string, mixed>>
	 */
	public static function choices( int $limit = 200 ): array {
		if ( ! function_exists( 'wc_get_products' ) ) {
			return [];
		}

		$products = wc_get_products( [
			'limit'   => $limit,
			'status'  => 'publish',
			'orderby' => 'title',
			'order'   => 'ASC',
		] );

		return array_map(
			static fn( $p ): array => [
				'id'   => $p->get_id(),
				'name' => html_entity_decode( $p->get_name(), ENT_QUOTES, 'UTF-8' ),
			],
			$products
		);
	}
}
