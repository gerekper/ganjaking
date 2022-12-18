<?php
$output = $title = $portfolio_layout = $columns = $view = $info_view = $info_view_style = $thumb_bg = $thumb_image = $image_counter = $cat = $cats = $post_in = $number = $slider = $load_more_posts = $load_more = $view_more = $view_more_class = $filter = $pagination = $animation_type = $animation_duration = $animation_delay = $ajax_load = $ajax_modal = $el_class = '';
global $portfolio_num;

$default_atts = array(
	'title'                => '',
	'portfolio_layout'     => 'timeline',
	'grid_layout'          => '1',
	'grid_height'          => '600',
	'spacing'              => '',
	'masonry_layout'       => '1',

	'content_animation'    => '',
	'columns'              => '3',
	'view'                 => 'classic',
	'info_view'            => '',
	'info_color'           => '',
	'custom_portfolios'    => '',
	'info_color2'          => '',
	'info_view_type_style' => '',
	'thumb_bg'             => '',
	'thumb_image'          => '',
	'image_counter'        => '',
	'cats'                 => '',
	'cat'                  => '',
	'post_in'              => '',
	'orderby'              => '',
	'order'                => '',
	'slider'               => '',
	'number'               => 8,
	'excerpt_length'       => '',
	'view_more'            => false,
	'load_more_posts'      => '',
	'view_more_class'      => '',
	'filter'               => false,
	'pagination'           => false,
	'ajax_load'            => false,
	'ajax_modal'           => false,
	'animation_type'       => '',
	'animation_duration'   => 1000,
	'animation_delay'      => 0,
	'el_class'             => '',
	'show_lightbox_icon'   => '',
	'filter_style'         => '',
	'filter_type'          => '',
	'image_size'           => '',
	'posts_wrap_cls'       => '',
);

extract(
	shortcode_atts(
		$default_atts,
		$atts
	)
);

wp_enqueue_script( 'isotope' );

$args = array(
	'post_type'      => 'portfolio',
	'posts_per_page' => $number,
);

if ( ! $cats ) {
	$cats = $cat;
}

if ( $cats ) {
	$cat               = explode( ',', $cats );
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'portfolio_cat',
			'field'    => is_numeric( $cat[0] ) ? 'term_id' : 'slug',
			'terms'    => $cat,
		),
	);
}

if ( $post_in ) {
	$args['post__in'] = explode( ',', $post_in );
	$args['orderby']  = 'post__in';
}

if ( is_front_page() ) {
	$paged = get_query_var( 'page' );
} else {
	$paged = get_query_var( 'paged' );
}
if ( $load_more_posts && $paged ) {
	$args['paged'] = $paged;
}

if ( 'load-more-btn' == $load_more_posts ) {
	$load_more = true;
}

if ( $orderby ) {
	$args['orderby'] = $orderby;
}
if ( $order ) {
	$args['order'] = $order;
}

$posts = new WP_Query( $args );

$portfolio_taxs = array();

global $porto_settings;

