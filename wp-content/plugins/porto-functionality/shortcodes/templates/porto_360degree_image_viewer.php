<?php
$output = '';

extract(
	shortcode_atts(
		array(
			'img_source'         => '',
			'img_preview'        => '',
			'frame_count'        => 16,
			'friction'           => 0.33,
			'animation_type'     => '',
			'animation_duration' => '',
			'animation_delay'    => '',
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( '360-degrees-product-viewer' );

if ( $img_source ) {
	$attachment = wp_get_attachment_image_src( (int) $img_source, 'full' );
	if ( isset( $attachment ) ) {
		$img_source = $attachment[0];
	}
}
if ( $img_source && $img_preview ) {
	$animation_attrs = '';
	if ( $animation_type ) {
		$animation_attrs .= ' data-appear-animation="' . esc_attr( $animation_type ) . '"';
		if ( $animation_delay ) {
			$animation_attrs .= ' data-appear-animation-delay="' . esc_attr( $animation_delay ) . '"';
		}
		if ( $animation_duration && 1000 != $animation_duration ) {
			$animation_attrs .= ' data-appear-animation-duration="' . esc_attr( $animation_duration ) . '"';
		}
	}
	$output             .= '<div class="cd-product-viewer-wrapper' . ( $el_class ? ' ' . esc_attr( $el_class ) : '' ) . '" data-frame="' . ( (int) $frame_count ) . '" data-friction="' . ( (float) $friction ) . '">';
		$output         .= '<div>';
			$output     .= '<figure class="product-viewer"' . $animation_attrs . '>';
				$output .= wp_get_attachment_image( (int) $img_preview, 'full' );
				$output .= '<div class="product-sprite" data-image="' . esc_url( $img_source ) . '" style="background-image: url(' . esc_url( $img_source ) . ');"></div>';
			$output     .= '</figure>';

			$output         .= '<div class="cd-product-viewer-handle">';
				$output     .= '<span class="fill"></span>';
				$output     .= '<span class="handle">';
					$output .= esc_html__( 'Handle', 'porto-functionality' );
					$output .= '<span class="bars"></span>';
				$output     .= '</span>';
			$output         .= '</div>';
		$output             .= '</div>';
	$output                 .= '</div>';
}

echo porto_filter_output( $output );
