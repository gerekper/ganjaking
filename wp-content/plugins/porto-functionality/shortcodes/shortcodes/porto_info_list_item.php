<?php
// Porto info_list_item

add_action( 'vc_after_init', 'porto_load_info_list_item_shortcode' );

function porto_load_info_list_item_shortcode() {

	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'                    => __( 'Porto Info List Item', 'porto-functionality' ),
			'base'                    => 'porto_info_list_item',
			'class'                   => 'porto_info_list_item',
			'icon'                    => 'fas fa-tasks',
			'category'                => __( 'Porto', 'porto-functionality' ),
			'description'             => __( 'Text blocks connected together in one list.', 'porto-functionality' ),
			'as_child'                => array( 'only' => 'porto_info_list' ),
			'content_element'         => true,
			'show_settings_on_create' => true,
			'is_container'            => false,
			'params'                  => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon Type', 'porto-functionality' ),
					'param_name' => 'icon_type',
					'value'      => array(
						__( 'Font Awesome', 'porto-functionality' ) => 'fontawesome',
						__( 'Simple Line Icon', 'porto-functionality' ) => 'simpleline',
						__( 'Porto Icon', 'porto-functionality' ) => 'porto',
						__( 'Icon Image', 'porto-functionality' ) => 'image',
					),
				),
				array(
					'type'        => 'iconpicker',
					'class'       => '',
					'heading'     => __( 'Icon', 'porto-functionality' ),
					'param_name'  => 'list_icon',
					'description' => __( 'Select the icon.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome' ),
					),
				),
				array(
					'type'        => 'iconpicker',
					'class'       => '',
					'heading'     => __( 'Icon', 'porto-functionality' ),
					'param_name'  => 'list_icon_simpleline',
					'description' => __( 'Select the icon.', 'porto-functionality' ),
					'settings'    => array(
						'type'         => 'simpleline',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'simpleline' ),
					),
				),
				array(
					'type'        => 'iconpicker',
					'class'       => '',
					'heading'     => __( 'Icon', 'porto-functionality' ),
					'param_name'  => 'list_icon_porto',
					'description' => __( 'Select the icon.', 'porto-functionality' ),
					'settings'    => array(
						'type'         => 'porto',
						'iconsPerPage' => 4000,
					),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'porto' ),
					),
				),
				array(
					'type'        => 'attach_image',
					'class'       => '',
					'heading'     => __( 'Icon Image', 'porto-functionality' ),
					'param_name'  => 'list_icon_img',
					'description' => __( 'Select the icon image.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'icon_type',
						'value'   => array( 'image' ),
					),
				),
				array(
					'type'       => 'number',
					'class'      => '',
					'heading'    => __( 'Desc Font Size', 'porto-functionality' ),
					'param_name' => 'desc_font_size',
				),
				array(
					'type'             => 'textarea_html',
					'class'            => '',
					'heading'          => __( 'Description', 'porto-functionality' ),
					'param_name'       => 'content',
					'value'            => '',
					'description'      => __( 'Provide the description for this icon box.', 'porto-functionality' ),
					'edit_field_class' => 'vc_col-xs-12 vc_column wpb_el_type_textarea_html vc_wrapper-param-type-textarea_html vc_shortcode-param',
				),
				$custom_class,
			),
		)
	);

	class WPBakeryShortCode_porto_info_list_item extends WPBakeryShortCode {
	}

}