if ( $filter ) {
	$tax_args = array(
		'taxonomy'   => 'portfolio_cat',
		'hide_empty' => true,
		'orderby'    => isset( $porto_settings['portfolio-cat-orderby'] ) ? $porto_settings['portfolio-cat-orderby'] : 'name',
		'order'      => isset( $porto_settings['portfolio-cat-order'] ) ? $porto_settings['portfolio-cat-order'] : 'asc',
	);
	if ( ! empty( $cats ) && is_numeric( $cat[0] ) ) {
		$tax_args['include'] = sanitize_text_field( $cats );
	}
	$taxs = get_terms( $tax_args );

	foreach ( $taxs as $tax ) {
		$portfolio_taxs[ urldecode( $tax->slug ) ] = $tax->name;
	}

	if ( empty( $filter_type ) && 'infinite' != $load_more_posts && 'load-more-btn' != $load_more_posts && is_array( $posts->posts ) && ! empty( $posts->posts ) ) {
		$posts_portfolio_taxs = array();
		foreach ( $posts->posts as $post ) {
			$post_taxs = wp_get_post_terms( $post->ID, 'portfolio_cat', array( 'fields' => 'id=>slug' ) );
			if ( is_array( $post_taxs ) && ! empty( $post_taxs ) ) {
				foreach ( $post_taxs as $post_tax_id => $post_tax_slug ) {
					if ( is_array( $cat ) && ! empty( $cat ) && in_array( $post_tax_id, $cat ) ) {
						$posts_portfolio_taxs[ urldecode( $post_tax_slug ) ] = 1;
					}

					if ( empty( $cat ) || ! isset( $cat ) ) {
						$posts_portfolio_taxs[ urldecode( $post_tax_slug ) ] = 1;
					}
				}
			}
		}

		foreach ( $portfolio_taxs as $key => $value ) {
			if ( ! isset( $posts_portfolio_taxs[ $key ] ) ) {
				unset( $portfolio_taxs[ $key ] );
			}
		}
	}
}

$shortcode_id = porto_generate_rand( 4 );

