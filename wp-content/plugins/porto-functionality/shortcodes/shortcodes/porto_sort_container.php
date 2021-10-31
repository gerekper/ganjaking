<?php

// Porto Sort Container
add_action( 'vc_after_init', 'porto_load_sort_container_shortcode' );

function porto_load_sort_container_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sort Container', 'porto-functionality' ),
			'base'            => 'porto_sort_container',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'We can sort of any elements', 'porto-functionality' ),
			'icon'            => 'porto_vc_sort_container',
			'as_parent'       => array( 'only' => 'porto_sort_item' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Container ID', 'porto-functionality' ),
					'param_name'  => 'id',
					'value'       => '',
					'admin_label' => true,
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sort_Container' ) ) {
		class WPBakeryShortCode_Porto_Sort_Container extends WPBakeryShortCodesContainer {
		}
	}
}
