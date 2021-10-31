<?php

// Porto Block
add_action( 'vc_after_init', 'porto_load_block_shortcode' );

function porto_load_block_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Block', 'porto-functionality' ),
			'base'        => 'porto_block',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'A simple block that supports on porto template builder', 'porto-functionality' ),
			'icon'        => 'dashicons dashicons-tagcloud',
			'params'      => array(
				array(
					'type'       => 'label',
					'heading'    => __( 'Input block id & slug name', 'porto-functionality' ),
					'param_name' => 'label',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Block ID', 'porto-functionality' ),
					'param_name'  => 'id',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Block Slug Name', 'porto-functionality' ),
					'param_name'  => 'name',
					'admin_label' => true,
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Block' ) ) {
		class WPBakeryShortCode_Porto_Block extends WPBakeryShortCode {
		}
	}
}
