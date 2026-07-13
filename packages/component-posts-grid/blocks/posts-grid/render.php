<?php
/**
 * Block render callback — maps Gutenberg attributes to Blade props.
 *
 * Referenced by block.json: "render": "file:./render.php".
 * WordPress calls it with ($attributes, $content, $block).
 *
 * @package BalefireInc\Sage\PostsGrid
 */

declare( strict_types=1 );

use BalefireInc\Sage\PostsGrid\Renderer;

echo Renderer::render( [
	'intro' => $attributes['intro'] ?? '',
	'postsPerPage' => $attributes['postsPerPage'] ?? 0,
	'columns' => $attributes['columns'] ?? 3,
	'showExcerpt' => $attributes['showExcerpt'] ?? true,
	'showDate' => $attributes['showDate'] ?? true,
	'heading' => $attributes['heading'] ?? '',
	'maxWidth' => $attributes['maxWidth'] ?? 'wide',
	'showAuthor' => $attributes['showAuthor'] ?? true,
	'backgroundTone' => $attributes['backgroundTone'] ?? 'transparent',
], get_block_wrapper_attributes() );
