<?php get_header(); ?>
<?php
$builder_id = porto_check_builder_condition( 'archive' );
if ( $builder_id && 'publish' == get_post_status( $builder_id ) ) {
	echo do_shortcode( '[porto_block id="' . esc_attr( $builder_id ) . '"]' );
} else {
	global $porto_settings;
	$post_layout = isset( $porto_settings['post-layout'] ) ? $porto_settings['post-layout'] : 'large';
	if ( is_category() ) {
		global $wp_query;

		$term    = $wp_query->queried_object;
		$term_id = $term->term_id;

		$post_options = get_metadata( $term->taxonomy, $term->term_id, 'post_options', true ) == 'post_options' ? true : false;

		$post_layout = $post_options ? get_metadata( $term->taxonomy, $term->term_id, 'post_layout', true ) : $post_layout;

		if ( 'grid' == $post_layout || 'masonry' == $post_layout ) {
			global $porto_blog_columns;
			$grid_columns = get_metadata( $term->taxonomy, $term->term_id, 'post_grid_columns', true );
			if ( $grid_columns ) {
				$porto_blog_columns = $grid_columns;
			}
		}
	}

	$skeleton_lazyload = apply_filters( 'porto_skeleton_lazyload', ! empty( $porto_settings['show-skeleton-screen'] ) && in_array( 'blog', $porto_settings['show-skeleton-screen'] ) && ! porto_is_ajax(), 'blog' );
	$el_class          = $skeleton_lazyload ? ' skeleton-loading' : '';

	$post_loop_start       = '';
	$post_loop_end         = '';
	$html_after_pagination = '';
	$html_start            = '';

	$wrap_cls   = '';
	$wrap_attrs = '';
	if ( ! empty( $porto_settings['blog-infinite'] ) ) {
		$wrap_cls    = 'porto-ajax-load';
		$wrap_attrs .= ' data-post_type="post" data-post_layout="' . $post_layout . '"';
		if ( 'ajax' == $porto_settings['blog-infinite'] ) {
			$wrap_cls .= ' load-ajax';
		} else {
			$wrap_cls .= ' load-infinite';
		}

		if ( ! wp_script_is( 'porto-infinite-scroll' ) ) {
			wp_enqueue_script( 'porto-infinite-scroll' );
		}
	}
	if ( 'timeline' == $post_layout ) {
		global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

		$prev_post_year      = null;
		$prev_post_month     = null;
		$first_timeline_loop = false;
		$post_count          = 1;

		$post_loop_start .= '<div class="blog-posts posts-' . esc_attr( $post_layout ) . ( empty( $porto_settings['post-style'] ) ? '' : 'blog-posts-' . esc_attr( $porto_settings['post-style'], $wrap_cls ) ) . '"' . $wrap_attrs . '>';
		$post_loop_start .= '<section class="timeline' . ( $skeleton_lazyload ? ' skeleton-loading-wrap' : '' ) . '">';
		$post_loop_start .= '<div class="timeline-body posts-container' . $el_class . '">';
		$post_loop_end   .= '</div></section></div>';

	} elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) {

		$post_loop_start .= '<div class="blog-posts posts-' . esc_attr( $post_layout ) . ( empty( $porto_settings['post-style'] ) ? '' : 'blog-posts-' . esc_attr( $porto_settings['post-style'], $wrap_cls ) ) . '"' . $wrap_attrs . '>';
		$post_loop_start .= '<div class="row posts-container' . $el_class . '">';
		$post_loop_end   .= '</div></div>';
	} else {
		if ( $wrap_cls ) {
			$html_start            .= '<div class="' . $wrap_cls . '"' . $wrap_attrs . '>';
			$html_after_pagination .= '</div>';
		}
		$post_loop_start .= '<div class="blog-posts posts-' . esc_attr( $post_layout ) . $el_class . ' posts-container">';
		$post_loop_end   .= '</div>';
	}
	?>

<div id="content" role="main">
	<?php
	if ( have_posts() ) {
		echo porto_filter_output( $html_start );
		echo porto_filter_output( $post_loop_start );

		if ( is_archive() ) {
			global $page_share;
			$page_for_posts_id = get_option( 'page_for_posts' );
			$page_share        = get_post_meta( $page_for_posts_id, 'page_share', true );

			if ( category_description() ) {
				echo '<div class="page-content">';
					echo category_description();
				echo '</div>';
			}
		}

		if ( $skeleton_lazyload ) {
			$porto_settings['skeleton_lazyload'] = true;
			ob_start();
			$posts_count = 0;
		}
		while ( have_posts() ) {
			the_post();
			get_template_part( 'content', 'blog-' . $post_layout );
			if ( $skeleton_lazyload ) {
				$posts_count++;
			}
		}
		if ( $skeleton_lazyload ) {
			$blog_content = ob_get_clean();
			echo '<script type="text/template">' . json_encode( $blog_content ) . '</script>';
		}
		echo porto_filter_output( $post_loop_end );

		if ( $skeleton_lazyload ) {
			echo porto_filter_output( str_replace( 'skeleton-loading', 'skeleton-body', $post_loop_start ) );
			$post_class = 'post';
			if ( 'timeline' == $post_layout ) {
				$post_class .= ' timeline-box';
			} elseif ( 'grid' == $post_layout || 'masonry' == $post_layout ) {
				$columns = isset( $porto_settings['grid-columns'] ) ? $porto_settings['grid-columns'] : '3';
				global $porto_blog_columns;
				if ( $porto_blog_columns ) {
					$columns = $porto_blog_columns;
				}
				$post_class .= ' ' . porto_grid_post_column_class( $columns );
			}
			for ( $i = 0; $i < $posts_count; $i++ ) {
				echo '<article class="' . esc_attr( $post_class ) . '"></article>';
			}
			echo porto_filter_output( $post_loop_end );
		}

		porto_pagination();
		wp_reset_postdata();

		echo porto_filter_output( $html_after_pagination );
	} else {
		echo '<h2 class="entry-title">' . esc_html__( 'Nothing Found', 'porto' ) . '</h2>';
		if ( is_home() && current_user_can( 'publish_posts' ) ) {
			echo '<p>';
			/* translators: $1: opening A tag which has url to the admin post new url $2: closing A tag */
			printf( esc_html__( 'Ready to publish your first post? %1$sGet started here%2$s.', 'porto' ), '<a href="' . esc_url( admin_url( 'post-new.php' ) ) . '">', '</a>' );
			echo '</p>';
		} elseif ( is_search() ) {
			echo '<p>' . esc_html__( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'porto' ) . '</p>';
			get_search_form();
		} else {
			echo '<p>' . esc_html__( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'porto' ) . '</p>';
			get_search_form();
		}
	}
	?>
</div>
<?php } ?>
<?php get_footer(); ?>
