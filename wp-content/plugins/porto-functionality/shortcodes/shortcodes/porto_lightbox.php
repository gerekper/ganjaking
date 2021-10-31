<?php

// Porto Lightbox
add_action( 'vc_after_init', 'porto_load_lightbox_shortcode' );

function porto_load_lightbox_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Lightbox', 'porto-functionality' ),
			'base'            => 'porto_lightbox',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Display the lightbox', 'porto-functionality' ),
			'icon'            => 'fas fa-clone',
			'content_element' => true,
			'controls'        => 'full',
			'is_container'    => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
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
						__( 'Button', 'porto-functionality' ) => 'btn',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'value'      => porto_sh_commons( 'size' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Skin Color', 'porto-functionality' ),
					'param_name' => 'btn_skin',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Contextual Class', 'porto-functionality' ),
					'param_name' => 'btn_context',
					'value'      => porto_sh_commons( 'contextual' ),
					'dependency' => array(
						'element' => 'type',
						'value'   => array( 'btn' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Lightbox Type', 'porto-functionality' ),
					'param_name' => 'lightbox_type',
					'value'      => array(
						__( 'Content', 'porto-functionality' ) => '',
						__( 'Video or Google Map', 'porto-functionality' ) => 'iframe',
						__( 'Ajax', 'porto-functionality' ) => 'ajax',
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Video or Google Map Url', 'porto-functionality' ),
					'param_name' => 'iframe_url',
					'dependency' => array(
						'element' => 'lightbox_type',
						'value'   => array( 'iframe' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Ajax Url', 'porto-functionality' ),
					'param_name' => 'ajax_url',
					'dependency' => array(
						'element' => 'lightbox_type',
						'value'   => array( 'ajax' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Lightbox Animation', 'porto-functionality' ),
					'param_name' => 'lightbox_animation',
					'dependency' => array(
						'element' => 'lightbox_type',
						'value'   => array( '' ),
					),
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Fade Zoom', 'porto-functionality' ) => 'zoom-anim',
						__( 'Fade Slide', 'porto-functionality' ) => 'move-anim',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Lightbox Size', 'porto-functionality' ),
					'param_name' => 'lightbox_size',
					'dependency' => array(
						'element' => 'lightbox_type',
						'value'   => array( '' ),
					),
					'value'      => array(
						__( 'Normal', 'porto-functionality' ) => '',
						__( 'Large', 'porto-functionality' )  => 'lg',
						__( 'Small', 'porto-functionality' )  => 'sm',
						__( 'Extra Small', 'porto-functionality' ) => 'xs',
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Lightbox' ) ) {
		class WPBakeryShortCode_Porto_Lightbox extends WPBakeryShortCodesContainer {
		}
	}
}
