<?php

/**
 * Porto Contact form widget
 *
 * @since 2.4.0
 */

add_action( 'vc_after_init', 'porto_load_contact_form_shortcode' );

function porto_load_contact_form_shortcode() {
	$animation_type     = porto_vc_animation_type();
	$animation_duration = porto_vc_animation_duration();
	$animation_delay    = porto_vc_animation_delay();
	$custom_class       = porto_vc_custom_class();

	$contact_forms = array();
	$wpforms       = array();
	if ( is_admin() && function_exists( 'porto_get_post_type_items' ) ) {
		$contact_forms = porto_get_post_type_items( 'wpcf7_contact_form', array(), false );
		$wpforms       = porto_get_post_type_items( 'wpforms', array(), false );
	}

	vc_map(
		array(
			'name'        => 'Porto ' . __( 'Contact Form', 'porto-functionality' ),
			'base'        => 'porto_contact_form',
			'category'    => __( 'Porto', 'porto-functionality' ),
			'description' => __( 'Display contact form built using WPForms Lite and Contact Form 7.', 'porto-functionality' ),
			'icon'        => 'far fa-envelope-open',
			'params'      => array(
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Source', 'porto-functionality' ),
					'param_name' => 'source',
					'value'      => array(
						__( 'Contact Form 7', 'porto-functionality' ) => '',
						__( 'WPForms Lite', 'porto-functionality' ) => 'wpforms',
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Contact Forms', 'porto-functionality' ),
					'param_name'  => 'cf7_form',
					'value'       => array_flip( $contact_forms ),
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'source',
						'value'   => array( '' ),
					),
				),
				array(
					'type'        => 'dropdown',
					'heading'     => __( 'Contact Forms', 'porto-functionality' ),
					'param_name'  => 'wpform',
					'value'       => array_flip( $wpforms ),
					'admin_label' => true,
					'dependency'  => array(
						'element' => 'source',
						'value'   => array( 'wpforms' ),
					),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'tg',
					'selectors'  => array(
						'{{WRAPPER}}',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Label Typography', 'porto-functionality' ),
					'param_name' => 'lbl_tg',
					'selectors'  => array(
						'{{WRAPPER}} label',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Label Bottom Spacing', 'porto-functionality' ),
					'param_name' => 'lbl_mb',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} label' => 'margin-bottom: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'The Height of input and select box', 'porto-functionality' ),
					'param_name' => 'ih',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} select' => 'height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'The Height of textarea', 'porto-functionality' ),
					'param_name' => 'tah',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} textarea' => 'height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_number',
					'heading'     => __( 'Font Size', 'porto-functionality' ),
					'description' => __( 'Inputs the font size of form and form fields.', 'porto-functionality' ),
					'param_name'  => 'fs',
					'units'       => array( 'px', 'rem', 'em' ),
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'font-size: {{VALUE}}{{UNIT}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Text Color', 'porto-functionality' ),
					'description' => __( 'Controls the color of the form and form fields.', 'porto-functionality' ),
					'param_name'  => 'clr',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'color: {{VALUE}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Placeholder Color', 'porto-functionality' ),
					'description' => __( 'Controls the placeholder color of form fields.', 'porto-functionality' ),
					'param_name'  => 'ph_clr',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"]::placeholder, {{WRAPPER}} input[type="email"]::placeholder, {{WRAPPER}} textarea::placeholder, {{WRAPPER}} .form-control::placeholder' => 'color: {{VALUE}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Background Color', 'porto-functionality' ),
					'description' => __( 'Controls the background color of form fields such as input and select boxes.', 'porto-functionality' ),
					'param_name'  => 'field_bgc',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'background-color: {{VALUE}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Form Field Border Width (px)', 'porto-functionality' ),
					'description' => __( 'Controls the border size of the form fields such as input and select boxes.', 'porto-functionality' ),
					'param_name'  => 'field_bw',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'border-width: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Form Field Border Color', 'porto-functionality' ),
					'description' => __( 'Controls the border color of form fields such as input and select boxes.', 'porto-functionality' ),
					'param_name'  => 'field_bc',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="datetime"], {{WRAPPER}} input[type="number"], {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'border-color: {{VALUE}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'colorpicker',
					'heading'     => __( 'Form Field Border Color on Focus', 'porto-functionality' ),
					'description' => __( 'Controls the border color of form fields such as input and select boxes on focus status.', 'porto-functionality' ),
					'param_name'  => 'field_bcf',
					'selectors'   => array(
						'{{WRAPPER}} input[type="text"]:focus, {{WRAPPER}} input[type="email"]:focus, {{WRAPPER}} textarea:focus, {{WRAPPER}} .form-control:focus, {{WRAPPER}} select:focus' => 'border-color: {{VALUE}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'        => 'porto_dimension',
					'heading'     => __( 'Form Field Border Radius (px)', 'porto-functionality' ),
					'description' => __( 'Controls the border radius of form fields such as input, select boxes and buttons.', 'porto-functionality' ),
					'param_name'  => 'br',
					'selectors'   => array(
						'{{WRAPPER}} input, {{WRAPPER}} textarea, {{WRAPPER}} .form-control, {{WRAPPER}} select' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'       => __( 'Form Fields', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Form Field Padding(px)', 'porto-functionality' ),
					'param_name' => 'form_space',
					'selectors'  => array(
						'{{WRAPPER}} input[type="email"],{{WRAPPER}} input[type="number"],{{WRAPPER}} input[type="password"],{{WRAPPER}} input[type="search"],{{WRAPPER}} input[type="tel"],{{WRAPPER}} input[type="text"],{{WRAPPER}} input[type="url"],{{WRAPPER}} input[type="color"],{{WRAPPER}} input[type="date"],{{WRAPPER}} input[type="datetime"],{{WRAPPER}} input[type="datetime-local"],{{WRAPPER}} input[type="month"],{{WRAPPER}} input[type="time"],{{WRAPPER}} input[type="week"],{{WRAPPER}} textarea,{{WRAPPER}} .form-control,{{WRAPPER}} select' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Form Fields', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'btn_tg',
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'The Height of Buttons', 'porto-functionality' ),
					'param_name' => 'bh',
					'units'      => array( 'px', 'rem', 'em' ),
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'height: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Padding', 'porto-functionality' ),
					'param_name' => 'btn_pd',
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'padding: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_dimension',
					'heading'    => __( 'Border Radius', 'porto-functionality' ),
					'param_name' => 'btn_br',
					'value'      => '',
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'border-radius: {{TOP}} {{RIGHT}} {{BOTTOM}} {{LEFT}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'btn_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'btn_bw',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element'            => 'btn_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'btn_style',
					'text'       => __( 'Normal', 'porto-functionality' ),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'btn_bgc',
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'param_name' => 'btn_clr',
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'btn_bc',
					'dependency' => array(
						'element'            => 'btn_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} button, {{WRAPPER}} .btn, {{WRAPPER}} input[type="button"], {{WRAPPER}} input[type="submit"]' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'btn_style_hover',
					'text'       => __( 'Hover', 'porto-functionality' ),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'btn_bgc_hover',
					'selectors'  => array(
						'{{WRAPPER}} button:hover, {{WRAPPER}} .btn:hover, {{WRAPPER}} input[type="button"]:hover, {{WRAPPER}} input[type="submit"]:hover' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Text Color', 'porto-functionality' ),
					'param_name' => 'btn_clr_hover',
					'selectors'  => array(
						'{{WRAPPER}} button:hover, {{WRAPPER}} .btn:hover, {{WRAPPER}} input[type="button"]:hover, {{WRAPPER}} input[type="submit"]:hover' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'btn_bc_hover',
					'dependency' => array(
						'element'            => 'btn_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} button:hover, {{WRAPPER}} .btn:hover, {{WRAPPER}} input[type="button"]:hover, {{WRAPPER}} input[type="submit"]:hover' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Buttons', 'porto-functionality' ),
				),

				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'error_style',
					'text'       => __( 'Error Message', 'porto-functionality' ),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'error_tg',
					'selectors'  => array(
						'{{WRAPPER}} label.wpforms-error, {{WRAPPER}} .wpcf7-not-valid-tip',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'error_clr',
					'selectors'  => array(
						'{{WRAPPER}} label.wpforms-error, {{WRAPPER}} .wpcf7-not-valid-tip' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_param_heading',
					'param_name' => 'msg_style',
					'text'       => __( 'General Message', 'porto-functionality' ),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_typography',
					'heading'    => __( 'Typography', 'porto-functionality' ),
					'param_name' => 'msg_tg',
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form .wpcf7-response-output',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Color', 'porto-functionality' ),
					'param_name' => 'msg_clr',
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form .wpcf7-response-output' => 'color: {{VALUE}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Background Color', 'porto-functionality' ),
					'param_name' => 'msg_bgc',
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form .wpcf7-response-output' => 'background-color: {{VALUE}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'dropdown',
					'heading'    => __( 'Border Style', 'porto-functionality' ),
					'param_name' => 'msg_bs',
					'std'        => '',
					'value'      => array(
						__( 'Default', 'porto-functionality' ) => '',
						__( 'None', 'porto-functionality' )   => 'none',
						__( 'Solid', 'porto-functionality' )  => 'solid',
						__( 'Dashed', 'porto-functionality' ) => 'dashed',
						__( 'Dotted', 'porto-functionality' ) => 'dotted',
						__( 'Double', 'porto-functionality' ) => 'double',
						__( 'Inset', 'porto-functionality' )  => 'inset',
						__( 'Outset', 'porto-functionality' ) => 'outset',
					),
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form .wpcf7-response-output' => 'border-style: {{VALUE}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'porto_number',
					'heading'    => __( 'Border Width', 'porto-functionality' ),
					'param_name' => 'msg_bw',
					'units'      => array( 'px' ),
					'dependency' => array(
						'element'            => 'msg_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form .wpcf7-response-output' => 'border-width: {{VALUE}}{{UNIT}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				array(
					'type'       => 'colorpicker',
					'heading'    => __( 'Border Color', 'porto-functionality' ),
					'param_name' => 'msg_bc',
					'dependency' => array(
						'element'            => 'msg_bs',
						'value_not_equal_to' => array( '', 'none' ),
					),
					'selectors'  => array(
						'{{WRAPPER}} .wpforms-confirmation-container, {{WRAPPER}} form.wpcf7-form .wpcf7-response-output' => 'border-color: {{VALUE}};',
					),
					'group'      => __( 'Message', 'porto-functionality' ),
				),
				$custom_class,
				$animation_type,
				$animation_duration,
				$animation_delay,
			),
		)
	);

	if ( ! class_exists( 'WPBakeryShortCode_Porto_Contact_Form' ) ) {
		class WPBakeryShortCode_Porto_Contact_Form extends WPBakeryShortCode {
		}
	}
}
