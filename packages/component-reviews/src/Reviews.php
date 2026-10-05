<?php
/**
 * Reads the `review` post type into plain card data.
 *
 * The CPT and its fields are registered by ACF Local JSON in the theme
 * (post_type_review.json / group_review_details.json). Everything here degrades
 * to an empty list if that is missing, rather than fatal-ing.
 *
 * @package BalefireInc\Sage\Reviews
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Reviews;

class Reviews {

	public const POST_TYPE = 'review';

	/**
	 * Resolve the reviews to render.
	 *
	 * @param int    $count   How many.
	 * @param string $orderby 'date' | 'rand'.
	 * @return array<int, array<string, mixed>>
	 */
	public static function cards( int $count = 9, string $orderby = 'date' ): array {
		if ( ! post_type_exists( self::POST_TYPE ) ) {
			return [];
		}

		$query = new \WP_Query( [
			'post_type'           => self::POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, $count ),
			'orderby'             => $orderby === 'rand' ? 'rand' : 'date',
			'order'               => 'DESC',
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
		] );

		return array_map( [ self::class, 'card' ], $query->posts );
	}

	/**
	 * One page of every review, newest first, for the grid layout.
	 *
	 * Always date order: a random order would reshuffle on every page, so
	 * reviews would repeat and others never appear.
	 *
	 * @param int $per_page Reviews per page.
	 * @param int $paged    1-based page number.
	 * @return array{cards: array<int, array<string, mixed>>, pages: int, current: int}
	 */
	public static function page( int $per_page, int $paged ): array {
		if ( ! post_type_exists( self::POST_TYPE ) ) {
			return [ 'cards' => [], 'pages' => 0, 'current' => 1 ];
		}

		$paged = max( 1, $paged );
		$query = new \WP_Query( [
			'post_type'           => self::POST_TYPE,
			'post_status'         => 'publish',
			'posts_per_page'      => max( 1, $per_page ),
			'paged'               => $paged,
			'orderby'             => [ 'date' => 'DESC', 'ID' => 'DESC' ],
			'ignore_sticky_posts' => true,
		] );

		return [
			'cards'   => array_map( [ self::class, 'card' ], $query->posts ),
			'pages'   => (int) $query->max_num_pages,
			'current' => $paged,
		];
	}

	/**
	 * The page number the current request asks for.
	 *
	 * A singular page carries /page/N/ in `paged`; a static front page
	 * carries it in `page` instead.
	 */
	public static function currentPage(): int {
		return max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	}

	/**
	 * Map one review to card data.
	 *
	 * @param \WP_Post $post Review.
	 * @return array<string, mixed>
	 */
	private static function card( \WP_Post $post ): array {
		/*
		 * The body is block markup (the legacy testimonials were Gutenberg
		 * paragraphs). Run it through the_content so blocks resolve, then strip
		 * to text: the card clamps it to three lines and the lightbox shows it
		 * whole, and neither wants stray markup.
		 */
		$body = wp_strip_all_tags( apply_filters( 'the_content', $post->post_content ) );
		$body = trim( html_entity_decode( $body, ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );

		return [
			'id'       => $post->ID,
			// WordPress stores titles HTML-encoded; decode so Blade escapes once.
			'name'     => html_entity_decode( get_the_title( $post ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ),
			'body'     => $body,
			'location' => (string) self::field( 'acffg_location', $post->ID ),
			'avatarId' => (int) get_post_thumbnail_id( $post ),
		];
	}

	/**
	 * One ACF field, or '' when ACF is absent.
	 *
	 * @param string $name    Field name.
	 * @param int    $post_id Post id.
	 * @return mixed
	 */
	private static function field( string $name, int $post_id ) {
		if ( ! function_exists( 'get_field' ) ) {
			return '';
		}

		$value = get_field( $name, $post_id );

		return $value === null || $value === false ? '' : $value;
	}
}
