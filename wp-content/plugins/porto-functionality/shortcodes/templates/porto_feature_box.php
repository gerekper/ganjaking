<?php
$output = $skin = $show_icon = $icon_type = $icon_porto = $icon_image = $icon = $icon_simpleline = $box_style = $box_dir = $animation_type = $animation_duration = $animation_delay = $el_class = $icon_circle_style = '';
extract(
	shortcode_atts(
		array(
			'skin'               => 'custom',
			'show_icon'          => false,
			'icon_type'          => 'fontawesome',
			'icon'               => '',
			'icon_simpleline'    => '',
			'icon_porto'         => '',
			'icon_image'         => '',
			'icon_size'          => '14',
			'box_style'          => '',
			'box_dir'            => '',
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
	case 'porto':
		$icon_class = $icon_porto;
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
if ( '' == $box_style || 'feature-box-style-3' == $box_style || 'feature-box-style-6' == $box_style ) {
	if ( 'icon-image' != $icon_class && $icon_size > 20 ) {
		$num               = (float) $icon_size * 1.7;
		$icon_circle_style = ' style="width:' . $num . 'px;height:' . $num . 'px;line-height:' . ( $num - 2 ) . 'px;"';
	}
}
$output = '<div class="porto-feature-box wpb_content_element ' . esc_attr( $el_class ) . '"';
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
$output .= '<div class="feature-box' . esc_attr( ( 'custom' != $skin ? ' feature-box-' . $skin : '' ) . ( $box_style ? ' ' . $box_style : '' ) . ( $box_dir ? ' ' . $box_dir : '' ) ) . '">';

if ( $icon_class ) {
	if ( $box_style && 'custom' != $skin ) {
		$icon_class .= ' text-color-' . $skin;
	}
	$output .= '<div class="feature-box-icon' . ( 'custom' != $skin ? ' feature-box-icon-' . esc_attr( $skin ) : '' ) . '"' . $icon_circle_style . '><i class="' . esc_attr( $icon_class ) . '" style="font-size:' . (float) $icon_size . 'px">';
	if ( 'icon-image' == $icon_class && $icon_image ) {
		$icon_image = preg_replace( '/[^\d]/', '', $icon_image );
		$image_url  = wp_get_attachment_url( $icon_image );
		$image_url  = str_replace( array( 'http:', 'https:' ), '', $image_url );
		$alt_text   = get_post_meta( $icon_image, '_wp_attachment_image_alt', true );
		if ( $image_url ) {
			$output .= '<img alt="' . esc_attr( $alt_text ) . '" src="' . esc_url( $image_url ) . '">';
		}
	}
	$output .= '</i></div>';
}
$output .= '<div class="feature-box-info' . ( $icon_class ? '' : ' p-none' ) . '">' . do_shortcode( $content ) . '</div>';
$output .= '</div>';

$output .= '</div>';

echo porto_filter_output( $output );
