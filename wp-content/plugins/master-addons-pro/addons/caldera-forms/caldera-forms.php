<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Box_Shadow;

use MasterAddons\Inc\Helper\Master_Addons_Helper;


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.


class Caldera_Forms extends Widget_Base
{

	public function get_name()
	{
		return 'ma-caldera-forms';
	}

	public function get_title()
	{
		return esc_html__('Caldera Forms', MELA_TD);
	}

	public function get_icon()
	{
		return 'ma-el-icon eicon-mail';
	}

	public function get_categories()
	{
		return ['master-addons'];
	}


	protected function _register_controls()
	{

		/*-----------------------------------------------------------------------------------*/
		/*	Content Tab
			/*-----------------------------------------------------------------------------------*/

		/**
		 * Content Tab: Caldera Forms
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_info_box',
			[
				'label'                 => esc_html__('Caldera Forms', MELA_TD),
			]
		);

		$this->add_control(
			'contact_form_list',
			[
				'label'                 => esc_html__('Contact Form', MELA_TD),
				'type'                  => Controls_Manager::SELECT,
				'label_block'           => true,
				'options'               => Master_Addons_Helper::ma_el_get_caldera_forms(),
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
				'prefix_class'          => 'ma-el-caldera-form-labels-',
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
					'{{WRAPPER}} .ma-el-caldera-form .has-error .parsley-required' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*	Style Tab
			/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'ma_caldera_form_section_style',
			[
				'label'                 => esc_html__('Design Layout', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);


		// Premium Version Codes
		

			$this->add_control(
				'ma_caldera_form_layout_style',
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
					],
				]
			);
		
		$this->end_controls_section();



		/**
		 * Style Tab: Form Title & Description
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_form_title_style',
			[
				'label'                 => __('Title & Description', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
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
					'{{WRAPPER}} .ma-el-caldera-form-heading' => 'text-align: {{VALUE}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'title_heading',
			[
				'label'                 => __('Title', MELA_TD),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_title_text_color',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-title' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_title_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-contact-form-title',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'form_title_margin',
			[
				'label'                 => __('Margin', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'description_heading',
			[
				'label'                 => __('Description', MELA_TD),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_control(
			'form_description_text_color',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-description' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_description_typography',
				'label'                 => __('Typography', MELA_TD),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .ma-el-contact-form-description',
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'form_description_margin',
			[
				'label'                 => __('Margin', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'allowed_dimensions'    => 'vertical',
				'placeholder'           => [
					'top'      => '',
					'right'    => 'auto',
					'bottom'   => '',
					'left'     => 'auto',
				],
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_title_description'   => 'yes',
				],
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
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .form-group label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'typography_label',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .form-group label',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'text-align: {{VALUE}};',
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
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_text_indent',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
				'separator'             => 'before'
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'height: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group textarea' => 'width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'                 => __('Padding', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'field_box_shadow',
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .form-group textarea, {{WRAPPER}} .ma-el-caldera-form .form-group select',
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
					'{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .ma-el-caldera-form .form-group textarea:focus' => 'background-color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .ma-el-caldera-form .form-group textarea:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'focus_box_shadow',
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .ma-el-caldera-form .form-group textarea:focus',
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
					'{{WRAPPER}} .ma-el-caldera-form .help-block' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_description_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .help-block',
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
					'{{WRAPPER}} .ma-el-caldera-form .help-block' => 'padding-top: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input::-webkit-input-placeholder, {{WRAPPER}} .ma-el-caldera-form .form-group textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .ma-el-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .ma-el-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
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
			'checkbox_border_width',
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
			'checkbox_border_color',
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
				'prefix_class'          => 'ma-el-caldera-form-button-',
				'condition'             => [
					'button_width_type' => 'custom',
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
					'full-width'    => __('Full Width', MELA_TD),
					'custom'        => __('Custom', MELA_TD),
				],
				'prefix_class'          => 'ma-el-caldera-form-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'                 => __('Width', MELA_TD),
				'type'                  => Controls_Manager::SLIDER,
				'default'               => [
					'size'      => '135',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'width: {{SIZE}}{{UNIT}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'color: {{VALUE}}',
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
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'                 => __('Border Radius', MELA_TD),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'button_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]',
				'separator'             => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'                  => 'button_box_shadow',
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"], {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"]:hover, {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]:hover' => 'background-color: {{VALUE}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"]:hover, {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]:hover' => 'color: {{VALUE}}',
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
					'{{WRAPPER}} .ma-el-caldera-form .form-group input[type="submit"]:hover, {{WRAPPER}} .ma-el-caldera-form .form-group input[type="button"]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Style Tab: Success Message
		 * -------------------------------------------------
		 */
		$this->start_controls_section(
			'section_success_message_style',
			[
				'label'                 => __('Success Message', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'success_message_bg_color',
			[
				'label'                 => __('Background Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .caldera-grid .alert-success' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'success_message_text_color',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .caldera-grid .alert-success' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'success_message_border',
				'label'                 => __('Border', MELA_TD),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .caldera-grid .alert-success',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'success_message_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .caldera-grid .alert-success',
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
				'label'                 => __('Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .has-error .help-block' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_fields_heading',
			[
				'label'                 => __('Error Fields', MELA_TD),
				'type'                  => Controls_Manager::HEADING,
				'separator'             => 'before',
			]
		);

		$this->add_control(
			'error_fields_label_color',
			[
				'label'                 => __('Label Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-caldera-form .has-error .control-label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'error_field_border',
				'label'                 => __('Input Border', MELA_TD),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .ma-el-caldera-form .has-error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-caldera-form .has-error textarea',
			]
		);

		$this->end_controls_section();


		
	}

	protected function render()
	{

		$settings = $this->get_settings();

		// if Caldera Forms Missing
		if (!class_exists('Caldera_Forms')) {
			Master_Addons_Helper::jltma_elementor_plugin_missing_notice(array('plugin_name' => esc_html__('Caldera Forms', MELA_TD)));
			return;
		}

		$this->add_render_attribute(
			'contact-form',
			'class',
			[
				'ma-cf',
				'ma-el-caldera-form',
				'ma-cf',
				'ma-cf-' . $settings['ma_caldera_form_layout_style'],
			]
		);

		if ($settings['placeholder_switch'] != 'yes') {
			$this->add_render_attribute('contact-form', 'class', 'placeholder-hide');
		}

		if ($settings['custom_title_description'] == 'yes') {
			$this->add_render_attribute('contact-form', 'class', 'title-description-hide');
		}

		if ($settings['custom_radio_checkbox'] == 'yes') {
			$this->add_render_attribute('contact-form', 'class', 'ma-el-custom-radio-checkbox');
		}

		if (class_exists('Caldera_Forms')) {
			if (!empty($settings['contact_form_list'])) { ?>
				<div <?php echo $this->get_render_attribute_string('contact-form'); ?>>
					<?php if ($settings['custom_title_description'] == 'yes') { ?>
						<div class="ma-el-caldera-form-heading">
							<?php if ($settings['form_title_custom'] != '') { ?>
								<h3 class="ma-el-contact-form-title ma-el-caldera-form-title">
									<?php echo esc_attr($settings['form_title_custom']); ?>
								</h3>
							<?php } ?>
							<?php if ($settings['form_description_custom'] != '') { ?>
								<div class="ma-el-contact-form-description ma-el-caldera-form-description">
									<?php echo $this->parse_text_editor($settings['form_description_custom']); ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
					$pp_form_id = $settings['contact_form_list'];

					echo do_shortcode('[caldera_form id="' . $pp_form_id . '" ]');
					?>
				</div>
<?php
			}
		}
	}


	protected function _content_template()
	{
	}
}
