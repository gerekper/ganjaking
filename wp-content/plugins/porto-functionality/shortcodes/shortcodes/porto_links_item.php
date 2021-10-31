<?php

// Porto Links Item
add_action( 'vc_after_init', 'porto_load_links_item_shortcode' );

function porto_load_links_item_shortcode() {
	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Links Item', 'porto-functionality' ),
			'base'        => 'porto_links_item',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display the links', 'porto-functionality' ),
			'icon'        => 'porto_vc_links_item',
			'as_child'    => array( 'only' => 'porto_links_block' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Label', 'porto-functionality' ),
					'param_name'  => 'label',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Link', 'porto-functionality' ),
					'param_name' => 'link',
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
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',

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

				array(
					'type'       => 'iconpicker',
					'heading'    => __( 'Select Icon', 'porto-functionality' ),
					'param_name' => 'icon_porto',
					'value'      => '',
					'settings'   => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => 'porto',
					),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Links_Item' ) ) {
		class WPBakeryShortCode_Porto_Links_Item extends WPBakeryShortCode {
		}
	}
}
