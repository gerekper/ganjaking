<?php
$output = $title = $number = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'number'             => 5,
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

if ( ! $title ) {
	$atts['title'] = '';
}

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="vc_widget_woo_top_rated_products wpb_content_element' . esc_attr( $el_class ) . '"';
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

$type = 'WC_Widget_Top_Rated_Products';
$args = array( 'widget_id' => 'woocommerce_top_rated_products_' . $number );

ob_start();
the_widget( $type, $atts, $args );
$output .= ob_get_clean();

$output .= '</div>';

echo porto_filter_output( $output );
