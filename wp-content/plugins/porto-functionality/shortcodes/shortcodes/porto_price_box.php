<?php

// Porto Price Box
add_action( 'vc_after_init', 'porto_load_price_box_shortcode' );

function porto_load_price_box_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Price Box', 'porto-functionality' ),
			'base'        => 'porto_price_box',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Simple filled or outline pricing table to display price of your product or service', 'porto-functionality' ),
			'icon'        => 'fas fa-dollar-sign',
			'as_child'    => array( 'only' => 'porto_price_boxes' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Description', 'porto-functionality' ),
					'param_name' => 'desc',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Popular Price Box', 'porto-functionality' ),
					'param_name' => 'is_popular',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'true' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Popular Label', 'porto-functionality' ),
					'param_name' => 'popular_label',
					'dependency' => array(
						'element'   => 'is_popular',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price', 'porto-functionality' ),
					'param_name'  => 'price',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Price Unit', 'porto-functionality' ),
					'param_name' => 'price_unit',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price Label', 'porto-functionality' ),
					'description' => 'For example, "Per Month"',
					'param_name'  => 'price_label',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'value'      => porto_sh_commons( 'colors' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Button', 'porto-functionality' ),
					'param_name' => 'show_btn',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'true' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Button Label', 'porto-functionality' ),
					'param_name' => 'btn_label',
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Action', 'porto-functionality' ),
					'param_name' => 'btn_action',
					'value'      => porto_sh_commons( 'popup_action' ),
				),
				array(
					'type'       => 'vc_link',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'btn_link',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'open_link' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Video or Map URL (Link)', 'porto-functionality' ),
					'param_name' => 'popup_iframe',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_iframe' ),
					),
				),
				array(
					'type'        => 'textarea',
					'heading'     => __( 'Popup Block', 'porto-functionality' ),
					'param_name'  => 'popup_block',
					'description' => __( 'Please add block slug name.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popup Size', 'porto-functionality' ),
					'param_name' => 'popup_size',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
					'value'      => array(
						__( 'Medium', 'porto-functionality' ) => 'md',
						__( 'Large', 'porto-functionality' )  => 'lg',
						__( 'Small', 'porto-functionality' )  => 'sm',
						__( 'Extra Small', 'porto-functionality' ) => 'xs',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popup Animation', 'porto-functionality' ),
					'param_name' => 'popup_animation',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
					'value'      => array(
						__( 'Fade', 'porto-functionality' ) => 'mfp-fade',
						__( 'Zoom', 'porto-functionality' ) => 'mfp-with-zoom',
						__( 'Fade Zoom', 'porto-functionality' ) => 'my-mfp-zoom-in',
						__( 'Fade Slide', 'porto-functionality' ) => 'my-mfp-slide-bottom',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Style', 'porto-functionality' ),
					'param_name' => 'btn_style',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Outline', 'porto-functionality' ) => 'borders',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'value'      => porto_sh_commons( 'size' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Position', 'porto-functionality' ),
					'param_name' => 'btn_pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => '',
						__( 'Bottom', 'porto-functionality' ) => 'bottom',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Skin Color', 'porto-functionality' ),
					'param_name' => 'btn_skin',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'Content', 'porto-functionality' ),
					'param_name' => 'content',
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Price_Box' ) ) {
		class WPBakeryShortCode_Porto_Price_Box extends WPBakeryShortCode {
		}
	}
}
