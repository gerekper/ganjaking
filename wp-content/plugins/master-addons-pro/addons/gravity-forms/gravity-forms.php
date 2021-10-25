<?php

namespace MasterAddons\Addons;

use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 6/27/19
 */
if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class Gravity_Forms extends Widget_Base
{

	public function get_name()
	{
		return 'ma-gravity-forms';
	}

	public function get_title()
	{
		return esc_html__('Gravity Forms', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon fa fa-envelope-o';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}

	protected function _register_controls()
	{





			/**
			 * Master Addons: Gravity Form
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_gravity_form',
				[
					'label'                 => __('Gravity Forms', MELA_TD),
				]
			);



			$this->add_control(
				'contact_form_list',
				[
					'label'                 => esc_html__('Contact Form', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'label_block'           => true,
					'options'               => Master_Addons_Helper::ma_el_get_gravity_forms(),
					'default'               => '0',
				]
			);

			$this->add_control(
				'custom_title_description',
				[
					'label'                 => __('Custom Title & Description', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'label_on'              => __('Yes', MELA_TD),
					'label_off'             => __('No', MELA_TD),
					'return_value'          => 'yes',
				]
			);

			$this->add_control(
				'form_title',
				[
					'label'                 => __('Title', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'default'               => 'yes',
					'label_on'              => __('Show', MELA_TD),
					'label_off'             => __('Hide', MELA_TD),
					'return_value'          => 'yes',
					'condition'             => [
						'custom_title_description!'   => 'yes',
					],
				]
			);

			$this->add_control(
				'form_description',
				[
					'label'                 => __('Description', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'default'               => 'yes',
					'label_on'              => __('Show', MELA_TD),
					'label_off'             => __('Hide', MELA_TD),
					'return_value'          => 'yes',
					'condition'             => [
						'custom_title_description!'   => 'yes',
					],
				]
			);

			$this->add_control(
				'form_title_custom',
				[
					'label'                 => esc_html__('Title', MELA_TD),
					'type'                  => Controls_Manager::TEXT,
					'label_block'           => true,
					'default'               => '',
					'condition'             => [
						'custom_title_description'   => 'yes',
					],
				]
			);

			$this->add_control(
				'form_description_custom',
				[
					'label'                 => esc_html__('Description', MELA_TD),
					'type'                  => Controls_Manager::TEXTAREA,
					'default'               => '',
					'condition'             => [
						'custom_title_description'   => 'yes',
					],
				]
			);

			$this->add_control(
				'labels_switch',
				[
					'label'                 => __('Labels', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'default'               => 'yes',
					'label_on'              => __('Show', MELA_TD),
					'label_off'             => __('Hide', MELA_TD),
					'return_value'          => 'yes',
				]
			);

			$this->add_control(
				'placeholder_switch',
				[
					'label'                 => __('Placeholder', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'default'               => 'yes',
					'label_on'              => __('Show', MELA_TD),
					'label_off'             => __('Hide', MELA_TD),
					'return_value'          => 'yes',
				]
			);

			$this->add_control(
				'form_ajax',
				[
					'label'                 => __('Use Ajax', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'description'           => __('Use ajax to submit the form', MELA_TD),
					'label_on'              => __('Yes', MELA_TD),
					'label_off'             => __('No', MELA_TD),
					'return_value'          => 'yes',
				]
			);

			$this->end_controls_section();

			/**
			 * Content Tab: Errors
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_errors',
				[
					'label'                 => __('Errors', MELA_TD),
				]
			);

			$this->add_control(
				'error_messages',
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
						'{{WRAPPER}} .ma-el-gravity-form .validation_message' => 'display: {{VALUE}} !important;',
					],
				]
			);

			$this->add_control(
				'validation_errors',
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
						'{{WRAPPER}} .ma-el-gravity-form .validation_error' => 'display: {{VALUE}} !important;',
					],
				]
			);

			$this->end_controls_section();

			/*-----------------------------------------------------------------------------------*/
			/*	STYLE TAB
			/*-----------------------------------------------------------------------------------*/

			/**
			 * Style Tab: Title and Description
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_general_style',
				[
					'label'                 => __('Title & Description', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'heading_alignment',
				[
					'label'                 => __('Alignment', MELA_TD),
					'type'                  => Controls_Manager::CHOOSE,
					'options'               => [
						'left'      => [
							'title' => __('Left', MELA_TD),
							'icon'  => 'fa fa-align-left',
						],
						'center'    => [
							'title' => __('Center', MELA_TD),
							'icon'  => 'fa fa-align-center',
						],
						'right'     => [
							'title' => __('Right', MELA_TD),
							'icon'  => 'fa fa-align-right',
						],
					],
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gform_heading, {{WRAPPER}} .ma-el-gravity-form .ma-el-gravity-form-heading' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'title_heading',
				[
					'label'                 => __('Title', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'title_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gform_title, {{WRAPPER}} .ma-el-gravity-form .ma-el-gravity-form-title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'title_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gform_title, {{WRAPPER}} .ma-el-gravity-form .ma-el-gravity-form-title',
				]
			);

			$this->add_control(
				'description_heading',
				[
					'label'                 => __('Description', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'description_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gform_description, {{WRAPPER}} .ma-el-gravity-form .ma-el-gravity-form-description' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'description_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gform_description, {{WRAPPER}} .ma-el-gravity-form .ma-el-gravity-form-description',
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Labels
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_label_style',
				[
					'label'                 => __('Labels', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
					'condition'             => [
						'labels_switch'   => 'yes',
					],
				]
			);

			$this->add_control(
				'text_color_label',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield label' => 'color: {{VALUE}}',
					],
					'condition'             => [
						'labels_switch'   => 'yes',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'typography_label',
					'label'                 => __('Typography', MELA_TD),
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gfield label',
					'condition'             => [
						'labels_switch'   => 'yes',
					],
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Input & Textarea
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_fields_style',
				[
					'label'                 => __('Input & Textarea', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'input_alignment',
				[
					'label'                 => __('Alignment', MELA_TD),
					'type'                  => Controls_Manager::CHOOSE,
					'options'               => [
						'left'      => [
							'title' => __('Left', MELA_TD),
							'icon'  => 'fa fa-align-left',
						],
						'center'    => [
							'title' => __('Center', MELA_TD),
							'icon'  => 'fa fa-align-center',
						],
						'right'     => [
							'title' => __('Right', MELA_TD),
							'icon'  => 'fa fa-align-right',
						],
					],
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield input[type="text"], {{WRAPPER}} .ma-el-gravity-form .gfield textarea' => 'text-align: {{VALUE}};',
					],
				]
			);

			$this->start_controls_tabs('tabs_fields_style');

			$this->start_controls_tab(
				'tab_fields_normal',
				[
					'label'                 => __('Normal', MELA_TD),
				]
			);

			$this->add_control(
				'field_bg_color',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '#f9f9f9',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'field_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_responsive_control(
				'field_spacing',
				[
					'label'                 => __('Spacing', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'field_padding',
				[
					'label'                 => __('Padding', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'default'               => [
						'top'       => '10',
						'right'     => '10',
						'bottom'    => '10',
						'left'      => '10',
						'unit'      => '',
						'isLinked'  => true,
					],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'text_indent',
				[
					'label'                 => __('Text Indent', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 60,
							'step'  => 1,
						],
						'%'         => [
							'min'   => 0,
							'max'   => 30,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select' => 'text-indent: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'input_width',
				[
					'label'                 => __('Input Width', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px' => [
							'min'   => 0,
							'max'   => 1200,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield select' => 'width: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'input_height',
				[
					'label'                 => __('Input Height', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px' => [
							'min'   => 0,
							'max'   => 80,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield select' => 'height: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'textarea_width',
				[
					'label'                 => __('Textarea Width', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px' => [
							'min'   => 0,
							'max'   => 1200,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield textarea' => 'width: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_responsive_control(
				'textarea_height',
				[
					'label'                 => __('Textarea Height', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px' => [
							'min'   => 0,
							'max'   => 400,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield textarea' => 'height: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'field_border',
					'label'                 => __('Border', MELA_TD),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select',
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'field_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'field_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select',
					'separator'             => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'field_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-gravity-form .gfield textarea, {{WRAPPER}} .ma-el-gravity-form .gfield select',
					'separator'             => 'before',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_fields_focus',
				[
					'label'                 => __('Focus', MELA_TD),
				]
			);

			$this->add_control(
				'field_bg_color_focus',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield input:focus, {{WRAPPER}} .ma-el-gravity-form .gfield textarea:focus' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'focus_input_border',
					'label'                 => __('Border', MELA_TD),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gfield input:focus, {{WRAPPER}} .ma-el-gravity-form .gfield textarea:focus',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'focus_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gfield input:focus, {{WRAPPER}} .ma-el-gravity-form .gfield textarea:focus',
					'separator'             => 'before',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			/**
			 * Style Tab: Field Description
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_field_description_style',
				[
					'label'                 => __('Field Description', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'field_description_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield .gfield_description' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'field_description_typography',
					'label'                 => __('Typography', MELA_TD),
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gfield .gfield_description',
				]
			);

			$this->add_responsive_control(
				'field_description_spacing',
				[
					'label'                 => __('Spacing', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield .gfield_description' => 'padding-top: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Section Field
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_field_style',
				[
					'label'                 => __('Section Field', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'section_field_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield.gsection .gsection_title' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'section_field_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gfield.gsection .gsection_title',
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'section_field_border_type',
				[
					'label'                 => __('Border Type', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'default'               => 'solid',
					'options'               => [
						'none'      => __('None', MELA_TD),
						'solid'     => __('Solid', MELA_TD),
						'double'    => __('Double', MELA_TD),
						'dotted'    => __('Dotted', MELA_TD),
						'dashed'    => __('Dashed', MELA_TD),
					],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield.gsection' => 'border-bottom-style: {{VALUE}}',
					],
					'separator'             => 'before',
				]
			);

			$this->add_responsive_control(
				'section_field_border_height',
				[
					'label'                 => __('Border Height', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size'  => 1,
					],
					'range'                 => [
						'px' => [
							'min'   => 1,
							'max'   => 20,
							'step'  => 1,
						],
					],
					'size_units'            => ['px'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield.gsection' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
					],
					'condition'             => [
						'section_field_border_type!'   => 'none',
					],
				]
			);

			$this->add_control(
				'section_field_border_color',
				[
					'label'                 => __('Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield.gsection' => 'border-bottom-color: {{VALUE}}',
					],
					'condition'             => [
						'section_field_border_type!'   => 'none',
					],
				]
			);

			$this->add_responsive_control(
				'section_field_margin',
				[
					'label'                 => __('Margin', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield.gsection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'separator'             => 'before',
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Section Field
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_price_style',
				[
					'label'                 => __('Price', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'price_label_color',
				[
					'label'                 => __('Price Label Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .ginput_product_price_label' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'price_text_color',
				[
					'label'                 => __('Price Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .ginput_product_price' => 'color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Placeholder
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_placeholder_style',
				[
					'label'                 => __('Placeholder', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
					'condition'             => [
						'placeholder_switch'   => 'yes',
					],
				]
			);

			$this->add_control(
				'text_color_placeholder',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield input::-webkit-input-placeholder, {{WRAPPER}} .ma-el-gravity-form .gfield textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
					],
					'condition'             => [
						'placeholder_switch'   => 'yes',
					],
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Radio & Checkbox
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_radio_checkbox_style',
				[
					'label'                 => __('Radio & Checkbox', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'custom_radio_checkbox',
				[
					'label'                 => __('Custom Styles', MELA_TD),
					'type'                  => Controls_Manager::SWITCHER,
					'label_on'              => __('Yes', MELA_TD),
					'label_off'             => __('No', MELA_TD),
					'return_value'          => 'yes',
				]
			);

			$this->add_responsive_control(
				'radio_checkbox_size',
				[
					'label'                 => __('Size', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size'      => '15',
						'unit'      => 'px'
					],
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 80,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}}',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->start_controls_tabs('tabs_radio_checkbox_style');

			$this->start_controls_tab(
				'radio_checkbox_normal',
				[
					'label'                 => __('Normal', MELA_TD),
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'radio_checkbox_color',
				[
					'label'                 => __('Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]' => 'background: {{VALUE}}',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_responsive_control(
				'radio_checkbox_border_width',
				[
					'label'                 => __('Border Width', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 15,
							'step'  => 1,
						],
					],
					'size_units'            => ['px'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'radio_checkbox_border_color',
				[
					'label'                 => __('Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]' => 'border-color: {{VALUE}}',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'checkbox_heading',
				[
					'label'                 => __('Checkbox', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'checkbox_border_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'radio_heading',
				[
					'label'                 => __('Radio Buttons', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'radio_border_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'radio_checkbox_checked',
				[
					'label'                 => __('Checked', MELA_TD),
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->add_control(
				'radio_checkbox_color_checked',
				[
					'label'                 => __('Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"]:checked:before, {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]:checked:before' => 'background: {{VALUE}}',
					],
					'condition'             => [
						'custom_radio_checkbox' => 'yes',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			/**
			 * Style Tab: Submit Button
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_submit_button_style',
				[
					'label'                 => __('Submit Button', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_responsive_control(
				'button_align',
				[
					'label'                 => __('Alignment', MELA_TD),
					'type'                  => Controls_Manager::CHOOSE,
					'options'               => [
						'left'        => [
							'title'   => __('Left', MELA_TD),
							'icon'    => 'eicon-h-align-left',
						],
						'center'      => [
							'title'   => __('Center', MELA_TD),
							'icon'    => 'eicon-h-align-center',
						],
						'right'       => [
							'title'   => __('Right', MELA_TD),
							'icon'    => 'eicon-h-align-right',
						],
					],
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer,
                    {{WRAPPER}} .ma-el-gravity-form .gform_page_footer'   => 'text-align: {{VALUE}};',
					],
					'condition'             => [
						'button_width_type!' => 'full-width',
					],
				]
			);

			$this->add_control(
				'button_width_type',
				[
					'label'                 => __('Width', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'default'               => 'custom',
					'options'               => [
						'auto'          => __('Auto', MELA_TD),
						'full-width'    => __('Full Width', MELA_TD),
						'custom'        => __('Custom', MELA_TD),
					],
					'prefix_class'          => 'ma-el-gravity-form-button-',
				]
			);

			$this->add_responsive_control(
				'button_width',
				[
					'label'                 => __('Width', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size'      => '100',
						'unit'      => 'px'
					],
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 1200,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'width: {{SIZE}}{{UNIT}}',
					],
					'condition'             => [
						'button_width_type' => 'custom',
					],
				]
			);

			$this->start_controls_tabs('tabs_button_style');

			$this->start_controls_tab(
				'tab_button_normal',
				[
					'label'                 => __('Normal', MELA_TD),
				]
			);

			$this->add_control(
				'button_bg_color_normal',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"],
                    {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'button_text_color_normal',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"],
                    {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'button_border_normal',
					'label'                 => __('Border', MELA_TD),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]',
				]
			);

			$this->add_control(
				'button_border_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_padding',
				[
					'label'                 => __('Padding', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'button_margin',
				[
					'label'                 => __('Margin Top', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]' => 'margin-top: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'button_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]',
					'separator'             => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'button_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"], {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]',
					'separator'             => 'before',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_button_hover',
				[
					'label'                 => __('Hover', MELA_TD),
				]
			);

			$this->add_control(
				'button_bg_color_hover',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]:hover' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'button_text_color_hover',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'button_border_color_hover',
				[
					'label'                 => __('Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_footer input[type="submit"]:hover, {{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="submit"]:hover' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			/**
			 * Style Tab: Pagination
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_pagination_style',
				[
					'label'                 => __('Pagination', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'pagination_buttons_width_type',
				[
					'label'                 => __('Width', MELA_TD),
					'type'                  => Controls_Manager::SELECT,
					'default'               => 'auto',
					'options'               => [
						'auto'          => __('Auto', MELA_TD),
						'full-width'    => __('Full Width', MELA_TD),
						'custom'        => __('Custom', MELA_TD),
					],
					'prefix_class'          => 'ma-el-gravity-form-pagination-buttons-',
				]
			);

			$this->add_responsive_control(
				'pagination_buttons_width',
				[
					'label'                 => __('Width', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'default'               => [
						'size'      => '100',
						'unit'      => 'px'
					],
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 1200,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'width: {{SIZE}}{{UNIT}}',
					],
					'condition'             => [
						'pagination_buttons_width_type' => 'custom',
					],
				]
			);

			$this->start_controls_tabs('tabs_pagination_buttons_style');

			$this->start_controls_tab(
				'tab_pagination_buttons_normal',
				[
					'label'                 => __('Normal', MELA_TD),
				]
			);

			$this->add_control(
				'pagination_buttons_bg_color_normal',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pagination_buttons_text_color_normal',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'pagination_buttons_border_normal',
					'label'                 => __('Border', MELA_TD),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]',
				]
			);

			$this->add_control(
				'pagination_buttons_border_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pagination_buttons_padding',
				[
					'label'                 => __('Padding', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'pagination_buttons_margin',
				[
					'label'                 => __('Margin Top', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'size_units'            => ['px', 'em', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]' => 'margin-top: {{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'pagination_buttons_typography',
					'label'                 => __('Typography', MELA_TD),
					'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]',
					'separator'             => 'before',
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'pagination_buttons_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]',
					'separator'             => 'before',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_pagination_buttons_hover',
				[
					'label'                 => __('Hover', MELA_TD),
				]
			);

			$this->add_control(
				'pagination_buttons_bg_color_hover',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]:hover' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pagination_buttons_text_color_hover',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]:hover' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'pagination_buttons_border_color_hover',
				[
					'label'                 => __('Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_page_footer input[type="button"]:hover' => 'border-color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->end_controls_section();

			/**
			 * Style Tab: Progress Bar
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_progress_bar_style',
				[
					'label'                 => __('Progress Bar', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->start_controls_tabs('tabs_progress_bar_style');

			$this->start_controls_tab(
				'tab_progress_bar_default',
				[
					'label'                 => __('Default', MELA_TD),
				]
			);

			$this->add_control(
				'progress_bar_default_bg',
				[
					'label'                 => __('Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar' => 'background-color: {{VALUE}}',
					],
				]
			);

			$this->add_control(
				'progress_bar_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_percentage span' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'progress_bar_typography',
					'label'                 => __('Typography', MELA_TD),
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_percentage span',
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'                  => 'progress_bar_default_border',
					'label'                 => __('Border', MELA_TD),
					'placeholder'           => '1px',
					'default'               => '1px',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar',
				]
			);

			$this->add_control(
				'progress_bar_border_radius',
				[
					'label'                 => __('Border Radius', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px', '%'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar, {{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_percentage, {{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_responsive_control(
				'progress_bar_default_padding',
				[
					'label'                 => __('Padding', MELA_TD),
					'type'                  => Controls_Manager::DIMENSIONS,
					'size_units'            => ['px'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'progress_bar_default_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar',
				]
			);

			$this->end_controls_tab();

			$this->start_controls_tab(
				'tab_progress_bar_progress',
				[
					'label'                 => __('Progress', MELA_TD),
				]
			);

			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'                  => 'progress_bar_bg',
					'label'                 => __('Background', MELA_TD),
					'types'                 => ['classic', 'gradient'],
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_percentage',
					'exclude'               => ['image'],
				]
			);

			$this->add_responsive_control(
				'progress_bar_height',
				[
					'label'                 => __('Height', MELA_TD),
					'type'                  => Controls_Manager::SLIDER,
					'range'                 => [
						'px'        => [
							'min'   => 0,
							'max'   => 100,
							'step'  => 1,
						],
					],
					'size_units'            => ['px'],
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_percentage, {{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar:after' => 'height: {{SIZE}}{{UNIT}}',
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar:after' => 'margin-top: -{{SIZE}}{{UNIT}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Box_Shadow::get_type(),
				[
					'name'                  => 'progress_bar_progress_box_shadow',
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar:after',
				]
			);

			$this->end_controls_tab();

			$this->end_controls_tabs();

			$this->add_control(
				'progress_bar_label_heading',
				[
					'label'                 => __('Label', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
				]
			);

			$this->add_control(
				'progress_bar_label_color',
				[
					'label'                 => __('Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_wrapper .gf_progressbar_title, {{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_step' => 'color: {{VALUE}}',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'                  => 'progress_bar_label_typography',
					'label'                 => __('Typography', MELA_TD),
					'selector'              => '{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_progressbar_wrapper .gf_progressbar_title, {{WRAPPER}} .ma-el-gravity-form .gform_wrapper .gf_step',
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Errors
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_error_style',
				[
					'label'                 => __('Errors', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'error_messages_heading',
				[
					'label'                 => __('Error Messages', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'condition'             => [
						'error_messages' => 'show',
					],
				]
			);

			$this->add_control(
				'error_message_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield .validation_message' => 'color: {{VALUE}}',
					],
					'condition'             => [
						'error_messages' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_errors_heading',
				[
					'label'                 => __('Validation Errors', MELA_TD),
					'type'                  => Controls_Manager::HEADING,
					'separator'             => 'before',
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_error_description_color',
				[
					'label'                 => __('Error Description Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .validation_error' => 'color: {{VALUE}}',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_error_border_color',
				[
					'label'                 => __('Error Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper .validation_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
						'{{WRAPPER}} .ma-el-gravity-form .gfield_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_errors_bg_color',
				[
					'label'                 => __('Error Field Background Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield_error' => 'background: {{VALUE}}',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_error_field_label_color',
				[
					'label'                 => __('Error Field Label Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gfield_error .gfield_label' => 'color: {{VALUE}}',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_error_field_input_border_color',
				[
					'label'                 => __('Error Field Input Border Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-color: {{VALUE}}',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->add_control(
				'validation_error_field_input_border_width',
				[
					'label'                 => __('Error Field Input Border Width', MELA_TD),
					'type'                  => Controls_Manager::NUMBER,
					'default'               => 1,
					'min'                   => 1,
					'max'                   => 10,
					'step'                  => 1,
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-width: {{VALUE}}px',
					],
					'condition'             => [
						'validation_errors' => 'show',
					],
				]
			);

			$this->end_controls_section();

			/**
			 * Style Tab: Thank You Message
			 * -------------------------------------------------
			 */
			$this->start_controls_section(
				'section_ty_style',
				[
					'label'                 => __('Thank You Message', MELA_TD),
					'tab'                   => Controls_Manager::TAB_STYLE,
				]
			);

			$this->add_control(
				'ty_message_text_color',
				[
					'label'                 => __('Text Color', MELA_TD),
					'type'                  => Controls_Manager::COLOR,
					'default'               => '',
					'selectors'             => [
						'{{WRAPPER}} .ma-el-gravity-form .gform_confirmation_wrapper .gform_confirmation_message' => 'color: {{VALUE}}',
					],
				]
			);

			$this->end_controls_section();
		 //Premium Code use block end
	}


	protected function render()
	{
		$settings = $this->get_settings();


		if (!class_exists('GFCommon')) {
			Master_Addons_Helper::jltma_elementor_plugin_missing_notice(array('plugin_name' => esc_html__('Gravity Form', MELA_TD)));
			return;
		}


		$this->add_render_attribute('master-addons-gf', 'class', [
			'master-addons-gf',
			'ma-cf',
			'ma-el-gravity-form',
			'master-addons-gf-' . esc_attr($this->get_id())
		]);

		if ($settings['labels_switch'] != 'yes') {
			$this->add_render_attribute('master-addons-gf', 'class', 'labels-hide');
		}

		if ($settings['placeholder_switch'] != 'yes') {
			$this->add_render_attribute('master-addons-gf', 'class', 'placeholder-hide');
		}

		if ($settings['custom_title_description'] == 'yes') {
			$this->add_render_attribute('master-addons-gf', 'class', 'title-description-hide');
		}

		if ($settings['custom_radio_checkbox'] == 'yes') {
			$this->add_render_attribute('master-addons-gf', 'class', 'ma-el-custom-radio-checkbox');
		}

		if (class_exists('GFCommon')) {
			if (!empty($settings['contact_form_list'])) { ?>
				<div <?php echo $this->get_render_attribute_string('master-addons-gf'); ?>>
					<?php if ($settings['custom_title_description'] == 'yes') { ?>
						<div class="ma-el-gravity-form-heading">
							<?php if ($settings['form_title_custom'] != '') { ?>
								<h3 class="master-addons-gf-title ma-el-gravity-form-title">
									<?php echo esc_attr($settings['form_title_custom']); ?>
								</h3>
							<?php } ?>
							<?php if ($settings['form_description_custom'] != '') { ?>
								<div class="master-addons-gf-description ma-el-gravity-form-description">
									<?php echo $this->parse_text_editor($settings['form_description_custom']); ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
					$jltma_form_id = $settings['contact_form_list'];
					$jltma_form_title = $settings['form_title'];
					$jltma_form_description = $settings['form_description'];
					$jltma_form_ajax = $settings['form_ajax'];

					gravity_form($jltma_form_id, $jltma_form_title, $jltma_form_description, $display_inactive = false, $field_values = null, $jltma_form_ajax, '', $echo = true);
					?>
				</div>
<?php
			} else {
				esc_html__e('Please select a Contact Form!', MELA_TD);
			}
		}
	}

	protected function _content_template()
	{
	}
}
