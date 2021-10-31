<?php

$output = $title = $view = $hover_image_effect = $overview = $socials = $socials_style = $number = $cat = $cats = $items_desktop = $items_tablets = $items_mobile = $items_row = $slider_config = $show_nav = $show_nav_hover = $nav_pos = $nav_type = $show_dots = $ajax_load = $ajax_modal = $animation_type = $animation_duration = $animation_delay = $el_class = '';

extract(
	shortcode_atts(
		array(

			'title'              => '',
			'view'               => 'classic',
			'hover_image_effect' => 'zoom',
			'overview'           => true,
			'socials'            => true,
			'socials_style'      => '',
			'number'             => 8,
			'cats'               => '',
			'cat'                => '',
			'spacing'            => '',
			'items'              => '',
			'items_desktop'      => 4,
			'items_tablets'      => 3,
			'items_mobile'       => 2,
			'items_row'          => 1,
			'stage_padding'      => '',
			'slider_config'      => false,
			'show_nav'           => false,
			'show_nav_hover'     => false,
			'nav_pos'            => '',
			'nav_type'           => '',
			'show_dots'          => false,
			'dots_style'         => '',
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

if ( $stage_padding ) {
	$el_class .= ' stage-margin';
	$options['stagePadding'] = (int) $stage_padding;
}
if ( $slider_config ) {
	if ( $show_nav ) {
		if ( $nav_pos ) {
			$carousel_class .= ' ' . $nav_pos;
		}
		if ( $nav_type ) {
			$carousel_class .= ' ' . $nav_type;
		}
		if ( $show_nav_hover ) {
			$carousel_class .= ' show-nav-hover';
		}
		if ( 'nav-style-1' == $nav_type && ! $stage_padding ) {
			$carousel_class         .= ' stage-margin';
			$options['stagePadding'] = 40;
		}
	}
	$options['nav']  = $show_nav;
	$options['dots'] = $show_dots;
	if ( $show_dots && $dots_style ) {
		$carousel_class .= ' ' . $dots_style;
	}
}

if ( ! empty( $items ) ) {
	$options['items'] = (int) $items;
}
$options['lg']     = (int) $items_desktop;
$options['md']     = (int) $items_tablets;
$options['sm']     = (int) $items_mobile;
$options['xs']     = 1;
$options['margin'] = $spacing || '0' == $spacing ? absint( $spacing ) : 25;

if ( $ajax_load ) {
	$options['loop'] = false;
}

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

$options   = json_encode( $options );
$items_row = (int) $items_row;
if ( $items_row < 1 ) {
	$items_row = 1;
}

$args = array(
	'post_type'      => 'member',
	'posts_per_page' => $number,
);

if ( ! $cats ) {
	$cats = $cat;
}

if ( $cats ) {
	$cat               = explode( ',', $cats );
	$args['tax_query'] = array(
		array(
			'taxonomy' => 'member_cat',
			'field'    => 'term_id',
			'terms'    => $cat,
		),
	);
}

$posts = new WP_Query( $args );

if ( $posts->have_posts() ) {
	$el_class = porto_shortcode_extract_class( $el_class );
	$output   = '<div class="porto-recent-members wpb_content_element ' . esc_attr( $el_class ) . '"';
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

	global $porto_member_view, $porto_member_overview, $porto_member_socials, $porto_member_socials_style, $porto_member_ajax_load, $porto_member_ajax_modal, $porto_custom_zoom;

	$porto_member_view          = $view;
	$porto_custom_zoom          = $hover_image_effect;
	$porto_member_overview      = $overview ? 'yes' : 'no';
	$porto_member_socials       = $socials ? 'yes' : 'no';
	$porto_member_socials_style = $socials_style ? 'yes' : 'no';
	$porto_member_ajax_load     = $ajax_load ? 'yes' : 'no';
	$porto_member_ajax_modal    = $ajax_modal ? 'yes' : 'no';
	ob_start();

	?>

	<?php
	if ( $ajax_load ) :
		?>
		<div class="page-members"><?php endif; ?>

	<?php if ( $ajax_load && ! $ajax_modal ) : ?>
		<div id="memberAjaxBox" class="ajax-box">
			<div class="bounce-loader">
				<div class="bounce1"></div>
				<div class="bounce2"></div>
				<div class="bounce3"></div>
			</div>
			<div class="ajax-box-content" id="memberAjaxBoxContent"></div>

			<?php if ( function_exists( 'porto_title_archive_name' ) && porto_title_archive_name( 'member' ) ) : ?>
				<?php /* translators: %s: Member archive name */ ?>
				<div class="hide ajax-content-append"><h4 class="m-t-sm m-b-lg"><?php echo sprintf( __( 'More %s:', 'porto-functionality' ), porto_title_archive_name( 'member' ) ); ?></h4></div>
			<?php endif; ?>
		</div>
	<?php endif; ?>

	<div class="member-carousel porto-carousel owl-carousel <?php echo empty( $nav_pos ) && ( ( 'classic' != $view && 'onimage' != $view ) || $overview ) ? ' nav-center-images-only' : '', ' ' . esc_attr( $carousel_class ); ?>" data-plugin-options="<?php echo esc_attr( $options ); ?>">
		<?php
		$i = 0;

		while ( $posts->have_posts() ) {
			$posts->the_post();
			global $previousday;
			unset( $previousday );

			if ( 0 == $i % $items_row ) {
				echo '<div class="member-slide">';
			}
			get_template_part( 'content', 'member-item' );
			if ( $i % $items_row == $items_row - 1 ) {
				echo '</div>';
			}
			$i++;
		}
		?>
	</div>

	<?php
	if ( $ajax_load ) :
		?>
		</div><?php endif; ?>

	<?php

	$output           .= str_replace( '<div class="member-slide"></div>', '', ob_get_clean() );
	$porto_member_view = $porto_member_overview = $porto_member_socials = $porto_member_ajax_load = $porto_member_ajax_modal = '';
	$output           .= '</div>';
	echo porto_filter_output( $output );
}
wp_reset_postdata();