if ( $posts->have_posts() ) {
	$el_class = porto_shortcode_extract_class( $el_class );

	$wrapper_id = 'porto-portfolio-' . rand( 1000, 9999 );

	$output = '<div id="' . $wrapper_id . '" class="porto-portfolios porto-portfolios' . $shortcode_id . ' wpb_content_element ' . esc_attr( $el_class ) . '"';
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

	if ( 'left-info-no-bg' == $info_view ) {
		$style_printed = false;
		$inline_style  = '';
		if ( $info_color ) {
			$style_printed = true;

			$inline_style .= '<style>';
			$inline_style .= '#' . $wrapper_id . ' .thumb-info .thumb-info-title { color: ' . esc_attr( $info_color ) . ' }';
		}
		if ( $custom_portfolios && $info_color2 ) {
			if ( ! $style_printed ) {
				$style_printed = true;

				$inline_style .= '<style>';
			}
			$custom_portfolios = explode( ',', $custom_portfolios );
			foreach ( $custom_portfolios as $index => $p ) {
				if ( $index ) {
					$inline_style .= ',';
				}
				$inline_style .= '#' . $wrapper_id . ' article.portfolio:nth-child(' . trim( $p ) . ') .thumb-info-title';
			}
			$inline_style .= '{ color: ' . esc_attr( $info_color2 ) . ' }';
		}

		if ( $style_printed ) {
			$inline_style .= '</style>';
		}
		$output .= $inline_style;
	}

	global $porto_portfolio_columns, $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_style, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_slider, $porto_portfolio_image_counter, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal, $porto_portfolio_thumbs_html, $porto_portfolio_show_zoom;

	$porto_portfolio_columns     = $columns;
	$porto_portfolio_view        = $view;
	$porto_portfolio_thumb       = $info_view;
	$porto_portfolio_thumb_style = $info_view_type_style;
	$porto_portfolio_thumb_bg    = $thumb_bg;
	$porto_portfolio_thumb_image = $thumb_image;

	$porto_portfolio_slider = $slider;

	$porto_portfolio_image_counter = $image_counter;
	$portfolio_columns             = $columns;
	$portfolio_view                = $view;
	$porto_portfolio_ajax_load     = $ajax_load ? 'yes' : 'no';
	$porto_portfolio_ajax_modal    = $ajax_modal ? 'yes' : 'no';

	if ( $show_lightbox_icon ) {
		$porto_portfolio_show_zoom = ( 'show' == $show_lightbox_icon );
	}

	ob_start();

	$wrap_cls   = 'page-portfolios clearfix portfolios-' . $portfolio_layout;
	$wrap_attrs = ' id="porto_portfolios_' . porto_generate_rand( 4 ) . '"';
	if ( ! empty( $shortcode_class ) ) {
		$wrap_cls .= ' ' . trim( $shortcode_class );
	}
	if ( ! empty( $title ) ) {
		$wrap_cls .= ' m-t-lg';
	}
	if ( $load_more_posts ) {
		$wrap_cls   .= ' porto-ajax-load';
		$wrap_attrs .= ' data-post_type="portfolio" data-post_layout="' . esc_attr( 'creative' == $portfolio_layout || 'masonry-creative' == $portfolio_layout ? 'masonry' : $portfolio_layout ) . '"';

		if ( 'pagination' == $load_more_posts ) {
			$wrap_cls .= ' load-ajax';
		} elseif ( 'load-more-btn' == $load_more_posts ) {
			$wrap_cls .= ' load-more';
			wp_enqueue_script( 'porto-jquery-infinite-scroll' );
		} else {
			$wrap_cls .= ' load-infinite';
			wp_enqueue_script( 'porto-jquery-infinite-scroll' );
		}
	}
	if ( 'ajax' == $filter_type || $load_more_posts ) {
		// extra options
		$options = array();
		foreach ( $default_atts as $key => $val ) {
			if ( ! empty( $atts[ $key ] ) ) {
				$options[ $key ] = $atts[ $key ];
			}
		}
		$wrap_attrs .= ' data-ajax_load_options="' . esc_attr( json_encode( $options ) ) . '"';

		wp_enqueue_script( 'porto-infinite-scroll' );
	}

	if ( isset( $porto_settings['portfolio-archive-link-zoom'] ) && $porto_settings['portfolio-archive-link-zoom'] ) :
		?>
		<div class="portfolios-lightbox<?php echo empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) ? '' : ' with-thumbs'; ?>"><?php endif; ?>

	<div class="<?php echo esc_attr( $wrap_cls ); ?>"<?php echo porto_filter_output( $wrap_attrs ); ?>>

	<?php if ( $ajax_load && ! $ajax_modal ) : ?>
		<div id="portfolioAjaxBox" class="ajax-box">
			<div class="bounce-loader">
				<div class="bounce1"></div>
				<div class="bounce2"></div>
				<div class="bounce3"></div>
			</div>
			<div class="ajax-box-content" id="portfolioAjaxBoxContent"></div>
		</div>
	<?php endif; ?>

	<?php if ( is_array( $portfolio_taxs ) && ! empty( $portfolio_taxs ) ) : ?>
		<ul class="portfolio-filter nav sort-source<?php echo ! empty( $filter_style ) ? ' sort-source-' . esc_attr( $filter_style ) : ' nav-pills', 'ajax' == $filter_type ? ' porto-ajax-filter' : ''; ?>">
			<li class="active" data-filter="*"><a href="#"><?php esc_html_e( 'Show All', 'porto-functionality' ); ?></a></li>
			<?php foreach ( $portfolio_taxs as $portfolio_tax_slug => $portfolio_tax_name ) : ?>
				<li data-filter="<?php echo esc_attr( $portfolio_tax_slug ); ?>"><a href="#"><?php echo esc_html( $portfolio_tax_name ); ?></a></li>
			<?php endforeach; ?>
		</ul>
		<?php if ( 'style-3' == $filter_style ) { ?>
		<?php } elseif ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout ) { ?>
			<hr>
		<?php } elseif ( 'timeline' == $portfolio_layout ) { ?>
			<hr class="invisible">
		<?php } else { ?>
			<hr class="tall">
		<?php } ?>
	<?php endif; ?>

	<?php
	$is_creative_layout = false;
	$container_attrs    = '';

	// infinite scrolling
	if ( ( 'infinite' == $load_more_posts || 'load-more-btn' == $load_more_posts ) && $posts->max_num_pages ) {
		$container_attrs .= ' data-cur_page="' . ( $paged ? (int) $paged : 1 ) . '" data-max_page="' . intval( $posts->max_num_pages ) . '"';
	}

	if ( 'timeline' == $portfolio_layout ) :
		global $prev_post_year, $prev_post_month, $first_timeline_loop, $post_count;

		$prev_post_year      = null;
		$prev_post_month     = null;
		$first_timeline_loop = false;
		$post_count          = 1;

		if ( ! empty( $_REQUEST['last_date'] ) ) {
			$arr = explode( '-', sanitize_text_field( $_REQUEST['last_date'] ) );
			if ( 2 == count( $arr ) ) {
				$prev_post_year  = $arr[0];
				$prev_post_month = $arr[1];
			}
		}
		?>

		<section class="timeline">

			<div class="timeline-body portfolios-container<?php echo empty( $posts_wrap_cls ) ? '' : ' ' . esc_attr( $posts_wrap_cls ); ?>"<?php echo porto_filter_output( $container_attrs ); ?>>

		<?php
	else :
		$classes = array( 'portfolios-container' );
		if ( ! empty( $posts_wrap_cls ) ) {
			$classes[] = esc_attr( $posts_wrap_cls );
		}

		if ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout || 'creative' == $portfolio_layout || 'masonry-creative' == $portfolio_layout || ( $filter && is_array( $portfolio_taxs ) && ! empty( $portfolio_taxs ) ) ) {
			$classes[] = 'portfolio-row';
		}
		if ( 'grid' == $portfolio_layout || 'masonry' == $portfolio_layout || 'creative' == $portfolio_layout || 'masonry-creative' == $portfolio_layout ) {
			if ( $portfolio_columns ) {
				$classes[] = 'portfolio-row-' . esc_attr( $portfolio_columns );
			}
			$classes[] = esc_attr( $portfolio_view );
		}

		if ( 'creative' == $portfolio_layout || 'masonry-creative' == $portfolio_layout ) {
			global $porto_post_count, $porto_grid_layout;

			if ( 'creative' == $portfolio_layout ) {
				$porto_grid_layout = porto_creative_grid_layout( $grid_layout );
				$classes[]         = 'grid-creative';
			} else {
				$porto_grid_layout = porto_creative_masonry_layout( $masonry_layout );
			}

			$portfolio_layout        = 'masonry';
			$porto_portfolio_columns = -1;
			$porto_post_count        = 0;
			$is_creative_layout      = true;

			if ( $paged && (int) $paged > 1 && ( 'infinite' == $load_more_posts || $load_more ) ) {
				$porto_post_count = (int) $number * ( (int) $paged - 1 ) % count( $porto_grid_layout );
			}

			$grid_height_number = trim( preg_replace( '/[^0-9]/', '', $grid_height ) );
			$unit               = trim( str_replace( $grid_height_number, '', $grid_height ) );
			porto_creative_grid_style( $porto_grid_layout, $grid_height_number, $wrapper_id, $spacing || 0 === $spacing || '0' === $spacing ? $spacing : false, true, $unit, 'article.portfolio' );

			$container_attrs .= ' data-plugin-masonry data-plugin-options="' . esc_attr(
				json_encode(
					array(
						'itemSelector'    => '.portfolio',
						'animationEngine' => 'best-available',
						'masonry'         => array( 'columnWidth' => '.grid-col-sizer' ),
					)
				)
			) . '"';
		}
		?>
		<div class="<?php echo implode( ' ', $classes ); ?>"<?php echo porto_filter_output( $container_attrs ); ?>>

	<?php endif; ?>

	<?php
	$portfolio_num = 0;
	if ( $image_size ) {
		$image_size = array( 'image_size' => $image_size );
	} else {
		$image_size = array();
	}
	if ( $content_animation ) {
		$image_size['content_animation'] = $content_animation;
	}
	if ( $excerpt_length ) {
		if ( isset( $porto_settings['portfolio-excerpt-length'] ) ) {
			$global_excerpt_length = $porto_settings['portfolio-excerpt-length'];
		}
		$porto_settings['portfolio-excerpt-length'] = $excerpt_length;
	}
	while ( $posts->have_posts() ) {
		$posts->the_post();
		++$portfolio_num;
		porto_get_template_part( 'content', 'archive-portfolio-' . $portfolio_layout, $image_size );
	}
	if ( $is_creative_layout ) {
		echo '<div class="grid-col-sizer"></div>';
	}
	if ( isset( $global_excerpt_length ) ) {
		$porto_settings['portfolio-excerpt-length'] = $global_excerpt_length;
	}
	if ( ! empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) && ( 'medium' == $portfolio_layout || 'full' == $portfolio_layout || 'large' == $portfolio_layout ) ) {
		while ( $posts->have_posts() ) {
			global $post;
			$posts->the_post();

			$archive_image = (int) get_post_meta( $post->ID, 'portfolio_archive_image', true );
			if ( $archive_image ) {
				$featured_images   = array();
				$featured_image    = array(
					'thumb'         => wp_get_attachment_thumb_url( $archive_image ),
					'full'          => wp_get_attachment_url( $archive_image ),
					'attachment_id' => $archive_image,
				);
				$featured_images[] = $featured_image;
			} else {
				$featured_images = porto_get_featured_images();
			}
			foreach ( $featured_images as $featured_image ) {
				$attachment_id = $featured_image['attachment_id'];
				if ( $attachment_id ) {
					$attachment_thumb             = porto_get_attachment( $attachment_id, 'thumbnail' );
					$porto_portfolio_thumbs_html .= '<span><img src="' . esc_url( $attachment_thumb['src'] ) . '" alt="' . esc_attr( $attachment_thumb['alt'] ) . '" ></span>';
				}
				break;
			}
		}
	}
	?>

	<?php
	if ( ! empty( $porto_settings['portfolio-archive-img-lightbox-thumb'] ) ) :
		$thumbs_carousel_options = array(
			'items'  => 15,
			'loop'   => false,
			'dots'   => false,
			'nav'    => false,
			'margin' => 8,
		);
		?>
		<div class="porto-portfolios-lighbox-thumbnails">
			<div class="owl-carousel owl-theme nav-center" data-plugin-options='<?php echo json_encode( $thumbs_carousel_options ); ?>'>
				<?php echo porto_filter_output( $porto_portfolio_thumbs_html ); ?>
			</div>
		</div>
	<?php endif; ?>

	<?php if ( 'timeline' == $portfolio_layout ) : ?>

			</div>

		</section>

	<?php else : ?>

		</div>

	<?php endif; ?>

	<?php if ( $load_more_posts && function_exists( 'porto_pagination' ) ) : ?>
		<input type="hidden" class="shortcode-id" value="<?php echo esc_attr( $shortcode_id ); ?>"/>
		<?php porto_pagination( $posts->max_num_pages, $load_more ); ?>
	<?php endif; ?>

	</div>

	<?php if ( $view_more ) : ?>

		<div class="<?php echo 'timeline' == $portfolio_layout ? 'm-t-n-xxl' : 'push-top'; ?> m-b-xxl text-center">
			<a class="btn<?php echo ! empty( $view_more_class ) ? ' ' . esc_attr( str_replace( '.', '', $view_more_class ) ) : ' btn-primary'; ?>" href="<?php echo get_post_type_archive_link( 'portfolio' ); ?>"><?php esc_html_e( 'View More', 'porto-functionality' ); ?></a>
		</div>
	<?php endif; ?>

	<?php
	if ( isset( $porto_settings['portfolio-archive-link-zoom'] ) && $porto_settings['portfolio-archive-link-zoom'] ) :
		?>
		</div><?php endif; ?>

	<?php
	$output .= ob_get_clean();

	$porto_portfolio_columns = $porto_portfolio_view = $porto_portfolio_thumb = $porto_portfolio_thumb_style = $porto_portfolio_thumb_bg = $porto_portfolio_thumb_image = $porto_portfolio_slider = $porto_portfolio_ajax_load = $porto_portfolio_ajax_modal = $porto_portfolio_thumbs_html = '';

	$output .= '</div>';

	echo porto_filter_output( $output );
}

wp_reset_postdata();
