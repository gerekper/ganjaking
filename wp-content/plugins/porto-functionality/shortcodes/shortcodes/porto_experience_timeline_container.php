<?php

// Porto Experience Timeline Container
add_action( 'vc_after_init', 'porto_load_experience_timeline_container_shortcode' );

function porto_load_experience_timeline_container_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => 'Porto ' . __( 'Experience Timeline Container', 'porto-functionality' ),
			'base'                    => 'porto_experience_timeline_container',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Show events or posts by timeline layouts', 'porto-functionality' ),
			'icon'                    => 'fas fa-list-ul',
			'as_parent'               => array( 'only' => 'porto_experience_timeline_item' ),
			'content_element'         => true,
			'show_settings_on_create' => false,
			'controls'                => 'full',
			'js_view'                 => 'VcColumnView',
			'params'                  => array(
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Experience_Timeline_Container' ) ) {
		class WPBakeryShortCode_Porto_Experience_Timeline_Container extends WPBakeryShortCodesContainer {
		}
	}
}
