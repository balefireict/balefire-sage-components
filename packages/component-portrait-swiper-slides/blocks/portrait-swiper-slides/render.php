<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\PortraitSwiperSlides
 */

declare( strict_types=1 );

use BalefireInc\Sage\PortraitSwiperSlides\Renderer;

echo Renderer::render( [
	'slides' => $attributes['slides'] ?? [],
	'slidesPerView' => $attributes['slidesPerView'] ?? 4,
	'spaceBetween' => $attributes['spaceBetween'] ?? 16,
	'showPagination' => $attributes['showPagination'] ?? true,
	'showNavigation' => $attributes['showNavigation'] ?? true,
	'overlayColor' => $attributes['overlayColor'] ?? '',
] );
