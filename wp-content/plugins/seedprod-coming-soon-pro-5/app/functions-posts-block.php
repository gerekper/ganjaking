<?php

/**
 * Ajax Endpoints.
 */
if ( defined( 'DOING_AJAX' ) ) {
	add_action( 'wp_ajax_seedprod_pro_get_all_post_types', 'seedprod_pro_get_all_post_types' );
	add_action( 'wp_ajax_seedprod_pro_get_all_post_categories', 'seedprod_pro_get_all_post_categories' );
	add_action( 'wp_ajax_seedprod_pro_get_all_post_tags', 'seedprod_pro_get_all_post_tags' );
	add_action( 'wp_ajax_seedprod_pro_get_all_post_authors', 'seedprod_pro_get_all_post_authors' );
	add_action( 'wp_ajax_seedprod_pro_render_posts_block_preview', 'seedprod_pro_render_posts_block_preview' );
}

/**
 * Get list of all post types.
 *
 * @return JSON object.
 */
function seedprod_pro_get_all_post_types() {
	$post_types = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$args = array(
			'publicly_queryable' => true,
		);

		// Public post types.
		$post_types = array_filter( get_post_types( $args ), 'is_post_type_viewable' );
		// Convert to object.
		$post_types = array_map( 'get_post_type_object', $post_types );
	}

	wp_send_json( $post_types );
}

/**
 * Get list of all post categories.
 *
 * @return JSON object.
 */
function seedprod_pro_get_all_post_categories() {
	$post_categories = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$args            = array(
			'hide_empty' => false,
		);
		$post_categories = get_categories( $args );
	}

	wp_send_json( $post_categories );
}

/**
 * Get list of all post tags.
 *
 * @return JSON object.
 */
function seedprod_pro_get_all_post_tags() {
	$post_tags = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		$args      = array(
			'hide_empty' => false,
		);
		$post_tags = get_tags( $args );
	}

	wp_send_json( $post_tags );
}

/**
 * Get list of all post authors.
 *
 * @return JSON object.
 */
function seedprod_pro_get_all_post_authors() {
	$post_authors = array();

	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Get all available users.
		$args = array();

		$post_authors = get_users( $args );
	}

	wp_send_json( $post_authors );
}

/**
 * Render posts preview.
 *
 * @return JSON object.
 */
