<?php
/**
 * bullet-list.php
 * Template part for the WP Post Modules Plugin
 *
 * @version 2.4.0
 * All variables coming from parent file wp-post-modules-el.php
 */
	$out = '';
	$protocol = is_ssl() ? 'https' : 'http';
	$id = 'wppm-ajax-posts-' . $GLOBALS['wppm_ajax_container_count'];

	$out = sprintf( '%s%s<ul%s class="wppm bullet-list">',
		$ajaxnav || $ajaxloadmore ? sprintf( '<div id="%s" class="wppm-ajax-posts%s" data-params=\'%s\' data-maxposts="%s">',
		$id,
		$ajaxnav ? ' nav-enabled' : ' loadmore-enabled',
		json_encode( $opts, JSON_FORCE_OBJECT ), $custom_query->found_posts ) : '',
		$enable_schema ? ' itemscope="itemscope" itemtype="' . $protocol . '://schema.org/Blog"' : '',
		$ajaxnav ? ' id="' . $id . '-sub-1"' : ''
	);

	// Main loop
	while ( $custom_query->have_posts() ) :
		$custom_query->the_post();
		global $multipage;
		$multipage = 0;

		// Set post title
		$title = wppm_el_generate_title( $hsource, $h_cust_field_key, $h_length, $h_meta_box );

		// Post classes
		$post_id = get_the_ID();
		$post_class_obj = get_post_class( $post_id );
		$post_classes = 'wppm-el-post ';
		if ( isset( $post_class_obj ) && is_array( $post_class_obj ) ) {
			$post_classes .= implode( ' ', $post_class_obj );
		}
		if ( is_sticky( $post_id ) ) {
			$post_classes .= ' sticky';
		}

		// List type module style
		$format = apply_filters( 'wppm_bullet_list_output', '<li%7$s%8$s  class="%2$s"><%5$s%8$s class="entry-title"><a href="%3$s" title="%9$s"%10$s>%4$s</a></%5$s></li>' );

		$out .= sprintf ( $format,
			get_the_id(),
			$post_classes,
			esc_url( get_permalink() ),
			$title,
			sanitize_text_field( $htag ),
			$enable_schema && $container_type != '' ? ' itemscope itemtype="' . $protocol . '://schema.org/' . esc_attr( $container_type ) . '"' : '',
			$enable_schema && $container_prop != '' ? ' itemprop="' . esc_attr( $container_prop ) . '"' : '',
			$enable_schema && $heading_prop != '' ? ' itemprop="' . esc_attr( $heading_prop ) . '"' : '',
			wp_strip_all_tags( $title ),
			$new_tab ? ' target="_blank"' : '',
		);

	endwhile;

	if ( $ajaxnav ) {
		$out .= sprintf( '</ul><div class="wppm-loading-spinner"></div><div class="wppm-ajax-nav"><a class="prev-link disabled" href="#"><span class="screen-reader-text">%s</span><i class="eicon eicon-chevron-left"></i></a> <a class="next-link" href="#"><span class="screen-reader-text">%s</span><i class="eicon eicon-chevron-right"></i></a>%s</div></div>',
			__( 'Prev', 'wppm-el' ),
			__( 'Next', 'wppm-el' ),
			$nav_status ? '<span class="nav-status" data-format="' . esc_attr( $nav_status_text ) . '"></span>' : ''
		);
	}
	elseif ( $ajaxloadmore ) {
		$out .= sprintf( '</ul><div class="wppm-ajax-loadmore"><div class="wppm-loading-spinner"></div><a class="wppm-more-link" href="#">%s</a></div></div>',
			esc_attr( $loadmore_text )
		);
	}
	else {
		$out .= '</ul>';
	}
	echo $out;