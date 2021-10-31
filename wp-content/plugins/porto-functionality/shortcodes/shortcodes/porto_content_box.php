<?php

// Porto Content Box
add_action( 'vc_after_init', 'porto_load_content_box_shortcode' );

function porto_load_content_box_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Content Box', 'porto-functionality' ),
			'base'            => 'porto_content_box',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Contain your any elements as you mind', 'porto-functionality' ),
			'icon'            => 'far fa-newspaper',
			'as_parent'       => array( 'except' => 'porto_content_box' ),
			'content_element' => true,
			'controls'        => 'full',
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Skin Color', 'porto-functionality' ),
					'param_name'  => 'skin',
					'std'         => 'custom',
					'value'       => porto_sh_commons( 'colors' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Top Color', 'porto-functionality' ),
					'param_name'  => 'border_top_color',
					'dependency'  => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Border Radius', 'porto-functionality' ),
					'param_name'  => 'border_radius',
					'description' => __( 'Enter the border radius in px.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Border Top Width', 'porto-functionality' ),
					'param_name'  => 'border_top_width',
					'description' => __( 'Enter the border top width in px.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Background Type', 'porto-functionality' ),
					'param_name'  => 'bg_type',
					'value'       => porto_sh_commons( 'content_boxes_bg_type' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Gradient Top Color', 'porto-functionality' ),
					'param_name' => 'bg_top_color',
					'dependency' => array(
						'element' => 'bg_type',
						'value'   => array( 'featured-boxes-custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Gradient Bottom Color', 'porto-functionality' ),
					'param_name' => 'bg_bottom_color',
					'dependency' => array(
						'element' => 'bg_type',
						'value'   => array( 'featured-boxes-custom' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Text Align', 'porto-functionality' ),
					'param_name' => 'align',
					'value'      => porto_sh_commons( 'align' ),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Box Style', 'porto-functionality' ),
					'param_name'  => 'box_style',
					'value'       => porto_sh_commons( 'content_boxes_style' ),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Box Effect', 'porto-functionality' ),
					'param_name'  => 'box_effect',
					'value'       => porto_sh_commons( 'content_box_effect' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Icon', 'porto-functionality' ),
					'param_name' => 'show_icon',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library', 'js_composer' ),
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
						__( 'Custom Image Icon', 'porto-functionality' ) => 'image',
					),
					'param_name' => 'icon_type',
					'dependency' => array(
						'element'   => 'show_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
					'param_name' => 'icon_image',
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon_simpleline',
					'value'      => '',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon_porto',
					'value'      => '',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Background Color', 'porto-functionality' ),
					'param_name' => 'icon_bg_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Border Color', 'porto-functionality' ),
					'param_name' => 'icon_border_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Wrap Border Color', 'porto-functionality' ),
					'param_name' => 'icon_wrap_border_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Box Shadow Color', 'porto-functionality' ),
					'param_name' => 'icon_shadow_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Hover Color', 'porto-functionality' ),
					'param_name' => 'icon_hcolor',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Hover Background Color', 'porto-functionality' ),
					'param_name' => 'icon_hbg_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Hover Border Color', 'porto-functionality' ),
					'param_name' => 'icon_hborder_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Wrap Hover Border Color', 'porto-functionality' ),
					'param_name' => 'icon_wrap_hborder_color',
					'dependency' => array(
						'element' => 'skin',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Hover Box Shadow Color', 'porto-functionality' ),
					'param_name' => 'icon_hshadow_color',
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Content_Box' ) ) {
		class WPBakeryShortCode_Porto_Content_Box extends WPBakeryShortCodesContainer {
		}
	}
}
