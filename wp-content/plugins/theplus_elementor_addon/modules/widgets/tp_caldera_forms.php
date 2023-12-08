<?php 
/*
Widget Name: Caldera Forms
Description: Third party plugin Caldera Forms style.
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
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Caldera_Forms extends Widget_Base {
		
	public function get_name() {
		return 'tp-caldera-forms';
	}

    public function get_title() {
        return esc_html__('Caldera Forms', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-envelope-open theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-adapted');
    }
	
    protected function register_controls() {
		/*Layout Content start*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Caldera Forms', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'caldera_forms',
			[
				'label' => esc_html__( 'Select Form', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => theplus_caldera_forms(),
			]
		);
				
		$this->end_controls_section();
		/*Layout Content end*/
		
		/*label styling start*/		
		$this->start_controls_section(
			'section_s_label',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
		);		
		$this->add_responsive_control(
			'label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .control-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .control-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .control-label',
			]
		);
		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .control-label' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
		);
		$this->add_control(
			'inline_help_label_color',
			[
				'label' => esc_html__( 'Inline/Description Text', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .help-block' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
		);
		$this->add_control(
			'req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .field_required' => 'color: {{VALUE}} !important',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=tel],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=credit_card_cvc],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=phone],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=url],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=color_picker],
				{{WRAPPER}} .pt_plus_caldera_forms input[type=date],
				{{WRAPPER}} .pt_plus_caldera_forms select.form-control',
			]
		);		
		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms input::-webkit-input-placeholder,
					{{WRAPPER}} .pt_plus_caldera_forms  email::-webkit-input-placeholder,
					{{WRAPPER}} .pt_plus_caldera_forms  number::-webkit-input-placeholder,
					{{WRAPPER}} .pt_plus_caldera_forms  select::-webkit-input-placeholder' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
				{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
				{{WRAPPER}} .pt_plus_caldera_forms .help-block,
				{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
				{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
				{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
				{{WRAPPER}} .pt_plus_caldera_forms .help-block' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms select:focus.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container:focus' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type="text"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms select:focus.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container:focus',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_border_hover',
			[
				'label' => esc_html__( 'Focus', 'theplus' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms select:focus.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container:focus' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type="text"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms select:focus.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'box_border' => 'yes',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type="text"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"],
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"],
					{{WRAPPER}} .pt_plus_caldera_forms select.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type="text"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="email"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="number"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="tel"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="credit_card_cvc"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="phone"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="url"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="color_picker"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms input[type="date"]:focus,
					{{WRAPPER}} .pt_plus_caldera_forms select:focus.form-control,
					{{WRAPPER}} .pt_plus_caldera_forms .flag-container:focus',
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
					'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'textarea_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control',
			]
		);
		$this->add_control(
			'textarea_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms  textarea::-webkit-input-placeholder' => 'color: {{VALUE}};',
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
							'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control',
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
							'{{WRAPPER}} .pt_plus_caldera_forms textarea:focus.form-control' => 'color: {{VALUE}};',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'textarea_field_focus_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .pt_plus_caldera_forms textarea:focus.form-control',
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
					'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
						'condition' => [
							'ta_box_border' => 'yes',
						],						
					]
				);
				$this->add_control(
					'ta_box_border_color',
					[
						'label' => esc_html__( 'Border Color', 'theplus' ),
						'type' => Controls_Manager::COLOR,				
						'selectors'  => [
							'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'ta_box_border' => 'yes',
						],
					]
				);
				$this->end_controls_tab();
				
				$this->start_controls_tab(
					'tab_ta_border_hover',
					[
						'label' => esc_html__( 'Focus', 'theplus' ),
						'condition' => [
							'ta_box_border' => 'yes',
						],
					]
				);
				$this->add_control(
					'ta_box_border_hover_color',
					[
						'label' => esc_html__( 'Border Color', 'theplus' ),
						'type' => Controls_Manager::COLOR,
						'default' => '',
						'selectors'  => [
							'{{WRAPPER}} .pt_plus_caldera_forms textarea:focus.form-control' => 'border-color: {{VALUE}};',
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
							'{{WRAPPER}} .pt_plus_caldera_forms textarea:focus.form-control' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
						],
						'condition' => [
							'ta_box_border' => 'yes',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms textarea.form-control',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms textarea:focus.form-control',
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
				'selector' => '{{WRAPPER}} .caldera-grid .checkbox label,{{WRAPPER}} .caldera-grid .checkbox-inline'
			]
		);
		$this->add_control(
			'checked_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .caldera-grid .checkbox label,{{WRAPPER}} .caldera-grid .checkbox-inline' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} span.caldera_checkbox_label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
  		);	
		$this->add_control(
			'checked_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'checked_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .caldera-grid .checkbox input[type=checkbox]:checked + .caldera_checkbox_label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .caldera-grid .checkbox input[type=checkbox]:checked + .caldera_checkbox_label' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .checkbox label .caldera_checkbox_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
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
				'selector' => '{{WRAPPER}} .caldera-grid .radio label,{{WRAPPER}} .caldera-grid .radio-inline',
			]
		);		
		$this->add_control(
			'radio_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .caldera-grid .radio label,{{WRAPPER}} .caldera-grid .radio-inline' => 'color: {{VALUE}};',
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
						'min' => 5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} span.caldera_radio_label:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
  		);	
		$this->add_control(
			'radio_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'radio_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .caldera-grid .radio input[type=radio]:checked + .caldera_radio_label:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'radio_unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label' => 'background: {{VALUE}};',
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
					'{{WRAPPER}} .caldera-grid .radio input[type=radio]:checked + .caldera_radio_label' => 'background: {{VALUE}};',
				],
			]
		);
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
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .radio label .caldera_radio_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Checkbox/Radio Field Style*/	
		/*toggle button style start*/
		$this->start_controls_section(
            'section_toggle_styling',
            [
                'label' => esc_html__('Toggle Button', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'toggle_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'toggle_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'toggle_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn',
			]
		);
		$this->start_controls_tabs( 'tabs_toggle' );
		$this->start_controls_tab(
			'tab_toggle_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			't_text_color_n',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			't_bg_color_n',
			[
				'label'     => esc_html__( 'Background', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 't_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn,{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover,{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success',
			]
		);
		$this->add_responsive_control(
			't_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn,{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover,{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 't_box_shadow_n',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_toggle_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			't_text_color_h',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			't_bg_color_h',
			[
				'label'     => esc_html__( 'Background', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			't_border_color_h',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 't_box_shadow_h',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn:hover',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_toggle_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			't_text_color_a',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			't_bg_color_a',
			[
				'label'     => esc_html__( 'Background', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			't_border_color_a',
			[
				'label'     => esc_html__( 'Border Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success' => 'border-color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 't_box_shadow_a',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .cf-toggle-group-buttons .btn.btn-success',
				'separator' => 'before',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		/*toggle button style end*/
		/*file style start*/		
		$this->start_controls_section(
            'section_file_styling',
            [
                'label' => esc_html__('File/Upload Field', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'file_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'file_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'file_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn',
			]
		);
		$this->start_controls_tabs( 'tabs_file_style' );
			$this->start_controls_tab(
				'tab_file_normal',
				[
					'label' => esc_html__( 'Normal', 'theplus' ),
				]
			);
			$this->add_control(
				'file_color',
				[
					'label'     => esc_html__( 'Text Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'file_bg_color',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'file_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn',
			]
			);
			$this->add_responsive_control(
				'file_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'file_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type=file],{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn',
			]
			);
			$this->end_controls_tab();
			$this->start_controls_tab(
				'tab_file_hover',
				[
					'label' => esc_html__( 'Hover', 'theplus' ),
				]
			);
			$this->add_control(
				'file_color_hover',
				[
					'label'     => esc_html__( 'Text Color', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pt_plus_caldera_forms input[type=file]:hover,{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn:hover' => 'color: {{VALUE}};',
					],
				]
			);
			$this->add_control(
				'file_bg_color_hover',
				[
					'label'     => esc_html__( 'Background', 'theplus' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .pt_plus_caldera_forms input[type=file]:hover,{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn:hover' => 'background: {{VALUE}};',
					],
				]
			);
			$this->add_control(
			'file_border_color_hover',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type=file]:hover,{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn:hover' => 'border-color: {{VALUE}};',
				],
			]
		);		
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'file_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type=file]:hover,{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form .form-control.cf2-file .btn:hover',
			]
			);
			$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*file style end*/		
		/*section break/ summary start*/
		$this->start_controls_section(
            'section_sbreak_summry_styling',
            [
                'label' => esc_html__('Section Break / Summary', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'sb_color',
			[
				'label'     => esc_html__( 'Section Break Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid hr' => 'border-color: {{VALUE}};',
					],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'summry_heading_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms h2',
			]
		);	
		$this->add_control(
			'summry_heading_color',
			[
				'label'     => esc_html__( 'Summry Heading Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms h2' => 'color: {{VALUE}};',
				],				
			]
		);
		$this->add_control(
			'summry_heading_alignment',
			[
				'label' => esc_html__( 'Summary Heading  Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms h2' => 'text-align: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'summry_heading_margin_bottom',
			[
				'label' => esc_html__( 'Bottom Space', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms h2' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'summry_padding',
			[
				'label' => esc_html__( 'Summry Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-summary-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'c_summry_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-summary-field ul>li',
			]
		);
		$this->add_control(
			'summry_color',
			[
				'label'     => esc_html__( 'Summry Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-summary-field ul>li' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
		$this->end_controls_section();
		/*section break/ summary end*/		
		/*caldera special start*/
		$this->start_controls_section(
            'section_c_special_styling',
            [
                'label' => esc_html__('Caldera Special', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'c_s_cal_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .total-line',
			]
		);
		$this->add_control(
			'c_s_cal_color',
			[
				'label'     => esc_html__( 'Calculation Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .total-line' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_control(
			'consent_field_head',
			[
				'label' => esc_html__( 'Consent Field', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'consent_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms label.caldera-forms-gdpr-field-label,{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-consent-field-linked_text',
			]
		);
		$this->add_control(
			'consent_field_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms label.caldera-forms-gdpr-field-label,{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-consent-field-linked_text' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'consent_field_p_color',
			[
				'label' => esc_html__( 'Privacy Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-consent-field-linked_text:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'consent_field_req_color',
			[
				'label' => esc_html__( '* Sign Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-forms-consent-field span' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->end_controls_section();
		/*caldera special end*/
		/*button Style start*/
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]',
			]
		);
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type = submit]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]:hover',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'border-style: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_button_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]:hover' => 'border-color: {{VALUE}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms input[type = submit]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
				],
				'condition' => [
					'button_box_border' => 'yes',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type = submit],
				{{WRAPPER}} .pt_plus_caldera_forms input[type = button],
				{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]',
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
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms input[type = submit]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = button]:hover,
					{{WRAPPER}} .pt_plus_caldera_forms input[type = reset]:hover',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .form-group' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .form-group' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group',				
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pt_plus_caldera_forms .form-group' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group',
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
					'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group:hover',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'oute_r__border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group:hover',
			]
			);
			$this->add_responsive_control(
				'oute_r_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pt_plus_caldera_forms .form-group:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'oute_r_shadow_hover',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .form-group:hover',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form',				
			]
			);
			$this->add_responsive_control(
				'form_border_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'form_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form',
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
					'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form:hover',
				]
			);
			$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form:hover',
			]
			);
			$this->add_responsive_control(
				'form_border_radius_hover',
				[
					'label'      => esc_html__( 'Border Radius', 'theplus' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'form_shadow_hover',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form:hover',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_success_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success',
			]
		);
		$this->add_control(
			'response_success_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'response_success_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success',
				]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'response_success_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success',
			]
		);
		$this->add_responsive_control(
			'response_success_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera-grid .alert.alert-success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
		$this->add_responsive_control(
			'response_validation_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .parsley-required' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_validation_typography',
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .parsley-required',
			]
		);
		$this->add_control(
			'response_validation_color',
			[
				'label'     => esc_html__( 'Text Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .parsley-required' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'response_validation_bg',
			[
				'label'     => esc_html__( 'Background', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .pt_plus_caldera_forms .parsley-required' => 'background: {{VALUE}};',
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'response_validation_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .pt_plus_caldera_forms .parsley-required',
			]
		);
		$this->add_responsive_control(
			'response_validation_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_caldera_forms .parsley-required' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .pt_plus_caldera_forms .caldera_forms_form' => 'max-width: {{SIZE}}{{UNIT}}',
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
	}
	private function get_shortcode() {
		$settings = $this->get_settings_for_display();
		/**/
		if (!$settings['caldera_forms']) {
			return '<h3 class="theplus-posts-not-found">'.esc_html__('Please select a Caldera Forms', 'theplus').'</h3>';
		}

		$attributes = [
			'id'	=> $settings['caldera_forms'],
		];
		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[caldera_form %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
		
	}

	public function render() {	
		$settings = $this->get_settings_for_display();
			
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "plus-caldera-forms-widget";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		
		$output = '<div class="pt_plus_caldera_forms '.esc_attr($animated_class).'" '.$animation_attr.'>';
		$output .= do_shortcode($this->get_shortcode());
		$output .= '</div>';
		echo $before_content.$output.$after_content;
	}
}