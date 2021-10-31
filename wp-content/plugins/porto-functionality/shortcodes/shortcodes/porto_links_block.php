<?php

// Porto Links Block
add_action( 'vc_after_init', 'porto_load_links_block_shortcode' );

function porto_load_links_block_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Links Block', 'porto-functionality' ),
			'base'            => 'porto_links_block',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Display the links', 'porto-functionality' ),
			'icon'            => 'fas fa-external-link-square-alt',
			'as_parent'       => array( 'except' => 'porto_links_block' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'value'       => 'Navigation',
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Icon', 'porto-functionality' ),
					'param_name' => 'show_icon',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library', 'js_composer' ),
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Custom Image Icon', 'porto-functionality' ) => 'image',
					),
					'param_name' => 'icon_type',
					'dependency' => array(
						'element'   => 'show_icon',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'image',
					),
					'param_name' => 'icon_image',
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'fontawesome',
					),
				),
				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon_simpleline',
					'value'      => '',
					'settings'   => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'simpleline',
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Links_Block' ) ) {
		class WPBakeryShortCode_Porto_Links_Block extends WPBakeryShortCodesContainer {
		}
	}
}
