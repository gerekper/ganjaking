<?php

/**
 * Porto Contact form widget
 *
 * @since 2.4.0
 */

add_action( 'vc_after_init', 'porto_load_cursor_effect_shortcode' );

function porto_load_cursor_effect_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Cursor Effect', 'porto-functionality' ),
			'base'        => 'porto_cursor_effect',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Change mouse pointer with some effects', 'porto-functionality' ),
			'icon'        => 'far fa-hand-pointer',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Target', 'porto-functionality' ),
					'param_name'  => 'selector',
					'description' => __( 'Please input the target using a jQuery selector which this cursor effect is applied to. It you leave it empty, this cursor effect will be applied to all pages.', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Hover Effect on Target', 'porto-functionality' ),
					'param_name' => 'hover_effect',
					'std'        => 'plus',
					'value'      => array(
						__( 'Change Mouse Cursor', 'porto-functionality' ) => 'plus',
						__( 'Outline Target', 'porto-functionality' ) => 'fit',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon for cursor inner', 'porto-functionality' ),
					'param_name' => 'icon_type',
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Icon', 'porto-functionality' ),
					'param_name' => 'inner_icon',
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
					'heading'    => __( 'Icon Font Size', 'porto-functionality' ),
					'param_name' => 'icon_fs',
					'units'      => array( 'px', 'em', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner-icon' => '--porto-cursor-inner-fs: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Icon Color', 'porto-functionality' ),
					'param_name' => 'inner_clr',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Transition Duration (ms)', 'porto-functionality' ),
					'param_name' => 'tr_dr',
					'min'        => 0,
					'max'        => 2000,
					'suffix'     => 'ms',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer, {{WRAPPER}}.cursor-inner' => 'transition-duration: {{VALUE}}ms;',
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Transition Delay (ms)', 'porto-functionality' ),
					'param_name' => 'tr_dl',
					'min'        => 0,
					'max'        => 2000,
					'suffix'     => 'ms',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'transition-delay: {{VALUE}}ms;',
					),
				),
				array(
					'type'       => 'number',
					'heading'    => __( 'Cursor Inner Transition Delay (ms)', 'porto-functionality' ),
					'param_name' => 'inner_tr_dl',
					'min'        => 0,
					'max'        => 2000,
					'suffix'     => 'ms',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'transition-delay: {{VALUE}}ms;',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
				),

				array(
					'type'       => 'number',
					'heading'    => __( 'Cursor Size', 'porto-functionality' ),
					'param_name' => 'cursor_w',
					'min'        => 0,
					'max'        => 100,
					'suffix'     => 'px',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'width: {{VALUE}}px; height: {{VALUE}}px;',
						'{{WRAPPER}}.cursor-inner' => 'left: calc( {{VALUE}}px / 2 ); top: calc( {{VALUE}}px / 2 );',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'bd_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'bd_bw',
					'units'      => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'bd_clr',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'bg',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-outer' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Inner Size', 'porto-functionality' ),
					'param_name' => 'inner_cursor_w',
					'units'      => array( 'px', 'em', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => '--porto-cursor-inner-size: {{VALUE}}{{UNIT}}; width: {{VALUE}}{{UNIT}}; height: {{VALUE}}{{UNIT}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'inner_bd_bs',
					'std'        => '',
					'value'      => array(
						__( 'None', 'porto-functionality' )   => '',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'border-style: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'inner_bd_bw',
					'units'      => array( 'px' ),
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'border-width: {{VALUE}}{{UNIT}}; margin-top: calc( var( --porto-cursor-inner-size, calc( {{VALUE}}{{UNIT}} * 2 ) ) / -2 ); margin-left: calc( var( --porto-cursor-inner-size, calc( {{VALUE}}{{UNIT}} * 2 ) ) / -2 );',
						'{{WRAPPER}}.cursor-inner.cursor-inner-icon' => 'margin-top: calc( var( --porto-cursor-inner-size, calc( 1em + {{VALUE}}{{UNIT}} * 2 ) ) / -2 ); margin-left: calc( var( --porto-cursor-inner-size, calc( 1em + {{VALUE}}{{UNIT}} * 2 ) ) / -2 );',
					),
					'dependency' => array(
						'element'   => 'inner_bd_bs',
						'not_empty' => true,
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'inner_bd_clr',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'border-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'inner_bd_bs',
						'not_empty' => true,
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'inner_bg',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'inner_br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}}.cursor-inner' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'dependency' => array(
						'element' => 'hover_effect',
						'value'   => array( 'plus' ),
					),
					'group'      => __( 'Cursor Inner', 'porto-functionality' ),
				),
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Cursor_Effect' ) ) {
		class WPBakeryShortCode_Porto_Cursor_Effect extends WPBakeryShortCode {
		}
	}
}
