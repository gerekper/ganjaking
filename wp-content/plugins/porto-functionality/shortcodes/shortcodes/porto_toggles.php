<?php

// Porto Toggles
add_action( 'vc_after_init', 'porto_load_toggles_shortcode' );

function porto_load_toggles_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Toggles', 'porto-functionality' ),
			'base'            => 'porto_toggles',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Add toggle for your faq', 'porto-functionality' ),
			'icon'            => 'fas fa-indent',
			'as_parent'       => array( 'only' => 'vc_toggle' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => porto_sh_commons( 'toggle_type' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Size', 'porto-functionality' ),
					'param_name' => 'size',
					'value'      => porto_sh_commons( 'toggle_size' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'One toggle open at a time', 'porto-functionality' ),
					'param_name' => 'one_toggle',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Skin Color', 'porto-functionality' ),
					'param_name'  => 'skin',
					'std'         => 'custom',
					'value'       => porto_sh_commons( 'colors' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Toggles' ) ) {
		class WPBakeryShortCode_Porto_Toggles extends WPBakeryShortCodesContainer {
		}
	}
}
