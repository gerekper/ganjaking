<?php
// Porto Call To Action Box

add_action( 'vc_after_init', 'porto_load_carousel_logo_shortcode' );

function porto_load_carousel_logo_shortcode() {

	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Carousel Logo', 'porto-functionality' ),
			'base'        => 'porto_carousel_logo',
			'class'       => 'porto_carousel_logo',
			'icon'        => 'far fa-circle',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display your partners with a simple logo slider', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Logo', 'porto-functionality' ),
					'param_name' => 'logo_img',
				),
				array(
					'type'       => 'attach_image',
					'heading'    => __( 'Logo on hover', 'porto-functionality' ),
					'param_name' => 'logo_hover_img',
				),
				array(
					'type'        => 'textarea_html',
					'class'       => '',
					'heading'     => __( 'Text ', 'porto-functionality' ),
					'param_name'  => 'content',
					'admin_label' => true,
					'value'       => '',
				),
				$custom_class,
			),
		)
	);

	if ( class_exists( 'WPBakeryShortCode' ) ) {
		class WPBakeryShortCode_porto_carousel_logo extends WPBakeryShortCode {
		}
	}
}
