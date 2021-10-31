<?php
// Porto Info List

add_action( 'vc_after_init', 'porto_load_social_icons_element' );

function porto_load_social_icons_element() {

	$custom_class = porto_vc_custom_class();
	$right        = is_rtl() ? 'left' : 'right';
	vc_map(
		array(
			'name'        => __( 'Porto Social Icons', 'porto-functionality' ),
			'base'        => 'porto_social_icons',
			'class'       => 'porto_social_icons',
			'icon'        => 'fas fa-share-alt',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display social links', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'       => 'porto_param_heading',
					'text'       => __( 'Please select social links in Porto -> Theme Options -> Header -> Social Links.', 'porto-functionality' ),
					'param_name' => 'social_selectors',
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Icon Font Size', 'porto-functionality' ),
					'param_name' => 'icon_size',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} a' => 'font-size: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} a:not(:hover)' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Color', 'porto-functionality' ),
					'param_name' => 'icon_hover_color',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} a:hover' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Background Color', 'porto-functionality' ),
					'param_name' => 'icon_color_bg',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} a:not(:hover)' => 'background-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Hover Background Color', 'porto-functionality' ),
					'param_name' => 'icon_hover_color_bg',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} a:hover' => 'background-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Border Style', 'porto-functionality' ),
					'param_name' => 'icon_border_style',
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Solid', 'porto-functionality' ) => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' ) => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'std'        => '',
					'selectors'  => array(
						'{{WRAPPER}} a' => 'border-style: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Border Color', 'porto-functionality' ),
					'param_name' => 'icon_color_border',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} a' => 'border-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Icon Border Width', 'porto-functionality' ),
					'param_name' => 'icon_border_size',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} a' => 'border-width: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Icon Border Radius', 'porto-functionality' ),
					'param_name' => 'icon_border_radius',
					'selectors'  => array(
						'{{WRAPPER}} a' => 'border-radius: {{VALUE}};',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Icon Width and Height', 'porto-functionality' ),
					'param_name' => 'icon_border_spacing',
					'selectors'  => array(
						'{{WRAPPER}} a' => 'width: {{VALUE}};height: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Spacing between icons', 'porto-functionality' ),
					'param_name' => 'icon_spacing',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} a' => 'margin-' . $right . ': {{VALUE}}{{UNIT}};',
					),
				),
				$custom_class,
			),
		)
	);

	class WPBakeryShortCode_porto_social_icons extends WPBakeryShortCode {
	}
}
