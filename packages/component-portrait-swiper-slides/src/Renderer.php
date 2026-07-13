<?php
/**
 * Renderer — delegates to the Blade view for Portrait Swiper Slides.
 *
 * Bridge between the block entry point and the Blade
 * component. Requires a Sage or Acorn-powered theme; Blade is the only
 * render path, keeping markup in a single source of truth.
 *
 * @package BalefireInc\Sage\PortraitSwiperSlides
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\PortraitSwiperSlides;

class Renderer {

	/**
	 * Render the component from the given props.
	 *
	 * @param array $props Component props (matches Blade @props).
	 * @return string HTML output.
	 */
	public static function render( array $props ): string {
		$props = self::defaults( $props );

		// Sage 10 / Acorn — use the Roots\view() helper.
		if ( function_exists( '\Roots\view' ) ) {
			return \Roots\view( 'bma::components.portrait-swiper-slides', $props )->render();
		}

		// Acorn installed standalone.
		if ( function_exists( '\Acorn\view' ) ) {
			return \Acorn\view( 'bma::components.portrait-swiper-slides', $props )->render();
		}

		// No Blade runtime available — fail loud, not silent.
		return '<!-- balefire/portrait-swiper-slides: Sage/Acorn Blade runtime not found. '
			. 'This component requires a Sage or Acorn-powered theme. -->';
	}

	/**
	 * Merge props with defaults (mirrors the Blade @props defaults).
	 *
	 * @param array $props Raw props.
	 * @return array Resolved props.
	 */
	private static function defaults( array $props ): array {
		return wp_parse_args( $props, [
			'slides' => [],
			'slidesPerView' => 4,
			'spaceBetween' => 16,
			'showPagination' => true,
			'showNavigation' => true,
			'overlayColor' => '',
		] );
	}
}
