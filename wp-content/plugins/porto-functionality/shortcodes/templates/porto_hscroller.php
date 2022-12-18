<?php
/**
 * Horizontal Scroller Template
 *
 * @since 2.6.0
 */
extract(
	shortcode_atts(
		array(
			'scroller_count_lg'  => '3',
			'scroller_count_md'  => '1',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

wp_enqueue_script( 'porto-gsap' );
wp_enqueue_script( 'porto-scroll-trigger' );

$output = $option = '';
if ( ! empty( $shortcode_class ) ) {
	$el_class .= $shortcode_class;
}
$hscroll_options = array(
	'lg' => $scroller_count_lg,
	'md' => $scroller_count_md,
);

// Animation
if ( $animation_type ) {
	$option .= ' data-appear-animation=' . esc_attr( $animation_type );
	if ( $animation_delay ) {
		$option .= ' data-appear-animation-delay=' . esc_attr( $animation_delay );
	}
	if ( $animation_duration && 1000 != $animation_duration ) {
		$option .= ' data-appear-animation-duration=' . esc_attr( $animation_duration );
	}
}

$output .= '<div class="horizontal-scroller-wrapper ' . esc_attr( $el_class ) . '" data-plugin-hscroll=' . esc_attr( json_encode( $hscroll_options ) ) . esc_attr( $option ) . '><div class="horizontal-scroller"><div class="horizontal-scroller-scroll"><div class="horizontal-scroller-items" style="--porto-hscroll-lg-width:' . esc_attr( $hscroll_options['lg'] ) . ';--porto-hscroll-md-width:' . esc_attr( $hscroll_options['md'] ) . ';">';

// content
$output .= do_shortcode( $content );

$output .= '</div></div></div></div>';

echo porto_filter_output( $output );
