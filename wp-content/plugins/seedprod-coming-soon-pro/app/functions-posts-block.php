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
			'skin'                    => isset( $_GET['skin'] ) ? sanitize_text_field( wp_unslash( $_GET['skin'] ) ) : '',
			'skinlayout'              => isset( $_GET['skinlayout'] ) ? sanitize_text_field( wp_unslash( $_GET['skinlayout'] ) ) : '',
			'slidetoshow'             => isset( $_GET['slidetoshow'] ) ? sanitize_text_field( wp_unslash( $_GET['slidetoshow'] ) ) : '',
			'autoplay'                => isset( $_GET['autoplay'] ) ? sanitize_text_field( wp_unslash( $_GET['autoplay'] ) ) : '',
			'speed'                   => isset( $_GET['speed'] ) ? sanitize_text_field( wp_unslash( $_GET['speed'] ) ) : '',
			'masonary'                => isset( $_GET['masonary'] ) ? sanitize_text_field( wp_unslash( $_GET['masonary'] ) ) : '',
			'imageposition'           => isset( $_GET['imageposition'] ) ? sanitize_text_field( wp_unslash( $_GET['imageposition'] ) ) : '',
			'badge'                   => isset( $_GET['badge'] ) ? sanitize_text_field( wp_unslash( $_GET['badge'] ) ) : '',
			'badgetaxonomy'           => isset( $_GET['badgetaxonomy'] ) ? sanitize_text_field( wp_unslash( $_GET['badgetaxonomy'] ) ) : '',
			'avatar'                  => isset( $_GET['avatar'] ) ? sanitize_text_field( wp_unslash( $_GET['avatar'] ) ) : '',
		);

		if ( 'default' === $args['query_type'] ) {
			echo do_shortcode( "[defaultposts show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "'  skin='" . $args['skin'] . "'  badge='" . $args['badge'] . "' masonary='" . $args['masonary'] . "' skinlayout='" . $args['skinlayout'] . "' slidetoshow='" . $args['slidetoshow'] . "' autoplay='" . $args['autoplay'] . "' speed='" . $args['speed'] . "'  imageposition='" . $args['imageposition'] . "'  badgetaxonomy='" . $args['badgetaxonomy'] . "'  avatar='" . $args['avatar'] . "' ]" );
		}

		if ( 'custom' === $args['query_type'] ) {
			echo do_shortcode( "[customposts query_by_post_type='" . $args['query_by_post_type'] . "' post_type='" . $args['post_type'] . "' order='" . $args['order'] . "' orderby='" . $args['orderby'] . "' query_by_category='" . $args['query_by_category'] . "' cat='" . $args['cat'] . "' query_by_tags='" . $args['query_by_tags'] . "' tag__in='" . $args['tags'] . "' query_by_authors='" . $args['query_by_authors'] . "' author='" . $args['authors'] . "' show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "' skin='" . $args['skin'] . "'  badge='" . $args['badge'] . "'  masonary='" . $args['masonary'] . "'  skinlayout='" . $args['skinlayout'] . "' slidetoshow='" . $args['slidetoshow'] . "' autoplay='" . $args['autoplay'] . "' speed='" . $args['speed'] . "'  imageposition='" . $args['imageposition'] . "' badgetaxonomy='" . $args['badgetaxonomy'] . "'  avatar='" . $args['avatar'] . "'  ]" );
		}

		if ( 'manual' === $args['query_type'] ) {
			echo do_shortcode( "[manualposts manual_query='" . $args['manual_query'] . "' show_featured_image='" . $args['show_featured_image'] . "' show_title='" . $args['show_title'] . "' title_html_tag='" . $args['title_html_tag'] . "' pagination='" . $args['pagination'] . "' posts_per_page='" . $args['posts_per_page'] . "' show_meta_options='" . $args['show_meta_options'] . "' show_date_modified_meta='" . $args['show_date_modified_meta'] . "' show_author_meta='" . $args['show_author_meta'] . "' show_date_meta='" . $args['show_date_meta'] . "' show_time_meta='" . $args['show_time_meta'] . "' show_comment_count_meta='" . $args['show_comment_count_meta'] . "' meta_separator='" . $args['meta_separator'] . "' show_excerpt='" . $args['show_excerpt'] . "' excerpt_length='" . $args['excerpt_length'] . "' show_read_more='" . $args['show_read_more'] . "' read_more_text='" . $args['read_more_text'] . "' columns='" . $args['columns'] . "'  skin='" . $args['skin'] . "'  badge='" . $args['badge'] . "'  masonary='" . $args['masonary'] . "'  skinlayout='" . $args['skinlayout'] . "' slidetoshow='" . $args['slidetoshow'] . "' autoplay='" . $args['autoplay'] . "' speed='" . $args['speed'] . "' imageposition='" . $args['imageposition'] . "' badgetaxonomy='" . $args['badgetaxonomy'] . "'  avatar='" . $args['avatar'] . "' ]" );
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
			'skin'                    => 'classic',
			'skinlayout'              => 'gridlayout',
			'slidetoshow'             => 2,
			'autoplay'                => 'true',
			'speed'                   => 100,
			'imageposition'           => 'left',
			'badge'                   => 'true',
			'badgetaxonomy'           => '',
			'avatar'                  => 'false',
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

	$skinlayout          = sanitize_text_field( wp_unslash( $shortcode_args['skinlayout'] ) );
	$slidetoshowsettings = sanitize_text_field( wp_unslash( $shortcode_args['slidetoshow'] ) );

	if ( have_posts() ) {
		$posts_render = '';

		$k = 0;
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

			$width_class    = '';
			$index_carousel = $k + 1;
			$extra_styles   = '';

			$start_div = '';
			$end_div   = '';

			if ( 'carousel' === $skinlayout ) {
				$width_class  = 'show-carousel-area';
				$extra_styles = 'opacity: 1;';
				$start_div    = "<div class='seedprod-carousel-post-block'>";
				if ( $index_carousel > $slidetoshowsettings ) {
					$width_class  = 'hidden-carousel-area';
					$extra_styles = 'opacity: 0; position:absolute;';
				}
				$end_div = '</div>';
			}

			if ( 'masonary' === $skinlayout ) {
				$start_div    = "<div class='seedprod-masonary-post-block sp-grid-cols-" . $slidetoshowsettings . "'>";
				$end_div = '</div>';
			}

			$extra_array                   = array();
			$extra_array['width_class']    = $width_class;
			$extra_array['index_carousel'] = $index_carousel;
			$extra_array['extra_styles']   = $extra_styles;
			$extra_array['k']              = $k;

			$shortcode_args['extra'] = $extra_array;

			$k = $k + 1;

			// Render post.
			$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
		}

		$render .= $start_div . $posts_render . $end_div;

		$pagination = '';

		$carousel_nav = '';

		if ( 'carousel' === $shortcode_args['skinlayout'] ) {

			$post_block_count = $wp_query->post_count;
			if ( $post_block_count > 0 ) {

				// echo $post_block_count;
				$numofpages = ceil( $post_block_count / $shortcode_args['slidetoshow'] );
				if ( $numofpages > 1 ) {

					$bgcolormode   = 'sp-bg-black';
					$textcolormode = 'sp-text-black';

					$carousel_nav .= ' 
							<div class="sp-postblock-nav sp-flex sp-justify-center sp-items-center sp-mt-2">

							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 sp-text-base ' . $textcolormode . '">
							<i class="fas fa-angle-left"></i>
							</button>
						';

					for ( $z = 0; $z < $numofpages; $z++ ) {
						$pstyles = 'opacity: 0.25;';
						if ( 0 === $z ) {
							$pstyles = 'opacity: 1;';
						}

						$carousel_nav .= ' 	
								<button data-index="' . $z . '" class="focus:sp-outline-none sp-w-3 sp-h-3 sp-block sp-mx-1 sp-opacity-25 sp-rounded-full sp-opacity-75 ' . $bgcolormode . '" style="' . $pstyles . '"></button>
							';

					}

					$carousel_nav .= ' 
							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 text-base ' . $textcolormode . '"><i class="fas fa-angle-right"></i></button></div>
						';

				}
			}
		}else{

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
		}

		$render .= $pagination;
		$render .= $carousel_nav;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">' . __( 'No posts were found', 'seedprod-pro' ) . '.</div>';
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
			'skin'                    => 'classic',
			'skinlayout'              => 'gridlayout',
			'slidetoshow'             => 2,
			'autoplay'                => 'true',
			'speed'                   => 100,
			'masonary'                => 'true',
			'imageposition'           => 'left',
			'badge'                   => 'true',
			'badgetaxonomy'           => '',
			'avatar'                  => 'false',
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
			}else{
				$paged = (get_query_var('page')) ? get_query_var('page') : 1;
			}
		}

		$args['paged'] = str_replace( '/', '', $paged );
	}

	// Only fetch published posts.
	$args['post_status'] = array( 'publish' );

	$query = new WP_Query( $args );

	$skinlayout          = sanitize_text_field( wp_unslash( $shortcode_args['skinlayout'] ) );
	$slidetoshowsettings = sanitize_text_field( wp_unslash( $shortcode_args['slidetoshow'] ) );

	if ( $query->have_posts() ) {
		$posts_render = '';

		$k = 0;
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

			$width_class    = '';
			$index_carousel = $k + 1;
			$extra_styles   = '';

			$start_div = '';
			$end_div   = '';

			if ( 'carousel' === $skinlayout ) {
				$width_class  = 'show-carousel-area';
				$extra_styles = 'opacity: 1;';
				$start_div    = "<div class='seedprod-carousel-post-block sp-grid-cols-" . $slidetoshowsettings . "'>";
				if ( $index_carousel > $slidetoshowsettings ) {
					$width_class  = 'hidden-carousel-area';
					$extra_styles = 'opacity: 0; position:absolute;';
				}
				$end_div = '</div>';
			}

			if ( 'masonary' === $skinlayout ) {
				$start_div    = "<div class='seedprod-masonary-post-block sp-grid-cols-" . $slidetoshowsettings . "'>";
				$end_div = '</div>';
			}

			$extra_array                   = array();
			$extra_array['width_class']    = $width_class;
			$extra_array['index_carousel'] = $index_carousel;
			$extra_array['extra_styles']   = $extra_styles;
			$extra_array['k']              = $k;

			$shortcode_args['extra'] = $extra_array;

			$k = $k + 1;

			// Render post.
			// if ( current_user_can( 'read_post', $id ) ) {
				$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
			// }
		}

		$render .= $start_div . $posts_render . $end_div;

		$pagination   = '';
		$carousel_nav = '';

		if ( 'carousel' === $shortcode_args['skinlayout'] ) {
			
			$post_block_count = $query->post_count;
			if ( $post_block_count > 0 ) {

				// echo $post_block_count;
				$numofpages = ceil( $post_block_count / $shortcode_args['slidetoshow'] );
				if ( $numofpages > 1 ) {

					$bgcolormode   = 'sp-bg-black';
					$textcolormode = 'sp-text-black';

					$carousel_nav .= ' 
							<div class="sp-postblock-nav sp-flex sp-justify-center sp-items-center sp-mt-2">

							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 sp-text-base ' . $textcolormode . '">
							<i class="fas fa-angle-left"></i>
							</button>
						';

					for ( $z = 0; $z < $numofpages; $z++ ) {
						$pstyles = 'opacity: 0.25;';
						if ( 0 === $z ) {
							$pstyles = 'opacity: 1;';
						}

						$carousel_nav .= ' 	
								<button data-index="' . $z . '" class="focus:sp-outline-none sp-w-3 sp-h-3 sp-block sp-mx-1 sp-opacity-25 sp-rounded-full sp-opacity-75 ' . $bgcolormode . '" style="' . $pstyles . '"></button>
							';

					}

					$carousel_nav .= ' 
							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 text-base ' . $textcolormode . '"><i class="fas fa-angle-right"></i></button></div>
						';

				}
			}
		}else{

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
		}

		$render .= $pagination;
		$render .= $carousel_nav;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">' . __( 'No posts were found', 'seedprod-pro' ) . '.</div>';
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
			'skin'                    => 'classic',
			'skinlayout'              => 'gridlayout',
			'slidetoshow'             => 2,
			'autoplay'                => 'true',
			'speed'                   => 100,
			'masonary'                => 'true',
			'imageposition'           => 'left',
			'badge'                   => 'true',
			'badgetaxonomy'           => '',
			'avatar'                  => 'false',
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

	if ( 'true' === $shortcode_args['`pagination`'] ) {
		// Update pagination current page. get_query_var( 'paged' ) doesn't return correct value. The $query_string does.
		if ( isset( $wp_query->query['paged'] ) ) {
			$paged = $wp_query->query['paged'];
		} else {
			if ( isset( $wp_query->query['page'] ) ) {
				$paged = $wp_query->query['page'];
			}else{
				$paged = (get_query_var('page')) ? get_query_var('page') : 1;
			}
		}

		$args['paged'] = str_replace( '/', '', $paged );
	}

	if ( ! isset( $args['posts_per_page'] ) ) {
		$args['posts_per_page'] = $shortcode_args['posts_per_page'];
	}

	$query = new WP_Query( $args );

	$skinlayout          = sanitize_text_field( wp_unslash( $shortcode_args['skinlayout'] ) );
	$slidetoshowsettings = sanitize_text_field( wp_unslash( $shortcode_args['slidetoshow'] ) );

	if ( $query->have_posts() ) {
		$posts_render = '';

		$k = 0;
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

			$width_class    = '';
			$index_carousel = $k + 1;
			$extra_styles   = '';

			$start_div = '';
			$end_div   = '';

			if ( 'carousel' === $skinlayout ) {
				$width_class  = 'show-carousel-area';
				$extra_styles = 'opacity: 1;';
				$start_div    = "<div class='seedprod-carousel-post-block'>";
				if ( $index_carousel > $slidetoshowsettings ) {
					$width_class  = 'hidden-carousel-area';
					$extra_styles = 'opacity: 0; position:absolute;';
				}
				$end_div = '</div>';
			}

			if ( 'masonary' === $skinlayout ) {
				$start_div    = "<div class='seedprod-masonary-post-block sp-grid-cols-" . $slidetoshowsettings . "'>";
				$end_div = '</div>';
			}

			$extra_array                   = array();
			$extra_array['width_class']    = $width_class;
			$extra_array['index_carousel'] = $index_carousel;
			$extra_array['extra_styles']   = $extra_styles;
			$extra_array['k']              = $k;

			$shortcode_args['extra'] = $extra_array;

			$k = $k + 1;

			// Render post.
			// if ( current_user_can( 'read_post', $id ) ) {
				$posts_render .= render_post( $shortcode_args, $id, $title, $link, $content, $modified_date, $author, $date, $time, $comments_number, $excerpt );
			// }
		}

		$render .= $start_div . $posts_render . $end_div;

		$pagination   = '';
		$carousel_nav = '';

		if ( 'carousel' === $shortcode_args['skinlayout'] ) {

			$post_block_count = $query->post_count;
			if ( $post_block_count > 0 ) {

				// echo $post_block_count;
				$numofpages = ceil( $post_block_count / $shortcode_args['slidetoshow'] );
				if ( $numofpages > 1 ) {

					$bgcolormode   = 'sp-bg-black';
					$textcolormode = 'sp-text-black';

					$carousel_nav .= ' 
							<div class="sp-postblock-nav sp-flex sp-justify-center sp-items-center sp-mt-2">

							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 sp-text-base ' . $textcolormode . '">
							<i class="fas fa-angle-left"></i>
							</button>
						';

					for ( $z = 0; $z < $numofpages; $z++ ) {
						$pstyles = 'opacity: 0.25;';
						if ( 0 === $z ) {
							$pstyles = 'opacity: 1;';
						}

						$carousel_nav .= ' 	
								<button data-index="' . $z . '" class="focus:sp-outline-none sp-w-3 sp-h-3 sp-block sp-mx-1 sp-opacity-25 sp-rounded-full sp-opacity-75 ' . $bgcolormode . '" style="' . $pstyles . '"></button>
							';

					}

					$carousel_nav .= ' 
							<button class="sp-outline-none focus:sp-outline-none sp-px-3 sp-opacity-50 text-base ' . $textcolormode . '"><i class="fas fa-angle-right"></i></button></div>
						';

				}
			}
		}else{

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
		}

		$render .= $pagination;
		$render .= $carousel_nav;

		// Restore original Post Data.
		wp_reset_postdata();
	} else {
		$render .= '<div class="posts-content">' . __( 'No posts were found', 'seedprod-pro' ) . '.</div>';
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

	$postskin = $shortcode_args['skin'];

	ob_start();
	include SEEDPROD_PRO_PLUGIN_PATH . 'resources/post/' . $postskin . '.php';
	return $output_cards = ob_get_clean();

}
