<?php

// Porto Animation
add_action( 'vc_after_init', 'porto_load_animation_shortcode' );

function porto_load_animation_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Animation', 'porto-functionality' ),
			'base'            => 'porto_animation',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'icon'            => 'fas fa-asterisk',
			'as_parent'       => array( 'except' => 'porto_animation' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Animation' ) ) {
		class WPBakeryShortCode_Porto_Animation extends WPBakeryShortCodesContainer {
		}
	}
}
