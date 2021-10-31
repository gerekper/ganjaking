<?php

// Porto Experience Timeline Item
add_action( 'vc_after_init', 'porto_load_floating_menu_item_shortcode' );

function porto_load_floating_menu_item_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Floating Menu Item', 'porto-functionality' ),
			'base'        => 'porto_floating_menu_item',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show menu with floating', 'porto-functionality' ),
			'icon'        => 'fas fa-ellipsis-v',
			'as_child'    => array( 'only' => 'porto_floating_menu_container' ),
			'params'      => array(
				array(
					'type'        => 'iconpicker',
					'heading'     => __( 'Icon', 'porto-functionality' ),
					'param_name'  => 'icon_simpleline',
					'value'       => '',
					'settings'    => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'URL (Link)', 'porto-functionality' ),
					'param_name'  => 'link',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Tooltip text', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Floating_Menu_Item' ) ) {
		class WPBakeryShortCode_Porto_Floating_Menu_Item extends WPBakeryShortCode {

		}
	}
}
