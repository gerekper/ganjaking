<?php

namespace MasterAddons\Addons;

// Elementor Classes
use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Typography;
use Elementor\Scheme_Color;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class WP_Forms extends Widget_Base
{

	public function get_name()
	{
		return 'ma-wpforms';
	}

	public function get_title()
	{
		return esc_html__('WPForms', MELA_TD);
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
		return 'https://master-addons.com/demos/wp-forms/';
	}

	protected function _register_controls()
	{

		/*-----------------------------------------------------------------------------------*/
		/*	Content Tab
			/*-----------------------------------------------------------------------------------*/

		$this->start_controls_section(
			'section_info_box',
			[
				'label'             => __('WPForms', MELA_TD),
			]
		);

		$this->add_control(
			'contact_form_list',
			[
				'label'             => esc_html__('Contact Form', MELA_TD),
				'type'              => Controls_Manager::SELECT,
				'label_block'       => true,
				'options'           => Master_Addons_Helper::ma_el_get_wpforms_forms(),
				'default'           => '0',
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
				'default'               => 'no',
			]
		);

		$this->add_control(
			'form_title',
			[
				'label'                 => __('Title', MELA_TD),
				'type'                  => Controls_Manager::SWITCHER,
				'label_on'              => __('Show', MELA_TD),
				'label_off'             => __('Hide', MELA_TD),
				'return_value'          => 'yes',
				'condition'             => [
					'custom_title_description!'   => 'yes',
				],
				'default'               => 'no',
			]
		);

		$this->add_control(
			'form_description',
			[
				'label'                 => __('Description', MELA_TD),
				'type'                  => Controls_Manager::SWITCHER,
				'label_on'              => __('Show', MELA_TD),
				'label_off'             => __('Hide', MELA_TD),
				'return_value'          => 'yes',
				'condition'             => [
					'custom_title_description!'   => 'yes',
				],
				'default'               => 'no',
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
				'prefix_class'          => 'ma-el-wpforms-labels-',
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
					'{{WRAPPER}} .ma-el-wpforms label.wpforms-error' => 'display: {{VALUE}} !important;',
				],
			]
		);

		$this->end_controls_section();

		/*-----------------------------------------------------------------------------------*/
		/*	STYLE TAB
			/*-----------------------------------------------------------------------------------*/


		/**
		 * Style Tab: Form Title & Description
		 * -------------------------------------------------
		 */

		$this->start_controls_section(
			'ma_wpform_section_style',
			[
				'label' => esc_html__('Design Layout', MELA_TD),
				'tab'                   => Controls_Manager::TAB_STYLE,
			]
		);


		// Premium Version Codes
		
			$this->add_control(
				'ma_wpform_layout_style',
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
						'6'   => __('Floating Label', MELA_TD),
						'7'   => __('Style Seven', MELA_TD),
						'8'   => __('Style Eight', MELA_TD),
						'9'   => __('Style Nine', MELA_TD),
						'10'  => __('Style Ten', MELA_TD),
						'11'  => __('Style Eleven', MELA_TD),
					],
				]
			);
		

		$this->end_controls_section();



		$this->start_controls_section(
			'section_form_title_style',
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
					'{{WRAPPER}} .wpforms-head-container, {{WRAPPER}} .ma-el-wpforms-heading' => 'text-align: {{VALUE}};',
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
			'form_title_text_color',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-title, {{WRAPPER}} .wpforms-title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_title_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-contact-form-title, {{WRAPPER}} .wpforms-title',
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
					'{{WRAPPER}} .ma-el-contact-form-title, {{WRAPPER}} .wpforms-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
			'form_description_text_color',
			[
				'label'                 => __('Text Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-contact-form-description, {{WRAPPER}} .wpforms-description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'form_description_typography',
				'label'                 => __('Typography', MELA_TD),
				'scheme'                => Scheme_Typography::TYPOGRAPHY_4,
				'selector'              => '{{WRAPPER}} .ma-el-contact-form-description, {{WRAPPER}} .wpforms-description',
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
					'{{WRAPPER}} .ma-el-contact-form-description, {{WRAPPER}} .wpforms-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label'             => __('Labels', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'typography_label',
				'label'             => __('Typography', MELA_TD),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field label',
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
				'label'             => __('Input & Textarea', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'text-align: {{VALUE}};',
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
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'field_border',
				'label'             => __('Border', MELA_TD),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->add_control(
			'field_radius',
			[
				'label'             => __('Border Radius', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
				'separator'         => 'before',
			]
		);

		$this->add_responsive_control(
			'input_width',
			[
				'label'             => __('Input Width', MELA_TD),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label'             => __('Input Height', MELA_TD),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 80,
						'step'  => 1,
					],
				],
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_width',
			[
				'label'             => __('Textarea Width', MELA_TD),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 1200,
						'step'  => 1,
					],
				],
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field textarea' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'             => __('Textarea Height', MELA_TD),
				'type'              => Controls_Manager::SLIDER,
				'range'             => [
					'px' => [
						'min'   => 0,
						'max'   => 400,
						'step'  => 1,
					],
				],
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'             => __('Padding', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'         => 'before',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'field_typography',
				'label'             => __('Typography', MELA_TD),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'field_box_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea, {{WRAPPER}} .ma-el-wpforms .wpforms-field select',
				'separator'         => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			[
				'label'                 => __('Focus', MELA_TD),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'focus_input_border',
				'label'             => __('Border', MELA_TD),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field input:focus, {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'focus_box_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-field input:focus, {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea:focus',
				'separator'         => 'before',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-sublabel' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'                  => 'field_description_typography',
				'label'                 => __('Typography', MELA_TD),
				'selector'              => '{{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-sublabel',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-description, {{WRAPPER}} .ma-el-wpforms .wpforms-field .wpforms-field-sublabel' => 'padding-top: {{SIZE}}{{UNIT}}',
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
				'label'             => __('Placeholder', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
				'condition'             => [
					'placeholder_switch'   => 'yes',
				],
			]
		);

		$this->add_control(
			'text_color_placeholder',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-field input::-webkit-input-placeholder, {{WRAPPER}} .ma-el-wpforms .wpforms-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
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
				'label'             => __('Submit Button', MELA_TD),
				'tab'               => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'             => __('Alignment', MELA_TD),
				'type'              => Controls_Manager::CHOOSE,
				'options'           => [
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
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container'   => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'display:inline-block;'
				],
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
				'prefix_class'          => 'ma-el-wpforms-form-button-',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'width: {{SIZE}}{{UNIT}}',
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
				'label'             => __('Normal', MELA_TD),
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'              => 'button_border_normal',
				'label'             => __('Border', MELA_TD),
				'placeholder'       => '1px',
				'default'           => '1px',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit',
			]
		);

		$this->add_control(
			'button_border_radius',
			[
				'label'             => __('Border Radius', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'             => __('Padding', MELA_TD),
				'type'              => Controls_Manager::DIMENSIONS,
				'size_units'        => ['px', 'em', '%'],
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'              => 'button_typography',
				'label'             => __('Typography', MELA_TD),
				'scheme'            => Scheme_Typography::TYPOGRAPHY_4,
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit',
				'separator'         => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'              => 'button_box_shadow',
				'selector'          => '{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit',
				'separator'         => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label'             => __('Hover', MELA_TD),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'             => __('Background Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'             => __('Text Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'             => __('Border Color', MELA_TD),
				'type'              => Controls_Manager::COLOR,
				'default'           => '',
				'selectors'         => [
					'{{WRAPPER}} .ma-el-wpforms .wpforms-submit-container .wpforms-submit:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
					'{{WRAPPER}} .ma-el-wpforms label.wpforms-error' => 'color: {{VALUE}}',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_field_input_border_color',
			[
				'label'                 => __('Error Field Input Border Color', MELA_TD),
				'type'                  => Controls_Manager::COLOR,
				'default'               => '',
				'selectors'             => [
					'{{WRAPPER}} .ma-el-wpforms input.wpforms-error, {{WRAPPER}} .ma-el-wpforms textarea.wpforms-error'
					=> 'border-color: {{VALUE}}',
				],
				'condition'             => [
					'error_messages' => 'show',
				],
			]
		);

		$this->add_control(
			'error_field_input_border_width',
			[
				'label'                 => __('Error Field Input Border Width', MELA_TD),
				'type'                  => Controls_Manager::NUMBER,
				'default'               => 1,
				'min'                   => 1,
				'max'                   => 10,
				'step'                  => 1,
				'selectors'             => [
					'{{WRAPPER}} .ma-el-wpforms input.wpforms-error, {{WRAPPER}} .ma-el-wpforms textarea.wpforms-error'
					=> 'border-width: {{VALUE}}px',
				],
				'condition'             => [
					'error_messages' => 'show',
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
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/wp-forms/" target="_blank" rel="noopener">', '</a>'),
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

		// if WP Forms Missing
		if (!function_exists('wpforms')) {
			Master_Addons_Helper::jltma_elementor_plugin_missing_notice(array('plugin_name' => esc_html__('WP Forms', MELA_TD)));
			return;
		}


		$this->add_render_attribute(
			'contact-form',
			'class',
			[
				'ma-el-contact-form',
				'ma-el-wpforms',
				'ma-cf',
				'ma-cf-' . $settings['ma_wpform_layout_style'],
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

		if (class_exists('WPForms')) {
			if (!empty($settings['contact_form_list'])) { ?>
				<div <?php echo $this->get_render_attribute_string('contact-form'); ?>>
					<?php if ($settings['custom_title_description'] == 'yes') { ?>
						<div class="ma-el-wpforms-heading">
							<?php if ($settings['form_title_custom'] != '') { ?>
								<h3 class="ma-el-contact-form-title ma-el-wpforms-title">
									<?php echo esc_attr($settings['form_title_custom']); ?>
								</h3>
							<?php } ?>
							<?php if ($settings['form_description_custom'] != '') { ?>
								<div class="ma-el-contact-form-description ma-el-wpforms-description">
									<?php echo $this->parse_text_editor($settings['form_description_custom']); ?>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
					<?php
					$ma_el_form_title = $settings['form_title'];
					$ma_el_form_description = $settings['form_description'];

					if ($settings['custom_title_description'] == 'yes') {
						$ma_el_form_title = false;
						$ma_el_form_description = false;
					}

					echo wpforms_display(
						$settings['contact_form_list'],
						$ma_el_form_title,
						$ma_el_form_description
					);
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
