<?php
// Porto Icon

add_action( 'vc_after_init', 'porto_load_icon_shortcode' );

function porto_load_icon_shortcode() {

	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Icon', 'porto-functionality' ),
			'base'        => 'porto_icon',
			'icon'        => 'fas fa-check',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Add a custom icon.', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Icon to display:', 'porto-functionality' ),
					'param_name' => 'icon_type',
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
						__( 'Custom Image Icon', 'porto-functionality' ) => 'custom',
					),
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'icon',
					'value'      => '',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome' ),
					),
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
				),
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Upload Image Icon', 'porto-functionality' ),
					'param_name'  => 'icon_img',
					'admin_label' => true,
					'value'       => '',
					'description' => __( 'Upload the custom image icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Image Width', 'porto-functionality' ),
					'param_name'  => 'img_width',
					'value'       => 48,
					'min'         => 16,
					'max'         => 512,
					'suffix'      => 'px',
					'description' => __( 'Provide image width', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'custom' ),
					),
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
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'class'      => '',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'value'      => '#333333',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'class'      => '',
					'heading'    => __( 'Icon or Image Style', 'porto-functionality' ),
					'param_name' => 'icon_style',
					'value'      => array(
						__( 'Simple', 'porto-functionality' ) => 'none',
						__( 'Circle Background', 'porto-functionality' ) => 'circle',
						__( 'Circle Image', 'porto-functionality' ) => 'circle_img',
						__( 'Square Background', 'porto-functionality' ) => 'square',
						__( 'Design your own', 'porto-functionality' ) => 'advanced',
					),
				),
				array(
					'type'        => 'colorpicker',
					'class'       => '',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'icon_color_bg',
					'value'       => '#ffffff',
					'description' => __( 'Select background color for icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_style',
						'value'   => array( 'circle', 'square', 'advanced', 'circle_img' ),
					),
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
						'value'   => array( 'circle_img', 'advanced' ),
					),
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
						'value'   => array( 'advanced', 'circle_img' ),
					),
				),
				array(
					'type'        => 'number',
					'class'       => '',
					'heading'     => __( 'Background Size', 'porto-functionality' ),
					'param_name'  => 'icon_border_spacing',
					'value'       => 50,
					'min'         => 1,
					'max'         => 500,
					'suffix'      => 'px',
					'description' => __( 'Spacing from center of the icon till the boundary of border / background', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'icon_style',
						'value'   => array( 'advanced', 'circle_img' ),
					),
				),
				array(
					'type'        => 'vc_link',
					'class'       => '',
					'heading'     => __( 'Link ', 'porto-functionality' ),
					'param_name'  => 'icon_link',
					'value'       => '',
					'description' => __( 'Add a custom link or select existing page.', 'porto-functionality' ),
				),
				$animation_type,
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Alignment', 'porto-functionality' ),
					'param_name' => 'icon_align',
					'value'      => array(
						''       => array(
							'title' => esc_html__( 'Default', 'porto-functionality' ),
						),
						'left'   => array(
							'title' => esc_html__( 'Left', 'porto-functionality' ),
							'icon'  => 'fas fa-align-left',
						),
						'center' => array(
							'title' => esc_html__( 'Center', 'porto-functionality' ),
							'icon'  => 'fas fa-align-center',
						),
						'right'  => array(
							'title' => esc_html__( 'Right', 'porto-functionality' ),
							'icon'  => 'fas fa-align-right',
						),
					),
					'std'        => '',
				),
				$custom_class,
				array(
					'type'             => 'css_editor',
					'heading'          => __( 'Css', 'porto-functionality' ),
					'param_name'       => 'css_porto_icon',
					'group'            => __( 'Design ', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-sm-12 vc_column no-vc-background no-vc-border creative_link_css_editor',
				),
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_icon extends WPBakeryShortCode {
		}
	}
}
