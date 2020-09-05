<?php

namespace wpbuddy\rich_snippets;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

if ( ! get_option( 'wpb_rs/rated', false ) ) {
	printf(
		'<p>%s</p>',
		__( 'You have not rated the plugin on CodeCanyon yet. Please support me by rate this plugin with 5 stars if you like it!', 'rich-snippets-schema' )
	);

	printf( '<p>%s</p>', str_repeat( '<span class="dashicons dashicons-star-empty"></span>', 5 ) );

	printf(
		'<p><a href="https://codecanyon.net/downloads#item-3464341" target="_blank" class="button button-primary wpb-rs-rating-button">%s</a></p>',
		__( 'Please help and rate on CodeCanyon!', 'rich-snippets-schema' )
	);

} else {
	$rating = intval( get_option( 'wpb_rs/rating', 1 ) );
	$rating = max( $rating, 1 );
	$rating = min( $rating, 5 );

	$remaining_stars = 5 - $rating;

	printf(
		'<p>%s</p>',
		sprintf(
			__( 'Your rating: %s%s', 'rich-snippets-schema' ),
			str_repeat( '<span class="dashicons dashicons-star-filled"></span>', $rating ),
			str_repeat( '<span class="dashicons dashicons-star-empty"></span>', $remaining_stars )
		)
	);

	if ( $rating < 5 ) {

		printf(
			'<p>%s</p>',
			sprintf(
				_n(
					'What can I do to get the last star? Please write a feature request on this page if you miss something.',
					'What can I do to get the last %d stars? Please write a feature request on this page if you miss something.',
					$remaining_stars,
					'rich-snippets-schema'
				),
				number_format_i18n( $remaining_stars )
			)
		);

		printf(
			'<p><a href="https://codecanyon.net/downloads#item-3464341" target="_blank" class="button button-primary wpb-rs-rating-button">%s</a></p>',
			__( 'Make me happy with 5 stars! 😍', 'rich-snippets-schema' )
		);
	} else {
		printf(
			'<p>%s</p>',
			__( 'Awesome rating! Thank you! You\'re my superhero! 💪', 'rich-snippets-schema' )
		);
	}

}