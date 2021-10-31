<?php

// Porto Feature Box
add_action( 'vc_after_init', 'porto_load_feature_box_shortcode' );

function porto_load_feature_box_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'            => 'Porto ' . __( 'Feature Box', 'porto-functionality' ),
			'base'            => 'porto_feature_box',
			'category'        => __( 'Porto', 'porto-functionality' ),
			'description'     => __( 'Show boxes with icon and description', 'porto-functionality' ),
			'icon'            => 'far fa-check-circle',
			'as_parent'       => array( 'except' => 'porto_feature_box' ),
			'content_element' => true,
			'controls'        => 'full',
			//'is_container' => true,
			'js_view'         => 'VcColumnView',
			'params'          => array(
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Skin Color', 'porto-functionality' ),
					'param_name'  => 'skin',
					'std'         => 'custom',
					'value'       => porto_sh_commons( 'colors' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Icon', 'porto-functionality' ),
					'param_name' => 'show_icon',
					'value'      => array( __( 'Yes, please', 'porto-functionality' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Icon library', 'porto-functionality' ),
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
				array(
					'type'       => 'number',
					'heading'    => __( 'Icon Size', 'porto-functionality' ),
					'param_name' => 'icon_size',
					'value'      => 14,
					'min'        => 12,
					'max'        => 72,
					'suffix'     => 'px',
					'dependency' => array(
						'element' => 'icon_type',
						'value'   => array( 'fontawesome', 'simpleline', 'porto' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Box Style', 'porto-functionality' ),
					'param_name'  => 'box_style',
					'value'       => porto_sh_commons( 'feature_box_style' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Box Direction', 'porto-functionality' ),
					'param_name' => 'box_dir',
					'value'      => porto_sh_commons( 'feature_box_dir' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Feature_Box' ) ) {
		class WPBakeryShortCode_Porto_Feature_Box extends WPBakeryShortCodesContainer {
		}
	}
}
