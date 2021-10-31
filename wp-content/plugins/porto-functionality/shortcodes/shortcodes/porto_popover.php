<?php

// Porto Popover
add_action( 'vc_after_init', 'porto_load_popover_shortcode' );

function porto_load_popover_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Popover', 'porto-functionality' ),
			'base'        => 'porto_popover',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display the popover for particular purpose', 'porto-functionality' ),
			'icon'        => 'porto-sc Simple-Line-Icons-share-alt',
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Prefix', 'porto-functionality' ),
					'param_name' => 'prefix',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Text', 'porto-functionality' ),
					'param_name'  => 'text',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Suffix', 'porto-functionality' ),
					'param_name' => 'suffix',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Display Type', 'porto-functionality' ),
					'param_name' => 'display',
					'value'      => array(
						__( 'Inline', 'porto-functionality' ) => '',
						__( 'Block', 'porto-functionality' )  => 'block',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Text Type', 'porto-functionality' ),
					'param_name' => 'type',
					'value'      => array(
						__( 'Link', 'porto-functionality' )   => '',
						__( 'Button Link', 'porto-functionality' ) => 'btn-link',
						__( 'Button', 'porto-functionality' ) => 'btn',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Link', 'porto-functionality' ),
					'param_name' => 'link',
					'dependency' => array(
						'element' => 'type',
						'value'   => array( '', 'btn-link' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'value'      => porto_sh_commons( 'size' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn', 'btn-link' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Skin Color', 'porto-functionality' ),
					'param_name' => 'btn_skin',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn', 'btn-link' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Contextual Class', 'porto-functionality' ),
					'param_name' => 'btn_context',
					'value'      => porto_sh_commons( 'contextual' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn', 'btn-link' ),
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Popover Title', 'porto-functionality' ),
					'param_name'  => 'popover_title',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Popover Text', 'porto-functionality' ),
					'param_name'  => 'popover_text',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popover Position', 'porto-functionality' ),
					'param_name' => 'popover_position',
					'value'      => porto_sh_commons( 'position' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popover Trigger', 'porto-functionality' ),
					'param_name' => 'popover_trigger',
					'value'      => porto_sh_commons( 'trigger' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popover Skin Color', 'porto-functionality' ),
					'param_name' => 'popover_skin',
					'value'      => porto_sh_commons( 'colors' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Popover' ) ) {
		class WPBakeryShortCode_Porto_Popover extends WPBakeryShortCode {
		}
	}
}
