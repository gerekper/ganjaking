<?php

// Porto Experience Timeline Container
add_shortcode( 'porto_experience_timeline_container', 'porto_shortcode_experience_timeline_container' );
add_action( 'vc_after_init', 'porto_load_experience_timeline_container_shortcode' );

function porto_shortcode_experience_timeline_container( $atts, $content = null ) {
	ob_start();
	if ( $template = porto_shortcode_template( 'porto_experience_timeline_container' ) ) {
		include $template;
	}
	return ob_get_clean();
}

function porto_load_experience_timeline_container_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => 'Porto ' . __( 'Experience Timeline Container', 'porto-functionality' ),
			'base'                    => 'porto_experience_timeline_container',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'icon'                    => 'porto_vc_experience_timeline',
			'as_parent'               => array( 'only' => 'porto_experience_timeline_item' ),
			'content_element'         => true,
			'show_settings_on_create' => false,
			'controls'                => 'full',
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Experience_Timeline_Container' ) ) {
		class WPBakeryShortCode_Porto_Experience_Timeline_Container extends WPBakeryShortCodesContainer {
		}
	}
}
