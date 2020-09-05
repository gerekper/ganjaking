<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

printf( '<p>%s</p>', str_repeat( '<span class="dashicons dashicons-star-empty"></span>', 5 ) );

printf(
	'<p><a href="https://wordpress.org/support/plugin/snip/reviews/#new-post" target="_blank" class="button button-primary wpb-rs-rating-button">%s</a></p>',
	__( 'Please rate this plugin with 5 stars!', 'rich-snippets-schema' )
);