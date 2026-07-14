<?php
/**
 * balefireict/component-article-cards — bootstrap.
 *
 * Registers the Gutenberg block, the [bma_article_cards] shortcode and the
 * editor script.
 *
 * Auto-loaded by Composer (autoload.files in composer.json).
 *
 * @package BalefireInc\Sage\ArticleCards
 */

declare( strict_types=1 );

// Not in a WordPress context (composer scripts, tooling) — do nothing.
if ( ! defined( 'ABSPATH' ) ) {
	return;
}

/**
 * Register the block and shortcode.
 */
$bma_article_cards_boot = static function (): void {

	// Honor the wp-admin "Balefire Blocks" toggle for this block.
	if ( ! \BalefireInc\Sage\Support\Settings::isBlockEnabled( 'article-cards' ) ) {
		return;
	}

	// --- Editor script -----------------------------------------------------
	// Registered as an inline script on an src-less handle: vendor/ may sit
	// outside the webroot (Bedrock), so no asset URL is ever assumed.
	if ( is_admin() && function_exists( 'wp_register_script' ) ) {
		wp_register_script(
			'balefire-article-cards-editor',
			false,
			[ 'wp-blocks', 'wp-element', 'wp-block-editor', 'wp-components', 'wp-i18n', 'wp-data' ],
			null,
			true
		);

		wp_add_inline_script(
			'balefire-article-cards-editor',
			'window.balefireArticleTerms = ' . wp_json_encode( \BalefireInc\Sage\ArticleCards\Articles::termChoices() ) . ';'
			. 'window.balefireArticlePosts = ' . wp_json_encode( \BalefireInc\Sage\ArticleCards\Articles::postChoices() ) . ';',
			'before'
		);

		$editor_js = file_get_contents( __DIR__ . '/../blocks/article-cards/editor.js' );
		if ( $editor_js !== false ) {
			wp_add_inline_script( 'balefire-article-cards-editor', $editor_js );
		}
	}

	// --- Gutenberg block ---------------------------------------------------
	if ( function_exists( 'register_block_type' ) ) {
		register_block_type( __DIR__ . '/../blocks/article-cards' );
	}

	// --- Shortcode (backward compat / WPBakery) ---------------------------
	if ( ! shortcode_exists( 'bma_article_cards' ) ) {
		add_shortcode( 'bma_article_cards', static function ( array $atts ): string {
			$atts = shortcode_atts(
				[
					'eyebrow'         => 'Get It Right',
					'title'           => '',
					'content'         => '',
					'ctalabel'        => '',
					'ctaurl'          => '',
					'source'          => 'filter',
					'taxonomy'        => 'category',
					'termid'          => 0,
					'postids'         => '',
					'count'           => 4,
					'columns'         => 4,
					'fallbackimageid' => 0,
				],
				$atts,
				'bma_article_cards'
			);

			$post_ids = $atts['postids'] !== ''
				? array_map( 'absint', explode( ',', (string) $atts['postids'] ) )
				: [];

			// Render via the same Blade view the block uses.
			return \BalefireInc\Sage\ArticleCards\Renderer::render( [
				'eyebrow'         => $atts['eyebrow'],
				'title'           => $atts['title'],
				'content'         => $atts['content'],
				'ctaLabel'        => $atts['ctalabel'],
				'ctaUrl'          => $atts['ctaurl'],
				'source'          => $atts['source'],
				'taxonomy'        => $atts['taxonomy'],
				'termId'          => (int) $atts['termid'],
				'postIds'         => $post_ids,
				'count'           => (int) $atts['count'],
				'columns'         => (int) $atts['columns'],
				'fallbackImageId' => (int) $atts['fallbackimageid'],
			] );
		} );
	}
};

if ( function_exists( 'add_action' ) ) {
	if ( did_action( 'init' ) ) {
		$bma_article_cards_boot();
	} else {
		add_action( 'init', $bma_article_cards_boot, 20 );
	}
} else {
	// Autoloaded before WordPress's plugin API exists (Bedrock requires
	// vendor/autoload.php from wp-config.php). Pre-initialized hooks are
	// adopted by WP_Hook::build_preinitialized_hooks() once plugin.php loads.
	$GLOBALS['wp_filter']['init'][20][] = [
		'function'      => $bma_article_cards_boot,
		'accepted_args' => 1,
	];
}

unset( $bma_article_cards_boot );
