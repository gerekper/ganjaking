<?php

// Porto Blockquote
add_action( 'vc_after_init', 'porto_load_blockquote_shortcode' );

function porto_load_blockquote_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Blockquote', 'porto-functionality' ),
			'base'        => 'porto_blockquote',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show blockquote with border', 'porto-functionality' ),
			'icon'        => 'fas fa-quote-left',
			'params'      => array(
				array(
					'type'        => 'textarea_html',
					'heading'     => __( 'Quote', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Footer Text 1', 'porto-functionality' ),
					'param_name' => 'footer_before',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Footer Text 2', 'porto-functionality' ),
					'param_name' => 'footer_after',
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'With Borders', 'porto-functionality' ) => 'with-borders',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Direction', 'porto-functionality' ),
					'param_name' => 'dir',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Reverse', 'porto-functionality' ) => 'blockquote-reverse',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'std'        => 'custom',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => array( '' ),
					),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'color',
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

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Blockquote' ) ) {
		class WPBakeryShortCode_Porto_Blockquote extends WPBakeryShortCode {
		}
	}
}
