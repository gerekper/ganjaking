<?php
if ( ! empty( $atts['align'] ) ) {
	$atts['el_class'] = empty( $atts['el_class'] ) ? 'align' . $atts['align'] : ' align' . $atts['align'];
}
if ( ! empty( $atts['nav_pos2'] ) && empty( $atts['nav_pos'] ) ) {
	$atts['nav_pos'] = $atts['nav_pos2'];
}

$output = $stage_padding = $margin = $autoplay = $autoplay_timeout = $autoplay_hover_pause = $items = $items_lg = $items_md = $items_sm = $items_xs = $show_nav = $show_nav_hover = $nav_pos = $nav_type = $show_dots = $dots_pos = $dots_align = $animate_in = $animate_out = $loop = $center = $video = $lazyload = $merge = $mergeFit = $mergeFit_lg = $mergeFit_md = $mergeFit_sm = $animation_type = $animation_duration = $animation_delay = $el_class = '';

if ( ! empty( $atts['items_responsive'] ) ) {
	$atts['items_responsive'] = json_decode( str_replace( '``', '"', $atts['items_responsive'] ), true );
	$atts['items']            = empty( $atts['items_responsive']['xl'] ) ? 6 : $atts['items_responsive']['xl'];
	$atts['items_lg']         = empty( $atts['items_responsive']['lg'] ) ? min( 4, $atts['items'] ) : $atts['items_responsive']['lg'];
	$atts['items_md']         = empty( $atts['items_responsive']['md'] ) ? min( 3, $atts['items_lg'] ) : $atts['items_responsive']['md'];
	$atts['items_sm']         = empty( $atts['items_responsive']['sm'] ) ? min( 2, $atts['items_md'] ) : $atts['items_responsive']['sm'];
	$atts['items_xs']         = empty( $atts['items_responsive']['xs'] ) ? min( 1, $atts['items_sm'] ) : $atts['items_responsive']['xs'];
}
extract(
	shortcode_atts(
		array(
			'stage_padding'        => 40,
			'show_items_padding'   => '',
			'margin'               => 10,
			'autoplay'             => false,
			'autoplay_timeout'     => 5000,
			'autoplay_hover_pause' => false,
			'items'                => 6,
			'items_lg'             => 4,
			'items_md'             => 3,
			'items_sm'             => 2,
			'items_xs'             => 1,
			'show_nav'             => false,
			'show_nav_hover'       => false,
			'nav_pos'              => '',
			'nav_type'             => '',
			'show_dots'            => false,
			'dots_pos'             => '',
			'dots_style'           => '',
			'dots_align'           => '',
			'animate_in'           => '',
			'animate_out'          => '',
			'loop'                 => false,
			'center'               => false,
			'video'                => false,
			'lazyload'             => false,
			'fullscreen'           => false,
			'merge'                => false,
			'mergeFit'             => true,
			'mergeFit_lg'          => true,
			'mergeFit_md'          => true,
			'mergeFit_sm'          => true,
			'mergeFit_xs'          => true,
			'animation_type'       => '',
			'animation_duration'   => 1000,
			'animation_delay'      => 0,
			'el_class'             => '',
			'className'            => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( $className ) {
	if ( $el_class ) {
		$el_class .= ' ' . $className;
	} else {
		$el_class = $className;
	}
}

if ( $stage_padding && empty( $show_items_padding ) ) {
	$el_class .= ' stage-margin';
}

if ( $show_nav ) {
	if ( $nav_pos ) {
		$el_class .= ' ' . $nav_pos;
	}
	if ( $nav_type ) {
		$el_class .= ' ' . $nav_type;
	}
	if ( $show_nav_hover ) {
		$el_class .= ' show-nav-hover';
	}
}

if ( $show_dots ) {
	if ( $dots_pos ) {
		$el_class .= ' ' . $dots_pos;
	}
	if ( $dots_style ) {
		$el_class .= ' ' . $dots_style;
	}
	$el_class .= ' ' . $dots_align;
}

$options                       = array();
$options['stagePadding']       = (int) $stage_padding;
$options['margin']             = (int) $margin;
$options['autoplay']           = $autoplay;
$options['autoplayTimeout']    = (int) $autoplay_timeout;
$options['autoplayHoverPause'] = $autoplay_hover_pause;
$options['items']              = (int) $items;
$options['lg']                 = (int) $items_lg;
$options['md']                 = (int) $items_md;
$options['sm']                 = (int) $items_sm;
$options['xs']                 = (int) $items_xs;
$options['nav']                = $show_nav;
$options['dots']               = $show_dots;
$options['animateIn']          = $animate_in;
$options['animateOut']         = $animate_out;
$options['loop']               = $loop;
$options['center']             = $center;
$options['video']              = $video;
$options['lazyLoad']           = $lazyload;
$options['fullscreen']         = $fullscreen;

$GLOBALS['porto_carousel_lazyload'] = true;

$classes = array( 'porto-carousel', 'owl-carousel' );

if ( strpos( $el_class, 'porto-standable-carousel' ) === false ) {
	$classes[] = 'has-ccols';
	if ( (int) $items > 1 ) {
		$classes[] = 'ccols-xl-' . $items;
	}
	if ( (int) $items_lg > 1 ) {
		$classes[] = 'ccols-lg-' . $items_lg;
	}
	if ( (int) $items_md > 1 ) {
		$classes[] = 'ccols-md-' . $items_md;
	}
	if ( (int) $items_sm > 1 ) {
		$classes[] = 'ccols-sm-' . $items_sm;
	}
	if ( (int) $items_xs > 1 ) {
		$classes[] = 'ccols-' . $items_xs;
	} else {
		$classes[] = 'ccols-1';
	}
}

if ( $merge ) {
	$options['merge'] = true;

	if ( $mergeFit ) {
		$options['mergeFit'] = true;
	}

	if ( $mergeFit_lg ) {
		$options['mergeFit_lg'] = true;
	}

	if ( $mergeFit_md ) {
		$options['mergeFit_md'] = true;
	}

	if ( $mergeFit_sm ) {
		$options['mergeFit_sm'] = true;
	}

	if ( $mergeFit_xs ) {
		$options['mergeFit_xs'] = true;
	}
}
$options = json_encode( $options );

$output = '';
if ( $fullscreen ) {
	$output .= '<div class="fullscreen-carousel">';
}
$output .= '<div class="' . esc_attr( implode( ' ', $classes ) ) . ' ' . esc_attr( trim( $el_class ) ) . '"';
if ( $animation_type ) {
	$output .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
}
$output .= ' data-plugin-options="' . esc_attr( $options ) . '"';
$output .= '>';

$output .= do_shortcode( $content );

$output .= '</div>';
if ( $fullscreen ) {
	$output .= '</div>';
}

$GLOBALS['porto_carousel_lazyload'] = false;
unset( $GLOBALS['porto_carousel_lazyload'] );

echo porto_filter_output( $output );
