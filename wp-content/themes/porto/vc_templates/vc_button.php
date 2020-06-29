<?php

if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Shortcode attributes
 * @var $atts
 * @var $color
 * @var $size
 * @var $icon
 * @var $target
 * @var $href
 * @var $el_class
 * @var $title
 *
 * Extra Params
 * @var $disabled
 * @var $label
 *
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Button
 */

$color  = $size = $icon = $target = $href = $el_class = $title = '';
$output = '';
$atts   = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts );

$a_class = '';

if ( '' !== $el_class ) {
	$tmp_class = explode( ' ', strtolower( $el_class ) );
	$tmp_class = str_replace( '.', '', $tmp_class );
	if ( in_array( 'prettyphoto', $tmp_class, true ) ) {
		wp_enqueue_script( 'prettyphoto' );
		wp_enqueue_style( 'prettyphoto' );
		$a_class .= ' prettyphoto';
		$el_class = str_ireplace( 'prettyphoto', '', $el_class );
	}

	if ( in_array( 'pull-right', $tmp_class, true ) && '' !== $href ) {
		$a_class .= ' pull-right';
		$el_class = str_ireplace( 'pull-right', '', $el_class );
	}

	if ( in_array( 'pull-left', $tmp_class, true ) && '' !== $href ) {
		$a_class .= ' pull-left';
		$el_class = str_ireplace( 'pull-left', '', $el_class );
	}
}

if ( 'same' === $target || '_self' === $target ) {
	$target = '';
}

$target = ( '' !== $target ) ? ' target="' . esc_attr( $target ) . '"' : '';

if ( $label ) {
	switch ( $color ) {
		case 'wpb_button':
			$color = ' label-default';
			break;
		case 'btn-inverse':
			$color = ' label-dark';
			break;
		default:
			$color = str_replace( 'btn-', 'label-', $color );
	}
} else {
	switch ( $color ) {
		case 'wpb_button':
			$color = ' btn-default';
			break;
		default:
			$color = ' ' . $color;
	}
}

switch ( $size ) {
	case 'btn-large':
		$size = ' btn-lg';
		break;
	case 'btn-small':
		$size = ' btn-sm';
		break;
	case 'btn-mini':
		$size = ' btn-xs';
		break;
	default:
		$size = '';
}

$icon     = ( '' !== $icon && 'none' != $icon ) ? ' ' . $icon : '';
$i_icon   = ( '' !== $icon ) ? ' <i class="icon"> </i>' : '';
$el_class = $this->getExtraClass( $el_class );

$css_class = apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, 'btn ' . $color . $size . $icon . $el_class, $this->settings['base'], $atts );

if ( $href ) {
	$output .= '<span class="' . esc_attr( $css_class ) . '">' . wp_kses_post( $title ) . $i_icon . '</span>';
	$output  = '<a class="wpb_button_a' . esc_attr( $a_class ) . ( $disabled ? ' disabled' : '' ) . '" title="' . esc_attr( $title ) . '" href="' . esc_url( $href ) . '"' . $target . '>' . $output . '</a>';
} else {
	if ( $label ) {
		$output .= '<span class="label ' . esc_attr( $color ) . ( $disabled ? '  disabled' : '' ) . '">' . wp_kses_post( $title ) . '</span>';
	} else {
		$output .= '<button class="' . esc_attr( $css_class ) . '"' . ( $disabled ? ' disabled="disabled"' : '' ) . '>' . wp_kses_post( $title ) . $i_icon . '</button>';
	}
}

echo porto_filter_output( $output );
