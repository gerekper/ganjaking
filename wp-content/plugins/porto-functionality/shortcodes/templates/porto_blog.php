<?php

global $porto_settings;

$output = $title = $post_layout = $post_style = $columns = $cat = $cats = $post_in = $number = $view_more = $view_more_class = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'post_layout'        => 'timeline',
			'grid_layout'        => '1',
			'grid_height'        => '600',
			'masonry_layout'     => '1',
			'meta_type'          => '',

			'post_style'         => '',
			'columns'            => '3',
			'no_spacing'         => false,
			'cats'               => '',
			'cat'                => '',
			'post_in'            => '',
			'number'             => 8,
			'orderby'            => '',
			'order'              => '',
			'view_more'          => '',
			'view_more_class'    => '',
			'image_size'         => '',
			'excerpt_length'     => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
			'className'          => '',

			'navigation'         => 1,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_nav_hover'     => false,
			'pagination'         => 0,
			'dots_pos'           => '',
			'dots_style'         => '',
			'autoplay'           => '',
			'autoplay_timeout'   => 5000,
			'margin'             => '',
		),
		$atts
	)
);
if ( 'masonry' == $post_layout ) {
	wp_enqueue_script( 'isotope' );
}

if ( ! $excerpt_length && defined( 'PORTO_DEMO' ) && PORTO_DEMO && ( 'grid' == $post_layout || 'masonry' == $post_layout || 'timeline' == $post_layout ) ) {
	$excerpt_length = 20;
}

$args = array(
	'post_type'      => 'post',
	'posts_per_page' => is_array( $number ) && isset( $number['size'] ) ? $number['size'] : $number,
);

if ( ! $cats ) {
	$cats = $cat;
}

if ( $cats ) {
	$cats_arr = explode( ',', $cats );
	if ( isset( $cats_arr[0] ) && is_numeric( trim( $cats_arr[0] ) ) ) {
		$args['cat'] = $cats;
	} else {
		$args['category_name'] = $cats;
	}
}

if ( $post_in ) {
	$args['post__in'] = explode( ',', $post_in );
	$args['orderby']  = 'post__in';
}

if ( 'show' === $view_more ) {
	if ( is_front_page() ) {
		$paged = get_query_var( 'page' );
	} else {
		$paged = get_query_var( 'paged' );
	}
	if ( $paged ) {
		$args['paged'] = $paged;
	}
}

if ( $orderby ) {
	$args['orderby'] = $orderby;
}
if ( $order ) {
	$args['order'] = $order;
}
$posts = new WP_Query( $args );

