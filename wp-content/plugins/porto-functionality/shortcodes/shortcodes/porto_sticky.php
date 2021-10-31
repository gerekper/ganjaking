<?php

// Porto Sticky
add_action( 'vc_after_init', 'porto_load_sticky_shortcode' );

function porto_load_sticky_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sticky', 'porto-functionality' ),
			'base'            => 'porto_sticky',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Stick any elements especially images or text', 'porto-functionality' ),
			'icon'            => 'fas fa-compress',
			'as_parent'       => array( 'except' => 'porto_sticky' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Container Selector', 'porto-functionality' ),
					'description' => esc_html__( 'Closest parent element which contains sticky and background elements.', 'porto-functionality' ),
					'param_name'  => 'container_selector',
					'value'       => '',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Min Width (unit: px)', 'porto-functionality' ),
					'param_name'  => 'min_width',
					'description' => __( 'Wll be disable sticky if window width is smaller than min width', 'porto-functionality' ),
					'value'       => '767',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Top (unit: px)', 'porto-functionality' ),
					'param_name'  => 'top',
					'description' => __( 'Top position when active', 'porto-functionality' ),
					'value'       => '110',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Bottom (unit: px)', 'porto-functionality' ),
					'param_name'  => 'bottom',
					'description' => __( 'Bottom position when active', 'porto-functionality' ),
					'value'       => '0',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Active Class', 'porto-functionality' ),
					'param_name' => 'active_class',
					'value'      => 'sticky-active',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Auto Fit', 'porto-functionality' ),
					'param_name' => 'autofit',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => '',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sticky' ) ) {
		class WPBakeryShortCode_Porto_Sticky extends WPBakeryShortCodesContainer {
		}
	}
}
