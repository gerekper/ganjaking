<?php

// Porto Recent Members
if ( function_exists( 'register_block_type' ) ) {
	register_block_type(
		'porto/porto-recent-members',
		array(
			'attributes'      => array(
				'title'              => array(
					'type' => 'string',
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
				'socials_style'      => array(
					'type'    => 'boolean',
					'default' => true,
				),
				'spacing'            => array(
					'type' => 'integer',
				),
				'items'              => array(
					'type' => 'integer',
				),
				'items_desktop'      => array(
					'type'    => 'integer',
					'default' => 4,
				),
				'items_tablets'      => array(
					'type'    => 'integer',
					'default' => 3,
				),
				'items_mobile'       => array(
					'type'    => 'integer',
					'default' => 2,
				),
				'items_row'          => array(
					'type'    => 'integer',
					'default' => 1,
				),
				'cats'               => array(
					'type' => 'string',
				),
				'number'             => array(
					'type'    => 'integer',
					'default' => 8,
				),
				'ajax_load'          => array(
					'type' => 'boolean',
				),
				'ajax_modal'         => array(
					'type' => 'boolean',
				),
				'slider_config'      => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_nav'           => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'show_nav_hover'     => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'nav_pos'            => array(
					'type'    => 'string',
					'default' => '',
				),
				'nav_pos2'           => array(
					'type' => 'string',
				),
				'nav_type'           => array(
					'type' => 'string',
				),
				'show_dots'          => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'dots_pos'           => array(
					'type' => 'string',
				),
				'dots_style'         => array(
					'type' => 'string',
				),
				'autoplay'           => array(
					'type'    => 'boolean',
					'default' => false,
				),
				'autoplay_timeout'   => array(
					'type'    => 'integer',
					'default' => 5000,
				),
				'el_class'           => array(
					'type' => 'string',
				),
			),
			'editor_script'   => 'porto_blocks',
			'render_callback' => 'porto_shortcode_recent_members',
		)
	);

	function porto_shortcode_recent_members( $atts, $content = null ) {
		ob_start();
		if ( $template = porto_shortcode_template( 'porto_recent_members' ) ) {
			if ( isset( $atts['className'] ) ) {
				$atts['el_class'] = $atts['className'];
			}
			include $template;
		}
		return ob_get_clean();
	}
}

add_action( 'vc_after_init', 'porto_load_recent_members_shortcode' );

function porto_load_recent_members_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Recent Members', 'porto-functionality' ),
			'base'        => 'porto_recent_members',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show members by slider', 'porto-functionality' ),
			'icon'        => 'fas fa-users',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'View Type', 'porto-functionality' ),
					'param_name'  => 'view',
					'std'         => 'classic',
					'value'       => porto_sh_commons( 'member_view' ),
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
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Social Links', 'porto-functionality' ),
					'param_name' => 'socials',
					'std'        => 'yes',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Social Links Advance Style', 'porto-functionality' ),
					'param_name' => 'socials_style',
					'std'        => 'yes',
					'dependency' => array(
						'element'   => 'socials',
						'not_empty' => true,
					),
					//'value' => array( __( 'Yes', 'js_composer' ) => 'yes' )
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
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Members Count', 'porto-functionality' ),
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
					'type'        => 'textfield',
					'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
					'description' => __( 'Default is 25px', 'porto-functionality' ),
					'param_name'  => 'spacing',
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
					'type'       => 'number',
					'heading'    => __( 'Stage Padding (px)', 'porto-functionality' ),
					'param_name' => 'stage_padding',
					'value'      => '',
					'group'      => __( 'Slider Options', 'porto-functionality' ),
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
						__( 'Middle Inside', 'porto-functionality' ) => 'nav-pos-inside',
						__( 'Middle Outside', 'porto-functionality' ) => 'nav-pos-outside',
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
						'value'   => array( '', 'nav-pos-inside', 'nav-pos-outside', 'nav-bottom' ),
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
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Recent_Members' ) ) {
		class WPBakeryShortCode_Porto_Recent_Members extends WPBakeryShortCode {
		}
	}
}
