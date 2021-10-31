<?php

// Porto Sticky Nav
add_action( 'vc_after_init', 'porto_load_sticky_nav_shortcode' );

function porto_load_sticky_nav_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Sticky Nav', 'porto-functionality' ),
			'base'            => 'porto_sticky_nav',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Stick navigation', 'porto-functionality' ),
			'icon'            => 'porto_vc_sticky_nav',
			'as_parent'       => array( 'only' => 'porto_sticky_nav_link' ),
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
					'type'        => 'textfield',
					'heading'     => __( 'Min Width (unit: px)', 'porto-functionality' ),
					'param_name'  => 'min_width',
					'description' => __( 'Wll be disable sticky if window width is smaller than min width', 'porto-functionality' ),
					'value'       => 991,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'bg_color',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Color', 'porto-functionality' ),
					'param_name' => 'link_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Background Color', 'porto-functionality' ),
					'param_name' => 'link_bg_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Active Color', 'porto-functionality' ),
					'param_name' => 'link_acolor',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Link Active Background Color', 'porto-functionality' ),
					'param_name' => 'link_abg_color',
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Sticky_Nav' ) ) {
		class WPBakeryShortCode_Porto_Sticky_Nav extends WPBakeryShortCodesContainer {
		}
	}
}
