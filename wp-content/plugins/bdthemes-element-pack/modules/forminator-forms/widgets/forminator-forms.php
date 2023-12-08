<?php
namespace ElementPack\Modules\ForminatorForms\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Forminator_Forms extends Module_Base {

	public function get_name() {
		return 'bdt-forminator-forms';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Forminator Forms', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-forminator-forms';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'forminator', 'ninja', 'form', 'contact', 'custom', 'builder', 'forms' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-forminator-forms', 'ep-font' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/DdBvY0dnGsk';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'forminator_form',
			[
				'label'   => esc_html__( 'Select Form', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_forminator_forms_options(),
			]
		);

		$this->add_control(
			'hide_label',
			[
				'label'        => __( 'Hide Label', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-forminator-forms-label-hide--',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_label_style',
			[
				'label' => __( 'Labels', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'hide_label' => ''
				]
			]
		);

		$this->add_control(
			'text_color_label',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-label' => 'color: {{VALUE}}',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography_label',
				'selector' => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-label',
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label'      => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-label' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_fields_style',
			[
				'label' => __( 'Input & Textarea', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'input_alignment',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_fields_style' );

		$this->start_controls_tab(
			'tab_fields_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'field_bg_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'field_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list',
			]
		);

		$this->add_responsive_control(
			'field_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_indent',
			[
				'label'      => __( 'Text Indent', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 60,
						'step' => 1,
					],
					'%'  => [
						'min'  => 0,
						'max'  => 30,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'text-indent: {{SIZE}}{{UNIT}}',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'input_width',
			[
				'label'      => __( 'Input Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label'      => __( 'Input Height', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 80,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_width',
			[
				'label'      => __( 'Textarea Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea' => 'width: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label'      => __( 'Textarea Height', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 400,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea' => 'height: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'field_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator'  => 'before',
			]
		);

		$this->add_responsive_control(
			'field_spacing',
			[
				'label'      => __( 'Spacing', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-row' => 'margin-bottom: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'field_typography',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'field_box_shadow',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list',
			]
		);

		$this->add_control(
			'text_color_placeholder',
			[
				'label'     => __( 'Placeholder Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-field input::-webkit-input-placeholder, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'field_bg__hover_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_field_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'hover_field_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'field_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'hover_box_shadow',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:hover, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:hover',
			]
		);

		$this->add_control(
			'text_color_placeholder_hover',
			[
				'label'     => __( 'Placeholder Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-field input:hover::-webkit-input-placeholder, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-field textarea:hover::-webkit-input-placeholder' => 'color: {{VALUE}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_fields_focus',
			[
				'label' => __( 'Focus', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'focus_field_bg_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:focus' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'focus_field_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:focus' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'focus_field_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'field_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'focus_box_shadow',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-input:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-textarea:focus, {{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-select-list:focus',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]' => 'width: {{SIZE}}{{UNIT}} !important; height: {{SIZE}}{{UNIT}} !important;',
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
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]' => 'background: {{VALUE}}',
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
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]' => 'border-width: {{SIZE}}{{UNIT}}',
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
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]' => 'border-color: {{VALUE}}',
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

        $this->add_responsive_control(
            'checkbox_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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

        $this->add_responsive_control(
            'radio_border_radius',
            [
                'label' => __('Border Radius', 'bdthemes-element-pack'),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"], {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                    '{{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="checkbox"]:checked:before, {{WRAPPER}} #bdt-forminator-forms-{{ID}}.bdt-custom-radio-checkbox input[type="radio"]:checked:before' => 'background: {{VALUE}}',
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
			'section_submit_button_style',
			[
				'label' => __( 'Submit Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-pagination-footer, {{WRAPPER}} #bdt-forminator-forms-{{ID}} #submit'   => 'justify-content: {{VALUE}} !important; display: flex;',
				],
				'condition' => [
					'button_width_type' => 'custom',
				],
			]
		);

		$this->add_control(
			'button_width_type',
			[
				'label'        => __( 'Width', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SELECT,
				'default'      => 'custom',
				'options'      => [
					'full-width' => __( 'Full Width', 'bdthemes-element-pack' ),
					'custom'     => __( 'Custom', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-forminator-forms-button-',
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label'      => __( 'Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1200,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button' => 'width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition'  => [
					'button_width_type' => 'custom',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color_normal',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_bg_color_normal',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border_normal',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label'      => __( 'Margin Top', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::SLIDER,
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-pagination-footer, {{WRAPPER}} #bdt-forminator-forms-{{ID}} #submit' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => __( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'button_box_shadow',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_bg_color_hover',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button:hover' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button:hover' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'button_border_color_hover',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button:hover' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'button_hover_box_shadow',
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-button:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_error_style',
			[
				'label'     => __( 'Errors', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'error_message_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-error-message' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'error_message_background_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-error-message' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'error_message_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-error-message',
			]
		);

		$this->add_responsive_control(
			'error_message_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-error-message' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'error_message_typography',
				'label'     => __( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-error-message',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_confirmation_style',
			[
				'label' => __( 'Confirmation Message', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'confirmation_alignment',
			[
				'label'     => __( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'confirmation_typography',
				'label'    => __( 'Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success',
			]
		);

		$this->add_control(
			'confirmation_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'confirmation_bg_color',
			[
				'label'     => __( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success' => 'background-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'confirmation_border',
				'label'       => __( 'Border', 'bdthemes-element-pack' ),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success',
			]
		);

		$this->add_responsive_control(
			'confirmation_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} #bdt-forminator-forms-{{ID}} .forminator-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['forminator_form']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select a Forminator Forms From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'id' => $settings['forminator_form'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[forminator_form %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

        // $this->add_render_attribute( 'forminator_wrapper', 'class', 'bdt-forminator-forms' );
        // $this->add_render_attribute( 'forminator_wrapper', 'id', 'bdt-forminator-forms' );

		$id = 'bdt-forminator-forms-' . $this->get_id();

		$this->add_render_attribute(
			[
				'forminator_wrapper' => [
					'class'    => ['bdt-forminator-forms'],
					'id' => $id,
				],
			]
		);
        
        if ( $settings['custom_radio_checkbox'] == 'yes' ) {
            $this->add_render_attribute( 'forminator_wrapper', 'class', 'bdt-custom-radio-checkbox' );
        }
		
		?>

		<div <?php echo $this->get_render_attribute_string('forminator_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
