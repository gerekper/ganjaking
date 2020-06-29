<?php
$output = $prefix = $text = $suffix = $display = $type = $btn_size = $btn_skin = $btn_context = $lightbox_type = $iframe_url = $ajax_url = $lightbox_animation = $lightbox_size = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'prefix'             => '',
			'text'               => '',
			'suffix'             => '',
			'display'            => '',
			'type'               => '',
			'btn_size'           => '',
			'btn_skin'           => '',
			'btn_context'        => '',
			'lightbox_type'      => '',
			'iframe_url'         => '',
			'ajax_url'           => '',
			'lightbox_animation' => '',
			'lightbox_size'      => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

if ( 'block' == $display ) {
	$el_class .= ' wpb_content_element';
} else {
	$el_class .= ' inline';
}

$output = '<div class="porto-lightbox ' . esc_attr( $el_class ) . '"';
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

$output .= $prefix;

$link             = '';
$class            = '';
$valid_characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
$rand             = '';
$length           = 32;
for ( $n = 1; $n < $length; $n++ ) {
	$which_character = rand( 0, strlen( $valid_characters ) - 1 );
	$rand           .= $valid_characters{$which_character};
}

switch ( $lightbox_type ) {
	case 'iframe':
		$class .= 'porto-popup-iframe';
		$link  .= $iframe_url;
		break;
	case 'ajax':
		$class .= 'porto-popup-ajax';
		$link  .= $ajax_url;
		break;
	default:
		$class .= 'porto-popup-content';
		$link  .= '#' . $rand;
		break;
}

if ( 'btn' == $type ) {
	$class .= ' btn';
	if ( $btn_size ) {
		$class .= ' btn-' . $btn_size;
	}
	if ( 'custom' != $btn_skin ) {
		$class .= ' btn-' . $btn_skin;
	}
	if ( $btn_context ) {
		$class .= ' btn-' . $btn_context;
	}
	if ( 'custom' == $btn_skin && ! $btn_context ) {
		$class .= ' btn-default';
	}
}

$output .= ' <a href="' . ( ! $link ? 'javascript:;' : esc_url( $link ) ) . '" title="' . esc_attr( $text ) . '" class="' . $class . '"';
if ( '' == $lightbox_type && $lightbox_animation ) {
	if ( 'zoom-anim' == $lightbox_animation ) {
		$output .= ' data-animation="my-mfp-zoom-in"';
	}
	if ( 'move-anim' == $lightbox_animation ) {
		$output .= ' data-animation="my-mfp-slide-bottom"';
	}
}
$output .= '>';
$output .= $text;
$output .= '</a> ';

$output .= $suffix;

if ( '' == $lightbox_type ) {
	$output .= '<div id="' . $rand . '" class="dialog' . ( $lightbox_size ? ' dialog-' . $lightbox_size : '' ) . ( $lightbox_animation ? ' ' . $lightbox_animation . '-dialog' : '' ) . ' mfp-hide">';
	$output .= do_shortcode( $content );
	$output .= '</div>';
}

$output .= '</div>';

echo porto_filter_output( $output );
