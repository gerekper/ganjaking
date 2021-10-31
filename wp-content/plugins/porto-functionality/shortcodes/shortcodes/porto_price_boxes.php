<?php

// Porto Price Boxes
add_action( 'vc_after_init', 'porto_load_price_boxes_shortcode' );

function porto_load_price_boxes_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Price Boxes', 'porto-functionality' ),
			'base'            => 'porto_price_boxes',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Simple filled or outline pricing table to display prices of your products or services', 'porto-functionality' ),
			'icon'            => 'fas fa-dollar-sign',
			'as_parent'       => array( 'only' => 'porto_price_box' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Style', 'porto-functionality' ),
					'param_name' => 'style',
					'value'      => porto_sh_commons( 'price_boxes_style' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Size', 'porto-functionality' ),
					'param_name' => 'size',
					'value'      => porto_sh_commons( 'price_boxes_size' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Counts of Price Box on Desktop', 'porto-functionality' ),
					'param_name' => 'count_md',
					'std'        => '4',
					'value'      => porto_sh_commons( 'bootstrap_columns' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Counts of Price Box on Tablet', 'porto-functionality' ),
					'param_name' => 'count_sm',
					'std'        => '2',
					'value'      => porto_sh_commons( 'bootstrap_columns' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Border in Price Boxes', 'porto-functionality' ),
					'param_name' => 'border',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Space between Price Boxes', 'porto-functionality' ),
					'param_name' => 'space',
					'std'        => '',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Price_Boxes' ) ) {
		class WPBakeryShortCode_Porto_Price_Boxes extends WPBakeryShortCodesContainer {
		}
	}
}