if ( $posts->have_posts() ) {
	$el_class = porto_shortcode_extract_class( $el_class );

	if ( $className ) {
		if ( $el_class ) {
			$el_class .= ' ' . $className;
		} else {
			$el_class = $className;
		}
	}

	$wrapper_id = 'porto-blog-' . rand( 1000, 9999 );

	$output = '<div id="' . $wrapper_id . '" class="porto-blog wpb_content_element ' . esc_attr( $el_class ) . '"';
	if ( $animation_type ) {
		$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}
	$output .= '>';

	$output .= porto_shortcode_widget_title(
		array(
			'title'      => $title,
			'extraclass' => '',
		)
	);

	global $porto_blog_columns;

	$porto_blog_columns = $columns;

	ob_start();

	$is_creative_layout = false;
	if ( 'timeline' == $post_layout ) {
		global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count, $porto_post_style;

		$prev_post_year      = null;
		$prev_post_month     = null;
		$first_timeline_loop = false;
		$post_count          = 1;
		$porto_post_style    = $post_style;
		if ( 'no_margin' == $porto_post_style ) {
			$porto_post_style = 'hover_info';
		}
		?>

		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?><?php echo ! empty( $post_style ) ? ' blog-posts-' . esc_attr( $post_style ) : '', $no_spacing ? ' blog-posts-no_margin' : ''; ?>">
			<section class="timeline">
				<div class="timeline-body">

		<?php
	} elseif ( 'grid' == $post_layout || 'masonry' == $post_layout || 'creative' == $post_layout || 'masonry-creative' == $post_layout || 'slider' == $post_layout ) {
		global $porto_post_style;
		if ( 'creative' == $post_layout && ! in_array( $post_style, array( 'hover_info', 'hover_info2' ) ) ) {
			$post_style = 'hover_info';
		}

		$porto_post_style = $post_style;
		if ( 'no_margin' == $porto_post_style ) {
			$porto_post_style = 'hover_info';
		}

		$container_class = '';
		$container_attrs = '';
		if ( 'creative' == $post_layout || 'masonry-creative' == $post_layout ) {
			global $porto_post_count, $porto_grid_layout;
			wp_enqueue_script( 'isotope' );

			if ( 'creative' == $post_layout ) {
				$porto_grid_layout = porto_creative_grid_layout( $grid_layout );
				$container_class   = ' grid-creative';
			} else {
				$porto_grid_layout = porto_creative_masonry_layout( $masonry_layout );
			}

			$post_layout        = 'masonry';
			$porto_blog_columns = -1;
			$porto_post_count   = 0;
			$is_creative_layout = true;

			$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
			$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
			porto_creative_grid_style( $porto_grid_layout, $grid_height_number, $wrapper_id, false, true, $unit, 'article.post' );

			$container_attrs .= ' data-plugin-options="' . esc_attr( json_encode( array( 'masonry' => array( 'columnWidth' => '.grid-col-sizer' ) ) ) ) . '"';
		} elseif ( 'slider' == $post_layout ) {
			$container_class .= ' owl-carousel porto-carousel has-ccols';
			if ( $navigation ) {
				if ( $nav_pos ) {
					$container_class .= ' ' . $nav_pos;
				}
				if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
					$container_class .= ' ' . $nav_pos2;
				}
				if ( $nav_type ) {
					$container_class .= ' ' . $nav_type;
				} else {
					$container_class .= ' show-nav-middle';
				}
				if ( $show_nav_hover ) {
					$container_class .= ' show-nav-hover';
				}
			}

			if ( $pagination ) {
				if ( $dots_pos ) {
					$container_class .= ' ' . $dots_pos;
				}
				if ( $dots_style ) {
					$container_class .= ' ' . $dots_style;
				}
			}

			$options           = array( 'themeConfig' => true );
			$options['items']  = (int) $columns;
			$options['lg']     = (int) $columns;
			if ( '0' == $margin || ! empty( $margin ) ) {
				$options['margin'] = (int) $margin;
			} else {
				$options['margin'] = (int) $porto_settings['grid-gutter-width'];
			}
			switch ( $columns ) {
				case '1':
					$no_spacing       = true;
					$container_class .= ' ccols-1';
					break;
				case '2':
					$container_class .= ' ccols-md-2 ccols-1';
					$options['md']    = 2;
					$options['xs']    = 1;
					break;
				case '3':
					$container_class .= ' ccols-xl-3 ccols-md-2 ccols-1';
					$options['md']    = 2;
					$options['xs']    = 1;
					break;
				case '4':
					$container_class .= ' ccols-xl-4 ccols-md-3 ccols-sm-2 ccols-1';
					$options['md']    = 3;
					$options['sm']    = 2;
					$options['xs']    = 1;
					break;
				case '5':
				case '6':
					$container_class .= ' ccols-xl-' . $columns . ' ccols-md-4 ccols-sm-3 ccols-2';
					$options['md']    = 4;
					$options['sm']    = 3;
					$options['xs']    = 2;
					break;
			}

			$options['nav']   = $navigation ? true : false;
			$options['dots']  = $pagination ? true : false;
			$container_attrs .= ' data-plugin-options="' . esc_attr( json_encode( $options ) ) . '"';

			$porto_blog_columns = 1;
		}

		if ( 'slider' != $post_layout ) {
			$container_class .= ' row';
		} else {
			$post_layout = 'grid';
		}
		?>
		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?><?php echo ! empty( $post_style ) ? ' blog-posts-' . esc_attr( $post_style ) : '', $no_spacing ? ' blog-posts-no_margin' : ''; ?>">
			<div class="posts-container<?php echo esc_attr( $container_class ); ?>"<?php echo porto_filter_output( $container_attrs ); ?>>
		<?php
	} else {
		?>

		<div class="blog-posts posts-<?php echo esc_attr( $post_layout ); ?>">

		<?php
	}

	if ( $excerpt_length ) {
		$global_excerpt_length                         = $porto_settings['blog-excerpt-length'];
		$global_post_excerpt_length                    = $porto_settings['post-related-excerpt-length'];
		$porto_settings['blog-excerpt-length']         = $excerpt_length;
		$porto_settings['post-related-excerpt-length'] = $excerpt_length;
	}

	$args = array( 'meta_type' => $meta_type );
	if ( $image_size ) {
		$args['image_size'] = $image_size;
	}
	$template_name = $post_layout;
	if ( 'modern' == $post_style && 'grid' == $post_layout ) {
		$template_name  = $post_style;
		if ( empty( $options ) ) {
			$column_classes = porto_grid_post_column_class( $columns );
		}
	}

	while ( $posts->have_posts() ) {
		$posts->the_post();
		if ( function_exists( 'porto_get_template_part' ) ) {
			if ( isset( $column_classes ) ) {
				echo '<div class="' . esc_attr( $column_classes ) . '">';
			}
			porto_get_template_part( 'content', 'blog-' . $template_name, $args );
			if ( isset( $column_classes ) ) {
				echo '</div>';
			}
		} else {
			get_template_part( 'content', 'blog-' . $template_name );
		}
	}
	if ( $excerpt_length ) {
		$porto_settings['blog-excerpt-length']         = $global_excerpt_length;
		$porto_settings['post-related-excerpt-length'] = $global_post_excerpt_length;
	}
	if ( $is_creative_layout ) {
		echo '<div class="grid-col-sizer"></div>';
	}
	?>

	<?php if ( 'timeline' == $post_layout ) { ?>

				</div>
			</section>
		</div>

	<?php } elseif ( 'grid' == $post_layout || 'masonry' == $post_layout || 'creative' == $post_layout || 'masonry-creative' == $post_layout ) { ?>

			</div>
		</div>

	<?php } else { ?>

		</div>

	<?php } ?>

	<?php if ( 'show' === $view_more ) : ?>
		<?php porto_pagination( $posts->max_num_pages, ( 'load_more' === $view_more ), $posts ); ?>
	<?php elseif ( get_option( 'show_on_front' ) == 'page' && $view_more ) : ?>
		<div class="<?php echo 'timeline' == $post_layout ? 'm-t-n-xxl' : 'push-top'; ?> m-b-xxl text-center">
			<a class="btn btn-primary<?php echo ! empty( $view_more_class ) ? ' ' . str_replace( '.', '', $view_more_class ) : ''; ?>" href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ); ?>"><?php esc_html_e( 'View More', 'porto-functionality' ); ?></a>
		</div>
	<?php endif; ?>

	<?php
		$output .= ob_get_clean();

		$porto_blog_columns = $porto_post_style = '';

		$output .= '</div>';

		echo porto_filter_output( $output );
}

wp_reset_postdata();
