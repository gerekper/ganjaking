<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Porto Contact Form widget
 *
 * @since 2.4.0
 */

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Repeater;

class Porto_Elementor_Contact_Form_Widget extends \Elementor\Widget_Base {

	public function get_name() {
		return 'porto_contact_form';
	}

	public function get_title() {
		return __( 'Porto Contact Form', 'porto-functionality' );
	}

	public function get_categories() {
		return array( 'porto-elements' );
	}

	public function get_keywords() {
		return array( 'contact', 'form', 'forms', 'field', 'button', 'submit', 'mail', 'wpform', 'wpforms', 'lite' );
	}

	public function get_icon() {
		return 'eicon-form-horizontal';
	}

	public function get_custom_help_url() {
		return 'https://www.portotheme.com/wordpress/porto/documentation/contact-form-widget/';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_contact_form',
			array(
				'label' => __( 'Contact Form', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'source',
			array(
				'type'    => Controls_Manager::SELECT,
				'label'   => __( 'Source', 'porto-functionality' ),
				'default' => '',
				'options' => array(
					''        => __( 'Contact Form 7', 'porto-functionality' ),
					'wpforms' => __( 'WPForms Lite', 'porto-functionality' ),
				),
			)
		);

		$this->add_control(
			'cf7_form',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Contact Forms', 'porto-functionality' ),
				'options'     => 'wpcf7_contact_form',
				'label_block' => true,
				'condition'   => array(
					'source' => '',
				),
			)
		);

		$this->add_control(
			'wpform',
			array(
				'type'        => 'porto_ajaxselect2',
				'label'       => __( 'Contact Forms', 'porto-functionality' ),
				'options'     => 'wpforms',
				'label_block' => true,
				'condition'   => array(
					'source' => 'wpforms',
				),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_fields',
			array(
				'label' => __( 'Form Fields', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}}',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'lbl_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Label Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} label',
			)
		);

