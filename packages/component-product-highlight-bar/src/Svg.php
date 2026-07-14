<?php
/**
 * SVG sanitizer for editor-pasted markup.
 *
 * Anything an editor pastes into the block's "Custom SVG" field reaches the
 * page as raw markup, so it is an XSS sink: an <svg> can legally carry
 * <script>, <foreignObject>, event handlers (onload=, onclick=…), and
 * javascript: URLs in href/xlink:href. WordPress does not filter block
 * attributes for you, and wp_kses' default allowlists have no SVG vocabulary.
 *
 * The approach here is an allowlist, not a blocklist: parse the markup, drop
 * every element and attribute not explicitly permitted, and re-serialize.
 * Anything unrecognized is discarded rather than passed through — a paste of
 * something exotic may come back stripped, which is the intended trade.
 *
 * @package BalefireInc\Sage\ProductHighlightBar
 */

declare( strict_types=1 );

namespace BalefireInc\Sage\ProductHighlightBar;

use DOMAttr;
use DOMDocument;
use DOMElement;
use DOMNode;

class Svg {

	/**
	 * Elements permitted in pasted markup.
	 *
	 * Deliberately excluded: script, style, foreignObject, image, a, use,
	 * and the animation elements (animate, animateTransform, set, handler) —
	 * each is either a script vector or can reference an external document.
	 */
	private const ALLOWED_ELEMENTS = [
		'svg', 'g', 'path', 'circle', 'ellipse', 'line', 'polyline', 'polygon',
		'rect', 'defs', 'clippath', 'lineargradient', 'radialgradient', 'stop',
		'title', 'desc',
	];

	/**
	 * Attributes permitted on any allowed element.
	 *
	 * No href/xlink:href: they are the javascript:-URL vector and the only
	 * reason an icon would ever reach out of the document.
	 */
	private const ALLOWED_ATTRIBUTES = [
		'viewbox', 'xmlns', 'width', 'height', 'fill', 'stroke', 'stroke-width',
		'stroke-linecap', 'stroke-linejoin', 'stroke-miterlimit', 'stroke-dasharray',
		'stroke-dashoffset', 'stroke-opacity', 'fill-opacity', 'fill-rule',
		'clip-rule', 'clip-path', 'opacity', 'transform', 'd', 'points',
		'x', 'y', 'x1', 'y1', 'x2', 'y2', 'cx', 'cy', 'r', 'rx', 'ry',
		'offset', 'stop-color', 'stop-opacity', 'gradientunits', 'gradienttransform',
		'preserveaspectratio', 'id',
	];

	/**
	 * Sanitize pasted SVG markup.
	 *
	 * @param string $markup Raw markup from the editor.
	 * @return string Safe markup, or '' if nothing usable survived.
	 */
	public static function sanitize( string $markup ): string {
		$markup = trim( $markup );

		if ( $markup === '' ) {
			return '';
		}

		// Cheap structural gate before handing anything to the parser.
		if ( stripos( $markup, '<svg' ) === false ) {
			return '';
		}

		$doc = new DOMDocument();

		// Entities are how you smuggle a payload past a naive parser; refuse
		// any document that declares one, and never resolve external ones.
		$previous = libxml_use_internal_errors( true );
		$loaded   = $doc->loadXML( $markup, LIBXML_NONET | LIBXML_NOENT );
		libxml_clear_errors();
		libxml_use_internal_errors( $previous );

		if ( ! $loaded || ! $doc->documentElement ) {
			return '';
		}

		if ( $doc->doctype !== null ) {
			return '';
		}

		$root = $doc->documentElement;

		if ( strtolower( $root->nodeName ) !== 'svg' ) {
			return '';
		}

		self::scrub( $root );

		$clean = $doc->saveXML( $root );

		return is_string( $clean ) ? $clean : '';
	}

	/**
	 * Recursively drop disallowed elements and attributes.
	 *
	 * Children are walked over a static snapshot: removing a node from a live
	 * DOMNodeList while iterating it skips siblings.
	 *
	 * @param DOMElement $element Element to scrub, in place.
	 */
	private static function scrub( DOMElement $element ): void {
		$children = [];

		foreach ( $element->childNodes as $child ) {
			$children[] = $child;
		}

		foreach ( $children as $child ) {
			if ( $child instanceof DOMElement ) {
				if ( ! in_array( strtolower( $child->nodeName ), self::ALLOWED_ELEMENTS, true ) ) {
					$element->removeChild( $child );
					continue;
				}

				self::scrub( $child );
				continue;
			}

			// Text nodes are harmless inside <title>/<desc>; everything else
			// (comments, CDATA, processing instructions) goes.
			if ( ! ( $child instanceof \DOMText ) ) {
				$element->removeChild( $child );
			}
		}

		$attributes = [];

		foreach ( $element->attributes as $attribute ) {
			$attributes[] = $attribute;
		}

		foreach ( $attributes as $attribute ) {
			if ( ! $attribute instanceof DOMAttr ) {
				continue;
			}

			$name = strtolower( $attribute->nodeName );

			// on* handlers, href/xlink:href, and anything not on the list.
			if ( ! in_array( $name, self::ALLOWED_ATTRIBUTES, true ) ) {
				$element->removeAttribute( $attribute->nodeName );
				continue;
			}

			// A permitted attribute can still carry a script payload —
			// url(javascript:…) in clip-path, for one.
			if ( preg_match( '/(javascript|data|vbscript)\s*:/i', $attribute->nodeValue ?? '' ) ) {
				$element->removeAttribute( $attribute->nodeName );
			}
		}
	}
}
