<?php

// Porto Animation
add_action( 'vc_after_init', 'porto_load_animation_shortcode' );

function porto_load_animation_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	// Mouse Parallax
	$mouse_parallax = array(
		'type'        => 'checkbox',
		'heading'     => __( 'Mouse Parallax?', 'porto-functionality' ),
		'description' => __( 'Animate your elements chasing your mouse move.', 'porto-functionality' ),
		'param_name'  => 'mouse_parallax',
		'value'       => array( __( 'Yes, please', 'porto-functionality' ) => 'yes' ),
		'std'         => 'no',
		'group'       => __( 'Animation', 'porto-functionality' ),
	);

	$mouse_parallax_inverse = array(
		'type'        => 'checkbox',
		'heading'     => __( 'Mouse Parallax Inverse?', 'porto-functionality' ),
		'description' => __( 'Animate your elements inversely chasing your mouse move.', 'porto-functionality' ),
		'param_name'  => 'mouse_parallax_inverse',
		'value'       => array( __( 'Yes, please', 'porto-functionality' ) => 'yes' ),
		'std'         => 'no',
		'dependency'  => array(
			'element' => 'mouse_parallax',
			'value'   => 'yes',
		),
		'group'       => __( 'Animation', 'porto-functionality' ),
	);

	$mouse_parallax_speed = array(
		'type'        => 'textfield',
		'heading'     => __( 'Speed', 'porto-functionality' ),
		'param_name'  => 'mouse_parallax_speed',
		'description' => __( 'Control your elements chasing speed.', 'porto-functionality' ),
		'value'       => '',
		'std'         => '0.5',
		'dependency'  => array(
			'element' => 'mouse_parallax',
			'value'   => 'yes',
		),
		'group'       => __( 'Animation', 'porto-functionality' ),
	);

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Animation', 'porto-functionality' ),
			'base'            => 'porto_animation',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Give your any elements animations including mouse parallax, fadeInUp, fadeInDown, fadeInLeft and so on', 'porto-functionality' ),
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
				$mouse_parallax,
				$mouse_parallax_inverse,
				$mouse_parallax_speed,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Animation' ) ) {
		class WPBakeryShortCode_Porto_Animation extends WPBakeryShortCodesContainer {
		}
	}
}
