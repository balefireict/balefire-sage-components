<?php
/**
 * Reads Mission terms and their ACF fields into plain card data.
 *
 * The `mission` taxonomy and its term fields are registered from
 * acf-exports/mission-taxonomy.json (ACF > Tools > Import). Everything here
 * degrades to an empty card list if that import has not been run, rather than
 * fatal-ing on a missing taxonomy.
 *
 * @package BalefireInc\Sage\MissionCards
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\MissionCards;

class Missions {

	public const TAXONOMY = 'mission';

	/**
	 * Resolve the cards to render.
	 *
	 * @param array $term_ids Specific terms, in display order. Empty = all.
	 * @param int   $limit    Maximum cards.
	 * @return array<int, array<string, mixed>> Card data.
	 */
	public static function cards( array $term_ids = [], int $limit = 3 ): array {
		if ( ! taxonomy_exists( self::TAXONOMY ) ) {
			return [];
		}

		$term_ids = array_values( array_filter( array_map( 'absint', $term_ids ) ) );

		$args = [
			'taxonomy'   => self::TAXONOMY,
			'hide_empty' => false,
			'number'     => $limit > 0 ? $limit : 0,
		];

		/*
		 * Ordering note: Simple Taxonomy Ordering is enabled for this taxonomy,
		 * and it hooks `terms_clauses` to force ORDER BY its `tax_position` term
		 * meta on every front-end term query. It overrides whatever `orderby` is
		 * passed here, so card order is set by dragging in wp-admin > Missions —
		 * not by the order terms are ticked in the block, and not alphabetically.
		 *
		 * The `orderby` below is therefore only a fallback for when that plugin
		 * is inactive or the taxonomy is not enabled in its settings.
		 */
		if ( $term_ids !== [] ) {
			$args['include'] = $term_ids;
			$args['orderby'] = 'include';
		} else {
			$args['orderby'] = 'name';
			$args['order']   = 'ASC';
		}

		$terms = get_terms( $args );

		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return [];
		}

		return array_map( [ self::class, 'card' ], $terms );
	}

	/**
	 * Map one term to card data.
	 *
	 * @param \WP_Term $term Mission term.
	 * @return array<string, mixed>
	 */
	private static function card( \WP_Term $term ): array {
		return [
			// WordPress stores term names HTML-encoded ("Hunting &amp; Backcountry").
			// Decode here so Blade's {{ }} escapes exactly once — otherwise the
			// page renders a literal "&amp;". The ACF fields below come back raw
			// and need no such treatment.
			'title'    => html_entity_decode( $term->name, ENT_QUOTES, 'UTF-8' ),
			'permalink' => get_term_link( $term ),
			'imageId'  => (int) self::field( 'mission_image', $term ),
			'audience' => (string) self::field( 'mission_audience', $term ),
			'blurb'    => (string) ( self::field( 'mission_blurb', $term ) ?: $term->description ),
			'ctaLabel' => (string) self::field( 'mission_cta_label', $term ),
			'ctaUrl'   => (string) self::field( 'mission_cta_url', $term ),
		];
	}

	/**
	 * One ACF field off a term, or '' when ACF is not present.
	 *
	 * ACF accepts a WP_Term as its second argument and resolves it to the
	 * "term_{id}" meta id itself.
	 *
	 * @param string   $name Field name.
	 * @param \WP_Term $term Term.
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
	 * Term choices for the editor's term picker.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public static function choices(): array {
		if ( ! taxonomy_exists( self::TAXONOMY ) ) {
			return [];
		}

		$terms = get_terms( [
			'taxonomy'   => self::TAXONOMY,
			'hide_empty' => false,
			'orderby'    => 'name',
		] );

		if ( is_wp_error( $terms ) || ! is_array( $terms ) ) {
			return [];
		}

		return array_map(
			static fn( \WP_Term $t ): array => [
				'id'   => $t->term_id,
				'name' => $t->name,
			],
			$terms
		);
	}
}
