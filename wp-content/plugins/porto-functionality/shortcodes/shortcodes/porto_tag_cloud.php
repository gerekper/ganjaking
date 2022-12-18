<?php
/**
 * WPB Porto Tag Cloud Shortcodes
 *
 * @since 2.6.0
 */

add_action( 'vc_after_init', 'porto_load_tag_cloud_shortcode' );
function porto_load_tag_cloud_shortcode() {
	$custom_class = porto_vc_custom_class();
	$left         = is_rtl() ? 'right' : 'left';
	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Tag Cloud', 'porto-functionality' ),
			'base'        => 'porto_tag_cloud',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Show taxonomy as tag cloud.', 'porto-functionality' ),
			'icon'        => 'fas fa-tags',
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Taxonomy Name', 'porto-functionality' ),
					'param_name'  => 'taxonomy',
					'description' => __( 'Please input the tag taxonomy name. e.g: post_tag, product_tag and etc.', 'porto-functionality' ),
					'group'       => esc_html__( 'Taxonomy', 'porto-functionality' ),
					'admin_label' => true,
				),
				array(
					'type'       => 'checkbox',
					'heading'    => esc_html__( 'Show Count', 'porto-functionality' ),
					'param_name' => 'show_count',
					'group'      => esc_html__( 'Taxonomy', 'porto-functionality' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Hide Title', 'porto-functionality' ),
					'param_name' => 'hide_title',
					'value'      => array( __( 'Yes', 'js_composer' ) => 'yes' ),
					'selectors'  => array(
						'{{WRAPPER}} .widgettitle' => 'display:none;',
					),
					'group'      => esc_html__( 'Taxonomy', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'tag_font',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Font Size', 'porto-functionality' ),
					'param_name' => 'tag_size',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'font-size: {{VALUE}}{{UNIT}} !important;',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Margin', 'porto-functionality' ),
					'description' => __( 'Controls the margin of the each tag.', 'porto-functionality' ),
					'param_name'  => 'tag_margin',
					'selectors'   => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'margin: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'       => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Padding', 'porto-functionality' ),
					'description' => __( 'Controls the padding of the each tag.', 'porto-functionality' ),
					'param_name'  => 'tag_padding',
					'selectors'   => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'       => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Border', 'porto-functionality' ),
					'description' => __( 'Controls the border of the each tag.', 'porto-functionality' ),
					'param_name'  => 'tag_border',
					'selectors'   => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'border-top-width: {{TOP}};border-right-width: {{RIGHT}};border-bottom-width: {{BOTTOM}};border-left-width: {{LEFT}};border-style: solid;',
					),
					'group'       => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'tag_color',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'tag_bd_color',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'border-color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'tag_bg_color',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'background-color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Hover Color', 'porto-functionality' ),
					'param_name' => 'tag_color_hover',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a:hover' => 'color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Hover Color', 'porto-functionality' ),
					'param_name' => 'tag_bd_color_hover',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a:hover' => 'border-color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Hover Color', 'porto-functionality' ),
					'param_name' => 'tag_bg_color_hover',
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a:hover' => 'background-color: {{VALUE}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'tag_br',
					'units'      => array( 'px', '%' ),
					'selectors'  => array(
						'{{WRAPPER}} .widget .tagcloud a' => 'border-radius: {{VALUE}}{{UNIT}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Bottom Spacing', 'porto-functionality' ),
					'param_name' => 'tag_mb',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .widget_tag_cloud' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'      => esc_html__( 'Tag Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Between Spacing', 'porto-functionality' ),
					'param_name' => 'tag_between',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .tag-link-count' => "margin-{$left}: {{VALUE}}{{UNIT}};",
					),
					'group'      => esc_html__( 'Tag Count Style', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'show_count',
						'not_empty' => true,
					),
				),
				$custom_class,
			),
		)
	);
}
