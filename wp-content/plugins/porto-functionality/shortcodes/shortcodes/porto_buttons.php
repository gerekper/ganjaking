<?php
// Porto Buttons

add_action( 'vc_after_init', 'porto_load_buttons_shortcode' );

function porto_load_buttons_shortcode() {

	$animation_type        = porto_vc_animation_type();
	$animation_duration    = porto_vc_animation_duration();
	$animation_delay       = porto_vc_animation_delay();
	$custom_class          = porto_vc_custom_class();
	$custom_class['group'] = 'General';

	vc_map(
		array(
			'name'            => __( 'Porto Advanced Button', 'porto-functionality' ),
			'base'            => 'porto_buttons',
			'icon'            => 'fas fa-minus',
			'class'           => 'porto_buttons',
			'content_element' => true,
			'controls'        => 'full',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Create creative buttons.', 'porto-functionality' ),
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Button Title', 'porto-functionality' ),
					'param_name'  => 'btn_title',
					'value'       => '',
					'description' => '',
					'group'       => 'General',
					'admin_label' => true,
				),
				array(
					'type'        => 'vc_link',
					'heading'     => __( 'Button Link', 'porto-functionality' ),
					'param_name'  => 'btn_link',
					'value'       => '',
					'description' => '',
					'group'       => 'General',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Button Alignment', 'porto-functionality' ),
					'param_name'  => 'btn_align',
					'value'       => array(
						__( 'Left Align', 'porto-functionality' ) => 'porto-btn-left',
						__( 'Center Align', 'porto-functionality' ) => 'porto-btn-center',
						__( 'Right Align', 'porto-functionality' ) => 'porto-btn-right',
						__( 'Inline', 'porto-functionality' ) => 'porto-btn-inline',
					),
					'description' => '',
					'group'       => 'General',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'value'      => array(
						__( 'Normal Button', 'porto-functionality' ) => 'porto-btn-normal',
						__( 'Mini Button', 'porto-functionality' ) => 'porto-btn-mini',
						__( 'Small Button', 'porto-functionality' ) => 'porto-btn-small',
						__( 'Large Button', 'porto-functionality' ) => 'porto-btn-large',
						__( 'Button Block', 'porto-functionality' ) => 'porto-btn-block',
						__( 'Custom Size', 'porto-functionality' ) => 'porto-btn-custom',
					),
					'group'      => 'General',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Button Width', 'porto-functionality' ),
					'param_name'  => 'btn_width',
					'value'       => '',
					'min'         => 10,
					'max'         => 1000,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element' => 'btn_size',
						'value'   => 'porto-btn-custom',
					),
					'group'       => 'General',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Button Height', 'porto-functionality' ),
					'param_name'  => 'btn_height',
					'value'       => '',
					'min'         => 10,
					'max'         => 1000,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element' => 'btn_size',
						'value'   => 'porto-btn-custom',
					),
					'group'       => 'General',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Button Left / Right Padding', 'porto-functionality' ),
					'param_name'  => 'btn_padding_left',
					'value'       => '',
					'min'         => 10,
					'max'         => 1000,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element' => 'btn_size',
						'value'   => 'porto-btn-custom',
					),
					'group'       => 'General',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Button Top / Bottom Padding', 'porto-functionality' ),
					'param_name'  => 'btn_padding_top',
					'value'       => '',
					'min'         => 10,
					'max'         => 1000,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element' => 'btn_size',
						'value'   => 'porto-btn-custom',
					),
					'group'       => 'General',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Button Title Color', 'porto-functionality' ),
					'param_name'  => 'btn_title_color',
					'value'       => '#000000',
					'description' => '',
					'group'       => 'General',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'btn_bg_color',
					'value'       => '#e0e0e0',
					'description' => '',
					'group'       => 'General',
				),
				$custom_class,
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Rel Attribute', 'porto-functionality' ),
					'param_name'  => 'rel',
					'description' => __( 'This is useful when you want to trigger third party features. Example- prettyPhoto, thickbox etc', 'porto-functionality' ),
					'group'       => 'General',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Hover Background Effect', 'porto-functionality' ),
					'param_name' => 'btn_hover',
					'value'      => array(
						__( 'No Effect', 'porto-functionality' ) => 'porto-btn-no-hover-bg',
						__( 'Fade Background', 'porto-functionality' ) => 'porto-btn-fade-bg',
						__( 'Fill Background from Top', 'porto-functionality' ) => 'porto-btn-top-bg',
						__( 'Fill Background from Bottom', 'porto-functionality' ) => 'porto-btn-bottom-bg',
						__( 'Fill Background from Left', 'porto-functionality' ) => 'porto-btn-left-bg',
						__( 'Fill Background from Right', 'porto-functionality' ) => 'porto-btn-right-bg',
						__( 'Fill Background from Center Horizontally', 'porto-functionality' ) => 'porto-btn-center-hz-bg',
						__( 'Fill Background from Center Vertically', 'porto-functionality' ) => 'porto-btn-center-vt-bg',
						__( 'Fill Background from Center Diagonal', 'porto-functionality' ) => 'porto-btn-center-dg-bg',
					),
					'group'      => 'Background',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Hover Background Color', 'porto-functionality' ),
					'param_name'  => 'btn_bg_color_hover',
					'value'       => '',
					'description' => '',
					'group'       => 'Background',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Hover Text Color', 'porto-functionality' ),
					'param_name'  => 'btn_title_color_hover',
					'value'       => '',
					'description' => '',
					'group'       => 'Background',
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Button Border Style', 'porto-functionality' ),
					'param_name'  => 'btn_border_style',
					'value'       => array(
						'None'   => '',
						'Solid'  => 'solid',
						'Dashed' => 'dashed',
						'Dotted' => 'dotted',
						'Double' => 'double',
						'Inset'  => 'inset',
						'Outset' => 'outset',
					),
					'description' => '',
					'group'       => 'Styling',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'btn_color_border',
					'value'       => '',
					'description' => '',
					'dependency'  => array(
						'element'   => 'btn_border_style',
						'not_empty' => true,
					),
					'group'       => 'Styling',
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Color on Hover', 'porto-functionality' ),
					'param_name'  => 'btn_color_border_hover',
					'value'       => '',
					'description' => '',
					'dependency'  => array(
						'element'   => 'btn_border_style',
						'not_empty' => true,
					),
					'group'       => 'Styling',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Border Width', 'porto-functionality' ),
					'param_name'  => 'btn_border_size',
					'value'       => 1,
					'min'         => 1,
					'max'         => 10,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element'   => 'btn_border_style',
						'not_empty' => true,
					),
					'group'       => 'Styling',
				),
				array(
					'type'        => 'number',
					'heading'     => __( 'Border Radius', 'porto-functionality' ),
					'param_name'  => 'btn_radius',
					'value'       => 3,
					'min'         => 0,
					'max'         => 500,
					'suffix'      => 'px',
					'description' => '',
					'dependency'  => array(
						'element'   => 'btn_border_style',
						'not_empty' => true,
					),
					'group'       => 'Styling',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_adv_btn',
					'group'            => __( 'Styling', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'btn_porto_typography',
					'group'      => 'Typography',
					'selectors'  => array(
						'{{WRAPPER}}.porto-btn',
					),
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_buttons extends WPBakeryShortCode {
		}
	}
}
