<?php

// Porto Container
add_action( 'vc_after_init', 'porto_load_container_shortcode' );

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
			'description'  => __( 'Contain your sections and rows', 'porto-functionality' ),
			'icon'         => 'fas fa-arrows-alt-h',
			'is_container' => true,
			'js_view'      => 'VcColumnView',
			'params'       => array(
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Is half container?', 'porto-functionality' ),
					'param_name'  => 'is_half',
					'std'         => '',
					'admin_label' => true,
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Is full width under 992px?', 'porto-functionality' ),
					'param_name'  => 'is_full_md',
					'description' => __( 'If unchecked, this container becomes full width under 768px.', 'porto-functionality' ),
					'std'         => '',
					'admin_label' => true,
					'dependency'  => array(
						'element'   => 'is_half',
						'not_empty' => true,
					),
				),
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
