<?php

// Porto Experience Timeline Container
add_action( 'vc_after_init', 'porto_load_floating_menu_container_shortcode' );

function porto_load_floating_menu_container_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => 'Porto ' . __( 'Floating Menu Container', 'porto-functionality' ),
			'base'                    => 'porto_floating_menu_container',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Show menu with floating', 'porto-functionality' ),
			'icon'                    => 'fas fa-ellipsis-v',
			'as_parent'               => array( 'only' => 'porto_floating_menu_item' ),
			'content_element'         => true,
			'show_settings_on_create' => false,
			'controls'                => 'full',
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Floating_Menu_Container' ) ) {
		class WPBakeryShortCode_Porto_Floating_Menu_Container extends WPBakeryShortCodesContainer {
		}
	}
}
