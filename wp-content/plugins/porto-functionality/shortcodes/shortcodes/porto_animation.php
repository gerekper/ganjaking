<?php

// Porto Animation
add_shortcode( 'porto_animation', 'porto_shortcode_animation' );
add_action( 'vc_after_init', 'porto_load_animation_shortcode' );

function porto_shortcode_animation( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_animation' ) ) {
		include $template;
	}
	return ob_get_clean();
}

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
			'icon'            => 'porto_vc_animation',
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
