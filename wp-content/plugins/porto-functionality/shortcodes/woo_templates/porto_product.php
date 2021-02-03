<?php

$output = $title = $view = $column_width = $addlinks_pos = $id = $animation_type = $animation_duration = $animation_delay = $el_class = '';
extract(
	shortcode_atts(
		array(
			'title'              => '',
			'view'               => 'grid',
			'column_width'       => '',
			'id'                 => '',
			'use_simple'         => '',
			'addlinks_pos'       => '',
			'image_size'         => '',
			'animation_type'     => '',
			'animation_duration' => 1000,
			'animation_delay'    => 0,
			'el_class'           => '',
		),
		$atts
	)
);

$el_class = porto_shortcode_extract_class( $el_class );

$output = '<div class="porto-products wpb_content_element' . esc_attr( $el_class ) . '"';
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
	$output .= '<h2 class="section-title">' . wp_kses_post( $title ) . '</h2>';
}

global $porto_woocommerce_loop;

$porto_woocommerce_loop['view']         = $view;
$porto_woocommerce_loop['columns']      = 1;
$porto_woocommerce_loop['column_width'] = $column_width;
$porto_woocommerce_loop['addlinks_pos'] = $addlinks_pos;
if ( $image_size ) {
	$porto_woocommerce_loop['image_size'] = $image_size;
}
if ( $use_simple ) {
	if ( ! isset( $porto_settings['product-review'] ) || $porto_settings['product-review'] ) {
		remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	}
	$porto_woocommerce_loop['use_simple_layout'] = true;
}
$output .= do_shortcode( '[product id="' . $id . '" columns="1"]' );

unset( $GLOBALS['porto_woocommerce_loop'] );

if ( $use_simple && ( ! isset( $porto_settings['product-review'] ) || $porto_settings['product-review'] ) ) {
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}
$output .= '</div>';

echo porto_filter_output( $output );
