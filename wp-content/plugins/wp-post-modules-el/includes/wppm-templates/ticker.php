<?php
/**
 * ticker.php
 * Template part for the WP Post Modules Plugin
 *
 * @since 1.0.0
 * @version 2.4.0
 *
 * All variables coming from parent file wp-post-modules-el.php
 */

	$out = sprintf( '<div class="wppm-ticker-container">%s<div class="wppm-ticker" data-duration="%s"%s>',
		$ticker_label ? sprintf( '<div class="ticker-label">%s</div>',
				esc_attr( $ticker_label )
			) : '',
		(int)$duration,
		is_rtl() ? ' data-direction="right" dir="ltr"' : ''
	);

	while ( $custom_query->have_posts() ) :
		$custom_query->the_post();
		global $multipage;
		$multipage = 0;

		// Set post title
		$title = wppm_el_generate_title( $hsource, $h_cust_field_key, $h_length, $h_meta_box );

		$permalink = get_permalink();

		$post_id= get_the_ID();

		$post_class_obj = get_post_class( $post_id );
		$post_classes = 'wppm-el-post ';

		if ( isset( $post_class_obj ) && is_array( $post_class_obj ) ) {
			foreach( $post_class_obj as $post_class ) {
				$post_classes .= ' ' . $post_class;
			}
		}
		if ( is_sticky( $post_id ) ) {
			$post_classes .= ' sticky';
		}

		$format = apply_filters( 'wppm_ticker_list_output', '<span><a href="%1$s" title="%2$s"%3$s>%2$s</a></span>' );
		$out .= sprintf ( $format,
			$permalink,
			$title,
			$new_tab ? ' target="_blank"' : ''
		);

	endwhile;
	$out .= '</div></div>';
	echo $out;