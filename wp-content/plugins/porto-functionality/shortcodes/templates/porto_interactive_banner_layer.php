<?php

extract(
	shortcode_atts(
		array(
			'width'              => '',
			'height'             => '',
			'horizontal'         => 50,
			'vertical'           => 50,
			'layer_link'         => '',
			'css_ibanner_layer'  => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$output        = '';
$inline_styles = '';
$css_ib_styles = '';
if ( defined( 'VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG' ) ) {
	$css_ib_styles = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, vc_shortcode_custom_css_class( $css_ibanner_layer, ' ' ), 'porto_interactive_banner_layer', $atts );
}
$el_class      = porto_shortcode_extract_class( $el_class );

$classes = 'porto-ibanner-layer';
if ( $css_ib_styles ) {
	$classes .= ' ' . trim( $css_ib_styles );
}
if ( $el_class ) {
	$classes .= ' ' . trim( $el_class );
}

if ( is_rtl() ) {
	$left  = 'right';
	$right = 'left';
} else {
	$left  = 'left';
	$right = 'right';
}

if ( $width ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $width ) );
	if ( ! $unit ) {
		$width .= '%';
	}
	$inline_styles .= 'width:' . esc_attr( $width ) . ';';
}
if ( $height ) {
	$unit = trim( preg_replace( '/[0-9.]/', '', $height ) );
	if ( ! $unit ) {
		$height .= '%';
	}
	$inline_styles .= 'height:' . esc_attr( $height ) . ';';
}
if ( 50 === (int) $horizontal ) {
	if ( 50 === (int) $vertical ) {
		$inline_styles .= 'left: 50%;top: 50%;transform: translate(-50%, -50%);';
	} else {
		$inline_styles .= 'left: 50%;transform: translateX(-50%);';
	}
} elseif ( 50 > (int) $horizontal ) {
	$inline_styles .= $left . ':' . $horizontal . '%;';
} else {
	$inline_styles .= $right . ':' . ( 100 - $horizontal ) . '%;';
}
if ( 50 === (int) $vertical ) {
	if ( 50 !== (int) $horizontal ) {
		$inline_styles .= 'top: 50%;transform: translateY(-50%);';
	}
} elseif ( 50 > (int) $vertical ) {
	$inline_styles .= 'top:' . $vertical . '%;';
} else {
	$inline_styles .= 'bottom:' . ( 100 - $vertical ) . '%;';
}

$output = '<div class="' . esc_attr( $classes ) . '"';
if ( $inline_styles ) {
	$output .= ' style="' . esc_attr( $inline_styles ) . '"';
}
$output .= '>';

if ( $animation_type ) {
	$output .= '<div data-appear-animation="' . esc_attr( $animation_type ) . '"';
	if ( $animation_delay ) {
		$output .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$output .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
	}
	$output .= '>';
}

$output .= do_shortcode( $content );

if ( $animation_type ) {
	echo '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
