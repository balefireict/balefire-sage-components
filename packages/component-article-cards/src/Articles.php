<?php
/**
 * Resolves the block's props into render-ready article cards.
 *
 * Two sources:
 *
 * - `filter` — newest N posts, optionally narrowed to a term of either the
 *   built-in `category` taxonomy or the shared `mission` taxonomy. New posts
 *   appear on their own.
 * - `picked` — a hand-picked, ordered list of posts.
 *
 * @package BalefireInc\Sage\ArticleCards
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ArticleCards;

class Articles {

	/**
	 * Taxonomies the block can filter by.
	 */
	public const TAXONOMIES = [ 'category', 'mission' ];

	/**
	 * Categories shown on a card before we stop. The comp shows two; posts here
	 * carry up to five, and the meta line is a single row.
	 */
	private const MAX_TERMS = 2;

	/**
	 * Resolve props into cards.
	 *
	 * @param array $props Block props.
	 * @return array<int, array<string, mixed>>
	 */
	public static function resolve( array $props ): array {
		$source   = (string) ( $props['source'] ?? 'filter' );
		$count    = max( 1, (int) ( $props['count'] ?? 4 ) );
		$fallback = absint( $props['fallbackImageId'] ?? 0 );

		$posts = $source === 'picked'
			? self::picked( (array) ( $props['postIds'] ?? [] ) )
			: self::filtered(
				(string) ( $props['taxonomy'] ?? 'category' ),
				absint( $props['termId'] ?? 0 ),
				$count
			);

		return array_map(
			static fn( \WP_Post $post ): array => self::card( $post, $fallback ),
			$posts
		);
	}

	/**
	 * Newest posts, optionally within one term.
	 *
	 * @param string $taxonomy Taxonomy slug.
	 * @param int    $term_id  Term id, 0 for no filter.
	 * @param int    $count    How many.
	 * @return array<int, \WP_Post>
	 */
	private static function filtered( string $taxonomy, int $term_id, int $count ): array {
		$args = [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $count,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		];

		if ( $term_id > 0 && in_array( $taxonomy, self::TAXONOMIES, true ) && taxonomy_exists( $taxonomy ) ) {
			$args['tax_query'] = [
				[
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_id,
				],
			];
		}

		return ( new \WP_Query( $args ) )->posts;
	}

	/**
	 * Hand-picked posts, in the order they were chosen.
	 *
	 * @param array $post_ids Post ids.
	 * @return array<int, \WP_Post>
	 */
	private static function picked( array $post_ids ): array {
		$post_ids = array_values( array_filter( array_map( 'absint', $post_ids ) ) );

		if ( $post_ids === [] ) {
			return [];
		}

		return ( new \WP_Query( [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'post__in'            => $post_ids,
			'orderby'             => 'post__in',
			'posts_per_page'      => count( $post_ids ),
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		] ) )->posts;
	}

	/**
	 * Map one post to card data.
	 *
	 * @param \WP_Post $post     Post.
	 * @param int      $fallback Fallback attachment id when the post has none.
	 * @return array<string, mixed>
	 */
	private static function card( \WP_Post $post, int $fallback ): array {
		$image_id = (int) get_post_thumbnail_id( $post );

		return [
			'title'   => self::decode( get_the_title( $post ) ),
			'url'     => (string) get_permalink( $post ),
			'excerpt' => self::decode( wp_strip_all_tags( get_the_excerpt( $post ) ) ),
			'imageId' => $image_id > 0 ? $image_id : $fallback,
			'terms'   => self::terms( $post ),
		];
	}

	/**
	 * The card's meta line: the post's first categories.
	 *
	 * @param \WP_Post $post Post.
	 * @return string Comma-separated names, or ''.
	 */
	private static function terms( \WP_Post $post ): string {
		$names = wp_get_post_terms( $post->ID, 'category', [ 'fields' => 'names' ] );

		if ( is_wp_error( $names ) || $names === [] ) {
			return '';
		}

		return self::decode( implode( ', ', array_slice( $names, 0, self::MAX_TERMS ) ) );
	}

	/**
	 * WordPress stores titles and term names HTML-encoded. Decode once here so
	 * Blade's {{ }} escapes exactly once — otherwise the page renders a literal
	 * "&amp;".
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function decode( string $value ): string {
		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}

	/**
	 * Term choices for the editor, grouped by taxonomy.
	 *
	 * @return array<string, array<int, array<string, mixed>>>
	 */
	public static function termChoices(): array {
		$out = [];

		foreach ( self::TAXONOMIES as $taxonomy ) {
			if ( ! taxonomy_exists( $taxonomy ) ) {
				continue;
			}

			$terms = get_terms( [
				'taxonomy'   => $taxonomy,
				'hide_empty' => false,
			] );

			if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
				continue;
			}

			$out[ $taxonomy ] = array_map(
				static fn( \WP_Term $t ): array => [
					'id'   => $t->term_id,
					'name' => self::decode( $t->name ),
				],
				$terms
			);
		}

		return $out;
	}

	/**
	 * Post choices for the hand-picked mode.
	 *
	 * @param int $limit Maximum posts to offer.
	 * @return array<int, array<string, mixed>>
	 */
	public static function postChoices( int $limit = 100 ): array {
		$posts = ( new \WP_Query( [
			'post_type'           => 'post',
			'post_status'         => 'publish',
			'posts_per_page'      => $limit,
			'orderby'             => 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		] ) )->posts;

		return array_map(
			static fn( \WP_Post $p ): array => [
				'id'   => $p->ID,
				'name' => self::decode( get_the_title( $p ) ),
			],
			$posts
		);
	}
}
