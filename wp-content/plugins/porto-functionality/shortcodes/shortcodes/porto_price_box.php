<?php

// Porto Price Box
add_action( 'vc_after_init', 'porto_load_price_box_shortcode' );

function porto_load_price_box_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Price Box', 'porto-functionality' ),
			'base'        => 'porto_price_box',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Simple filled or outline pricing table to display price of your product or service', 'porto-functionality' ),
			'icon'        => 'fas fa-dollar-sign',
			'as_child'    => array( 'only' => 'porto_price_boxes' ),
			'params'      => array(
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Title', 'porto-functionality' ),
					'std'         => __( 'Professional', 'porto-functionality' ),
					'param_name'  => 'title',
					'admin_label' => true,
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Description', 'porto-functionality' ),
					'std'        => __( 'Most Popular', 'porto-functionality' ),
					'param_name' => 'desc',
				),
				array(
					'type'        => 'checkbox',
					'heading'     => __( 'Popular Price Box', 'porto-functionality' ),
					'description' => __( 'Choose to apply featured styling to the pricing box.', 'porto-functionality' ),
					'param_name'  => 'is_popular',
					'value'       => array( __( 'Yes, please', 'js_composer' ) => 'true' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Popular Label', 'porto-functionality' ),
					'param_name' => 'popular_label',
					'std'        => __( 'Popular', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'is_popular',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price', 'porto-functionality' ),
					'description' => __( 'Set the price.', 'porto-functionality' ),
					'param_name'  => 'price',
					'admin_label' => true,
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price Unit', 'porto-functionality' ),
					'description' => __( 'Set the curreny symbol if desired.', 'porto-functionality' ),
					'param_name'  => 'price_unit',
				),
				array(
					'type'        => 'textfield',
					'heading'     => __( 'Price Label', 'porto-functionality' ),
					'description' => __( 'For example, "Per Month"', 'porto-functionality' ),
					'param_name'  => 'price_label',
				),
				array(
					'type'       => 'textarea_html',
					'heading'    => __( 'Content', 'porto-functionality' ),
					'param_name' => 'content',
					'value'      => __(
						'<ul>
							<li><strong>5GB</strong> Disk Space</li>
							<li><strong>50GB</strong> Monthly Bandwidth</li>
							<li><strong>10</strong> Email Accounts</li>
							<li><strong>Unlimited</strong> subdomains</li>
						</ul>',
						'porto-functionality'
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Skin Color', 'porto-functionality' ),
					'param_name' => 'skin',
					'value'      => porto_sh_commons( 'colors' ),
				),
				array(
					'type'       => 'checkbox',
					'heading'    => __( 'Show Button', 'porto-functionality' ),
					'param_name' => 'show_btn',
					'value'      => array( __( 'Yes, please', 'js_composer' ) => 'true' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Button Label', 'porto-functionality' ),
					'std'        => __( 'Get In Touch', 'porto-functionality' ),
					'param_name' => 'btn_label',
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Action', 'porto-functionality' ),
					'param_name' => 'btn_action',
					'value'      => porto_sh_commons( 'popup_action' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'vc_link',
					'heading'    => __( 'URL (Link)', 'porto-functionality' ),
					'param_name' => 'btn_link',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'open_link' ),
					),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Video or Map URL (Link)', 'porto-functionality' ),
					'param_name' => 'popup_iframe',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_iframe' ),
					),
				),
				array(
					'type'        => 'textarea',
					'heading'     => __( 'Popup Block', 'porto-functionality' ),
					'param_name'  => 'popup_block',
					'description' => __( 'Please add block slug name.', 'porto-functionality' ),
					'dependency'  => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popup Size', 'porto-functionality' ),
					'param_name' => 'popup_size',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
					'value'      => array(
						__( 'Medium', 'porto-functionality' ) => 'md',
						__( 'Large', 'porto-functionality' )  => 'lg',
						__( 'Small', 'porto-functionality' )  => 'sm',
						__( 'Extra Small', 'porto-functionality' ) => 'xs',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Popup Animation', 'porto-functionality' ),
					'param_name' => 'popup_animation',
					'dependency' => array(
						'element' => 'btn_action',
						'value'   => array( 'popup_block' ),
					),
					'value'      => array(
						__( 'Fade', 'porto-functionality' ) => 'mfp-fade',
						__( 'Zoom', 'porto-functionality' ) => 'mfp-with-zoom',
						__( 'Fade Zoom', 'porto-functionality' ) => 'my-mfp-zoom-in',
						__( 'Fade Slide', 'porto-functionality' ) => 'my-mfp-slide-bottom',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Style', 'porto-functionality' ),
					'param_name' => 'btn_style',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'Outline', 'porto-functionality' ) => 'borders',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Size', 'porto-functionality' ),
					'param_name' => 'btn_size',
					'value'      => porto_sh_commons( 'size' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Position', 'porto-functionality' ),
					'param_name' => 'btn_pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => '',
						__( 'Bottom', 'porto-functionality' ) => 'bottom',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Button Skin Color', 'porto-functionality' ),
					'param_name' => 'btn_skin',
					'value'      => porto_sh_commons( 'colors' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'header_bg',
					'selectors'   => array(
						'{{WRAPPER}}.porto-price-box h3 strong' => 'background-color: {{VALUE}};',
					),
					'group'       => __( 'Header', 'porto-functionality' ),
					'qa_selector' => '.porto-price-box h3',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'header_padding',
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3 strong' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'      => __( 'Header', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Wrap Background Color', 'porto-functionality' ),
					'description' => __( 'Controls the background color including Price.', 'porto-functionality' ),
					'param_name'  => 'header_wrap_bg',
					'selectors'   => array(
						'{{WRAPPER}}.porto-price-box h3' => 'background-color: {{VALUE}};',
					),
					'group'       => __( 'Header', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Wrap Padding', 'porto-functionality' ),
					'param_name' => 'header_wrap_padding',
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'      => __( 'Header', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Title', 'porto-functionality' ),
					'param_name' => 'title_sz',
					'group'      => __( 'Header', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3 strong',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'title_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'group'      => __( 'Header', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3 strong' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Description', 'porto-functionality' ),
					'param_name' => 'desc_sz',
					'group'      => __( 'Header', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3 .desc',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'desc_color',
					'heading'    => __( 'Description Color', 'porto-functionality' ),
					'group'      => __( 'Header', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.porto-price-box h3 .desc' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'        => 'colorpicker',
					'param_name'  => 'price_bg',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'group'       => __( 'Pricing', 'porto-functionality' ),
					'selectors'   => array(
						'{{WRAPPER}} .plan-price' => 'background-color: {{VALUE}};',
					),
					'qa_selector' => '.plan-price',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'price_padding',
					'selectors'  => array(
						'{{WRAPPER}} .plan-price' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'      => __( 'Pricing', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Price', 'porto-functionality' ),
					'param_name' => 'price_sz',
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .plan-price .price',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'price_color',
					'heading'    => __( 'Price Color', 'porto-functionality' ),
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .plan-price .price' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Price Unit', 'porto-functionality' ),
					'param_name' => 'price_unit_sz',
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price-unit',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'price_unit_color',
					'heading'    => __( 'Unit Color', 'porto-functionality' ),
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price-unit' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Unit Position', 'porto-functionality' ),
					'param_name' => 'price_unit_pos',
					'value'      => array(
						__( 'Top', 'porto-functionality' ) => 'flex-start',
						__( 'Middle', 'porto-functionality' ) => 'center',
						__( 'Bottom', 'porto-functionality' ) => 'flex-end',
					),
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price' => 'align-items: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Price Label', 'porto-functionality' ),
					'param_name' => 'price_label_sz',
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price-label',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'price_label_color',
					'heading'    => __( 'Label Color', 'porto-functionality' ),
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price-label' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Spacing', 'porto-functionality' ),
					'param_name' => 'price_label_spacing',
					'units'      => array( 'px', 'em' ),
					'group'      => __( 'Pricing', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .price-label' => 'margin-top: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'content_bg',
					'selectors'   => array(
						'{{WRAPPER}}.plan ul' => 'background-color: {{VALUE}};',
					),
					'group'       => __( 'Content', 'porto-functionality' ),
					'qa_selector' => '.plan ul',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'content_padding',
					'selectors'  => array(
						'{{WRAPPER}}.plan ul' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'      => __( 'Content', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Content', 'porto-functionality' ),
					'param_name' => 'content_sz',
					'group'      => __( 'Content', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.plan ul',
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'content_color',
					'heading'    => __( 'Content Color', 'porto-functionality' ),
					'group'      => __( 'Content', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.plan li' => 'color: {{VALUE}};',
					),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Item Padding', 'porto-functionality' ),
					'param_name'  => 'content_item_padding',
					'selectors'   => array(
						'{{WRAPPER}}.plan.porto-price-box ul li' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'       => __( 'Content', 'porto-functionality' ),
					'qa_selector' => '.plan ul li:nth-child(2)',
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'content_item_br_color',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'group'      => __( 'Content', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.plan ul li' => 'border-color: {{VALUE}};',
					),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'content_item_br_width',
					'units'      => array( 'px', 'em' ),
					'group'      => __( 'Content', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}}.plan li' => 'border-top-width: {{VALUE}}{{UNIT}};',
						'.pricing-table-flat {{WRAPPER}}.plan-btn-bottom li:last-child' => 'border-bottom-width: {{VALUE}}{{UNIT}};',
						'.pricing-table-classic {{WRAPPER}}.plan li' => 'border-top-width: 0; border-bottom-width: {{VALUE}}{{UNIT}};',
					),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Button', 'porto-functionality' ),
					'param_name'  => 'button_sz',
					'group'       => __( 'Button', 'porto-functionality' ),
					'selectors'   => array(
						'{{WRAPPER}} .btn',
					),
					'dependency'  => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
					'qa_selector' => '.btn',
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Button Margin', 'porto-functionality' ),
					'param_name' => 'button_margin',
					'selectors'  => array(
						'{{WRAPPER}} .btn' => 'margin-top: {{TOP}};margin-right: {{RIGHT}};margin-bottom: {{BOTTOM}};margin-left: {{LEFT}};',
					),
					'group'      => __( 'Button', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Button Padding', 'porto-functionality' ),
					'param_name' => 'button_padding',
					'selectors'  => array(
						'{{WRAPPER}} .btn' => 'padding-top: {{TOP}};padding-right: {{RIGHT}};padding-bottom: {{BOTTOM}};padding-left: {{LEFT}};',
					),
					'group'      => __( 'Button', 'porto-functionality' ),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_text_color',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_br_color',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn' => 'border-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_background',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),

				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_text_hover_color',
					'heading'    => __( 'Hover Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn:hover' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_br_hover_color',
					'heading'    => __( 'Hover Border Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn:hover' => 'border-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'button_background_hover',
					'heading'    => __( 'Hover Background Color', 'porto-functionality' ),
					'group'      => __( 'Button', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .btn:hover' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'show_btn',
						'not_empty' => true,
					),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Ribbon', 'porto-functionality' ),
					'param_name'  => 'ribbon_sz',
					'group'       => __( 'Ribbon', 'porto-functionality' ),
					'selectors'   => array(
						'{{WRAPPER}} .plan-ribbon',
					),
					'dependency'  => array(
						'element'   => 'is_popular',
						'not_empty' => true,
					),
					'qa_selector' => '.plan-ribbon-wrapper',
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'ribbon_color',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'group'      => __( 'Ribbon', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .plan-ribbon' => 'color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'is_popular',
						'not_empty' => true,
					),
				),
				array(
					'type'       => 'colorpicker',
					'param_name' => 'ribbon_bg_color',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'group'      => __( 'Ribbon', 'porto-functionality' ),
					'selectors'  => array(
						'{{WRAPPER}} .plan-ribbon' => 'background-color: {{VALUE}};',
					),
					'dependency' => array(
						'element'   => 'is_popular',
						'not_empty' => true,
					),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Price_Box' ) ) {
		class WPBakeryShortCode_Porto_Price_Box extends WPBakeryShortCode {
		}
	}
}
