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
					'type'        => 'number',
					'heading'     => __( 'Column Spacing (px)', 'porto-functionality' ),
					'description' => __( 'Leave blank if you use theme default value.', 'porto-functionality' ),
					'param_name'  => 'spacing',
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'suffix'      => 'px',
					'selectors'   => array(
						'{{WRAPPER}} .members-container' => '--porto-el-spacing: {{VALUE}}px;--bs-gutter-x: {{VALUE}}px;',
					),
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
					'description' => __( 'Controls the hover effect of image.', 'porto' ),
					'std'         => 'zoom',
					'value'       => porto_sh_commons( 'custom_zoom' ),
					'dependency'  => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
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
					'heading'    => __( 'Members Count (per page)', 'porto-functionality' ),
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
					'heading'    => __( 'Show Category Filter', 'porto-functionality' ),
					'param_name' => 'filter',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Filter Style', 'porto-functionality' ),
					'param_name' => 'filter_style',
					'std'        => '',
					'value'      => array(
						__( 'Style 1', 'porto-functionality' ) => '',
						__( 'Style 2', 'porto-functionality' ) => 'style-2',
						__( 'Style 3', 'porto-functionality' ) => 'style-3',
					),
					'dependency' => array(
						'element'   => 'filter',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Filter Type', 'porto-functionality' ),
					'param_name' => 'filter_type',
					'std'        => '',
					'value'      => array(
						__( 'Filter using Javascript/CSS', 'porto-functionality' ) => '',
						__( 'Ajax Loading', 'porto-functionality' ) => 'ajax',
					),
					'dependency' => array(
						'element'   => 'filter',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Pagination Style', 'porto-functionality' ),
					'param_name' => 'pagination',
					'std'        => '',
					'value'      => array(
						__( 'None', 'porto-functionality' ) => '',
						__( 'Ajax Pagination', 'porto-functionality' ) => 'yes',
						__( 'Infinite Scroll', 'porto-functionality' ) => 'infinite',
						__( 'Load More (Button)', 'porto-functionality' ) => 'load_more',
					),
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Enable Ajax Load', 'porto-functionality' ),
					'param_name'  => 'ajax_load',
					'description' => __( 'If enabled, member content should be displayed at the top of members or on modal when you click member item in the list.', 'porto-functionality' ),
					'param_name'  => 'ajax_load',
					'value'       => array( __( 'Yes', 'js_composer' ) => 'yes' ),
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

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'title_style',
					'text'       => __( 'Name', 'porto-functionality' ),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'title_tg',
					'selectors'   => array(
						'{{WRAPPER}} .entry-title strong, {{WRAPPER}} .thumb-info .thumb-info-title, {{WRAPPER}} .member-name',
					),
					'qa_selector' => '.member:first-child .entry-title strong, .member:first-child .thumb-info-title, .member:first-child .member-name',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'title_clr',
					'selectors'  => array(
						'{{WRAPPER}} .entry-title, {{WRAPPER}} .thumb-info .thumb-info-title, {{WRAPPER}} .member-name' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'title_pd',
					'selectors'  => array(
						'{{WRAPPER}} .entry-title, {{WRAPPER}} .thumb-info .thumb-info-title, {{WRAPPER}} .member-name' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'title_bgc',
					'dependency' => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info .thumb-info-title' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
					'param_name' => 'title_bgc_hover',
					'dependency' => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info:hover .thumb-info-title' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'cats_style',
					'text'       => __( 'Categories', 'porto-functionality' ),
					'dependency' => array(
						'element' => 'view',
						'value'   => 'outimage_cat',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'cats_tg',
					'selectors'   => array(
						'{{WRAPPER}} .member-cats',
					),
					'dependency'  => array(
						'element' => 'view',
						'value'   => 'outimage_cat',
					),
					'qa_selector' => '.member:first-child .member-cats',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'cats_clr',
					'selectors'  => array(
						'{{WRAPPER}} .member-cats' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element' => 'view',
						'value'   => 'outimage_cat',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'meta_style',
					'text'       => __( 'Role', 'porto-functionality' ),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'meta_tg',
					'selectors'   => array(
						'{{WRAPPER}} .thumb-info-type, {{WRAPPER}} .member-role',
					),
					'qa_selector' => '.member:first-child .thumb-info-type,.member:first-child .member-role',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'meta_clr',
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info-type, {{WRAPPER}} .member-role' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'meta_pd',
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info-type, {{WRAPPER}} .member-role' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'meta_bgc',
					'dependency' => array(
						'element' => 'style',
						'value'   => array( '' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info-type' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'desc_style',
					'text'       => __( 'Description', 'porto-functionality' ),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Typography', 'porto-functionality' ),
					'param_name'  => 'desc_tg',
					'selectors'   => array(
						'{{WRAPPER}} .thumb-info-caption-text, {{WRAPPER}} .thumb-info-caption-text p, {{WRAPPER}} .member-overview p',
					),
					'qa_selector' => '.member:first-child .thumb-info-caption-text,.member:first-child .member-overview p',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'desc_clr',
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info-caption-text, {{WRAPPER}} .member-overview p' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'desc_pd',
					'selectors'  => array(
						'{{WRAPPER}} .thumb-info-caption-text, {{WRAPPER}} .member-overview p' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'social_icons_style',
					'text'       => __( 'Social Icons', 'porto-functionality' ),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Icon Font Size', 'porto-functionality' ),
					'param_name'  => 'icon_fs',
					'units'       => array( 'px', 'rem', 'em' ),
					'selectors'   => array(
						'{{WRAPPER}} .share-links a' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'qa_selector' => '.member:first-child .share-links',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Icon Width and Height', 'porto-functionality' ),
					'param_name' => 'icon_width',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} .share-links a' => 'width: {{VALUE}}{{UNIT}};height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'icon_color',
					'selectors'  => array(
						'{{WRAPPER}} .share-links a:not(:hover)' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'icon_color_bg',
					'selectors'  => array(
						'{{WRAPPER}} .share-links a:not(:hover)' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_boxshadow',
					'heading'    => __( 'Box Shadow', 'porto-functionality' ),
					'param_name' => 'icon_box_shadow',
					'unit'       => 'px',
					'positions'  => array(
						__( 'Horizontal', 'porto-functionality' ) => '',
						__( 'Vertical', 'porto-functionality' ) => '',
						__( 'Blur', 'porto-functionality' )   => '',
						__( 'Spread', 'porto-functionality' ) => '',
					),
					'selectors'  => array(
						'{{WRAPPER}} .share-links a',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Color', 'porto-functionality' ),
					'param_name' => 'icon_hover_color',
					'selectors'  => array(
						'{{WRAPPER}} .share-links a:hover' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
					'param_name' => 'icon_hover_color_bg',
					'selectors'  => array(
						'{{WRAPPER}} .share-links a:hover' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_boxshadow',
					'heading'    => __( 'Hover Box Shadow', 'porto-functionality' ),
					'param_name' => 'icon_box_shadow_hover',
					'unit'       => 'px',
					'positions'  => array(
						__( 'Horizontal', 'porto-functionality' ) => '',
						__( 'Vertical', 'porto-functionality' ) => '',
						__( 'Blur', 'porto-functionality' )   => '',
						__( 'Spread', 'porto-functionality' ) => '',
					),
					'selectors'  => array(
						'{{WRAPPER}} .share-links a:hover',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),

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