		$this->add_control(
			'lbl_mb',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'Label Bottom Spacing', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 50,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'ih',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'The Height of input and select box', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} select' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'tah',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'The Height of textarea', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 400,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 40,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 40,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} textarea' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'fs',
			array(
				'type'        => Controls_Manager::SLIDER,
				'label'       => __( 'Font Size', 'porto-functionality' ),
				'description' => __( 'Inputs the font size of form and form fields.', 'porto-functionality' ),
				'range'       => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 40,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 5,
					),
				),
				'default'     => array(
					'unit' => 'px',
				),
				'size_units'  => array(
					'px',
					'em',
					'rem',
				),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'font-size: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_control(
			'clr',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Text Color', 'porto-functionality' ),
				'description' => __( 'Controls the color of the form and form fields.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'ph_clr',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Placeholder Color', 'porto-functionality' ),
				'description' => __( 'Controls the placeholder color of form fields.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"]::placeholder, .elementor-element-{{ID}} input[type="email"]::placeholder, .elementor-element-{{ID}} textarea::placeholder, .elementor-element-{{ID}} .form-control::placeholder' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_bgc',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Form Field Background Color', 'porto-functionality' ),
				'description' => __( 'Controls the background color of form fields such as input and select boxes.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_bw',
			array(
				'type'        => Controls_Manager::DIMENSIONS,
				'label'       => __( 'Form Field Border Width (px)', 'porto-functionality' ),
				'description' => __( 'Controls the border size of the form fields such as input and select boxes.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units'  => array( 'px' ),
			)
		);

		$this->add_control(
			'field_bc',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Form Field Border Color', 'porto-functionality' ),
				'description' => __( 'Controls the border color of form fields such as input and select boxes.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"], .elementor-element-{{ID}} input[type="email"], .elementor-element-{{ID}} input[type="date"], .elementor-element-{{ID}} input[type="datetime"], .elementor-element-{{ID}} input[type="number"], .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'field_bcf',
			array(
				'type'        => Controls_Manager::COLOR,
				'label'       => __( 'Form Field Border Color on Focus', 'porto-functionality' ),
				'description' => __( 'Controls the border color of form fields such as input and select boxes on focus status.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input[type="text"]:focus, .elementor-element-{{ID}} input[type="email"]:focus, .elementor-element-{{ID}} textarea:focus, .elementor-element-{{ID}} .form-control:focus, .elementor-element-{{ID}} select:focus' => 'border-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'br',
			array(
				'type'        => Controls_Manager::DIMENSIONS,
				'label'       => __( 'Form Field Border Radius (px)', 'porto-functionality' ),
				'description' => __( 'Controls the border radius of form fields such as input, select boxes and buttons.', 'porto-functionality' ),
				'selectors'   => array(
					'.elementor-element-{{ID}} input, .elementor-element-{{ID}} textarea, .elementor-element-{{ID}} .form-control, .elementor-element-{{ID}} select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units'  => array( 'px' ),
			)
		);

		$this->add_responsive_control(
			'form_space',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Form Field Padding(px)', 'porto-functionality' ),
				'selectors'  => array(
					'{{WRAPPER}} input[type="email"],{{WRAPPER}} input[type="number"],{{WRAPPER}} input[type="password"],{{WRAPPER}} input[type="search"],{{WRAPPER}} input[type="tel"],{{WRAPPER}} input[type="text"],{{WRAPPER}} input[type="url"],{{WRAPPER}} input[type="color"],{{WRAPPER}} input[type="date"],{{WRAPPER}} input[type="datetime"],{{WRAPPER}} input[type="datetime-local"],{{WRAPPER}} input[type="month"],{{WRAPPER}} input[type="time"],{{WRAPPER}} input[type="week"],{{WRAPPER}} textarea,{{WRAPPER}} .form-control,{{WRAPPER}} select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_buttons',
			array(
				'label' => __( 'Buttons', 'porto-functionality' ),
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'btn_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]',
			)
		);

		$this->add_control(
			'bh',
			array(
				'type'       => Controls_Manager::SLIDER,
				'label'      => __( 'The Height of Buttons', 'porto-functionality' ),
				'range'      => array(
					'px'  => array(
						'step' => 1,
						'min'  => 0,
						'max'  => 100,
					),
					'em'  => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
					'rem' => array(
						'step' => 0.1,
						'min'  => 0,
						'max'  => 10,
					),
				),
				'default'    => array(
					'unit' => 'px',
				),
				'size_units' => array(
					'px',
					'em',
					'rem',
				),
				'selectors'  => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'height: {{SIZE}}{{UNIT}};',
				),
			)
		);

		$this->add_responsive_control(
			'btn_pd',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Padding', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px', 'em', 'rem' ),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'btn_bd',
				'selector' => '.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]',
				'exclude'  => array(
					'color',
				),
			)
		);

		$this->add_control(
			'btn_br',
			array(
				'type'       => Controls_Manager::DIMENSIONS,
				'label'      => __( 'Border Radius (px)', 'porto-functionality' ),
				'selectors'  => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				),
				'size_units' => array( 'px' ),
			)
		);

		$this->start_controls_tabs( 'tabs_btn_style' );

		$this->start_controls_tab(
			'tabs_btn_normal',
			array(
				'label' => __( 'Normal', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'btn_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_bc',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} button, .elementor-element-{{ID}} .btn, .elementor-element-{{ID}} input[type="submit"], .elementor-element-{{ID}} input[type="button"]' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'btn_bd_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_btn_hover',
			array(
				'label' => __( 'Hover', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'btn_hover_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} button:hover, .elementor-element-{{ID}} .btn:hover, .elementor-element-{{ID}} input[type="submit"]:hover, .elementor-element-{{ID}} input[type="button"]:hover' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_hover_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} button:hover, .elementor-element-{{ID}} .btn:hover, .elementor-element-{{ID}} input[type="submit"]:hover, .elementor-element-{{ID}} input[type="button"]:hover' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'btn_hover_bc',
			array(
				'label'     => __( 'Border Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} button:hover, .elementor-element-{{ID}} .btn:hover, .elementor-element-{{ID}} input[type="submit"]:hover, .elementor-element-{{ID}} input[type="button"]:hover' => 'border-color: {{VALUE}};',
				),
				'condition' => array(
					'btn_bd_border!' => '',
				),
			)
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_contact_form_messages',
			array(
				'label' => __( 'Messages', 'porto-functionality' ),
			)
		);

		$this->add_control(
			'error_heading',
			array(
				'label'     => __( 'Error Message', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'error_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} label.wpforms-error, .elementor-element-{{ID}} .wpcf7-not-valid-tip',
			)
		);

		$this->add_control(
			'error_clr',
			array(
				'label'     => __( 'Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} label.wpforms-error, .elementor-element-{{ID}} .wpcf7-not-valid-tip' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'msg_heading',
			array(
				'label'     => __( 'General Message', 'porto-functionality' ),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			)
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			array(
				'name'     => 'msg_tg',
				'scheme'   => Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'label'    => __( 'Typography', 'porto-functionality' ),
				'selector' => '.elementor-element-{{ID}} .wpforms-confirmation-container, .elementor-element-{{ID}} form .wpcf7-response-output',
			)
		);

		$this->add_control(
			'msg_clr',
			array(
				'label'     => __( 'Text Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .wpforms-confirmation-container, .elementor-element-{{ID}} form .wpcf7-response-output' => 'color: {{VALUE}};',
				),
			)
		);

		$this->add_control(
			'msg_bgc',
			array(
				'label'     => __( 'Background Color', 'porto-functionality' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => array(
					'.elementor-element-{{ID}} .wpforms-confirmation-container, .elementor-element-{{ID}} form .wpcf7-response-output' => 'background-color: {{VALUE}};',
				),
			)
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			array(
				'name'     => 'msg_bd',
				'selector' => '.elementor-element-{{ID}} .wpforms-confirmation-container, .elementor-element-{{ID}} form.wpcf7-form .wpcf7-response-output',
			)
		);

		$this->end_controls_section();
	}

	protected function render() {
		$atts = $this->get_settings_for_display();

		if ( $template = porto_shortcode_template( 'porto_contact_form' ) ) {
			include $template;
		}
	}
}
