<?php

// Porto Container
add_shortcode( 'porto_container', 'porto_shortcode_container' );
add_action( 'vc_after_init', 'porto_load_container_shortcode' );

function porto_shortcode_container( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_container' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_container_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'         => 'Porto ' . __( 'Container', 'porto-functionality' ),
			'base'         => 'porto_container',
			'category'     => __( 'Porto', 'porto-functionality' ),
			'icon'         => 'porto_vc_container',
			'is_container' => true,
			'js_view'      => 'VcColumnView',
			'params'       => array(
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Container' ) ) {
		class WPBakeryShortCode_Porto_Container extends WPBakeryShortCodesContainer {
		}
	}
}
