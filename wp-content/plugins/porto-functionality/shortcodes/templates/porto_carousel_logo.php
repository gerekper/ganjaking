<?php

$logo_img = $logo_hover_img = $el_class = '';
extract(
	shortcode_atts(
		array(
			'logo_img'       => '',
			'logo_hover_img' => '',
			'el_class'       => '',
		),
		$atts
	)
);

$sizes = $alt_text = $alt_text_hover = '';

if ( is_numeric( $logo_img ) ) {
	$attachment = wp_get_attachment_image_src( $logo_img, 'full' );
	if ( isset( $attachment ) ) {
		$alt_text = get_post_meta( $logo_img, '_wp_attachment_image_alt', true );
		$logo_img = $attachment[0];
		$sizes   .= ' width="' . esc_attr( $attachment[1] ) . '" height="' . esc_attr( $attachment[2] ) . '"';
	}
}
$hover_sizes = '';
if ( is_numeric( $logo_hover_img ) ) {
	$attachment = wp_get_attachment_image_src( $logo_hover_img, 'full' );
	if ( isset( $attachment ) ) {
		$alt_text_hover = get_post_meta( $logo_hover_img, '_wp_attachment_image_alt', true );
		$logo_hover_img = $attachment[0];
		$hover_sizes   .= ' width="' . esc_attr( $attachment[1] ) . '" height="' . esc_attr( $attachment[2] ) . '"';
	}
}

$html      = '';
$html     .= '<div class="carousel-logo-item background-color-light ' . esc_attr( $el_class ) . '">';
	$html .= '<div class="carousel-logo-pannel carousel-logo-pb center">';
if ( $logo_img ) {
	$html .= '<img src="' . esc_url( $logo_img ) . '" class="img-responsive" alt="' . esc_attr( $alt_text ) . '"' . $sizes . '>';
}
	$html .= '</div>';
	$html .= '<div class="carousel-logo-pannel carousel-logo-hover pt-xlg pl-md pr-md pb-sm ">';
if ( $logo_hover_img ) {
	$html     .= '<div class="carousel-logo-hover-img">';
		$html .= '<img src="' . esc_url( $logo_hover_img ) . '" class="img-responsive" alt="' . esc_attr( $alt_text_hover ) . '"' . $hover_sizes . '>';
	$html     .= '</div>';
}
		$html     .= '<div class="carousel-logo-description font-weight-normal">';
			$html .= do_shortcode( $content );
		$html     .= '</div>';
	$html         .= '</div>';
$html             .= '</div>';

echo porto_filter_output( $html );
