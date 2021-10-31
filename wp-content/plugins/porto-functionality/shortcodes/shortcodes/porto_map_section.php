<?php

// Porto Map Section
add_action( 'vc_after_init', 'porto_load_map_section_shortcode' );

function porto_load_map_section_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Map Section', 'porto-functionality' ),
			'base'            => 'porto_map_section',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Display the world map', 'porto-functionality' ),
			'icon'            => 'porto_vc_map_section',
			'as_parent'       => array( 'except' => 'porto_map_section' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Wrap as Container', 'porto-functionality' ),
					'param_name' => 'container',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Change Map Image', 'porto-functionality' ),
					'param_name' => 'customize',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Map Image', 'porto-functionality' ),
					'param_name' => 'image',
					'dependency' => array(
						'element'   => 'customize',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Image Gap (unit: px)', 'porto-functionality' ),
					'std'        => '164',
					'param_name' => 'gap',
					'dependency' => array(
						'element'   => 'customize',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Content Min Height (unit: px)', 'porto-functionality' ),
					'param_name' => 'min_height',
					'std'        => '400',
					'dependency' => array(
						'element'   => 'customize',
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Map_Section' ) ) {
		class WPBakeryShortCode_Porto_Map_Section extends WPBakeryShortCodesContainer {
		}
	}
}
