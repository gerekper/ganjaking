<?php
namespace ElementPack\Modules\CharitableDonationForm\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Charitable_Donation_Form extends Module_Base {

	public function get_name() {
		return 'bdt-charitable-donation-form';
	}

	public function get_title() {
		return BDTEP . __( 'Charitable Donation Form', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-charitable-donation-form';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'charitable', 'charity', 'donation form', 'donor', 'history', 'charitable', 'wall', 'form' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return ['ep-charitable-donation-form'];
        }
	}
	
	public function get_custom_help_url() {
		return 'https://youtu.be/aufVwEUZJhY';
	}

    protected function register_controls() {

		$this->start_controls_section(
			'section_charitable_form',
			[
				'label' => __( 'Donation Form', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => __( 'Form ID', 'bethemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_charitable_forms_options(),
			]
		);
		
		$this->end_controls_section();

		// Style
		$this->start_controls_section(
			'section_form_fields_style',
			[
				'label' => esc_html__( 'Form Fields', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'form_fields_email_text_color',
			[
				'label' => esc_html__( 'Details Text Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .donor-contact-details, {{WRAPPER}} .bdt-charitable-donation-form .donor-address' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_fields_update_text_color',
			[
				'label' => esc_html__( 'Update Text Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset a:not(.button)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_fields_prement_text_color',
			[
				'label' => esc_html__( 'Prement Text Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-fieldset-field-header' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'form_fields_background',
			[
				'label' => esc_html__( 'Background Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'form_fields_border',
                'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset'
            ]
        );

        $this->add_control(
            'form_fields_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_fields_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
		);
		
		$this->add_responsive_control(
			'form_fields_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'form_fields_shadow',
                'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-fields .charitable-fieldset'
            ]
		);

		$this->end_controls_section();
		
		$this->start_controls_section(
			'section_main_title_style',
			[
				'label' => esc_html__( 'Title', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'main_title_color',
			[
				'label' => esc_html__( 'Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-header' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'main_title_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-header' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-header',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_label_style',
			[
				'label' => esc_html__( 'Label', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field label' => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field.charitable-radio-list li' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_donation_style',
			[
				'label' => esc_html__( 'Donation Fields', 'bethemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'donation_text_color',
			[
				'label' => esc_html__( 'Text Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form label span.amount, {{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form label span.description' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'donation_background_color',
			[
				'label' => esc_html__( 'Background Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amounts .donation-amount' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'donation_active_bg_color',
			[
				'label' => esc_html__( 'Active Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amount.selected' => 'background-color: {{VALUE}}; border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'donation_fields_border',
                'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amounts .donation-amount'
            ]
        );

        $this->add_responsive_control(
            'donation_fields_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amounts .donation-amount' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'donation_fields_padding',
            [
                'label'      => __('Padding', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amounts .donation-amount' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
		);

		$this->add_responsive_control(
			'donation_fields_spacing',
			[
				'label' => esc_html__( 'Spacing', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .donation-amounts .donation-amount' => 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'donation_text_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form label span.amount, {{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form label span.description',
			]
		);

		$this->add_control(
			'donation_input_field_heading',
			[
				'label' => esc_html__( 'Input Field', 'bethemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'donation_input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .custom-donation-input' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'donation_input_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .custom-donation-input' => 'background-color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'donation_input_field_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .custom-donation-input' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
            'donation_infut_fields_radius',
            [
                'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-charitable-donation-form .charitable-donation-form .custom-donation-input' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Input Fields', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		
		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select' => 'color: {{VALUE}};',
				],
			]
		);
		
		$this->add_control(
			'input_field_background_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'placeholder_text_color',
			[
				'label' => __( 'Placeholder Text Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input:not([type="submit"])::-webkit-input-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input:not([type="submit"])::-moz-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input:not([type="submit"])::-ms-input-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input:not([type="submit"])::-o-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea::-webkit-input-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea::-moz-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea::-ms-input-placeholder' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea::-o-placeholder' => 'color: {{VALUE}} !important;',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select',
			]
		);
		
		$this->add_responsive_control(
			'input_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
		$this->add_responsive_control(
			'input_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
					{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="text"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="email"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="date"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="time"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="number"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="url"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field input[type="password"], 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field textarea, 
				{{WRAPPER}} .bdt-charitable-donation-form .charitable-form-field select',
			]
        );
		
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_checked',
			[
				'label'     => esc_html__( 'Checked', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'checked_normal_color',
			[
				'label' => __( 'Normal Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-radio-list input[type="radio"]:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'checked_active_color',
			[
				'label' => __( 'Active Color', 'bethemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-radio-list input[type="radio"]:checked:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__( 'Submit Button', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'submit_button_alignment',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_background',
				'selector'  => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'selector'    => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				]
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'button_typography',
				'label'     => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				'selector'  => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button:hover'  => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-charitable-donation-form .charitable-submit-field .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['form_id']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select a Charitable Forms From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'campaign_id' => $settings['form_id'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[charitable_donation_form %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {

        $this->add_render_attribute( 'charitable_wrapper', 'class', 'bdt-charitable-donation-form' );
		
		?>

		<div <?php echo $this->get_render_attribute_string('charitable_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
	
}