function seedprod_pro_render_posts_block_preview() {
	// Check query type, call relevant shortcode & pass relevant data.
	if ( check_ajax_referer( 'seedprod_nonce' ) ) {
		if ( ! current_user_can( apply_filters( 'seedprod_builder_preview_render_capability', 'edit_others_posts' ) ) ) {
			wp_send_json_error();
		}

		// Get all available args.
		$args = array(
			'columns'                 => isset( $_GET['columns'] ) ? sanitize_text_field( wp_unslash( $_GET['columns'] ) ) : '',
			'order'                   => isset( $_GET['order'] ) ? sanitize_text_field( wp_unslash( $_GET['order'] ) ) : '',
			'query_type'              => isset( $_GET['query_type'] ) ? sanitize_text_field( wp_unslash( $_GET['query_type'] ) ) : '',
			'post_type'               => isset( $_GET['post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['post_type'] ) ) : '',
			'orderby'                 => isset( $_GET['orderby'] ) ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) ) : '',
			'cat'                     => isset( $_GET['cat'] ) ? sanitize_text_field( wp_unslash( $_GET['cat'] ) ) : '',
			'tags'                    => isset( $_GET['tags'] ) ? sanitize_text_field( wp_unslash( $_GET['tags'] ) ) : '',
			'authors'                 => isset( $_GET['authors'] ) ? sanitize_text_field( wp_unslash( $_GET['authors'] ) ) : '',
			'manual_query'            => isset( $_GET['manual_query'] ) ? sanitize_text_field( wp_unslash( $_GET['manual_query'] ) ) : '',
			'show_featured_image'     => isset( $_GET['show_featured_image'] ) ? sanitize_text_field( wp_unslash( $_GET['show_featured_image'] ) ) : '',
			'show_title'              => isset( $_GET['show_title'] ) ? sanitize_text_field( wp_unslash( $_GET['show_title'] ) ) : '',
			'title_html_tag'          => isset( $_GET['title_html_tag'] ) ? sanitize_text_field( wp_unslash( $_GET['title_html_tag'] ) ) : '',
			'pagination'              => isset( $_GET['pagination'] ) ? sanitize_text_field( wp_unslash( $_GET['pagination'] ) ) : '',
			'posts_per_page'          => isset( $_GET['posts_per_page'] ) ? sanitize_text_field( wp_unslash( $_GET['posts_per_page'] ) ) : '',
			'show_meta_options'       => isset( $_GET['show_meta_options'] ) ? sanitize_text_field( wp_unslash( $_GET['show_meta_options'] ) ) : '',
			'show_date_modified_meta' => isset( $_GET['show_date_modified_meta'] ) ? sanitize_text_field( wp_unslash( $_GET['show_date_modified_meta'] ) ) : '',
			'show_author_meta'        => isset( $_GET['show_author_meta'] ) ? sanitize_text_field( wp_unslash( $_GET['show_author_meta'] ) ) : '',
			'show_date_meta'          => isset( $_GET['show_date_meta'] ) ? sanitize_text_field( wp_unslash( $_GET['show_date_meta'] ) ) : '',
			'show_time_meta'          => isset( $_GET['show_time_meta'] ) ? sanitize_text_field( wp_unslash( $_GET['show_time_meta'] ) ) : '',
			'show_comment_count_meta' => isset( $_GET['show_comment_count_meta'] ) ? sanitize_text_field( wp_unslash( $_GET['show_comment_count_meta'] ) ) : '',
			'meta_separator'          => isset( $_GET['meta_separator'] ) ? sanitize_text_field( wp_unslash( $_GET['meta_separator'] ) ) : '',
			'show_excerpt'            => isset( $_GET['show_excerpt'] ) ? sanitize_text_field( wp_unslash( $_GET['show_excerpt'] ) ) : '',
			'excerpt_length'          => isset( $_GET['excerpt_length'] ) ? sanitize_text_field( wp_unslash( $_GET['excerpt_length'] ) ) : '',
			'show_read_more'          => isset( $_GET['show_read_more'] ) ? sanitize_text_field( wp_unslash( $_GET['show_read_more'] ) ) : '',
			'read_more_text'          => isset( $_GET['read_more_text'] ) ? sanitize_text_field( wp_unslash( $_GET['read_more_text'] ) ) : '',
			'query_by_post_type'      => isset( $_GET['query_by_post_type'] ) ? sanitize_text_field( wp_unslash( $_GET['query_by_post_type'] ) ) : '',
			'query_by_category'       => isset( $_GET['query_by_category'] ) ? sanitize_text_field( wp_unslash( $_GET['query_by_category'] ) ) : '',
			'query_by_tags'           => isset( $_GET['query_by_tags'] ) ? sanitize_text_field( wp_unslash( $_GET['query_by_tags'] ) ) : '',
			'query_by_authors'        => isset( $_GET['query_by_authors'] ) ? sanitize_text_field( wp_unslash( $_GET['query_by_authors'] ) ) : '',
		);

		if ( 'default' === $args['query_type'] ) {
			echo do_shortcode( "[defaultposts show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "']" );
		}

		if ( 'custom' === $args['query_type'] ) {
			echo do_shortcode( "[customposts query_by_post_type='" . $args['query_by_post_type'] . "' post_type='" . $args['post_type'] . "' order='" . $args['order'] . "' orderby='" . $args['orderby'] . "' query_by_category='" . $args['query_by_category'] . "' cat='" . $args['cat'] . "' query_by_tags='" . $args['query_by_tags'] . "' tag__in='" . $args['tags'] . "' query_by_authors='" . $args['query_by_authors'] . "' author='" . $args['authors'] . "' show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "']" );
		}

		if ( 'manual' === $args['query_type'] ) {
			echo do_shortcode( "[manualposts manual_query='" . $args['manual_query'] . "' show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "']" );
		}

		exit;
	}
}

// Add [defaultposts] shortcode.
add_shortcode( 'defaultposts', 'seedprod_pro_posts_block_default_shortcode' );

/**
 * Render default posts shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return JSON object.
 */
function seedprod_pro_posts_block_default_shortcode( array $atts ) {
	global $query_string, $wp_query;

	// have_posts() wrapper.
	$shortcode_args = shortcode_atts(
		array(
			'show_featured_image'     => 'true',
			'show_title'              => 'true',
			'title_html_tag'          => 'h1',
			'pagination'              => 'false',
			'posts_per_page'          => -1,
			'show_meta_options'       => 'true',
			'show_date_modified_meta' => 'true',
			'show_author_meta'        => 'true',
			'show_date_meta'          => 'true',
			'show_time_meta'          => 'true',
			'show_comment_count_meta' => 'true',
			'meta_separator'          => ',',
			'show_excerpt'            => 'true',
			'excerpt_length'          => 20,
			'show_read_more'          => 'true',
			'read_more_text'          => 'true',
			'columns'                 => 4,
		),
		$atts
	);

	$render = '';

	$post_per_page = get_option( 'posts_per_page' );
	if ( empty( $post_per_page ) ) {
		$post_per_page = 10;
	}

	// Updating current query.
	query_posts( $query_string . '&posts_per_page=' . $post_per_page ); // phpcs:ignore WordPress.WP.DiscouragedFunctions.query_posts_query_posts

	if ( have_posts() ) {
		$posts_render = '';

		while ( have_posts() ) {
			the_post();

			$id              = get_the_ID();
			$title           = get_the_title();
			$link            = get_the_permalink();
			$content         = get_the_content();
			$excerpt         = get_the_excerpt();
			$modified_date   = get_the_modified_date();
			$author          = get_the_author();
			$date            = get_the_date();
			$time            = get_the_time();
			$comments_number = (int) get_comments_number();

			// Render post.
			$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
		}

		$render .= $posts_render;

		$pagination = '';

		if ( 'true' === $shortcode_args['pagination'] ) {
			$pagination .= '<div class="sp-custom-col-span-full sp-posts-block-pagination">';

			$big = 999999999; // need an unlikely integer

			if ( get_option( 'permalink_structure' ) ) {
				$format = 'page/%#%/';
			} else {
				$format = '&paged=%#%';
			}

			$pagination .= paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'total'     => $wp_query->max_num_pages,
					'current'   => max( 1, get_query_var( 'paged' ) ),
					'format'    => $format,
					'type'      => 'plain', // Default
					'end_size'  => 2, // Default
					'mid_size'  => 1, // Default
					'prev_next' => true,
					'add_args'  => false,
				)
			);

			$pagination .= '</div>';
		}

		$render .= $pagination;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">No posts were found.</div>';
	}

	return $render;
}

// Add [customposts] shortcode.
add_shortcode( 'customposts', 'seedprod_pro_posts_block_custom_shortcode' );

/**
 * Render custom posts shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return JSON object.
 */
function seedprod_pro_posts_block_custom_shortcode( array $atts ) {
	// WP Query wrapper.
	global $wp_query;

	$shortcode_args = shortcode_atts(
		array(
			'query_by_post_type'      => 'true',
			'post_type'               => array(),
			'order'                   => '',
			'orderby'                 => array(),
			'query_by_category'       => 'false',
			'cat'                     => array(),
			'query_by_tags'           => 'false',
			'tag__in'                 => array(),
			'query_by_authors'        => 'false',
			'author'                  => '',
			'show_featured_image'     => 'true',
			'show_title'              => 'true',
			'title_html_tag'          => 'h1',
			'pagination'              => 'false',
			'posts_per_page'          => -1,
			'show_meta_options'       => 'true',
			'show_date_modified_meta' => 'true',
			'show_author_meta'        => 'true',
			'show_date_meta'          => 'true',
			'show_time_meta'          => 'true',
			'show_comment_count_meta' => 'true',
			'meta_separator'          => ',',
			'show_excerpt'            => 'true',
			'excerpt_length'          => 20,
			'show_read_more'          => 'true',
			'read_more_text'          => 'true',
			'columns'                 => 4,
		),
		$atts
	);

	$render = '';
	$args   = array();

	if ( 'true' === $shortcode_args['query_by_post_type'] ) {
		$post_types = explode( ',', $shortcode_args['post_type'] );

		// Limit to publicly queryable types only.
		$args['post_type'] = array_filter( $post_types, 'is_post_type_viewable' );

		if ( empty( $args['post_type'] ) ) {
			$args['post_type'] = 'post';
		}
	}

	$args['orderby'] = explode( ',', $shortcode_args['orderby'] );

	if ( 'true' === $shortcode_args['query_by_tags'] ) {
		$tags_array      = 0 < strlen( $shortcode_args['tag__in'] ) ? explode( ',', $shortcode_args['tag__in'] ) : array();
		$args['tag__in'] = 0 < count( $tags_array ) ? $tags_array : array();
	}

	// Update orderby array.
	$orderby_count = count( $args['orderby'] );
	$orderby       = array();

	if ( is_array( $args['orderby'] ) && ( 0 < $orderby_count ) ) {
		for ( $i = 0; $i < $orderby_count; $i++ ) {
			$orderby[ $args['orderby'][ $i ] ] = $shortcode_args['order'];
		}
	}

	$args['orderby'] = $orderby;

	if ( ! empty( $shortcode_args['order'] ) && ( is_string( $shortcode_args['order'] ) || is_array( $shortcode_args['order'] ) ) ) {
		$args['order'] = $shortcode_args['order'];
	} else {
		$args['order'] = 'DESC';
	}

	if ( ! empty( $shortcode_args['posts_per_page'] ) && ( is_string( $shortcode_args['posts_per_page'] ) || is_int( $shortcode_args['posts_per_page'] ) ) ) {
		$args['posts_per_page'] = (int) $shortcode_args['posts_per_page'];
	} else {
		$args['posts_per_page'] = -1;
	}

	if ( 'true' === $shortcode_args['query_by_category'] ) {
		if ( ! empty( $shortcode_args['cat'] ) && ( is_int( $shortcode_args['cat'] ) || is_string( $shortcode_args['cat'] ) ) ) {
			$args['cat'] = $shortcode_args['cat'];
		}
	}

	if ( 'true' === $shortcode_args['query_by_authors'] ) {
		if ( ! empty( $shortcode_args['author'] ) && ( is_int( $shortcode_args['author'] ) || is_string( $shortcode_args['author'] ) ) ) {
			$args['author'] = $shortcode_args['author'];
		}
	}

	// Current pagination page.
	$paged = 1;

	if ( 'true' === $shortcode_args['pagination'] ) {
		// Update pagination current page. get_query_var( 'paged' ) doesn't return correct value. The $query_string does.
		if ( isset( $wp_query->query['paged'] ) ) {
			$paged = $wp_query->query['paged'];
		} else {
			if ( isset( $wp_query->query['page'] ) ) {
				$paged = $wp_query->query['page'];
			}
		}

		$args['paged'] = str_replace( '/', '', $paged );
	}

	// Only fetch published posts.
	$args['post_status'] = array( 'publish' );

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$posts_render = '';

		while ( $query->have_posts() ) {
			$query->the_post();

			$id              = get_the_ID();
			$title           = get_the_title();
			$link            = get_the_permalink();
			$content         = get_the_content();
			$excerpt         = get_the_excerpt();
			$modified_date   = get_the_modified_date();
			$author          = get_the_author();
			$date            = get_the_date();
			$time            = get_the_time();
			$comments_number = (int) get_comments_number();

			// Render post.
			//if ( current_user_can( 'read_post', $id ) ) {
				$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
			//}
		}

		$render .= $posts_render;

		$pagination = '';

		if ( 'true' === $shortcode_args['pagination'] ) {
			$pagination .= '<div class="sp-custom-col-span-full sp-posts-block-pagination">';

			$big = 999999999; // need an unlikely integer

			if ( get_option( 'permalink_structure' ) ) {
				$format = 'page/%#%/';
			} else {
				$format = '&paged=%#%';
			}

			$pagination .= paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'total'     => $query->max_num_pages,
					'current'   => max( 1, $paged ),
					'format'    => $format,
					'type'      => 'plain', // Default
					'end_size'  => 2, // Default
					'mid_size'  => 1, // Default
					'prev_next' => true,
					'add_args'  => false,
				)
			);

			$pagination .= '</div>';
		}

		$render .= $pagination;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">No posts were found.</div>';
	}

	return $render;
}

