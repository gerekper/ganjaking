<?php

global $porto_settings;

$output = $title = $view = $info_view = $thumb_bg = $thumb_image = $number = $cat = $cats = $items_desktop = $items_tablets = $items_mobile = $items_row = $slider_config = $show_nav = $show_nav_hover = $nav_pos = $nav_type = $show_dots = $ajax_load = $ajax_modal = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'view'               => 'classic',
			'info_view'          => '',
			'image_size'         => '',
			'thumb_bg'           => '',
			'thumb_image'        => '',
			'number'             => 8,
			'post_in'            => '',
			'cats'               => '',
			'cat'                => '',
			'items'              => '',
			'items_desktop'      => 4,
			'items_tablets'      => 3,
			'items_mobile'       => 2,
			'items_row'          => 1,
			'slider_config'      => false,
			'show_nav'           => false,
			'show_nav_hover'     => false,
			'nav_pos'            => '',
			'nav_pos2'           => '',
			'nav_type'           => '',
			'show_dots'          => false,
			'dots_style'         => '',
			'autoplay'           => false,
			'autoplay_timeout'   => 5000,
			'ajax_load'          => false,
			'ajax_modal'         => false,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'isotope' );

$carousel_class         = '';
$options                = array();
$options['themeConfig'] = true;
if ( $slider_config ) {
	if ( $show_nav ) {
		if ( $nav_pos ) {
			$carousel_class .= ' ' . $nav_pos;
		}
		if ( ( empty( $nav_pos ) || 'nav-center-images-only' == $nav_pos ) && $nav_pos2 ) {
			$carousel_class .= ' ' . $nav_pos2;
		}
		if ( $nav_type ) {
			$carousel_class .= ' ' . $nav_type;
		}
		if ( $show_nav_hover ) {
			$carousel_class .= ' show-nav-hover';
		}
		if ( 'nav-style-1' == $nav_type ) {
			$carousel_class         .= ' stage-margin';
			$options['stagePadding'] = 25;
		}
	}
	if ( $show_dots && $dots_style ) {
		$carousel_class .= ' ' . $dots_style;
	}
	if ( $autoplay ) {
		$options['autoplay'] = ( 'yes' == $autoplay ? true : false );
		if ( 5000 !== (int) $autoplay_timeout ) {
			$options['autoplayTimeout'] = (int) $autoplay_timeout;
		}
	}
	$options['nav']  = $show_nav;
	$options['dots'] = $show_dots;
}
if ( ! empty( $items ) ) {
	$options['items'] = (int) $items;
}
$options['lg'] = (int) $items_desktop;
$options['md'] = (int) $items_tablets;
$options['sm'] = (int) $items_mobile;

$carousel_class .= ' has-ccols ccols-1';
if ( ! empty( $items ) ) {
	$carousel_class .= ' ccols-xl-' . (int) $items;
}
if ( $items_desktop ) {
	$carousel_class .= ' ccols-lg-' . (int) $items_desktop;
}
if ( $items_tablets ) {
	$carousel_class .= ' ccols-md-' . (int) $items_tablets;
}
if ( $items_mobile ) {
	$carousel_class .= ' ccols-sm-' . (int) $items_mobile;
}

if ( $ajax_load ) {
	$options['loop'] = false;
}

$options = json_encode( $options );

$items_row = (int) $items_row;
if ( $items_row < 1 ) {
	$items_row = 1;
}

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
			'field'    => 'term_id',
			'terms'    => $cat,
		),
	);
}

if ( $post_in ) {
	$args['post__in'] = explode( ',', $post_in );
	$args['orderby']  = 'post__in';
}

$posts = new WP_Query( $args );

if ( $posts->have_posts() ) {
	$el_class = porto_shortcode_extract_class( $el_class );

	$output = '<div class="porto-recent-portfolios ' . esc_attr( $view ) . ' wpb_content_element ' . esc_attr( $el_class ) . '"';
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

	global $porto_portfolio_view, $porto_portfolio_thumb, $porto_portfolio_thumb_bg, $porto_portfolio_thumb_image, $porto_portfolio_ajax_load, $porto_portfolio_ajax_modal;

	$porto_portfolio_view        = $view;
	$porto_portfolio_thumb       = $info_view;
	$porto_portfolio_thumb_bg    = $thumb_bg;
	$porto_portfolio_thumb_image = $thumb_image;
	$porto_portfolio_ajax_load   = $ajax_load ? 'yes' : 'no';
	$porto_portfolio_ajax_modal  = $ajax_modal ? 'yes' : 'no';

	ob_start();
	?>

	<?php
	if ( $ajax_load ) :
		?>
		<div class="page-portfolios"><?php endif; ?>

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

	<?php if ( 'full' != $view ) : ?>
	<div class="row">
	<?php endif; ?>
		<div class="portfolio-carousel porto-carousel owl-carousel<?php echo esc_attr( $carousel_class ); ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
		<?php
			$i    = 0;
			$args = array();
		if ( $image_size ) {
			$args['image_size'] = $image_size;
		}
		while ( $posts->have_posts() ) {
			$posts->the_post();
			global $previousday;
			unset( $previousday );

			if ( 0 == $i % $items_row ) {
				echo '<div class="portfolio-slide">';
			}

			porto_get_template_part( 'content', 'portfolio-item', $args );

			if ( $i % $items_row == $items_row - 1 ) {
				echo '</div>';
			}
			$i++;
		}
		?>
		</div>
	<?php if ( 'full' != $view ) : ?>
	</div>
	<?php endif; ?>

	<?php
	if ( $ajax_load ) :
		?>
		</div><?php endif; ?>

	<?php
	$output .= ob_get_clean();

	$output .= '</div>';

	$porto_portfolio_view = $porto_portfolio_thumb = $porto_portfolio_thumb_bg = $porto_portfolio_thumb_image = $porto_portfolio_ajax_load = $porto_portfolio_ajax_modal = '';

	echo porto_filter_output( $output );
}

wp_reset_postdata();
