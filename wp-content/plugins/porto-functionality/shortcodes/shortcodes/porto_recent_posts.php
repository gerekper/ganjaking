<?php

// Porto Recent Posts
add_action( 'vc_after_init', 'porto_load_recent_posts_shortcode' );

function porto_load_recent_posts_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Recent Posts', 'porto-functionality' ),
			'base'        => 'porto_recent_posts',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show posts by slider', 'porto-functionality' ),
			'icon'        => 'porto-sc Simple-Line-Icons-docs',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'View Type', 'porto-functionality' ),
					'param_name' => 'view',
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' )   => '',
						__( 'Read More Link', 'porto-functionality' ) => 'style-1',
						__( 'Post Meta', 'porto-functionality' )  => 'style-2',
						__( 'Read More Button', 'porto-functionality' ) => 'style-3',
						__( 'Side Image', 'porto-functionality' ) => 'style-4',
						__( 'Post Cats', 'porto-functionality' )  => 'style-5',
						__( 'Post Author with photo', 'porto-functionality' ) => 'style-7',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Author Name', 'porto-functionality' ),
					'param_name' => 'author',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-1', 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Show', 'porto-functionality' ) => 'show',
						__( 'Hide', 'porto-functionality' ) => 'hide',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Style', 'porto-functionality' ),
					'param_name' => 'btn_style',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Normal', 'porto-functionality' ) => 'btn-normal',
						__( 'Borders', 'porto-functionality' ) => 'btn-borders',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Normal', 'porto-functionality' ) => 'btn-normal',
						__( 'Small', 'porto-functionality' )  => 'btn-sm',
						__( 'Extra Small', 'porto-functionality' ) => 'btn-xs',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Color', 'porto-functionality' ),
					'param_name' => 'btn_color',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( 'style-3' ),
					),
					'std'        => '',
					'value'      => array(
						__( 'Standard', 'porto-functionality' ) => '',
						__( 'Default', 'porto-functionality' ) => 'btn-default',
						__( 'Primary', 'porto-functionality' ) => 'btn-primary',
						__( 'Secondary', 'porto-functionality' ) => 'btn-secondary',
						__( 'Tertiary', 'porto-functionality' ) => 'btn-tertiary',
						__( 'Quaternary', 'porto-functionality' ) => 'btn-quaternary',
						__( 'Dark', 'porto-functionality' )  => 'btn-dark',
						__( 'Light', 'porto-functionality' ) => 'btn-light',
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Posts Count', 'porto-functionality' ),
					'param_name'  => 'number',
					'value'       => '8',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Post Image', 'porto-functionality' ),
					'param_name' => 'show_image',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Image Size', 'porto-functionality' ),
					'param_name' => 'image_size',
					'value'      => porto_sh_commons( 'image_sizes' ),
					'std'        => '',
					'dependency' => array(
						'element'   => 'show_image',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Post Metas', 'porto-functionality' ),
					'param_name' => 'show_metas',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'std'        => 'yes',
					'dependency' => array(
						'element' => 'view',
						'value'   => array( '', 'style-1', 'style-2', 'style-3', 'style-4', 'style-5' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Excerpt Length', 'porto-functionality' ),
					'param_name' => 'excerpt_length',
					'value'      => '20',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Large Desktop', 'porto-functionality' ),
					'param_name' => 'items',
					'value'      => '',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Desktop', 'porto-functionality' ),
					'param_name' => 'items_desktop',
					'value'      => '4',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Tablets', 'porto-functionality' ),
					'param_name' => 'items_tablets',
					'value'      => '3',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items to show on Mobile', 'porto-functionality' ),
					'param_name' => 'items_mobile',
					'value'      => '2',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Items Row', 'porto-functionality' ),
					'param_name' => 'items_row',
					'value'      => '1',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Change Slider Config', 'porto-functionality' ),
					'param_name' => 'slider_config',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav', 'porto-functionality' ),
					'param_name' => 'show_nav',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Position', 'porto-functionality' ),
					'param_name' => 'nav_pos',
					'value'      => array(
						__( 'Middle', 'porto-functionality' ) => '',
						__( 'Top', 'porto-functionality' ) => 'show-nav-title',
						__( 'Bottom', 'porto-functionality' ) => 'nav-bottom',
					),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Nav Type', 'porto-functionality' ),
					'param_name' => 'nav_type',
					'value'      => porto_sh_commons( 'carousel_nav_types' ),
					'dependency' => array(
						'element' => 'nav_pos',
						'value'   => array( '', 'nav-bottom' ),
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Nav on Hover', 'porto-functionality' ),
					'param_name' => 'show_nav_hover',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'show_nav',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Dots', 'porto-functionality' ),
					'param_name' => 'show_dots',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element'   => 'slider_config',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Position', 'porto-functionality' ),
					'param_name' => 'dots_pos',
					'std'        => '',
					'value'      => array(
						__( 'Bottom', 'porto-functionality' ) => '',
						__( 'Top beside title', 'porto-functionality' ) => 'show-dots-title',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Dots Style', 'porto-functionality' ),
					'param_name' => 'dots_style',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Circle inner dot', 'porto-functionality' ) => 'dots-style-1',
					),
					'dependency' => array(
						'element'   => 'show_dots',
						'not_empty' => true,
					),
					'group'      => __( 'Slider Options', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Recent_Posts' ) ) {
		class WPBakeryShortCode_Porto_Recent_Posts extends WPBakeryShortCode {
		}
	}
}
