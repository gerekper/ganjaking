<?php
namespace ElementPack\Modules\FluentForms\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Fluent_Forms extends Module_Base {

	public function get_name() {
		return 'bdt-fluent-forms';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Fluent Forms', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-fluent-forms';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'fluent', 'ninja', 'form', 'contact', 'custom', 'builder' ];
	}

    public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-fluent-forms' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/BWPuKe4PfQ4';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'fluent_form',
			[
				'label'   => esc_html__( 'Select Form', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_fluent_forms_options(),
			]
		);

		$this->end_controls_section();

		//Style
        $this->start_controls_section(
            'section_label_style',
            [
                'label' => __('Labels', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color_label',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group label' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'typography_label',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-el-group label',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_fields_style',
            [
                'label' => __('Input & Textarea', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'input_alignment',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-text-align-right',
                    ],
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'text-align: {{VALUE}};',
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
            'field_bg_color',
            [
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'field_text_color',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'field_border',
                'label' => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select',
                'separator' => 'before',
            ]
        );

        $this->add_control(
            'field_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_text_indent',
            [
                'label' => __('Text Indent', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 60,
                        'step' => 1,
                    ],
                    '%' => [
                        'min' => 0,
                        'max' => 30,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'text-indent: {{SIZE}}{{UNIT}}',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'input_width',
            [
                'label' => __('Input Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'input_height',
            [
                'label' => __('Input Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'textarea_width',
            [
                'label' => __('Textarea Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea' => 'width: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'textarea_height',
            [
                'label' => __('Textarea Height', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 400,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea' => 'height: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_spacing',
            [
                'label' => __('Spacing', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'field_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'field_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]), {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea, {{WRAPPER}} .bdt-fluent-forms .ff-el-group select',
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
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea:focus' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'focus_input_border',
                'label' => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea:focus',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'focus_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms input:not([type=radio]):not([type=checkbox]):not([type=submit]):not([type=button]):not([type=image]):not([type=file]):focus, {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea:focus',
                'separator' => 'before',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_placeholder_style',
            [
                'label' => __('Placeholder', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'text_color_placeholder',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group input::-webkit-input-placeholder, {{WRAPPER}} .bdt-fluent-forms .ff-el-group textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_radio_checkbox_style',
            [
                'label' => __('Radio & Checkbox', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'custom_radio_checkbox',
            [
                'label' => __('Custom Styles', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => __('Yes', 'bdthemes-element-pack'),
                'label_off' => __('No', 'bdthemes-element-pack'),
                'return_value' => 'yes',
            ]
        );

        $this->add_responsive_control(
            'radio_checkbox_size',
            [
                'label' => __('Size', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'default' => [
                    'size' => '15',
                    'unit' => 'px',
                ],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 80,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}',
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
                'label' => __('Normal', 'bdthemes-element-pack'),
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'radio_checkbox_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'checkbox_border_width',
            [
                'label' => __('Border Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 15,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'checkbox_border_color',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]' => 'border-color: {{VALUE}}',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'checkbox_heading',
            [
                'label' => __('Checkbox', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'checkbox_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'radio_heading',
            [
                'label' => __('Radio Buttons', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'radio_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"], {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'radio_checkbox_checked',
            [
                'label' => __('Checked', 'bdthemes-element-pack'),
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'radio_checkbox_color_checked',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="checkbox"]:checked:before, {{WRAPPER}} .bdt-fluent-forms.bdt-custom-radio-checkbox input[type="radio"]:checked:before' => 'background: {{VALUE}}',
                ],
                'condition' => [
                    'custom_radio_checkbox' => 'yes',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

        $this->end_controls_section();

        $this->start_controls_section(
            'section_break_style',
            [
                'label' => __('Section Break Style', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'section_break_label',
            [
                'label' => __('Label', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING
            ]
        );

        $this->add_control(
            'section_break_label_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break .ff-el-section-title' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'section_break_label_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '.bdt-fluent-forms .ff-el-section-break .ff-el-section-title',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'section_break_label_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break .ff-el-section-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_break_label_margin',
            [
                'label' => __('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break .ff-el-section-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_control(
            'section_break_description',
            [
                'label' => __('Description', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'section_break_description_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break div' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'section_break_description_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break div',
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'section_break_description_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_break_description_margin',
            [
                'label' => __('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-section-break div' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'section_break_alignment',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'prefix_class' => 'bdt-fluentform-section-break-content-'
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_address_line_style',
            [
                'label' => __('Address Line Style', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'address_line_label_color',
            [
                'label' => __('Label Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .fluent-address label' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'address_line_label_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .fluent-address label',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_submit_button_style',
            [
                'label' => __('Submit Button', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'button_align',
            [
                'label' => __('Alignment', 'bdthemes-element-pack'),
                'type' => Controls_Manager::CHOOSE,
                'options' => [
                    'left' => [
                        'title' => __('Left', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-left',
                    ],
                    'center' => [
                        'title' => __('Center', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-center',
                    ],
                    'right' => [
                        'title' => __('Right', 'bdthemes-element-pack'),
                        'icon' => 'eicon-h-align-right',
                    ],
                ],
                'default' => '',
                'prefix_class' => 'bdt-fluentform-form-button-',
                'condition' => [
                    'button_width_type' => 'custom',
                ],
            ]
        );

        $this->add_control(
            'button_width_type',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SELECT,
                'default' => 'custom',
                'options' => [
                    'full-width' => __('Full Width', 'bdthemes-element-pack'),
                    'custom' => __('Custom', 'bdthemes-element-pack'),
                ],
                'prefix_class' => 'bdt-fluentform-form-button-',
            ]
        );

        $this->add_responsive_control(
            'button_width',
            [
                'label' => __('Width', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 1200,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition' => [
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
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#409EFF',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_normal',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ffffff',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'button_border_normal',
                'label' => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit',
            ]
        );

        $this->add_control(
            'button_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'button_margin',
            [
                'label' => __('Margin Top', 'bdthemes-element-pack'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit' => 'margin-top: {{SIZE}}{{UNIT}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'button_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit',
                'separator' => 'before',
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name' => 'button_box_shadow',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit',
                'separator' => 'before',
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
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'background-color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_text_color_hover',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_control(
            'button_border_color_hover',
            [
                'label' => __('Border Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-el-group .ff-btn-submit:hover' => 'border-color: {{VALUE}}',
                ],
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

		$this->end_controls_section();


        // Style Tab: Success Message

		if( defined("FLUENTFORMPRO") ) {
             
            $this->start_controls_section(
                'section_pagination_style',
                [
                    'label' => __('Pagination', 'bdthemes-element-pack'),
                    'tab' => Controls_Manager::TAB_STYLE,
                ]
            );
    
            $this->start_controls_tabs('form_progressbar_style_tabs');
    
                $this->start_controls_tab(
                    'form_progressbar_normal',
                    [
                        'label' => __('Normal', 'bdthemes-element-pack'),
                    ]
                );
    
                $this->add_control(
                    'pagination_progressbar_label',
                    [
                        'label' => __('Label', 'bdthemes-element-pack'),
                        'type' => Controls_Manager::HEADING
                    ]
                );
    
                $this->add_control(
                    'show_label',
                    [
                        'label'     => __( 'Show Label', 'bdthemes-element-pack' ),
                        'type'      => Controls_Manager::SWITCHER,
                        'label_on'  => __( 'Show', 'bdthemes-element-pack' ),
                        'label_off' => __( 'Hide', 'bdthemes-element-pack' ),
                        'return_value' => 'yes',
                        'default'   => 'yes',
                        'prefix_class'  => 'bdt-ff-step-header-'
                    ]
                );
    
                $this->add_control(
                    'label_color',
                    [
                        'label'     => __( 'Label Color', 'bdthemes-element-pack' ),
                        'type'      => Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .ff-el-progress-status' => 'color: {{VALUE}}',
                        ],
                        'condition' => [
                            'show_label'    => 'yes'
                        ]
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'label_typography',
                        'label' => __( 'Typography', 'bdthemes-element-pack' ),
                        'selector' => '{{WRAPPER}} .ff-el-progress-status',
                        'condition' => [
                            'show_label'    => 'yes'
                        ]
                    ]
                );
    
                $this->add_control(
                    'label_space',
                    [
                        'label' => __( 'Spacing', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .ff-el-progress-status' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'condition' => [
                            'show_label'    => 'yes'
                        ],
                        'separator' => 'after'
                    ]
                );
    
                $this->add_control(
                    'pagination_progressbar',
                    [
                        'label' => __('Progressbar', 'bdthemes-element-pack'),
                        'type' => Controls_Manager::HEADING,
                    ]
                );
    
                $this->add_control(
                    'show_progressbar',
                    [
                        'label'     => __( 'Show Progressbar', 'bdthemes-element-pack' ),
                        'type'      => Controls_Manager::SWITCHER,
                        'label_on'  => __( 'Show', 'bdthemes-element-pack' ),
                        'label_off' => __( 'Hide', 'bdthemes-element-pack' ),
                        'return_value' => 'yes',
                        'default'   => 'yes',
                        'prefix_class'  => 'bdt-ff-step-progressbar-'
                    ]
                );
    
                $this->add_control(
                    'progressbar_height',
                    [
                        'label' => __( 'Height', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::SLIDER,
                        'size_units' => [ 'px' ],
                        'range' => [
                            'px' => [
                                'min' => 0,
                                'max' => 100,
                                'step' => 1,
                            ]
                        ],
                        'selectors' => [
                            '{{WRAPPER}} .ff-el-progress' => 'height: {{SIZE}}{{UNIT}};',
                        ],
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ]
                    ]
                );
    
                $this->add_control(
                    'progressbar_color',
                    [
                        'label' => __( 'Title Color', 'bdthemes-element-pack' ),
                        'type'  =>   Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .ff-el-progress-bar span' => 'color: {{VALUE}};',
                        ],
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ]
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'progressbar_border',
                        'label' => __( 'Border', 'bdthemes-element-pack' ),
                        'selector' => '{{WRAPPER}} .ff-el-progress',
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ]
                    ]
                );
    
                $this->add_control(
                    'progressbar_border_radius',
                    [
                        'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .ff-el-progress' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ]
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'progressbar_bg',
                        'label' => __( 'Background', 'bdthemes-element-pack' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .ff-el-progress',
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ],
                        'exclude'    => [
                            'image'
                        ]
                    ]
                );
    
                $this->end_controls_tab();
    
                $this->start_controls_tab(
                    'form_progressbar_filled',
                    [
                        'label' => __('Filled', 'bdthemes-element-pack'),
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'progressbar_bg_filled',
                        'label' => __( 'Background', 'bdthemes-element-pack' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .ff-el-progress-bar',
                        'condition' => [
                            'show_progressbar'  => 'yes'
                        ],
                        'exclude'    => [
                            'image'
                        ]
                    ]
                );
    
    
                $this->end_controls_tab();
    
            $this->end_controls_tabs();
    
    
            $this->start_controls_tabs(
                'form_pagination_button_style_tabs',
                [
                    'separator' => 'before'
                ]
            );
    
                $this->start_controls_tab(
                    'form_pagination_button',
                    [
                        'label' => __('Normal', 'bdthemes-element-pack'),
                    ]
                );
    
                $this->add_control(
                    'pagination_button_style',
                    [
                        'label' => __('Button', 'bdthemes-element-pack'),
                        'type' => Controls_Manager::HEADING
                    ]
                );
    
                $this->add_control(
                    'pagination_button_color',
                    [
                        'label' => __( 'Color', 'bdthemes-element-pack' ),
                        'type'  =>   Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .step-nav button' => 'color: {{VALUE}};',
                        ]
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Typography::get_type(),
                    [
                        'name' => 'pagination_button_typography',
                        'label' => __( 'Typography', 'bdthemes-element-pack' ),
                        'selector' => '{{WRAPPER}} .step-nav button',
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'pagination_button_bg',
                        'label' => __( 'Background', 'bdthemes-element-pack' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .step-nav button',
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Border::get_type(),
                    [
                        'name' => 'pagination_button_border',
                        'label' => __( 'Border', 'bdthemes-element-pack' ),
                        'selector' => '{{WRAPPER}} .step-nav button',
                    ]
                );
    
                $this->add_control(
                    'pagination_button_border_radius',
                    [
                        'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .step-nav button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
    
                $this->add_control(
                    'pagination_button_padding',
                    [
                        'label' => __( 'Padding', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .step-nav button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
    
                $this->end_controls_tab();
    
                $this->start_controls_tab(
                    'form_pagination_button_hover',
                    [
                        'label' => __('Hover', 'bdthemes-element-pack'),
                    ]
                );
    
                $this->add_control(
                    'pagination_button_hover_color',
                    [
                        'label' => __( 'Color', 'bdthemes-element-pack' ),
                        'type'  =>   Controls_Manager::COLOR,
                        'selectors' => [
                            '{{WRAPPER}} .step-nav button:hover' => 'color: {{VALUE}};',
                        ]
                    ]
                );
    
                $this->add_group_control(
                    Group_Control_Background::get_type(),
                    [
                        'name' => 'pagination_button_hover_bg',
                        'label' => __( 'Background', 'bdthemes-element-pack' ),
                        'types' => [ 'classic', 'gradient' ],
                        'selector' => '{{WRAPPER}} .step-nav button:hover',
                    ]
                );
    
                $this->add_control(
                    'pagination_button_border_hover_radius',
                    [
                        'label' => __( 'Border Radius', 'bdthemes-element-pack' ),
                        'type' => Controls_Manager::DIMENSIONS,
                        'size_units' => [ 'px', '%', 'em' ],
                        'selectors' => [
                            '{{WRAPPER}} .step-nav button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                        ],
                    ]
                );
    
                $this->end_controls_tab();
    
            $this->end_controls_tabs();
    
    
            $this->end_controls_section();
         }

        $this->start_controls_section(
            'section_success_message_style',
            [
                'label' => __('Success Message', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'success_message_bg_color',
            [
                'label' => __('Background Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-message-success' => 'background-color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'success_message_text_color',
            [
                'label' => __('Text Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .ff-message-success' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'success_message_border',
                'label' => __('Border', 'bdthemes-element-pack'),
                'placeholder' => '1px',
                'default' => '1px',
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-message-success',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'success_message_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .ff-message-success',
            ]
        );

        $this->end_controls_section();

        $this->start_controls_section(
            'section_error_style',
            [
                'label' => __('Error', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'error_messages_heading',
            [
                'label' => __('Error Messages', 'bdthemes-element-pack'),
                'type' => Controls_Manager::HEADING,
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->add_control(
            'error_message_text_color',
            [
                'label' => __('Color', 'bdthemes-element-pack'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .error.text-danger' => 'color: {{VALUE}}',
                ],
                'condition' => [
                    'error_messages' => 'show',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'error_message_typography',
                'label' => __('Typography', 'bdthemes-element-pack'),
                'selector' => '{{WRAPPER}} .bdt-fluent-forms .error.text-danger',
            ]
        );

        $this->add_responsive_control(
            'error_message_padding',
            [
                'label' => __('Padding', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .error.text-danger' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'error_message_margin',
            [
                'label' => __('Margin', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .bdt-fluent-forms .error.text-danger' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['fluent_form']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select a Fluent Forms From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'id' => $settings['fluent_form'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[fluentform %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'fluent_wrapper', 'class', 'bdt-fluent-forms' );
        
        if ( $settings['custom_radio_checkbox'] == 'yes' ) {
            $this->add_render_attribute( 'fluent_wrapper', 'class', 'bdt-custom-radio-checkbox' );
        }
		
		?>

		<div <?php echo $this->get_render_attribute_string('fluent_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
