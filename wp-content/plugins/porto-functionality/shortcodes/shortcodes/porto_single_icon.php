<?php
// Porto Single Icon
add_action( 'vc_after_init', 'porto_load_single_icon_shortcode' );

function porto_load_single_icon_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Icon Item', 'porto-functionality' ),
			'base'                    => 'porto_single_icon',
			'class'                   => 'porto_simple_icon',
			'icon'                    => 'fas fa-check',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Add a set of multiple icons and give some custom style.', 'porto-functionality' ),
			'as_child'                => array( 'only' => 'porto_icons' ),
			'show_settings_on_create' => true,
			'is_container'            => false,
			'params'                  => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon to display:', 'porto-functionality' ),
					'param_name' => 'icon_type',
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon',
					'value'      => '',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_simpleline',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon_porto',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Icon Size', 'porto-functionality' ),
					'param_name' => 'icon_size',
					'value'      => 32,
					'min'        => 12,
					'max'        => 72,
					'suffix'     => 'px',
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Space after Icon', 'porto-functionality' ),
					'param_name' => 'icon_margin',
					'value'      => 5,
					'min'        => 0,
					'max'        => 100,
					'suffix'     => 'px',
					'group'      => 'Other Settings',
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'value'      => '',
					'group'      => 'Select Icon',
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Icon Style', 'porto-functionality' ),
					'param_name' => 'icon_style',
					'value'      => array(
						__( 'Simple', 'porto-functionality' ) => 'none',
						__( 'Circle Background', 'porto-functionality' ) => 'circle',
						__( 'Square Background', 'porto-functionality' ) => 'square',
						__( 'Design your own', 'porto-functionality' ) => 'advanced',
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_bg',
					'value'       => '',
					'description' => __( 'Select background color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle', 'square', 'advanced' ),
					),
					'group'       => 'Select Icon',
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Icon Border Style', 'porto-functionality' ),
					'param_name'  => 'icon_border_style',
					'value'       => array(
						__( 'None', 'porto-functionality' )   => '',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'description' => __( 'Select the border style for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'advanced' ),
					),
					'group'       => 'Select Icon',
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_border',
					'value'       => '#333333',
					'description' => __( 'Select border color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
					'group'       => 'Select Icon',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Border Width', 'porto-functionality' ),
					'param_name'  => 'icon_border_size',
					'value'       => 1,
					'min'         => 1,
					'max'         => 10,
					'suffix'      => 'px',
					'description' => __( 'Thickness of the border.', 'porto-functionality' ),
					'dependency'  => array(
						'element'   => 'icon_border_style',
						'not_empty' => true,
					),
					'group'       => 'Select Icon',
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'icon_border_radius',
					'value'      => 500,
					'min'        => 1,
					'max'        => 500,
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'icon_style',
						'value'   => array( 'advanced' ),
					),
					'group'      => 'Select Icon',
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Background Size', 'porto-functionality' ),
					'param_name'  => 'icon_border_spacing',
					'value'       => 50,
					'min'         => 30,
					'max'         => 500,
					'suffix'      => 'px',
					'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'advanced' ),
					),
					'group'       => 'Select Icon',
				),
				array(
					'type'        => 'vc_link',
					'class'       => '',
					'heading'     => __( 'Link ', 'porto-functionality' ),
					'param_name'  => 'icon_link',
					'value'       => '',
					'description' => __( 'Add a custom link or select existing page.', 'porto-functionality' ),
					'group'       => 'Other Settings',
				),
				$animation_type,
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_single_icon extends WPBakeryShortCode {
		}
	}

}
