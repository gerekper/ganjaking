<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Background;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class Contact_Form_7 extends Widget_Base
{

	public function get_name()
	{
		return 'ma-contact-form-7';
	}

	public function get_title()
	{
		return esc_html__('Contact Form 7', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-mail';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	public function get_help_url()
	{
		return 'https://master-addons.com/demos/contact-form-7/';
	}

	protected function _register_controls()
	{

		/**
		 * Contact Form 7
		 * Title
		 * Form Name/ID
		 */

		$this->start_controls_section(
			'ma_cf7',
			[
				'label' => esc_html__('Contact Form 7', MELA_TD)
			]
		);

		$this->add_control(
			'ma_cf7_list',
			[
				'label'                 => esc_html__('Select Contact Form', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => true,
				'options'               => Master_Addons_Helper::maad_el_retrive_cf7(),
				'default'               => '0',
			]
		);

		$this->end_controls_section();




		/**
		 * Contact Form 7
		 * Layout Design
		 */

		$this->start_controls_section(
			'ma_cf7_section_style',
			[
				'label' => esc_html__('Design Layout', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		// Premium Version Codes
		

			$this->add_control(
				'ma_cf7_layout_style',
				[
					'label' => __('Design Variations', MELA_TD),
					'type' => Controls_Manager::SELECT,
					'default' => '1',
					'options' => [
						'1'   => __('Style One', MELA_TD),
						'2'   => __('Style Two', MELA_TD),
						'3'   => __('Style Three', MELA_TD),
						'4'   => __('Style Four', MELA_TD),
						'5'   => __('Style Five', MELA_TD),
						'6'   => __('Style Six', MELA_TD),
						'7'   => __('Style Seven', MELA_TD),
						'8'   => __('Style Eight', MELA_TD),
						'9'   => __('Style Nine', MELA_TD),
						'10'   => __('Style Ten', MELA_TD),
						'11'   => __('Style Eleven', MELA_TD),
					]
				]
			);
		


		$this->end_controls_section();



		/**
		 * Contact Form 7
		 * Error Messages
		 */

		$this->start_controls_section(
			'ma_cf7_section_errors',
			[
				'label' => esc_html__('Errors Style', MELA_TD)
			]
		);
		$this->add_control(
			'ma_cf7_error_messages',
			[
				'label'                 => __('Error Messages', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'show',
				'options'               => [
					'show'          => __('Show', MELA_TD),
					'hide'          => __('Hide', MELA_TD),
				],
				'selectors_dictionary'  => [
					'show'          => 'block',
					'hide'          => 'none',
				],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-not-valid-tip' => 'display: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_validation_errors',
			[
				'label'                 => __('Validation Errors', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'default'               => 'show',
				'options'               => [
					'show'          => __('Show', MELA_TD),
					'hide'          => __('Hide', MELA_TD),
				],
				'selectors_dictionary'  => [
					'show'          => 'block',
					'hide'          => 'none',
				],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-validation-errors' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();


		/**
		 * Style Tab: Form Design
		 */
		$this->start_controls_section(
			'ma_cf7_section_container_style',
			[
				'label'                 => __('Form Container', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'ma_cf7_background',
				'label'     => esc_html__('Background', MELA_TD),
				'types'     => ['classic', 'gradient'],
				'selector' => '{{WRAPPER}} .master-addons-cf7',
			]
		);

		$this->add_control(
			'ma_cf7ainer_border_top',
			[
				'label'                 => esc_html__('Border Top Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#6e00e9',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7::before' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_width',
			[
				'label' => esc_html__('Form Width', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 1500,
					],
					'em' => [
						'min' => 1,
						'max' => 80,
					],
				],
				'default'   => [
					'unit'  => '%',
					'size'  => '100'
				],
				'selectors' => [
					'{{WRAPPER}} .master-addons-cf7' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'ma_cf7_padding',
			[
				'label' => esc_html__('Form Padding', MELA_TD),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .master-addons-cf7' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => 40,
					'right' => 40,
					'bottom' => 40,
					'left' => 40,
					'unit' => 'px'
				]
			]
		);


		$this->end_controls_section();




		/**
		 * Style Tab: Title & Description
		 */
		$this->start_controls_section(
			'ma_cf7_section_title',
			[
				'label'                 => __('Title', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'title_text_color',
			[
				'label'                 => esc_html__('Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .master-addons-cf7-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'ma_cf7_heading_alignment',
			[
				'label'                 => esc_html__('Alignment', MELA_TD),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => esc_html__('Left', MELA_TD),
						'icon'  => 'fa fa-align-left',
					],
					'center'    => [
						'title' => esc_html__('Center', MELA_TD),
						'icon'  => 'fa fa-align-center',
					],
					'right'     => [
						'title' => esc_html__('Right', MELA_TD),
						'icon'  => 'fa fa-align-right',
					],
				],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .master-addons-cf7-title' => 'text-align: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'ma_cf7_title_typography',
				'label'                 => esc_html__('Typography', MELA_TD),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .master-addons-cf7-title',
			]
		);

		$this->end_controls_section();




		/**
		 * Style Tab: Input & Textarea
		 */
		$this->start_controls_section(
			'section_fields_style',
			[
				'label'                 => esc_html__('Input & Textarea', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_cf7_field_bg',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_field_text_color',
			[
				'label'                 => esc_html__('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-select' => 'color: {{VALUE}}',
				],
			]
		);



		$this->add_control(
			'ma_cf7_field_padding',
			[
				'label'                 => esc_html__('Padding', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'ma_cf7_field_width',
			[
				'label'                 => esc_html__('Field Width', MELA_TD),
				'type'                  => Controls_Manager::SLIDER,
				'range'                 => [
					'px'        => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'            => ['px', 'em', '%'],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'field_border',
				'label'                 => esc_html__('Border', MELA_TD),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-select',
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'ma_cf7_field_radius',
			[
				'label'                 => esc_html__('Border Radius', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-text, {{WRAPPER}} .master-addons-cf7 .wpcf7-form-control.wpcf7-textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .master-addons-cf7-9 .wpcf7-form-control,
                    {{WRAPPER}} .master-addons-cf7-10 .wpcf7-form-control,
                    {{WRAPPER}} .master-addons-cf7-11 .wpcf7-form-control' => 'border-radius:2em'

				],
			]
		);
		$this->end_controls_section();



		/**
		 * Style Tab: Label Section
		 */
		$this->start_controls_section(
			'ma_cf7_section_label_style',
			[
				'label'                 => esc_html__('Labels', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'                 => esc_html__('Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form label' => 'color: {{VALUE}}',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'ma_cf7_typography_label',
				'label'                 => esc_html__('Typography', MELA_TD),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-form label',
			]
		);

		$this->end_controls_section();





		/**
		 * Style Tab: Submit Button
		 */
		$this->start_controls_section(
			'ma_cf7_section_submit_button_style',
			[
				'label' => esc_html__('Submit Button', MELA_TD),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'ma_cf7_button_typography',
				'label'                 => esc_html__('Button Typography', MELA_TD),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]',
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'ma_cf7_tab_button_normal',
			[
				'label' => esc_html__('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'ma_cf7_button_bg_color_normal',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#6e00e9',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_button_text_color_normal',
			[
				'label'                 => esc_html__('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#FFF',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_button_border_color_normal',
			[
				'label'                 => esc_html__('Border Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => 'transparent',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();




		/*
		 * Hover Styles
		 */
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'                 => esc_html__('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'ma_cf7_button_bg_color_hover',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#FFF',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'ma_cf7_button_text_color_hover',
			[
				'label'                 => esc_html__('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#6e00e9',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]:hover' => 'color: {{VALUE}}',
				],
			]
		);
		//
		//		$this->add_group_control(
		//			Group_Control_Border::get_type(),
		//			[
		//				'name'                  => 'ma_cf7_button_border_width',
		//				'label'                 => esc_html__( 'Border', MELA_TD ),
		//				'placeholder'           => '1px',
		//				'default'               => '1px',
		////				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-validation-errors',
		//				'selectors'             => '{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]:hover',
		//				'separator'             => 'before',
		//			]
		//		);



		$this->add_control(
			'ma_cf7_button_border_width',
			[
				'label'                 => esc_html__('Border Width', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-submit' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px'
				]
			]
		);

		$this->add_control(
			'ma_cf7_button_border_color_hover',
			[
				'label'                 => esc_html__('Border Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '#6e00e9',
				'selectors'             => [
					//					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]:hover' => 'border-color:{{VALUE}}',
					'{{WRAPPER}} .ma-cf input[type="submit"]:hover' => 'border-color:{{VALUE}}',
				],
			]
		);


		$this->end_controls_tab();

		$this->end_controls_tabs();



		$this->add_control(
			'ma_cf7_button_border_radius',
			[
				'label'                 => esc_html__('Border Radius', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'separator'             => 'before',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => 0,
					'right' => 0,
					'bottom' => 0,
					'left' => 0,
					'unit' => 'px'
				]
			]
		);

		$this->add_control(
			'button_padding',
			[
				'label'                 => esc_html__('Padding', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-form input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => 20,
					'right' => 50,
					'bottom' => 20,
					'left' => 50,
					'unit' => 'px'
				]
			]
		);

		$this->end_controls_section();





		/**
		 * Style Tab: Errors
		 */
		$this->start_controls_section(
			'ma_cf7_section_error_style',
			[
				'label'                 => esc_html__('Errors', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'ma_cf7_error_messages_heading',
			[
				'label'                 => esc_html__('Error Messages', MELA_TD),
				'type'                  => Controls_Manager::HEADING,
				'condition'             => [
					'ma_cf7_error_messages' => 'show',
				],
			]
		);


		$this->add_control(
			'ma_cf7_error_alert_text_color',
			[
				'label'                 => esc_html__('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-not-valid-tip' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'ma_cf7_error_messages' => 'show',
				],
			]
		);


		$this->add_control(
			'ma_cf7_error_field_bg_color',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-not-valid-tip' => 'background: {{VALUE}}',
				],
				'condition'             => [
					'ma_cf7_error_messages' => 'show',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'error_field_border',
				'label'                 => esc_html__('Border', MELA_TD),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-not-valid-tip',
				'separator'             => 'before',
				'condition'             => [
					'ma_cf7_error_messages' => 'show',
				],
			]
		);


		$this->add_control(
			'ma_cf7_validation_errors_heading',
			[
				'label'                 => esc_html__('Validation Errors', MELA_TD),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'ma_cf7_validation_errors' => 'show',
				],
			]
		);

		$this->add_control(
			'ma_cf7_validation_errors_bg_color',
			[
				'label'                 => esc_html__('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-validation-errors' => 'background: {{VALUE}}',
				],
				'condition'             => [
					'ma_cf7_validation_errors' => 'show',
				],
			]
		);

		$this->add_control(
			'ma_cf7_validation_errors_color',
			[
				'label'                 => esc_html__('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .master-addons-cf7 .wpcf7-validation-errors' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'ma_cf7_validation_errors' => 'show',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'validation_errors_border',
				'label'                 => esc_html__('Border', MELA_TD),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .master-addons-cf7 .wpcf7-validation-errors',
				'separator'             => 'before',
				'condition'             => [
					'ma_cf7_validation_errors' => 'show',
				],
			]
		);

		$this->end_controls_section();




		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/contact-form-7/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/how-to-edit-contact-form-7/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=1fU6lWniRqo" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();



		
	}

	protected function render()
	{
		$settings = $this->get_settings();

		// if Contact Form 7 Missing
		if (!function_exists('wpcf7')) {
			Master_Addons_Helper::jltma_elementor_plugin_missing_notice(array('plugin_name' => esc_html__(
				'Contact Form 7',
				MELA_TD
			)));
			return;
		}

		$this->add_render_attribute(
			'master-addons-cf7',
			'class',
			[
				'master-addons-cf7',
				'ma-cf',
				'ma-cf-' . $settings['ma_cf7_layout_style'],
				'master-addons-cf7-' . esc_attr($this->get_id())
			]
		);

		if (function_exists('wpcf7')) {
			if (!empty($settings['ma_cf7_list'])) { ?>
				<div <?php echo $this->get_render_attribute_string('master-addons-cf7'); ?>>
					<?php echo do_shortcode('[contact-form-7 id="' . $settings['ma_cf7_list'] . '" ]'); ?>
				</div>
<?php
			}
		}
	}

	protected function _content_template()
	{
	}
}
