<?php 
/*
Widget Name: WP Forms
Description: Third party plugin WP forms style.
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

class ThePlus_Wp_Forms extends Widget_Base {
	
	public $TpDoc = THEPLUS_TPDOC;

	public function get_name() {
		return 'tp-wp-forms';
	}

    public function get_title() {
        return esc_html__('WPForms', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-envelope-open theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-adapted');
    }
	
	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "customize-wpforms-in-elementor";

		return esc_url($DocUrl);
	}

    protected function register_controls() {
		/*Layout Content*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'WPForms', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'wp_forms',
			[
				'label' => esc_html__( 'Select Form', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => theplus_wpforms_forms(),
			]
		);
		$this->add_control(
			'wp_forms_notice',
			[
				'label' => esc_html__( 'NOTICE :- Dashboard - WPForms - Settings', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control(
			'wp_forms_notice_dec',
			[
				'label' => esc_html__( 'Check Load Assets Globally to load form in wordpress backend.', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->end_controls_section();
		/*Layout Content*/
		
		/*label styling start*/		
		$this->start_controls_section(
			'section_s_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_control(
			'label_typ_head',
			[
				'label' => esc_html__( 'Label Typography', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container label.wpforms-field-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container label.wpforms-field-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .wpforms-container label.wpforms-field-label',
			]
		);
		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .wpforms-container label.wpforms-field-label' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
		);
		$this->add_control(
			'label_inline_typ_head',
			[
				'label' => esc_html__( 'Inline Label Typography', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);	
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'inline_label_typography',
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field-sublabel',
			]
		);
		$this->add_control(
			'sub_label_color',
			[
				'label' => esc_html__( 'Inline/Sub Label Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-label-inline,{{WRAPPER}} .wpforms-container .wpforms-field-sublabel' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-required-label' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*label styling end*/	
		/*description text start*/
		$this->start_controls_section(
			'section_dec_text',
			[
				'label' => esc_html__( 'Description Text', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);
		$this->add_responsive_control(
			'desc_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'desc_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],			
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'desc_typography',
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field-description',
			]
		);
		$this->add_control(
			'desc_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'dec_background',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'dec_border_switch',
			[
				'label' => esc_html__( 'Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'dec_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field-description',
				'condition' => [
					'dec_border_switch' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'dec_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'dec_border_switch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*description text end*/
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
				'selector' => '{{WRAPPER}} .wpforms-container input[type="text"],
				{{WRAPPER}} .wpforms-container input[type="email"],
				{{WRAPPER}} .wpforms-container input[type="number"],
				{{WRAPPER}} .wpforms-container select',
			]
		);		
		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-container input::-webkit-input-placeholder,
					{{WRAPPER}} .wpforms-container  email::-webkit-input-placeholder,
					{{WRAPPER}} .wpforms-container  number::-webkit-input-placeholder,
					{{WRAPPER}} .wpforms-container  select::-webkit-input-placeholder' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select,{{WRAPPER}} .wpforms-container .wpforms-field-sublabel.after,{{WRAPPER}} .wpforms-container .wpforms-field-description' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"]:focus,{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field select:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"]:focus,{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field select:focus',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"]:focus,{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field select:focus' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"]:focus,{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field select:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"],{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"],
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"],
				{{WRAPPER}} .wpforms-container .wpforms-field select',
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
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field input[type="text"]:focus,{{WRAPPER}} .wpforms-container .wpforms-field input[type="email"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field input[type="number"]:focus,
				{{WRAPPER}} .wpforms-container .wpforms-field select:focus',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'textarea_typography',
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
			]
		);
		$this->add_control(
			'textarea_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-container  textarea::-webkit-input-placeholder' => 'color: {{VALUE}};',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_focus_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea',
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
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field.wpforms-field-textarea textarea:focus',
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
				'selector' => '{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label,{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li.wpforms-image-choices-item .wpforms-image-choices-label',
			]
		);
		
		$this->add_control(
			'checked_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label,{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li.wpforms-image-choices-item .wpforms-image-choices-label' => 'color: {{VALUE}};',
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
						'min' => 10,
						'max' => 60,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
  		);	
		$this->add_control(
			'checked_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li:not(.wpforms-selected) label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'checked_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li.wpforms-selected label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li:not(.wpforms-selected) label:before' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li.wpforms-selected label:before' => 'background: {{VALUE}};',
				],
			]
		);		
		$this->add_control(
			'img_chkbx_heading',
			[
				'label' => esc_html__( 'Image choices & Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'img_chkbx_switch',
			[
				'label' => esc_html__( 'Must use image choices in WPForms', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);		
		$this->add_responsive_control(
			'img_chkbx_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-none label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
			]
		);		
		$this->add_control(
			'img_chkbx_field_bgcolor',
			[
				'label'     => esc_html__( 'Image Checkbox Normal', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern label' => 'background: {{VALUE}};',
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic label' => 'border:solid {{VALUE}};',
				],
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'img_chkbx_field_bgcolor_active',
			[
				'label'     => esc_html__( 'Image Checkbox Selected', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li.wpforms-selected label' => 'background: {{VALUE}};',
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li.wpforms-selected label' => 'border:solid {{VALUE}};',
				],			
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'img_chkbx_chk_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
			]
		);
				
		$this->add_control(
			'img_chkbx_chk_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'img_chkbx_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Icon Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'font-size: {{SIZE}}{{UNIT}}',
				],			
				'condition' => [
					'img_chkbx_switch' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'img_chkbx_icon_bg_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Icon Background Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',				
				],		
				'condition' => [
					'img_chkbx_switch' => 'yes',
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
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selectors'  => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .wpforms-field.wpforms-field-checkbox li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-modern li label,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_checked_field_bg',
			[
				'label' => esc_html__( 'Radio Button', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'radio_text_typography',
				'selector' => '{{WRAPPER}} .wpforms-field.wpforms-field-radio li label,{{WRAPPER}} .wpforms-field.wpforms-field-radio li.wpforms-image-choices-item .wpforms-image-choices-label',
			]
		);
		$this->add_control(
			'radio_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label,{{WRAPPER}} .wpforms-field.wpforms-field-radio li.wpforms-image-choices-item .wpforms-image-choices-label' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
			$this->add_responsive_control(
  			'radio_typography',
  			[
  				'label' => esc_html__( 'Icon Size', 'theplus' ),
  				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 60,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
  		);	
		$this->add_control(
			'radio_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li:not(.wpforms-selected) label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'radio_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li.wpforms-selected label:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'radio_unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li:not(.wpforms-selected) label:before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'radio_checked_field_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li.wpforms-selected label:before' => 'background: {{VALUE}};',
				],
			]
		);
		/*radio btn display style start*/
		$this->add_control(
			'img_rdo_heading',
			[
				'label' => esc_html__( 'Image choices & Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'img_rdo_switch',
			[
				'label' => esc_html__( 'Must use image choices in WPForms', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);			
		$this->add_responsive_control(
			'img_rdo_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-none label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
			]
		);		
		$this->add_control(
			'img_rdo_field_bgcolor',
			[
				'label'     => esc_html__( 'Image Radio Normal', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern label' => 'background: {{VALUE}};',
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic label' => 'border:solid {{VALUE}};',
				],
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'img_rdo_field_bg_active',
			[
				'label'     => esc_html__( 'Image Radio Selected', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li.wpforms-selected label' => 'background: {{VALUE}};',
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic li.wpforms-selected label' => 'border:solid {{VALUE}};',
				],
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'img_rdo_chk_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'color: {{VALUE}};',
				],
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
			]
		);
				
		$this->add_control(
			'img_rdo_chk_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
            'img_rdo_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Icon Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'font-size: {{SIZE}}{{UNIT}}',
				],			
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
            'img_rdo_icon_bg_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Icon Background Size', 'theplus'),
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern .wpforms-image-choices-image:after,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-classic .wpforms-image-choices-image:after' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',				
				],		
				'condition' => [
					'img_rdo_switch' => 'yes',
				],
            ]
        );
		/*radio btn display style end*/
		
		$this->add_control(
			'radio_border_options',
			[
				'label' => esc_html__( 'Border Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'radio_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'radio_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'selectors'  => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label:before,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'radio_border_width',
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
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'radio_unchecked_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'radio_unchecked_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wpforms-field.wpforms-field-radio li label:before,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-radio ul.wpforms-image-choices-modern li label,
					 {{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field-checkbox ul.wpforms-image-choices-classic li label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__('Width', 'theplus'),
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button',
			]
		);
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit]:hover,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit]:hover,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button:hover',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'selectors'  => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit]:hover,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit]:hover,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit],
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button',
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
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form button[type=submit]:hover,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-page-button:hover',
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
			'oute_r_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-field,{{WRAPPER}} .wpforms-container .wpforms-submit-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'oute_r_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-field,{{WRAPPER}} .wpforms-container .wpforms-submit-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_oute_r' );
			$this->start_controls_tab(
				'oute_r_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'oute_r_field_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field',				
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .wpforms-container .wpforms-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow',
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field',
			]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'oute_r_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'oute_r_field_bg_hover',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field:hover',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field:hover',
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .wpforms-container .wpforms-field:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow_hover',
				'selector' => '{{WRAPPER}} .wpforms-container .wpforms-field:hover',
			]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();	
		$this->end_controls_section();
		/*outer Style end*/
		/*form container start*/		
		$this->start_controls_section(
            'section_form_container',
            [
                'label' => esc_html__('Form Container', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_responsive_control(
			'form_cont_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'form_cont_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_form_container' );
			$this->start_controls_tab(
				'form_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'form_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wpforms-container',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-container',				
			]
			);
			$this->add_responsive_control(
				'form_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .wpforms-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'form_shadow',
				'selector' => '{{WRAPPER}} .wpforms-container',
			]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'form_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'form_bg_hover',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wpforms-container:hover',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-container:hover',
			]
			);
			$this->add_responsive_control(
				'form_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .wpforms-container:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'form_shadow_hover',
				'selector' => '{{WRAPPER}} .wpforms-container:hover',
			]
			);
			$this->end_controls_tab();
			$this->end_controls_tabs();	
		$this->end_controls_section();	
		/*form container end*/
		/*response message start*/
		$this->start_controls_section(
            'section_response_message',
            [
                'label' => esc_html__('Response Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->start_controls_tabs( 'tabs_response_style' );
		$this->start_controls_tab(
			'tab_response_success',
			[
				'label' => esc_html__( 'Success', 'theplus' ),
			]
		);		
		$this->add_responsive_control(
			'response_success_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-confirmation-container-full' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'response_success_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .wpforms-confirmation-container-full' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_success_typography',
				'selector' => '{{WRAPPER}} .wpforms-confirmation-container-full,{{WRAPPER}} .wpforms-confirmation-container-full p',
			]
		);
		$this->add_control(
			'response_success_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .wpforms-confirmation-container-full,{{WRAPPER}} .wpforms-confirmation-container-full p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'response_success_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .wpforms-confirmation-container-full',
				]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'response_success_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .wpforms-confirmation-container-full',
			]
		);
		$this->add_responsive_control(
			'response_success_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .wpforms-confirmation-container-full' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_response_validation',
			[
				'label' => esc_html__( 'Validation/Error', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_validation_typography',
				'selector' => '{{WRAPPER}} div.wpforms-container .wpforms-form label.wpforms-error',
			]
		);
		$this->add_control(
			'response_validation_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form label.wpforms-error' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'response_validation_field_color',
			[
				'label'     => esc_html__( 'Field Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field input.wpforms-error,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field textarea.wpforms-error,
					{{WRAPPER}} div.wpforms-container .wpforms-form .wpforms-field select.wpforms-error' => 'border:1px solid {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*response message end */
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
					'{{WRAPPER}} .wpforms-container' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->end_controls_section();
		/*extra option*/
		
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
	
		/*--Plus Extra ---*/
		$PlusExtra_Class = "";
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		$output = '<div class="pt_plus_wpforms '.esc_attr($animated_class).'" '.$animation_attr.'>';
			$output .= do_shortcode($this->get_shortcode());
		$output .= '</div>';

		echo $before_content.$output.$after_content;
	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if ( empty($settings['wp_forms']) ) {
			return '<h3 class="theplus-posts-not-found">' . esc_html__('Please select a WPForms', 'theplus') . '</h3>';
		}

		$attributes = [
			'id' => $settings['wp_forms'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode = [];
		$shortcode[] = sprintf( '[wpforms %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode( "", $shortcode );
	}
}