<?php
/**
 * Items — resolves query-mode props into render-ready FAQ items, and
 * builds the FAQPage JSON-LD payload for either source.
 *
 * Kept out of render.php so a theme can call the same query from a
 * template (`Items::query([...])`) and get the identical list the block
 * renders.
 *
 * @package BalefireInc\Sage\FaqItems
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\FaqItems;

class Items {

	/**
	 * Sort modes the block offers. Anything else falls back to menu_order.
	 */
	public const ORDER_BY = [ 'menu_order', 'title', 'date' ];

	/**
	 * Published posts of one type, optionally narrowed to terms, as items.
	 *
	 * @param array $args {
	 *     @type string $postType Post type slug. Default 'faq'.
	 *     @type string $taxonomy Taxonomy slug used with $termIds. Default 'faq_topic'.
	 *     @type int[]  $termIds  Term ids; empty means no filter.
	 *     @type int    $limit    Max items; 0 or less means all.
	 *     @type string $orderBy  One of self::ORDER_BY.
	 * }
	 * @return array<int, array{question: string, answerHtml: string, id: int}>
	 */
	public static function query( array $args ): array {
		$post_type = sanitize_key( (string) ( $args['postType'] ?? 'faq' ) );
		$taxonomy  = sanitize_key( (string) ( $args['taxonomy'] ?? 'faq_topic' ) );
		$term_ids  = array_values( array_filter( array_map( 'absint', (array) ( $args['termIds'] ?? [] ) ) ) );
		$limit     = (int) ( $args['limit'] ?? 8 );
		$order_by  = (string) ( $args['orderBy'] ?? 'menu_order' );

		if ( $post_type === '' || ! post_type_exists( $post_type ) ) {
			return [];
		}

		if ( ! in_array( $order_by, self::ORDER_BY, true ) ) {
			$order_by = 'menu_order';
		}

		$query = [
			'post_type'           => $post_type,
			'post_status'         => 'publish',
			'posts_per_page'      => $limit > 0 ? $limit : -1,
			'ignore_sticky_posts' => true,
			'no_found_rows'       => true,
			'suppress_filters'    => false,
		];

		switch ( $order_by ) {
			case 'title':
				$query['orderby'] = 'title';
				$query['order']   = 'ASC';
				break;
			case 'date':
				$query['orderby'] = 'date';
				$query['order']   = 'DESC';
				break;
			default:
				// Editors order FAQs by hand; ties fall back to title so the
				// list is stable when nothing has been ordered yet.
				$query['orderby'] = [
					'menu_order' => 'ASC',
					'title'      => 'ASC',
				];
				break;
		}

		if ( $term_ids !== [] && $taxonomy !== '' && taxonomy_exists( $taxonomy ) ) {
			$query['tax_query'] = [
				[
					'taxonomy' => $taxonomy,
					'field'    => 'term_id',
					'terms'    => $term_ids,
				],
			];
		}

		$posts = get_posts( $query );

		return array_values( array_map(
			static fn( \WP_Post $post ): array => [
				'id'         => (int) $post->ID,
				'question'   => self::decode( get_the_title( $post ) ),
				'answerHtml' => self::answer( $post ),
			],
			is_array( $posts ) ? $posts : []
		) );
	}

	/**
	 * FAQPage JSON-LD for a list of items, or '' when there is nothing to say.
	 *
	 * Items are `[ 'question' => string, 'answerHtml' => string ]`; the answer
	 * is flattened to plain text as Google's FAQ rich result expects.
	 *
	 * @param array $items Items.
	 * @return string JSON, safe to place inside <script type="application/ld+json">.
	 */
	public static function schema( array $items ): string {
		$entities = [];

		foreach ( $items as $item ) {
			$question = self::plain( (string) ( $item['question'] ?? '' ) );
			$answer   = self::plain( (string) ( $item['answerHtml'] ?? '' ) );

			if ( $question === '' || $answer === '' ) {
				continue;
			}

			$entities[] = [
				'@type'          => 'Question',
				'name'           => $question,
				'acceptedAnswer' => [
					'@type' => 'Answer',
					'text'  => $answer,
				],
			];
		}

		if ( $entities === [] ) {
			return '';
		}

		// JSON_HEX_TAG keeps a literal "</script>" in an answer from ending
		// the script element; wp_json_encode also escapes "/" by default.
		$json = wp_json_encode(
			[
				'@context'   => 'https://schema.org',
				'@type'      => 'FAQPage',
				'mainEntity' => $entities,
			],
			JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP
		);

		return is_string( $json ) ? $json : '';
	}

	/**
	 * A post's content run through the standard content pipeline.
	 *
	 * `the_content` is what WordPress itself would apply on the FAQ post's own
	 * page, so blocks, shortcodes, smart quotes and lazy images behave the
	 * same here. The item view runs wp_kses_post() over the result.
	 *
	 * @param \WP_Post $post Post.
	 * @return string HTML.
	 */
	private static function answer( \WP_Post $post ): string {
		$content = (string) $post->post_content;

		if ( trim( $content ) === '' ) {
			return '';
		}

		return (string) apply_filters( 'the_content', $content );
	}

	/**
	 * Collapse HTML to single-line plain text.
	 *
	 * @param string $html HTML or text.
	 * @return string
	 */
	private static function plain( string $html ): string {
		$text = wp_strip_all_tags( $html, true );
		$text = self::decode( $text );
		$text = (string) preg_replace( '/\s+/u', ' ', $text );

		return trim( $text );
	}

	/**
	 * WordPress stores titles HTML-encoded. Decode once so Blade's {{ }}
	 * escapes exactly once — otherwise the page renders a literal "&amp;".
	 *
	 * @param string $value Raw value.
	 * @return string
	 */
	private static function decode( string $value ): string {
		return html_entity_decode( $value, ENT_QUOTES | ENT_HTML5, 'UTF-8' );
	}
}
