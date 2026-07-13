<?php
/**
 * BlockAttributes — bridges get_block_wrapper_attributes() into Blade.
 *
 * WordPress hands block render callbacks their wrapper attributes (anchor
 * id, custom className, spacing style, alignment classes) as an escaped
 * HTML-attribute string. Blade components want an attribute bag. This
 * helper parses the string and returns an Illuminate ComponentAttributeBag
 * that a view's `{{ $attributes->class([...]) }}` merges natively, so the
 * supports declared in block.json actually reach the markup.
 *
 * @package BalefireInc\Sage\Support
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\Support;

class BlockAttributes {

	/**
	 * Parse a wrapper-attribute string into a ComponentAttributeBag.
	 *
	 * Returns null when there is nothing to parse or when the Blade
	 * runtime (Illuminate) isn't available — callers skip injection and
	 * the view's @props falls back to an empty bag.
	 *
	 * @param string $attributes_html Output of get_block_wrapper_attributes().
	 * @return \Illuminate\View\ComponentAttributeBag|null
	 */
	public static function bag( string $attributes_html ): ?object {
		if ( trim( $attributes_html ) === '' ) {
			return null;
		}

		if ( ! class_exists( '\Illuminate\View\ComponentAttributeBag' ) ) {
			return null;
		}

		$parsed = self::parse( $attributes_html );

		if ( $parsed === [] ) {
			return null;
		}

		return new \Illuminate\View\ComponentAttributeBag( $parsed );
	}

	/**
	 * Parse an HTML-attribute string into a name => value array.
	 *
	 * @param string $attributes_html e.g. 'class="wp-block-x" id="anchor"'.
	 * @return array<string, string|true>
	 */
	public static function parse( string $attributes_html ): array {
		if ( ! class_exists( '\WP_HTML_Tag_Processor' ) ) {
			return [];
		}

		$processor = new \WP_HTML_Tag_Processor( '<div ' . $attributes_html . '></div>' );

		if ( ! $processor->next_tag() ) {
			return [];
		}

		$attributes = [];

		foreach ( (array) $processor->get_attribute_names_with_prefix( '' ) as $name ) {
			$value = $processor->get_attribute( $name );
			// Boolean attributes come back as true; keep them truthy.
			$attributes[ $name ] = $value === true ? true : (string) $value;
		}

		return $attributes;
	}
}
