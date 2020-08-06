<?php

// Porto Block
add_shortcode( 'porto_block', 'porto_shortcode_block' );
add_action( 'vc_after_init', 'porto_load_block_shortcode' );

function porto_shortcode_block( $atts, $content = null ) {
	$load_posts_only = function_exists( 'porto_is_ajax' ) && porto_is_ajax() && isset( $_GET['load_posts_only'] );
	if ( $load_posts_only ) {
		return false;
	}

	ob_start();
	if ( $template = porto_shortcode_template( 'porto_block' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_block_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'     => 'Porto ' . __( 'Block', 'porto-functionality' ),
			'base'     => 'porto_block',
			'category' => __( 'Porto', 'porto-functionality' ),
			'icon'     => 'dashicons dashicons-tagcloud',
			'params'   => array(
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
