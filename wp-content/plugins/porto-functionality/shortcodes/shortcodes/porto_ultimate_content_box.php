<?php
// Porto Ultimate Content Box
add_action( 'vc_after_init', 'porto_load_ultimate_content_box_shortcode' );

function porto_load_ultimate_content_box_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Ultimate Content Box', 'porto-functionality' ),
			'base'                    => 'porto_ultimate_content_box',
			'icon'                    => 'far fa-object-group',
			'class'                   => 'porto_ultimate_content_box',
			'as_parent'               => array( 'except' => 'porto_ultimate_content_box' ),
			'controls'                => 'full',
			'show_settings_on_create' => true,
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Content Box.', 'porto-functionality' ),
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Background Type', 'porto-functionality' ),
					'param_name' => 'bg_type',
					'value'      => array(
						__( 'Background Color', 'porto-functionality' ) => 'bg_color',
						__( 'Background Image', 'porto-functionality' ) => 'bg_image',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'bg_clr',
					'dependency' => array(
						'element' => 'bg_type',
						'value'   => 'bg_color',
					),
				),
				array(
					'type'        => 'attach_image',
					'heading'     => __( 'Background Image', 'porto-functionality' ),
					'param_name'  => 'bg_img',
					'description' => __( 'Set background image for content box.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'bg_type',
						'value'   => 'bg_image',
					),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Border Style', 'porto-functionality' ),
					'param_name'  => 'box_border_style',
					'value'       => array(
						__( 'None', 'porto-functionality' )   => '',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
						__( 'Gradient', 'porto-functionality' ) => 'gradient',
					),
					'description' => __( 'Select the border style.', 'porto-functionality' ),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'border',
					'value'      => '',
					'min'        => 1,
					'max'        => 10,
					'suffix'     => 'px',
					'dependency' => array(
						'element'   => 'box_border_style',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Degree', 'porto-functionality' ),
					'param_name' => 'border_degree',
					'value'      => '',
					'min'        => 0,
					'max'        => 180,
					'suffix'     => 'deg',
					'dependency' => array(
						'element' => 'box_border_style',
						'value'   => array( 'gradient' ),
					),
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'box_border_color',
					'value'       => '',
					'description' => __( 'Select border color.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'box_border_style',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Border Color 2', 'porto-functionality' ),
					'param_name'  => 'box_border_color2',
					'value'       => '',
					'description' => __( 'Select secondary border color.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'box_border_style',
						'value'   => array( 'gradient' ),
					),
				),
				array(
					'type'       => 'porto_boxshadow',
					'heading'    => __( 'Box Shadow', 'porto-functionality' ),
					'param_name' => 'box_shadow',
					'unit'       => 'px',
					'positions'  => array(
						__( 'Horizontal', 'porto-functionality' ) => '',
						__( 'Vertical', 'porto-functionality' ) => '',
						__( 'Blur', 'porto-functionality' )   => '',
						__( 'Spread', 'porto-functionality' ) => '',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Content Position', 'porto-functionality' ),
					'param_name' => 'content_pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => '',
						__( 'Middle', 'porto-functionality' ) => 'center',
						__( 'Bottom', 'porto-functionality' ) => 'end',
					),
				),
				array(
					'type'       => 'vc_link',
					'heading'    => __( 'Content Box Link', 'porto-functionality' ),
					'param_name' => 'link',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'CSS Class for Content Box Link', 'porto-functionality' ),
					'param_name' => 'link_class',
					'dependency' => array(
						'element'   => 'link',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Min Height', 'porto-functionality' ),
					'param_name' => 'min_height',
				),
				$animation_type,
				$animation_duration,
				$animation_delay,
				$custom_class,

				//  Background
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Background Image Repeat', 'porto-functionality' ),
					'param_name' => 'bg_repeat',
					'value'      => array(
						__( 'Repeat', 'porto-functionality' ) => 'repeat',
						__( 'Repeat X', 'porto-functionality' ) => 'repeat-x',
						__( 'Repeat Y', 'porto-functionality' ) => 'repeat-y',
						__( 'No Repeat', 'porto-functionality' ) => 'no-repeat',
					),
					'group'      => 'Background',
					'dependency' => array(
						'element' => 'bg_type',
						'value'   => 'bg_image',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Background Image Size', 'porto-functionality' ),
					'param_name' => 'bg_size',
					'value'      => array(
						__( 'Cover - Image to be as large as possible', 'porto-functionality' ) => 'cover',
						__( 'Contain - Image will try to fit inside the container area', 'porto-functionality' ) => 'contain',
						__( 'Initial', 'porto-functionality' ) => 'initial',
					),
					'group'      => 'Background',
					'dependency' => array(
						'element' => 'bg_type',
						'value'   => 'bg_image',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Background Image Position', 'porto-functionality' ),
					'param_name'  => 'bg_position',
					'description' => __( 'You can use any number with px, em, %, etc. Example- 100px 100px.', 'porto-functionality' ),
					'group'       => 'Background',
					'dependency'  => array(
						'element' => 'bg_type',
						'value'   => 'bg_image',
					),
				),

				//  Hover
				array(
					'type'          => 'porto_boxshadow',
					'heading'       => __( 'Box Shadow', 'porto-functionality' ),
					'param_name'    => 'hover_box_shadow',
					'unit'          => 'px',
					'positions'     => array(
						__( 'Horizontal', 'porto-functionality' ) => '',
						__( 'Vertical', 'porto-functionality' ) => '',
						__( 'Blur', 'porto-functionality' )   => '',
						__( 'Spread', 'porto-functionality' ) => '',
					),
					'label_color'   => __( 'Shadow Color', 'porto-functionality' ),
					'default_style' => 'inherit',
					'group'         => 'Hover',
				),
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_contentbox',
					'group'            => __( 'Design', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCodesContainer' ) ) {
		class WPBakeryShortCode_porto_ultimate_content_box extends WPBakeryShortCodesContainer {
		}
	}
}
