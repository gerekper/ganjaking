<?php
$output = $title = $icon_image = $icon_simpleline = $icon = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'show_icon'          => false,
			'icon_type'          => 'fontawesome',
			'icon'               => '',
			'icon_image'         => '',
			'icon_simpleline'    => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

switch ( $icon_type ) {
	case 'simpleline':
		$icon_class = $icon_simpleline;
		break;
	case 'image':
		$icon_class = 'icon-image';
		break;
	default:
		$icon_class = $icon;
}

if ( ! $show_icon ) {
	$icon_class = '';
}

$output = '<div class="porto-links-block wpb_content_element ' . esc_attr( $el_class ) . '"';
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

if ( $title ) {
	$output .= '<div class="links-title">';
	if ( $icon_class ) {
		$output .= '<i class="' . esc_attr( $icon_class ) . '">';
		if ( 'icon-image' == $icon_class && $icon_image ) {
			$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
			$image_url  = wp_get_attachment_url( $icon_image );
			$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
			if ( $image_url ) {
				$alt_text = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
				$output  .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
			}
		}
		$output .= '</i>';
	}
	$output .= $title . '</div>';
}

$output .= '<div class="links-content"><ul>' . do_shortcode( $content ) . '</ul></div>';

$output .= '</div>';

echo porto_filter_output( $output );
