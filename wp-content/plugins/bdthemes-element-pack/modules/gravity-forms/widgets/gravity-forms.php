<?php

namespace ElementPack\Modules\GravityForms\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Core\Schemes;
use Elementor\Group_Control_Box_Shadow;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Gravity_Forms extends Module_Base
{

	public function get_name()
	{
		return 'bdt-gravity-form';
	}

	public function get_title()
	{
		return BDTEP . esc_html__('Gravity Forms', 'bdthemes-element-pack');
	}

	public function get_icon()
	{
		return 'bdt-wi-gravity-forms';
	}

	public function get_categories()
	{
		return ['element-pack'];
	}

	public function get_keywords()
	{
		return ['gravity', 'form', 'contact', 'community'];
	}

	public function get_style_depends()
	{
		if ($this->ep_is_edit_mode()) {
			return ['gforms_formsmain_css', 'ep-styles'];
		} else {
			return ['ep-gravity-forms'];
		}
	}

	public function get_custom_help_url()
	{
		return 'https://youtu.be/452ZExESiBI';
	}

	public function is_reload_preview_required() {
		return true;
	}

	protected function register_controls()
	{
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'gravity_form',
			[
				'label'   => esc_html__('Select Form', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => element_pack_gravity_forms_options(),
			]
		);


		$this->add_control(
			'title_hide',
			[
				'label'   => __('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'description_hide',
			[
				'label'   => __('Description', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_sub_label',
			[
				'label'   => __('Sub label', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_above .ginput_complex.ginput_container label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_below .ginput_complex.ginput_container label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_above div[class*="gfield_time_"].ginput_container label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_below div[class*="gfield_time_"].ginput_container label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_above div[class*="gfield_date_"].ginput_container label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_below div[class*="gfield_date_"].ginput_container label' => 'display: block;',
				],
			]
		);

		$this->add_control(
			'form_ajax',
			[
				'label'       => __('Use Ajax', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'description' => __('Use ajax to submit the form', 'bdthemes-element-pack'),
			]
		);

		// $this->add_control(
		// 	'custom_attributes',
		// 	[
		// 		'label' => __('Custom Attributes', 'bdthemes-element-pack'),
		// 		'type' => Controls_Manager::TEXTAREA,
		// 		'dynamic' => [
		// 			'active' => true,
		// 		],
		// 		'placeholder' => __('key|value', 'bdthemes-element-pack'),
		// 		'description' => sprintf(__('Set custom attributes for the gravity form. Each attribute in a separate line. Separate attribute key from the value using %s character. for example: field_values|param_name1=value1', 'bdthemes-element-pack'), '<code>|</code>'),
		// 		'classes' => 'elementor-control-direction-ltr',
		// 	]
		// );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography_label',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-gravity-forms .gfield label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color_placeholder',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input::-webkit-input-placeholder, 
                     {{WRAPPER}} .bdt-gravity-forms .gfield textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'input_alignment',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'      => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
					 {{WRAPPER}} .bdt-gravity-forms .gfield textarea' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs('tabs_fields_style');

		$this->start_controls_tab(
			'tab_fields_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
                     {{WRAPPER}} .bdt-gravity-forms .gfield textarea, {{WRAPPER}} .bdt-gravity-forms .gfield select' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_bg_color',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
                     {{WRAPPER}} .bdt-gravity-forms .gfield textarea, {{WRAPPER}} .bdt-gravity-forms .gfield select' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
								  {{WRAPPER}} .bdt-gravity-forms .gfield textarea, {{WRAPPER}} .bdt-gravity-forms .gfield select',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'field_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
					 {{WRAPPER}} .bdt-gravity-forms .gfield textarea, 
                     {{WRAPPER}} .bdt-gravity-forms .gfield select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), 
					 {{WRAPPER}} .bdt-gravity-forms .gfield textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'field_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield, {{WRAPPER}} .bdt-gravity-forms .ginput_container_address span *' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_above .ginput_complex.ginput_container_address label, {{WRAPPER}} .bdt-gravity-forms .gform_wrapper .field_sublabel_below .ginput_complex.ginput_container_address label' => 'margin-top: -{{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'text_indent',
			[
				'label' => __('Text Indent', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 60,
					],
					'%' => [
						'min'  => 0,
						'max'  => 30,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
                     {{WRAPPER}} .bdt-gravity-forms .gfield textarea' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label' => __('Textarea Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gform_wrapper input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), 
				{{WRAPPER}} .bdt-gravity-forms .gfield textarea',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'field_box_shadow',
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
				{{WRAPPER}} .bdt-gravity-forms .gfield textarea, 
				{{WRAPPER}} .bdt-gravity-forms .gfield select',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			[
				'label' => __('Focus', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'field_bg_color_focus',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input:focus, 
    				 {{WRAPPER}} .bdt-gravity-forms .gfield textarea:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'focus_input_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-gravity-forms .gfield input:focus, 
								  {{WRAPPER}} .bdt-gravity-forms .gfield textarea:focus',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'focus_box_shadow',
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gfield input:focus, 
				 				{{WRAPPER}} .bdt-gravity-forms .gfield textarea:focus',
				'separator' => 'before',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_description_style',
			[
				'label' => __('Field Description', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'field_description_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield .gfield_description' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'field_description_typography',
				'label'    => __('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-gravity-forms .gfield .gfield_description',
			]
		);

		$this->add_responsive_control(
			'field_description_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units'            => ['px', 'em', '%'],
				'selectors'             => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield .gfield_description' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_field_style',
			[
				'label' => __('Section Field', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'section_field_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield.gsection .gsection_title' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'section_field_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gfield.gsection .gsection_title',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'section_field_border_type',
			[
				'label'   => __('Border Type', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __('None', 'bdthemes-element-pack'),
					'solid'  => __('Solid', 'bdthemes-element-pack'),
					'double' => __('Double', 'bdthemes-element-pack'),
					'dotted' => __('Dotted', 'bdthemes-element-pack'),
					'dashed' => __('Dashed', 'bdthemes-element-pack'),
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield.gsection' => 'border-bottom-style: {{VALUE}}',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'section_field_border_height',
			[
				'label'   => __('Border Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min'  => 1,
						'max'  => 20,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield.gsection' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'section_field_border_type!' => 'none',
				],
			]
		);

		$this->add_control(
			'section_field_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield.gsection' => 'border-bottom-color: {{VALUE}}',
				],
				'condition' => [
					'section_field_border_type!'   => 'none',
				],
			]
		);

		$this->add_responsive_control(
			'section_field_margin',
			[
				'label'      => __('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield.gsection' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_price_style',
			[
				'label' => __('Price', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'price_label_color',
			[
				'label'     => __('Price Label Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .ginput_product_price_label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'price_text_color',
			[
				'label'     => __('Price Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .ginput_product_price' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_radio_checkbox_style',
			[
				'label' => __('Radio & Checkbox', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'custom_radio_checkbox',
			[
				'label' => __('Custom Styles', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-custom-rc-',
			]
		);

		$this->add_responsive_control(
			'radio_checkbox_size',
			[
				'label'   => __('Size', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', 'rem'],
				'default'    => [
					'unit' => 'px',
					'size' => 20,
				],
				'range'      => [
					'px' => [
						'min' => 15,
						'max' => 50,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_checkbox input[type=checkbox], 
                      {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_radio input[type=radio]' => 'width: {{SIZE}}{{UNIT}} !important; height:{{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_radio_checkbox_style');

		$this->start_controls_tab(
			'radio_checkbox_normal',
			[
				'label'     => __('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_checkbox input[type=checkbox], 
                      {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_radio input[type=radio]' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'radio_checkbox_border_width',
			[
				'label' => __('Border Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 15,
					],
				],
				'size_units' => ['px'],
				'selectors'  => [
					'{{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper ul.gfield_checkbox li input[type=checkbox], {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper ul.gfield_radio li input[type=radio]' => 'border-width: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper ul.gfield_checkbox li input[type=checkbox], {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper ul.gfield_radio li input[type=radio]' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'checkbox_heading',
			[
				'label'     => __('Checkbox', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'checkbox_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}.bdt-custom-rc-yes input[type="checkbox"], 
					 {{WRAPPER}}.bdt-custom-rc-yes input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_heading',
			[
				'label'     => __('Radio Buttons', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'radio_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}}.bdt-custom-rc-yes input[type="radio"], 
					 {{WRAPPER}}.bdt-custom-rc-yes input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label'     => __('Checked', 'bdthemes-element-pack'),
				'condition' => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->add_control(
			'radio_checkbox_color_checked',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_radio input[type=radio]:checked, 
                     {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_checkbox input[type=checkbox]:checked, 
                     {{WRAPPER}}.bdt-custom-rc-yes .bdt-gravity-forms .gform_wrapper .gfield_checkbox input[type=checkbox]:indeterminate' => 'background-color: {{VALUE}}',
				],
				'condition'             => [
					'custom_radio_checkbox' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_submit_button',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'        => [
						'title'   => __('Left', 'bdthemes-element-pack'),
						'icon'    => 'eicon-h-align-left',
					],
					'center'      => [
						'title'   => __('Center', 'bdthemes-element-pack'),
						'icon'    => 'eicon-h-align-center',
					],
					'right'       => [
						'title'   => __('Right', 'bdthemes-element-pack'),
						'icon'    => 'eicon-h-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer'   => 'text-align: {{VALUE}};',
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'display:inline-block;'
				],
				'condition'             => [
					'button_width_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'button_width_type',
			[
				'label'   => __('Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'full-width' => __('Full Width', 'bdthemes-element-pack'),
					'custom'     => __('Custom', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-gravity-form-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'   => __('Width', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => '100',
					'unit' => 'px'
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1200,
					],
				],
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'width: {{SIZE}}{{UNIT}}',
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
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border_normal',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => __('Margin Top', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => __('Typography', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]',
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} .bdt-gravity-forms .gform_footer input[type="submit"]',
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_error_style',
			[
				'label' => __('Error', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'error_messages_heading',
			[
				'label'     => __('Error Messages', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'error_message_text_color',
			[
				'label'     => __('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield .validation_message' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_errors_heading',
			[
				'label'     => __('Validation Errors', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'validation_error_description_color',
			[
				'label'     => __('Error Description Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .validation_error' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_error_border_color',
			[
				'label'     => __('Error Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper .validation_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .bdt-gravity-forms .gfield_error' => 'border-top-color: {{VALUE}}; border-bottom-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_errors_bg_color',
			[
				'label'     => __('Error Field Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield_error' => 'background: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_error_field_label_color',
			[
				'label'     => __('Error Field Label Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield_error .gfield_label' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_error_field_input_border_color',
			[
				'label'     => __('Error Field Input Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), 
                    {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'validation_error_field_input_border_width',
			[
				'label'     => __('Error Field Input Border Width', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 1,
				'min'       => 1,
				'max'       => 10,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gform_wrapper li.gfield_error input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), 
                    {{WRAPPER}} .gform_wrapper li.gfield_error textarea' => 'border-width: {{VALUE}}px',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional_option',
			[
				'label' => esc_html__('Additional Option', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'fullwidth_input',
			[
				'label'     => esc_html__('Fullwidth Input', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'selectors'  => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield input[type="text"], 
                     {{WRAPPER}} .bdt-gravity-forms .gfield select' => 'width: 100%;',
				],
			]
		);

		$this->add_control(
			'fullwidth_textarea',
			[
				'label'     => esc_html__('Fullwidth Texarea', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'selectors' => [
					'{{WRAPPER}} .bdt-gravity-forms .gfield textarea' => 'width: 100%;',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		if (!$settings['gravity_form']) {
			return '<div class="bdt-alert bdt-alert-warning">' . __('Please select a Contact Form From Setting!', 'bdthemes-element-pack') . '</div>';
		}

		$this->add_render_attribute('contact-form', 'class', ['bdt-gravity-forms']);

		$id                  = (int) $settings['gravity_form'];
		$display_title       = (isset($settings['title_hide']) && $settings['title_hide'] == 'yes') ? true : false;
		$display_description = $settings['description_hide'] ? true : false;
		$display_inactive    = false;
		$field_values        = isset($field_values) ? $field_values : '';
		$ajax                = $settings['form_ajax'] ? true : false;
		$tabindex            = '0';
		$echo                = true;

		?>
		
		<div <?php echo $this->get_render_attribute_string('contact-form'); ?>>
			<?php gravity_form($id, $display_title, $display_description, $display_inactive, $field_values, $ajax, $tabindex, $echo); ?>
		</div>
		<?php
	}
}