// Add [manualposts] shortcode.
add_shortcode( 'manualposts', 'seedprod_pro_posts_block_manual_shortcode' );

/**
 * Render manual posts shortcode.
 *
 * @param array $atts Shortcode attributes.
 * @return JSON object.
 */
function seedprod_pro_posts_block_manual_shortcode( array $atts ) {
	// WP Query wrapper.
	global $wp_query;

	$shortcode_args = shortcode_atts(
		array(
			'manual_query'            => '',
			'show_featured_image'     => 'true',
			'show_title'              => 'true',
			'title_html_tag'          => 'h1',
			'pagination'              => 'false',
			'posts_per_page'          => -1,
			'show_meta_options'       => 'true',
			'show_date_modified_meta' => 'true',
			'show_author_meta'        => 'true',
			'show_date_meta'          => 'true',
			'show_time_meta'          => 'true',
			'show_comment_count_meta' => 'true',
			'meta_separator'          => ',',
			'show_excerpt'            => 'true',
			'excerpt_length'          => 20,
			'show_read_more'          => 'true',
			'read_more_text'          => 'true',
			'columns'                 => 4,
		),
		$atts
	);

	$render                         = '';
	$shortcode_args['manual_query'] = json_decode( $shortcode_args['manual_query'] );

	// Split manual query.
	$args = array();

	if ( 0 < strlen( $shortcode_args['manual_query'] ) ) {
		$split_query = array();
		$split_query = explode( '~', $shortcode_args['manual_query'] );

		// Get specific wp_query params.
		$query_params_count = count( $split_query );

		if ( 0 < $query_params_count ) {
			for ( $i = 0; $i < $query_params_count; $i++ ) {
				$split_query_params = explode( '=', $split_query[ $i ] );
				$param_value        = $split_query_params[1];
				$param_key          = $split_query_params[0];

				// Check if array content passed or not.
				if ( ( '(' === $param_value[0] ) && ( ')' === $param_value[ strlen( $param_value ) - 1 ] ) ) {
					// Remove brackets.
					$array_query_string = substr( $param_value, 1, -1 );
					$array_query_string = explode( ',', $array_query_string );
					$args[ $param_key ] = $array_query_string;
				} elseif ( ( '{' === $param_value[0] ) && ( '}' === $param_value[ strlen( $param_value ) - 1 ] ) ) {
					// Check if associative array.
					$assoc_array_string = substr( $param_value, 1, -1 );
					$assoc_array_string = explode( ',', $assoc_array_string );

					// Get variables.
					$assoc_array_count = count( $assoc_array_string );
					$assoc_array       = array();

					if ( 0 < $assoc_array_count ) {
						for ( $j = 0; $j < $assoc_array_count; $j++ ) {
							$final_assoc                    = explode( '$', $assoc_array_string[ $j ] );
							$assoc_array[ $final_assoc[0] ] = $final_assoc[1];
						}
					}

					$args[ $param_key ] = $assoc_array;
				} else {
					$args[ $param_key ] = $param_value;
				}
			}
		}
	}

	// Current pagination page.
	$paged = 1;

	if ( 'true' === $shortcode_args['pagination'] ) {
		// Update pagination current page. get_query_var( 'paged' ) doesn't return correct value. The $query_string does.
		if ( isset( $wp_query->query['paged'] ) ) {
			$paged = $wp_query->query['paged'];
		} else {
			if ( isset( $wp_query->query['page'] ) ) {
				$paged = $wp_query->query['page'];
			}
		}

		$args['paged'] = str_replace( '/', '', $paged );
	}

	if ( ! isset( $args['posts_per_page'] ) ) {
		$args['posts_per_page'] = $shortcode_args['posts_per_page'];
	}

	$query = new WP_Query( $args );

	if ( $query->have_posts() ) {
		$posts_render = '';

		while ( $query->have_posts() ) {
			$query->the_post();

			$id              = get_the_ID();
			$title           = get_the_title();
			$link            = get_the_permalink();
			$content         = get_the_content();
			$excerpt         = get_the_excerpt();
			$modified_date   = get_the_modified_date();
			$author          = get_the_author();
			$date            = get_the_date();
			$time            = get_the_time();
			$comments_number = (int) get_comments_number();

			// Render post.
			//if ( current_user_can( 'read_post', $id ) ) {
				$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
			//}
		}

		$render .= $posts_render;

		$pagination = '';

		if ( 'true' === $shortcode_args['pagination'] ) {
			$pagination .= '<div class="sp-custom-col-span-full sp-posts-block-pagination">';

			$big = 999999999; // need an unlikely integer

			if ( get_option( 'permalink_structure' ) ) {
				$format = 'page/%#%/';
			} else {
				$format = '&paged=%#%';
			}

			$pagination .= paginate_links(
				array(
					'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
					'total'     => $query->max_num_pages,
					'current'   => max( 1, $paged ),
					'format'    => $format,
					'type'      => 'plain', // Default
					'end_size'  => 2, // Default
					'mid_size'  => 1, // Default
					'prev_next' => true,
					'add_args'  => false,
				)
			);

			$pagination .= '</div>';
		}

		$render .= $pagination;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">No posts were found.</div>';
	}

	return $render;
}

