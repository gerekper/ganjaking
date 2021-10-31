<?php
// Porto SVG Floating
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-svg-floating',
		array(
			'attributes'      => array(
				'float_svg' => array(
					'type' => 'string',
				),
				'float_path' => array(
					'type' => 'string',
				),
				'float_duration' => array(
					'type' => 'integer',
					'default' => 10000,
				),
				'float_easing'   => array(
					'type' => 'string',
					'default' => 'easingQuadraticInOut',
				),
				'float_repeat' => array(
					'type' => 'integer',
					'default' => 20,
				),
				'float_repeat_delay' => array(
					'type' => 'integer',
					'default' => 1000,
				),
				'float_yoyo' => array(
					'type' => 'boolean',
					'default' => true,
				),
				'page_builder' => array(
					'type' => 'string',
					'defualt' => 'gutenberg',
				),
				'el_class'             => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_svg_floating',
		)
	);

	function porto_shortcode_svg_floating( $atts, $content = null ) {
		ob_start();
		if ( $template = porto_shortcode_template( 'porto_svg_floating' ) ) {
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
			}
			$atts['page_builder'] = 'gutenberg';
			include $template;
		}
		return ob_get_clean();
	}
}

add_action( 'vc_after_init', 'porto_load_svg_floating_shortcode' );
function porto_load_svg_floating_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();
	vc_map(
		array(
			'name'        => __( 'Porto SVG Floating', 'porto-functionality' ),
			'base'        => 'porto_svg_floating',
			'class'       => 'porto_svg_floating',
			'icon'        => 'fab fa-shopware',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Please give you floating svg like Startup Agency Demo', 'porto-functionality' ),
			'params'      => array_merge(
				array(
					array(
						'type' => 'textarea_raw_html',
						'heading' => esc_html__( 'Floating SVG', 'porto-functionality' ),
						'param_name' => 'float_svg',
						// @codingStandardsIgnoreLine
						'value' => base64_encode( '' ),
						'description' => esc_html__( 'Please writer your svg code.', 'porto-functionality' ),
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Path', 'porto-functionality' ),
						'param_name'  => 'float_path',
						'value'       => '',
						'description' => __( 'Please write floating path id using comma. like #path1, #path2.', 'porto-functionality' ),
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Floating Duration', 'porto-functionality' ),
						'param_name'  => 'float_duration',
						'value'       => 10000,
						'min'         => 0,
						'max'         => 99999,
					),
					array(
						'type'        => 'dropdown',
						'heading'     => __( 'Easing Method', 'porto-functionality' ),
						'param_name'  => 'float_easing',
						'value'       => porto_sh_commons( 'easing_methods' ),
						'std' => 'easingQuadraticInOut',
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Floating Repeat', 'porto-functionality' ),
						'param_name'  => 'float_repeat',
						'value'       => 20,
						'min'         => 0,
						'max'         => 10000,
					),
					array(
						'type'        => 'number',
						'heading'     => __( 'Repeat Delay', 'porto-functionality' ),
						'param_name'  => 'float_repeat_delay',
						'value'       => 1000,
						'min'         => 0,
						'max'         => 100000,
					),
					array(
						'type'       => 'checkbox',
						'heading'    => __( 'yoyo', 'porto-functionality' ),
						'param_name' => 'float_yoyo',
						'value'      => array( __( 'Yes', 'porto-functionality' ) => 'yes' ),
						'std'        => 'yes',
					),
					array(
						'type'        => 'textfield',
						'heading'     => __( 'Extra Class', 'porto-functionality' ),
						'param_name'  => 'el_class',
						'value'       => '',
						'description' => __( 'Add extra class name that will be applied to the icon process, and you can use this class for your customizations.', 'porto-functionality' ),
					),
				),
				array(
					$animation_type,
					$animation_duration,
					$animation_delay,
				)
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_svg_floating extends WPBakeryShortCode {
		}
	}
}
