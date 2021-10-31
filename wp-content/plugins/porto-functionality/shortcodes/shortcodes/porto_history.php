<?php

// Porto History
add_action( 'vc_after_init', 'porto_load_history_shortcode' );

function porto_load_history_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'History', 'porto-functionality' ),
			'base'        => 'porto_history',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show history with image and text field by year', 'porto-functionality' ),
			'icon'        => 'fas fa-history',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Year', 'porto-functionality' ),
					'param_name'  => 'year',
					'admin_label' => true,
				),
				array(
					'type'       => 'label',
					'heading'    => __( 'Input Photo URL or Select Photo.', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Photo URL', 'porto-functionality' ),
					'param_name' => 'image_url',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Photo', 'porto-functionality' ),
					'param_name' => 'image',
				),
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'History', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_History' ) ) {
		class WPBakeryShortCode_Porto_History extends WPBakeryShortCode {
		}
	}
}
