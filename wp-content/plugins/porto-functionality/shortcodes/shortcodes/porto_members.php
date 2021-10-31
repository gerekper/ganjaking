<?php

// Porto Members
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-members',
		array(
			'attributes'      => array(
				'title'              => array(
					'type' => 'string',
				),
				'style'              => array(
					'type'    => 'string',
					'default' => '',
				),
				'columns'            => array(
					'type'    => 'integer',
					'default' => 4,
				),
				'view'               => array(
					'type'    => 'string',
					'default' => 'classic',
				),
				'hover_image_effect' => array(
					'type'    => 'string',
					'default' => 'zoom',
				),
				'overview'           => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'socials'            => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'role'               => array(
					'type' => 'boolean',
				),
				'cats'               => array(
					'type' => 'string',
				),
				'post_in'            => array(
					'type' => 'string',
				),
				'number'             => array(
					'type'    => 'integer',
					'default' => 8,
				),
				'view_more'          => array(
					'type' => 'boolean',
				),
				'view_more_class'    => array(
					'type' => 'string',
				),
				'pagination'         => array(
					'type' => 'boolean',
				),
				'filter'             => array(
					'type' => 'boolean',
				),
				'ajax_load'          => array(
					'type' => 'boolean',
				),
				'ajax_modal'         => array(
					'type' => 'boolean',
				),
				'el_class'           => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_members',
		)
	);

	function porto_shortcode_members( $atts, $content = null ) {
		ob_start();
		if ( $template = porto_shortcode_template( 'porto_members' ) ) {
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
			}
			include $template;
		}
		return ob_get_clean();
	}
}

add_action( 'vc_after_init', 'porto_load_members_shortcode' );

function porto_load_members_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Members', 'porto-functionality' ),
			'base'        => 'porto_members',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show members by beautiful layout. e.g. masonry, slider, grid and so on', 'porto-functionality' ),
			'icon'        => 'far fa-user',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Style', 'porto-functionality' ),
					'param_name'  => 'style',
					'std'         => '',
					'value'       => array(
						__( 'Baisc', 'porto-functionality' ) => '',
						__( 'Advanced', 'porto-functionality' ) => 'advanced',
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Columns', 'porto-functionality' ),
					'param_name'  => 'columns',
					'std'         => '4',
					'value'       => porto_sh_commons( 'member_columns' ),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'View Type', 'porto-functionality' ),
					'param_name'  => 'view',
					'std'         => 'classic',
					'value'       => porto_sh_commons( 'member_view' ),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Hover Image Effect', 'porto-functionality' ),
					'param_name'  => 'hover_image_effect',
					'std'         => 'zoom',
					'value'       => porto_sh_commons( 'custom_zoom' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Overview', 'porto-functionality' ),
					'param_name' => 'overview',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'dependency' => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Social Links', 'porto-functionality' ),
					'param_name' => 'socials',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Category IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of category ids', 'porto-functionality' ),
					'param_name'  => 'cats',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Member IDs', 'porto-functionality' ),
					'description' => __( 'comma separated list of member ids', 'porto-functionality' ),
					'param_name'  => 'post_in',
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Members Count', 'porto-functionality' ),
					'param_name' => 'number',
					'value'      => '8',
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Role', 'porto-functionality' ),
					'param_name' => 'role',
					'dependency' => array(
						'element' => 'view',
						'value'   => 'outimage_cat',
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Extra class name for Archive Link', 'porto-functionality' ),
					'param_name' => 'view_more_class',
					'dependency' => array(
						'element'   => 'view_more',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Filter', 'porto-functionality' ),
					'param_name' => 'filter',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Pagination', 'porto-functionality' ),
					'param_name' => 'pagination',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Enable Ajax Load', 'porto-functionality' ),
					'param_name' => 'ajax_load',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Ajax Load on Modal', 'porto-functionality' ),
					'param_name' => 'ajax_modal',
					'dependency' => array(
						'element'   => 'ajax_load',
						'not_empty' => true,
					),
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Members' ) ) {
		class WPBakeryShortCode_Porto_Members extends WPBakeryShortCode {
		}
	}
}