/**
 * Render post.
 *
 * @param array   $shortcode_args  Shortcode attributes.
 * @param integer $id              Post Id.
 * @param string  $title           Post title.
 * @param string  $link            Post link.
 * @param string  $content         Post content.
 * @param string  $modified_date   Post date.
 * @param string  $author          Post author.
 * @param string  $date            Post date.
 * @param string  $time            Post time.
 * @param string  $comments_number Post comments count.
 * @param string  $excerpt         Post excerpt.
 * @return string $render.
 */
function render_post( array $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt ) {
	$render = '<div class="sp-posts-single-block">';

	// Show featured image.
	if ( 'true' === $shortcode_args['show_featured_image'] ) {
		$featured_img_url = get_the_post_thumbnail_url( $id, 'full' );

		$render .= '<div class="sp-container">';

		if ( $featured_img_url ) {
			$render .= '<a href="' . esc_attr( $link ) . '" class="sp-posts-image-link sp-inline-block">';
			$render .= get_the_post_thumbnail(
				$id,
				'large',
				array(
					'class'   => 'sp-posts-image',
					'loading' => 'lazy',
					'alt'     => $title,
				)
			);
			$render .= '</a>';
		}

		$render .= '</div>';
	}

	// Show title.
	if ( 'true' === $shortcode_args['show_title'] ) {
		$render .= '<a href="' . esc_attr( $link ) . '">';
		$render .= '<div class="sp-container sp-posts-text sp-py-2">';
		$render .= '<' . esc_html( $shortcode_args['title_html_tag'] ) . ' class="sp-pt-2 sp-posts-block-title">';
		$render .= $title;
		$render .= '</' . esc_html( $shortcode_args['title_html_tag'] ) . '>';
		$render .= '</div>';
		$render .= '</a>';
	}

	// Show meta data.
	if ( 'true' === $shortcode_args['show_meta_options'] ) {
		$render .= '<div class="sp-container sp-posts-text sp-posts-block-meta-text">';

		if ( 'true' === $shortcode_args['show_date_modified_meta'] ) {
			$render .= esc_html( $modified_date );
		}

		if ( 'true' === $shortcode_args['show_author_meta'] ) {
			if ( 'true' === $shortcode_args['show_date_modified_meta'] ) {
				$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
			}
			$render .= esc_html( $author );
		}

		if ( 'true' === $shortcode_args['show_date_meta'] ) {
			if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] ) {
				$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
			}
			$render .= esc_html( $date );
		}

		if ( 'true' === $shortcode_args['show_time_meta'] ) {
			if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] || 'true' === $shortcode_args['show_date_meta'] ) {
				$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
			}
			$render .= esc_html( $time );
		}

		if ( 'true' === $shortcode_args['show_comment_count_meta'] ) {
			if ( 'true' === $shortcode_args['show_author_meta'] || 'true' === $shortcode_args['show_date_modified_meta'] || 'true' === $shortcode_args['show_date_meta'] || 'true' === $shortcode_args['show_time_meta'] ) {
				$render .= ' ' . esc_html( $shortcode_args['meta_separator'] ) . ' ';
			}

			if ( 1 > $comments_number ) {
				$render .= esc_html( 'No comments' );
			} elseif ( 1 === $comments_number ) {
				$render .= esc_html( $comments_number . ' Comment' );
			} elseif ( 2 <= $comments_number ) {
				$render .= esc_html( $comments_number . 'Comments' );
			}
		}

		$render .= '</div>';
	}

	// Show post content.
	if ( 'true' === $shortcode_args['show_excerpt'] ) {
		if ( ! empty( $excerpt ) ) {
			$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-excerpt">';
			$render .= esc_html( wp_trim_words( $excerpt, (int) $shortcode_args['excerpt_length'], null ) );
			$render .= '</div>';
		} else {
			if ( 0 < strlen( $content ) ) {
				$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-excerpt">';
				$render .= esc_html( wp_trim_words( $content, (int) $shortcode_args['excerpt_length'], null ) );
				$render .= '</div>';
			}
		}
	}

	// Show read more button.
	if ( 'true' === $shortcode_args['show_read_more'] ) {
		$render .= '<div class="sp-container sp-posts-text sp-py-2 sp-posts-block-read-more">';
		$render .= '<a href="' . esc_attr( $link ) . '">' . esc_html( $shortcode_args['read_more_text'] ) . '</a>';
		$render .= '</div>';
	}

	$render .= '</div>';

	return $render;
}
