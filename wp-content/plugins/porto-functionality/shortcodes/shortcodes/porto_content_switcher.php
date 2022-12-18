<?php
/**
 * Porto Content Switcher Shortcode
 *
 * @since 2.6.0
 */
add_action( 'vc_after_init', 'porto_load_content_switcher_shortcode' );

function porto_load_content_switcher_shortcode() {

	$custom_class = porto_vc_custom_class();

	vc_map(
		array(
			'name'        => __( 'Porto Content Switcher', 'porto-functionality' ),
			'base'        => 'porto_content_switcher',
			'class'       => 'porto_content_switcher',
			'icon'        => 'fas fa-toggle-off',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Toggle element for block content', 'porto-functionality' ),
			'params'      => array(
				array(
					'type'       => 'textfield',
					'heading'    => __( 'First Label', 'porto-functionality' ),
					'param_name' => 'first_label',
					'value'      => '',
				),
				array(
					'type'       => 'textarea_raw_html',
					'heading'    => __( 'Enter your html for first content', 'porto-functionality' ),
					'param_name' => 'first_content',
					'value'      => base64_encode( 'Pellentesque pellentesque tempor tellus eget hendrerit. Morbi id aliquam ligula.' ),
				),
				array(
					'type'       => 'textfield',
					'heading'    => __( 'Second Label', 'porto-functionality' ),
					'param_name' => 'second_label',
					'value'      => '',
				),
				array(
					'type'       => 'textarea_raw_html',
					'heading'    => __( 'Enter your html for second content', 'porto-functionality' ),
					'param_name' => 'second_content',
					'value'      => base64_encode( 'Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos.' ),
				),
				array(
					'type'       => 'porto_button_group',
					'heading'    => __( 'Switch Align', 'porto-functionality' ),
					'param_name' => 'switch_align',
					'value'      => array(
						'flex-start' => array(
							'title' => __( 'Left', 'porto-functionality' ),
							'icon'  => 'fas fa-align-left',
							'label' => __( 'Left', 'porto-functionality' ),
						),
						'center'     => array(
							'title' => __( 'Center', 'porto-functionality' ),
							'icon'  => 'fas fa-align-center',
							'label' => __( 'Center', 'porto-functionality' ),
						),
						'flex-end'   => array(
							'title' => __( 'Right', 'porto-functionality' ),
							'icon'  => 'fas fa-align-right',
							'label' => __( 'Right', 'porto-functionality' ),
						),
					),
					'std'        => 'center',
					'selectors'  => array(
						'{{WRAPPER}}.content-switcher-wrapper .content-switch' => 'justify-content: {{VALUE}};',
					),
					'group'      => __( 'Options', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Content Padding', 'porto' ),
					'param_name'  => 'content_padding',
					'selectors'   => array(
						'{{WRAPPER}}.content-switcher-wrapper .tab-content' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'qa_selector' => '.tab-content',
					'group'       => __( 'Options', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_typography',
					'heading'     => __( 'Label Typography', 'porto' ),
					'param_name'  => 'label_typography',
					'selectors'   => array(
						'{{WRAPPER}} .switcher-label',
					),
					'qa_selector' => '.text-first',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Label Spacing', 'porto' ),
					'param_name' => 'label_spacing',
					'units'      => array( 'px', 'rem' ),
					'selectors'  => array(
						'{{WRAPPER}} .switch-input' => 'margin-left: {{VALUE}}{{UNIT}}; margin-right: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Switch Size', 'porto' ),
					'param_name'  => 'switch_size',
					'units'       => array( 'px', 'em', 'rem' ),
					'selectors'   => array(
						'{{WRAPPER}} .switch-input' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'qa_selector' => '.switch-input',
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Label Color', 'porto-functionality' ),
					'param_name'  => 'label_color',
					'selectors'   => array(
						'{{WRAPPER}} .switcher-label' => 'color: {{VALUE}};',
					),
					'description' => __( 'Control the color for switch label.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Label Active Color', 'porto-functionality' ),
					'param_name'  => 'label_acolor',
					'selectors'   => array(
						'{{WRAPPER}} .switcher-label.active' => 'color: {{VALUE}};',
					),
					'description' => __( 'Control the active color for switch label.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Switch Color', 'porto-functionality' ),
					'param_name'  => 'switch_color',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .toggle-button:before' => 'background-color: {{VALUE}};',
					),
					'description' => __( 'Control the color for switcher.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Switch Active Color', 'porto-functionality' ),
					'param_name'  => 'switch_acolor',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .switch-toggle:checked+.toggle-button:before' => 'background-color: {{VALUE}};',
					),
					'description' => __( 'Control the active color for switcher.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'param_name'  => 'switch_background_color',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .toggle-button' => 'background-color: {{VALUE}};',
					),
					'description' => __( 'Control the background color for switcher.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Active Color', 'porto-functionality' ),
					'param_name'  => 'switch_background_acolor',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .switch-toggle:checked+.toggle-button' => 'background-color: {{VALUE}};',
					),
					'description' => __( 'Control the active background color for switcher.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Color', 'porto-functionality' ),
					'param_name'  => 'switch_border_color',
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .toggle-button' => 'border-color: {{VALUE}};',
					),
					'description' => __( 'Control the border color for switcher.', 'porto-functionality' ),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Border Active Color', 'porto-functionality' ),
					'param_name'  => 'switch_border_acolor',
					'description' => __( 'Control the active border color for switcher.', 'porto-functionality' ),
					'selectors'   => array(
						'{{WRAPPER}} .switch-input .switch-toggle:checked+.toggle-button' => 'border-color: {{VALUE}};',
					),
					'group'       => __( 'Style', 'porto-functionality' ),
				),
				$custom_class,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Content_Switcher' ) ) {
		class WPBakeryShortCode_Porto_Content_Switcher extends WPBakeryShortCode {
		}
	}
}
