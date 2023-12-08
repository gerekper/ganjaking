<?php 
/*
Widget Name: Ninja Form
Description: Third party plugin ninja form style.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Ninja_form extends Widget_Base {
	
	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-ninja-form';
	}

    public function get_title() {
        return esc_html__('Ninja Form', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-envelope-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-adapted');
    }
	
	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "customize-ninja-forms-in-elementor";

		return esc_url($DocUrl);
	}

    protected function register_controls() {
		/*Layout Content*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Ninja Form', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'contact_form',
			[
				'label' => esc_html__( 'Select Form', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => theplus_get_ninja_form_post(),
			]
		);
		$this->add_control(
			'form_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => theplus_get_style_list(1),
			]
		);		
		$this->end_controls_section();
		/*Layout Content*/
		/*form heading start*/
		$this->start_controls_section(
			'section_style_form_heading',
			[
				'label' => esc_html__( 'Form Heading', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'form_heading_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-form-title h3',
				'separator' => 'after',
			]
		);
		$this->add_control(
			'form_heading_color',
			[
				'label' => esc_html__( 'Form Heading', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-form-title h3' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->end_controls_section();
		/*form heading end*/
		/*help text start*/
		$this->start_controls_section(
			'section_help_text',
			[
				'label' => esc_html__( 'Field Hint', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_control(
			'help_icon_color',
			[
				'label' => esc_html__( 'Hint Icon', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 span.fa.fa-info-circle.nf-help:before' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'help_desc_color',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-description' => 'color: {{VALUE}}',
				],
			]
		);
		
		$this->end_controls_section();
		/*help text end*/
		/*label styling start*/		
		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__( 'Label Styling', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .nf-form-layout .nf-field-label label',
			]
		);
		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .nf-form-layout .nf-field-label label' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'sub_label_color',
			[
				'label' => esc_html__( 'Field Description Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-description,{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-description p' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .ninja-forms-req-symbol' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*label styling end*/
		
		/*Input Field Style*/
		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__( 'Input Fields Styling', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select',
			]
		);
		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nf-form-content input::-webkit-input-placeholder,
					{{WRAPPER}} .nf-form-content  email::-webkit-input-placeholder,
					{{WRAPPER}} .nf-form-content  number::-webkit-input-placeholder,
					{{WRAPPER}} .nf-form-content  select::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'input_inner_padding',
			[
			'label' => esc_html__( 'Inner Padding', 'theplus' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%'],
			'selectors' => [
				'{{WRAPPER}} .theplus-ninja-form .textbox-wrap:not(.submit-wrap) .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .firstname-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .lastname-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .email-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .number-wrap .nf-field-element .ninja-forms-field,				
				{{WRAPPER}} .theplus-ninja-form .date-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .city-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .address-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .list-select-wrap .nf-field-element .ninja-forms-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'input_inner_margin',
			[
			'label' => esc_html__( 'Margin', 'theplus' ),
			'type' => Controls_Manager::DIMENSIONS,
			'size_units' => [ 'px', '%'],
			'selectors' => [
				'{{WRAPPER}} .theplus-ninja-form .textbox-wrap:not(.submit-wrap) .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .firstname-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .lastname-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .email-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .number-wrap .nf-field-element .ninja-forms-field,				
				{{WRAPPER}} .theplus-ninja-form .date-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .city-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .address-wrap .nf-field-element .ninja-forms-field,
				{{WRAPPER}} .theplus-ninja-form .list-select-wrap .nf-field-element .ninja-forms-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
			],
			'separator' => 'after',
			]
		);

		$this->start_controls_tabs( 'tabs_input_field_style' );
		$this->start_controls_tab(
			'tab_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .list-select-wrap .nf-field-element select',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"]:focus,
				{{WRAPPER}} .theplus-ninja-form .list-select-wrap:focus .nf-field-element select' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"]:focus,
				{{WRAPPER}} .theplus-ninja-form .list-select-wrap:focus .nf-field-element select',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'input_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_border_style' );
		$this->start_controls_tab(
			'tab_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_shadow_style' );
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"],
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="text"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="email"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="number"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element input[type="tel"]:focus,
				{{WRAPPER}} .theplus-ninja-form .nf-field-element select:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Input Field Style*/
		/*textarea field start*/
		$this->start_controls_section(
			'section_style_textarea',
			[
				'label' => esc_html__( 'Textarea Fields Styling', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'textarea_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'textarea_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'textarea_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea',
			]
		);
		$this->add_control(
			'textarea_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nf-form-content  textarea::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
		);
			$this->start_controls_tabs( 'tabs_textarea_field_style' );
				$this->start_controls_tab(
					'tab_textarea_field_normal',
					[
						'label' => esc_html__( 'Normal', 'theplus' ),
					]
				);
				$this->add_control(
					'textarea_field_color',
					[
						'label'     => esc_html__( 'Text Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea',
					]
				);
				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'tab_textarea_field_focus',
					[
						'label' => esc_html__( 'Focus', 'theplus' ),
					]
				);
				$this->add_control(
					'textarea_field_focus_color',
					[
						'label'     => esc_html__( 'Text Color', 'theplus' ),
						'type'      => Controls_Manager::COLOR,
						'selectors' => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea:focus' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_focus_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea:focus',
					]
				);
				$this->end_controls_tab();
		$this->end_controls_tabs();	
		
		$this->add_control(
			'textarea_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'ta_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'ta_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'ta_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'ta_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ta_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_ta_border_style' );
				$this->start_controls_tab(
					'tab_ta_border_normal',
					[
						'label' => esc_html__( 'Normal', 'theplus' ),
					]
				);
				$this->add_control(
					'ta_box_border_color',
					[
						'label' => esc_html__( 'Border Color', 'theplus' ),
						'type' => Controls_Manager::COLOR,				
						'selectors'  => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'ta_box_border' => 'yes',
						],
					]
				);
				$this->add_responsive_control(
					'ta_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'tab_ta_border_hover',
					[
						'label' => esc_html__( 'Focus', 'theplus' ),
					]
				);
				$this->add_control(
					'ta_box_border_hover_color',
					[
						'label' => esc_html__( 'Border Color', 'theplus' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors'  => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea:focus' => 'border-color: {{VALUE}};',
						],
						'condition' => [
							'ta_box_border' => 'yes',
						],
					]
				);
				$this->add_responsive_control(
					'ta_border_hover_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
					]
				);
				$this->end_controls_tab();
				$this->end_controls_tabs();
				$this->add_control(
					'ta_shadow_options',
					[
						'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
						'type' => Controls_Manager::HEADING,
						'separator' => 'before',
					]
				);
		$this->start_controls_tabs( 'tabs_ta_shadow_style' );
		$this->start_controls_tab(
			'tab_ta_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ta_box_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_ta_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ta_box_active_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .nf-field-element textarea:focus',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*textarea field end*/		
		/*Checkbox/Radio Field Style*/
		$this->start_controls_section(
            'section_checked_styling',
            [
                'label' => esc_html__('CheckBox/Radio Field', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->start_controls_tabs( 'tabs_checkbox_field_style' );
		$this->start_controls_tab(
			'tab_unchecked_field_bg',
			[
				'label' => esc_html__( 'Check Box', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'checkbox_text_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label',
			]
		);
		$this->add_control(
			'checked_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
  			'checkbox_typography',
  			[
  				'label' => esc_html__( 'Icon Size', 'theplus' ),
  				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap.listcheckbox-wrap .nf-field-element label:before,{{WRAPPER}} .theplus-ninja-form .field-wrap.checkbox-wrap .nf-field-label label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
  		);	
		$this->add_control(
			'checked_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'checked_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-label label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-label label.nf-checked-label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'checked_field_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-label label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element label.nf-checked-label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-label label.nf-checked-label:before' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'check_box_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'check_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'check_box_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'check_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'unchecked_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} {{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'unchecked_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .checkbox-wrap .nf-field-element li label:before,
					{{WRAPPER}} .theplus-ninja-form .listcheckbox-wrap .nf-field-element li label:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_radio_field',
			[
				'label' => esc_html__( 'Radio Button', 'theplus' ),
			]
		);		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'radio_text_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap.listradio-wrap .nf-field-element label',
			]
		);
		$this->add_control(
			'radio_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap.listradio-wrap .nf-field-element label' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);			
		$this->add_control(
			'radio_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .listradio-wrap .nf-field-element label:after' => 'color: {{VALUE}};',
					'{{WRAPPER}} .listradio-wrap .nf-field-element label:after' => 'border:2px solid {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'radio_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .listradio-wrap .nf-field-element label.nf-checked-label:before' => 'background: {{VALUE}};',
					'{{WRAPPER}} .listradio-wrap .nf-field-element label.nf-checked-label:after' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Checkbox/Radio Field Style*/		
		/*button Style*/
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Submit/Send Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'button_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 2000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'max-width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]',
			]
		);
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_button_style' );
		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_hover_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap:hover input[type=button]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap:hover input[type=button]',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'button_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'button_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_button_border_style' );
		$this->start_controls_tab(
			'tab_button_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap:hover input[type=button]' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'button_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form .field-wrap:hover input[type=button]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'button_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_button_shadow_style' );
		$this->start_controls_tab(
			'tab_button_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap input[type=button]',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form .field-wrap:hover input[type=button]',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Send/Submit Button Style*/	
		/*outer Style start*/
		$this->start_controls_section(
            'section_oute_r_styling',
            [
                'label' => esc_html__('Outer Field', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'oute_r_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'oute_r_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'oute_r_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 label,.theplus-ninja-form.style-1 .nf-form-layout .nf-field-label label,{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg,
.theplus-ninja-form.style-1 .ninja-forms-req-symbol,{{WRAPPER}} .theplus-ninja-form.style-1 span' => 'color: {{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_bg_style' );
			$this->start_controls_tab(
				'tabs_bg_style_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'oute_r_field_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)',				
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container)',
			]
			);
			$this->end_controls_tab();
			
			$this->start_controls_tab(
				'tabs_bg_style_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'oute_r_field_bg_hover',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container):hover',
				]
			);		
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container):hover',
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container):hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow_hover',
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1 .nf-field-container:not(.submit-container):hover',
			]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*outer Style end*/	
		/*Form Container background Style*/
		$this->start_controls_section(
            'section_outer_styling',
            [
                'label' => esc_html__('Form Container', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_responsive_control(
			'outer_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'outer_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_outer_field_style' );
		$this->start_controls_tab(
			'tab_outer_field_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'outer_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_outer_field_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'outer_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'outer_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'outer_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'outer_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'outer_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_outer_border_style' );
		$this->start_controls_tab(
			'tab_outer_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'outer_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_outer_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'outer_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'outer_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'outer_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'outer_shadow_options',
			[
				'label' => esc_html__( 'Box Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->start_controls_tabs( 'tabs_outer_shadow_style' );
		$this->start_controls_tab(
			'tab_outer_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'outer_box_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_outer_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'outer_box_active_shadow',
				'selector' => '{{WRAPPER}} .theplus-ninja-form.style-1:hover',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*background Style*/
		/*Response Message Style*/
		$this->start_controls_section(
            'section_response_msg_styling',
            [
                'label' => esc_html__('Response Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_msg_typography',
				'selector' => '{{WRAPPER}} .nf-form-wrap .nf-response-msg',
			]
		);
		$this->add_responsive_control(
			'response_msg_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'response_msg_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_response_style' );
		$this->start_controls_tab(
			'tab_response_success',
			[
				'label' => esc_html__( 'Success', 'theplus' ),
			]
		);
		$this->add_control(
			'response_success_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'response_success_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .nf-form-wrap .nf-response-msg',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_response_validate',
			[
				'label' => esc_html__( 'Validation', 'theplus' ),
			]
		);
		$this->add_control(
			'response_validate_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .nf-form-wrap .nf-form-errors,{{WRAPPER}} .nf-form-wrap .nf-error-msg.nf-error-field-errors' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'response_validate_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .nf-form-wrap .nf-form-errors',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'response_box_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		
		$this->add_control(
			'response_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'response_box_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'response_msg_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'response_box_border' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'tabs_response_msg' );
		$this->start_controls_tab(
			'tab_response_msg_success',
			[
				'label' => esc_html__( 'Success', 'theplus' ),
			]
		);
		$this->add_control(
			'success_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'response_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'success_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-response-msg' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_response_msg_validate',
			[
				'label' => esc_html__( 'Validation', 'theplus' ),
			]
		);
		$this->add_control(
			'validate_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-form-errors' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'response_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'validate_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .nf-form-wrap .nf-form-errors' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Response Message Style*/
		/*extra option*/
		$this->start_controls_section(
            'section_extra_option_styling',
            [
                'label' => esc_html__('Extra Option', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
            'content_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'theplus'),
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 250,
						'max' => 2000,
						'step' => 5,
					],
					'%' => [
						'min' => 10,
						'max' => 100,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
			'required_field_inner_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-required-error,{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-field-errors' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'required_field_color',
			[
				'label' => esc_html__( 'Required Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-required-error,{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-field-errors' => 'color: {{VALUE}};',
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error .ninja-forms-field,{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-field-errors' => 'border:1px solid {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'required_field_bgcolor',
			[
				'label' => esc_html__( 'Required Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-required-error' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'required_field_border_color',
			[
				'label' => esc_html__( 'Required Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .theplus-ninja-form.style-1 .nf-error-msg.nf-error-required-error' => 'border:1px solid {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*Adv tab*/
		$this->start_controls_section(
            'section_plus_extra_adv',
            [
                'label' => esc_html__('Plus Extras', 'theplus'),
                'tab' => Controls_Manager::TAB_ADVANCED,
            ]
        );
		$this->end_controls_section();
		/*Adv tab*/

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$form_style = $settings["form_style"];
		
		/*--Plus Extra ---*/
		$PlusExtra_Class = "";
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$this->add_render_attribute( 'contact-form', 'class', [ 'theplus-contact-form', 'theplus-ninja-form', ] );
        $this->add_render_attribute( 'contact-form', 'id', [ 'theplus-ninja-form-' . get_the_ID(), ] );
      
		$output ='<div class="theplus-ninja-form '.esc_attr($form_style).' '.esc_attr($animated_class).'" '.$animation_attr.'>';
			$output .= do_shortcode( $this->get_shortcode() );				
		$output .= '</div>';

		echo $before_content.$output.$after_content;		
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();
		
		if ( empty($settings['contact_form']) ) {
			return '<h3 class="theplus-posts-not-found">'.esc_html__('Please select a Ninja Form', 'theplus').'</h3>';
		}

		$attributes = [
			'id' => $settings['contact_form'],
		];

		$this->add_render_attribute( 'form_shortcode', $attributes );

		$shortcode = [];
		$shortcode[] = sprintf( '[ninja_form %s]', $this->get_render_attribute_string( 'form_shortcode' ) );

		return implode( "", $shortcode );
	}
	
    protected function content_template() {
	
    }
}