<?php

// Porto Scroll Progress
add_action( 'vc_after_init', 'porto_load_scroll_progress_shortcode' );

function porto_load_scroll_progress_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Scroll Progress', 'porto-functionality' ),
			'base'        => 'porto_scroll_progress',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'display scroll progress bar in some positions.', 'porto-functionality' ),
			'icon'        => 'fas fa-scroll',
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Type & Position', 'porto-functionality' ),
					'description' => __( 'If you select "Around the Scroll to Top button", default scroll to top button will be hidden.', 'porto-functionality' ),
					'param_name'  => 'type',
					'std'         => '',
					'value'       => array(
						__( 'Horizontal progress bar', 'porto-functionality' ) => '',
						__( 'Around the Scroll to Top button', 'porto-functionality' ) => 'circle',
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Is Fixed Position?', 'porto-functionality' ),
					'param_name'  => 'position',
					'std'         => '',
					'value'       => array(
						__( 'No', 'porto-functionality' ) => '',
						__( 'Fixed on Top', 'porto-functionality' ) => 'top',
						__( 'Under Sticky Header', 'porto-functionality' ) => 'under-header',
						__( 'Fixed on Bottom', 'porto-functionality' ) => 'bottom',
					),
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset Height', 'porto-functionality' ),
					'param_name' => 'offset_top',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position',
						'value'   => array( 'top' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'margin-top: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset Height', 'porto-functionality' ),
					'param_name' => 'offset_bottom',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position',
						'value'   => array( 'bottom' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'        => 'dropdown',
					'class'       => '',
					'heading'     => __( 'Icon for scroll to top:', 'porto-functionality' ),
					'param_name'  => 'icon_type',
					'value'       => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
				),
				array(
					'type'       => 'iconpicker',
					'class'      => '',
					'heading'    => __( 'Icon ', 'porto-functionality' ),
					'param_name' => 'icon_cls',
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
					'type'       => 'porto_number',
					'heading'    => __( 'Size', 'porto-functionality' ),
					'param_name' => 'circle_size',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'width: {{VALUE}}{{UNIT}};height: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Position', 'porto-functionality' ),
					'param_name' => 'position1',
					'std'        => '',
					'value'      => array(
						__( 'Bottom & Right', 'porto-functionality' ) => '',
						__( 'Bottom & Left', 'porto-functionality' ) => 'bl',
						__( 'Top & Left', 'porto-functionality' ) => 'tl',
						__( 'Top & Right', 'porto-functionality' ) => 'tr',
					),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset X', 'porto-functionality' ),
					'param_name' => 'offset_x1',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position1',
						'value'   => array( 'tl', 'bl' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'left: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset X', 'porto-functionality' ),
					'param_name' => 'offset_x2',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position1',
						'value'   => array( 'tr', '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'right: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset Y', 'porto-functionality' ),
					'param_name' => 'offset_y1',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position1',
						'value'   => array( 'tl', 'tr' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'top: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Offset Y', 'porto-functionality' ),
					'param_name' => 'offset_y2',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'position1',
						'value'   => array( '', 'bl' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'bottom: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Thickness', 'porto-functionality' ),
					'param_name' => 'thickness1',
					'value'      => 3,
					'min'        => 1,
					'max'        => 20,
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'height: {{VALUE}}px;',
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Thickness of progress bar', 'porto-functionality' ),
					'param_name' => 'thickness2',
					'value'      => 3,
					'min'        => 1,
					'max'        => 20,
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} circle' => 'stroke-width: {{VALUE}}px;',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Icon Size', 'porto-functionality' ),
					'param_name' => 'icon_size',
					'units'      => array( 'px', 'rem', 'em' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'font-size: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'description' => __( 'Set the background color of icon part.', 'porto-functionality' ),
					'param_name'  => 'icon_bgcolor',
					'dependency'  => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
					'selectors'   => array(
						'{{WRAPPER}} i' => 'background-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'circle' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} i' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Border Radius (px)', 'porto-functionality' ),
					'param_name' => 'br',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}'                    => 'border-radius: {{VALUE}}px;',
						'{{WRAPPER}}::-moz-progress-bar' => 'border-radius: {{VALUE}}px;',
						'{{WRAPPER}}::-webkit-progress-bar' => 'border-radius: {{VALUE}}px;',
						'{{WRAPPER}}::-webkit-progress-value' => 'border-radius: {{VALUE}}px;',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Bar Color', 'porto-functionality' ),
					'param_name' => 'bgcolor',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}}' => 'background-color: {{VALUE}};',
						'{{WRAPPER}}::-webkit-progress-bar' => 'background-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Active Bar Color', 'porto-functionality' ),
					'param_name' => 'active_bgcolor',
					'selectors'  => array(
						'{{WRAPPER}}::-moz-progress-bar' => 'background-color: {{VALUE}};',
						'{{WRAPPER}}::-webkit-progress-value' => 'background-color: {{VALUE}};',
						'{{WRAPPER}} circle'             => 'stroke: {{VALUE}};',
					),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Scroll_Progress' ) ) {
		class WPBakeryShortCode_Porto_Scroll_Progress extends WPBakeryShortCode {
		}
	}
}
