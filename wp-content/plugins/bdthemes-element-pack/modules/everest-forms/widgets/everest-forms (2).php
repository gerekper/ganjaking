<?php
namespace ElementPack\Modules\EverestForms\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Everest_Forms extends Module_Base {

	public function get_name() {
		return 'bdt-everest-forms';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Everest Forms', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-everest-forms';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'everest', 'ninja', 'form', 'contact', 'custom', 'builder', 'everest' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-everest-forms', 'ep-font' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/jfZhIFpdvcg';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__( 'Layout', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'everest_form',
			[
				'label'   => esc_html__( 'Select Form', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_everest_forms_options(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_s_label',
			[
				'label' => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,				
			]
        );
        	
		$this->add_responsive_control(
			'label_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-label .evf-label' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-label .evf-label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .evf-field-label .evf-label',
			]
        );
        
		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Label', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-label .evf-label' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
        );
        
		$this->add_control(
			'inline_help_label_color',
			[
				'label' => esc_html__( 'Inline/Description Text', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .form-row .everest-forms-field-label-inline,{{WRAPPER}} .bdt-everest-forms .form-row .evf-field-description' => 'color: {{VALUE}}',
					'separator' => 'after',
				],
			]
        );
        
		$this->add_control(
			'req_symbol_color',
			[
				'label' => esc_html__( 'Required Symbol', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row label .required' => 'color: {{VALUE}} !important',
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
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select',
			]
        );	
        	
		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms input::-webkit-input-placeholder,
					{{WRAPPER}} .bdt-everest-forms  email::-webkit-input-placeholder,
					{{WRAPPER}} .bdt-everest-forms  number::-webkit-input-placeholder,
					{{WRAPPER}} .bdt-everest-forms  select::-webkit-input-placeholder,
					{{WRAPPER}} .bdt-everest-forms  url::-webkit-input-placeholder' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_responsive_control(
			'input_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->start_controls_tabs( 'tabs_input_field_style' );
		$this->start_controls_tab(
			'tab_input_field_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_control(
			'input_field_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select',
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_input_field_focus',
			[
				'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_control(
			'input_field_focus_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="email"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="number"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="url"]:focus,
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select:focus' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'input_field_focus_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-everest-forms input[type="text"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="email"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="number"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="url"]:focus,
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select:focus',
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
		$this->add_control(
			'input_border_options',
			[
				'label' => esc_html__( 'Border Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'box_border',
			[
				'label' => esc_html__( 'Box Border', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off' => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'border_style',
			[
				'label' => esc_html__( 'Border Style', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => esc_html__( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
				'condition' => [
					'box_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="email"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="number"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="url"]:focus,
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select:focus' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms input[type="text"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="email"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="number"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="url"]:focus,
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Box Shadow Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
        $this->start_controls_tabs( 'tabs_shadow_style' );
        
		$this->start_controls_tab(
			'tab_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms input[type="text"],
				{{WRAPPER}} .bdt-everest-forms input[type="email"],
				{{WRAPPER}} .bdt-everest-forms input[type="number"],
				{{WRAPPER}} .bdt-everest-forms input[type="url"],
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select',
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'box_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms input[type="text"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="email"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="number"]:focus,
				{{WRAPPER}} .bdt-everest-forms input[type="url"]:focus,
				{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row select:focus',
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
		$this->start_controls_section(
			'section_style_textarea',
			[
				'label' => esc_html__( 'Textarea Fields', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
        );
        
		$this->add_responsive_control(
			'textarea_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_responsive_control(
			'textarea_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'textarea_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea',
			]
        );
        
		$this->add_control(
			'textarea_placeholder_color',
			[
				'label'     => esc_html__( 'Placeholder Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms textarea::-webkit-input-placeholder' => 'color: {{VALUE}};',
				],
			]
        );
        
        $this->start_controls_tabs( 'tabs_textarea_field_style' );

        $this->start_controls_tab(
            'tab_textarea_field_normal',
            [
                'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'textarea_field_color',
            [
                'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'textarea_field_bg',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea',
            ]
        );

        $this->end_controls_tab();
        
        $this->start_controls_tab(
            'tab_textarea_field_focus',
            [
                'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_control(
            'textarea_field_focus_color',
            [
                'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea:focus' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'textarea_field_focus_bg',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea:focus',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();	
		
		$this->add_control(
			'textarea_border_options',
			[
				'label' => esc_html__( 'Border Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'ta_box_border',
			[
				'label' => esc_html__( 'Box Border', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off' => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'default' => 'no',
			]
        );
        
		$this->add_control(
			'ta_border_style',
			[
				'label' => esc_html__( 'Border Style', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => esc_html__( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'ta_box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'ta_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
                'condition' => [
                    'ta_box_border' => 'yes',
                ],						
            ]
        );

        $this->add_control(
            'ta_box_border_color',
            [
                'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,				
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ta_box_border' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ta_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
                'condition' => [
                    'ta_box_border' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'ta_box_border_hover_color',
            [
                'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea:focus' => 'border-color: {{VALUE}};',
                ],
                'condition' => [
                    'ta_box_border' => 'yes',
                ],
            ]
        );

        $this->add_responsive_control(
            'ta_border_hover_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
                'label' => esc_html__( 'Box Shadow Options', 'bdthemes-element-pack' ),
                'type' => Controls_Manager::HEADING,
                'separator' => 'before',
            ]
        );

        $this->start_controls_tabs( 'tabs_ta_shadow_style' );

		$this->start_controls_tab(
			'tab_ta_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ta_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea',
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_ta_shadow_hover',
			[
				'label' => esc_html__( 'Focus', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'ta_box_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field-container .evf-frontend-row textarea:focus',
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();	
        	
        $this->end_controls_section();
        
		$this->start_controls_section(
            'section_checked_styling',
            [
                'label' => esc_html__('CheckBox/Radio Field', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->start_controls_tabs( 'tabs_checkbox_field_style' );
		$this->start_controls_tab(
			'tab_unchecked_field_bg',
			[
				'label' => esc_html__( 'Check Box', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'checkbox_text_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox label.everest-forms-field-label-inline',
			]
		);
		
		$this->add_control(
			'checked_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox label.everest-forms-field-label-inline' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_responsive_control(
  			'checkbox_typography',
  			[
  				'label' => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
  				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
        );	
          
		$this->add_control(
			'checked_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'checked_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-form .evf-field-checkbox input[type=checkbox]:checked + .everest-forms-field-label-inline:before' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'checked_field_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-form .evf-field-checkbox input[type=checkbox]:checked + .everest-forms-field-label-inline:before' => 'background: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'check_box_border_options',
			[
				'label' => esc_html__( 'Border Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'check_box_border',
			[
				'label' => esc_html__( 'Box Border', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off' => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'check_box_border_style',
			[
				'label' => esc_html__( 'Border Style', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => esc_html__( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'check_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'unchecked_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'unchecked_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-checkbox .everest-forms-field-label-inline:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'check_box_border' => 'yes',
				],
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_radio_field',
			[
				'label' => esc_html__( 'Radio Button', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'radio_text_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .evf-field-radio label.everest-forms-field-label-inline',
			]
        );
        
		$this->add_control(
			'radio_field_text_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio label.everest-forms-field-label-inline' => 'color: {{VALUE}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_responsive_control(
  			'radio_typography',
  			[
  				'label' => esc_html__( 'Icon Size', 'bdthemes-element-pack' ),
  				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'font-size: {{SIZE}}{{UNIT}};',
				],
  			]
        );	
          
		$this->add_control(
			'radio_uncheck_color',
			[
				'label'     => esc_html__( 'UnChecked Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'radio_field_color',
			[
				'label'     => esc_html__( 'Checked Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [					
					'{{WRAPPER}} .bdt-everest-forms .everest-form .evf-field-radio input[type=radio]:checked + .everest-forms-field-label-inline:before' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'radio_unchecked_field_bgcolor',
			[
				'label'     => esc_html__( 'UnChecked Bg Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'background: {{VALUE}};',
				],
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'radio_checked_field_bgcolor',
			[
				'label'     => esc_html__( 'Checked Bg Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-form .evf-field-radio input[type=radio]:checked + .everest-forms-field-label-inline:before' => 'background: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'radio_border_options',
			[
				'label' => esc_html__( 'Border Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'radio_border',
			[
				'label' => esc_html__( 'Box Border', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off' => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'radio_border_style',
			[
				'label' => esc_html__( 'Border Style', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => esc_html__( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'radio_border_width',
			[
				'label' => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'radio_unchecked_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'radio_unchecked_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .evf-field-radio .everest-forms-field-label-inline:before' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'radio_border' => 'yes',
				],
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
		$this->start_controls_section(
            'section_button_styling',
            [
                'label' => esc_html__('Submit/Send Button', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
            'button_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'width: {{SIZE}}{{UNIT}}',
				],
				'separator' => 'after',
            ]
        );

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]',
			]
        );
        
		$this->add_responsive_control(
			'button_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
        );
        
		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
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
			'button_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]',
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
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit]:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]:hover' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_hover_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit]:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]:hover',
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
		$this->add_control(
			'button_border_options',
			[
				'label' => esc_html__( 'Border Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
		$this->add_control(
			'button_box_border',
			[
				'label' => esc_html__( 'Box Border', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off' => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'button_border_style',
			[
				'label' => esc_html__( 'Border Style', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'solid'  => esc_html__( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => esc_html__( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => esc_html__( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => esc_html__( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'button_box_border_width',
			[
				'label' => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'button_box_border_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
		);
		
		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
        );
        
		$this->add_control(
			'button_box_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit]:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_box_border' => 'yes',
				],
			]
        );
        
		$this->add_responsive_control(
			'button_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit]:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
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
				'label' => esc_html__( 'Box Shadow Options', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
        );
        
        $this->start_controls_tabs( 'tabs_button_shadow_style' );
        
		$this->start_controls_tab(
			'tab_button_shadow_normal',
			[
				'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit],{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]',
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_button_shadow_hover',
			[
				'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-part-button:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms button[type=submit]:hover,{{WRAPPER}} .bdt-everest-forms .everest-forms input[type=submit]:hover',
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
		$this->start_controls_section(
            'section_oute_r_styling',
            [
                'label' => esc_html__('Outer Field', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );

		$this->add_responsive_control(
			'oute_r_inner_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
        );
        
		$this->add_responsive_control(
			'oute_r_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
        $this->start_controls_tabs( 'tabs_oute_r' );

        $this->start_controls_tab(
            'oute_r_normal',
            [
                'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'oute_r_field_bg',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'oute_r__border',
                'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field',				
            ]
        );

        $this->add_responsive_control(
            'oute_r_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'oute_r_shadow',
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'oute_r_hover',
            [
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'oute_r_field_bg_hover',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'oute_r__border_hover',
                'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field:hover',
            ]
        );

        $this->add_responsive_control(
            'oute_r_border_radius_hover',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'oute_r_shadow_hover',
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .evf-field:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();	

        $this->end_controls_section();
        
		$this->start_controls_section(
            'section_form_container',
            [
                'label' => esc_html__('Form Container', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->add_responsive_control(
			'form_cont_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
        );
        
		$this->add_responsive_control(
			'form_cont_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->start_controls_tabs( 'tabs_form_container' );
        $this->start_controls_tab(
            'form_normal',
            [
                'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ),
            ]
        );
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'form_bg',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms',
            ]
        );
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_border',
                'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms',				
            ]
        );

        $this->add_responsive_control(
            'form_border_radius',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'form_shadow',
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms',
            ]
        );

        $this->end_controls_tab();

        $this->start_controls_tab(
            'form_hover',
            [
                'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ),
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'form_bg_hover',
                'types'     => [ 'classic', 'gradient' ],
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name' => 'form_border_hover',
                'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms:hover',
            ]
        );

        $this->add_responsive_control(
            'form_border_radius_hover',
            [
                'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} .bdt-everest-forms .everest-forms:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'form_shadow_hover',
                'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms:hover',
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();	

        $this->end_controls_section();	
        
		$this->start_controls_section(
            'section_response_message',
            [
                'label' => esc_html__('Response Message', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		
		$this->start_controls_tabs( 'tabs_response_style' );
		$this->start_controls_tab(
			'tab_response_success',
			[
				'label' => esc_html__( 'Success', 'bdthemes-element-pack' ),
			]
        );		
        
		$this->add_responsive_control(
			'response_success_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
		$this->add_responsive_control(
			'response_success_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success,{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice::before' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_success_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success',
			]
        );
        
		$this->add_control(
			'response_success_color',
			[
				'label'     => esc_html__( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success' => 'color: {{VALUE}};',
				],
			]
        );
        
		$this->add_group_control(
				Group_Control_Background::get_type(),
				[
					'name'      => 'response_success_bg',
					'types'     => [ 'classic', 'gradient' ],
					'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success',
				]
        );
        
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'response_success_border',
				'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success',
			]
        );
        
		$this->add_responsive_control(
			'response_success_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms .everest-forms-notice--success' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->end_controls_tab();
        
		$this->start_controls_tab(
			'tab_response_validation',
			[
				'label' => esc_html__( 'Validation/Error', 'bdthemes-element-pack' ),
			]
        );
        
		$this->add_responsive_control(
			'response_validation_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
        );
        
		$this->add_responsive_control(
			'response_validation_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
        );
        
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'response_validation_typography',
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error',
			]
        );
        
		$this->add_control(
			'response_validation_color',
			[
				'label'     => esc_html__( 'Text Color/Field Border', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error' => 'color: {{VALUE}};',
					'{{WRAPPER}} .everest-forms .evf-field-container .evf-frontend-row .evf-frontend-grid .evf-field.everest-forms-invalid .select2-container,
					{{WRAPPER}} .everest-forms .evf-field-container .evf-frontend-row .evf-frontend-grid .evf-field.everest-forms-invalid input.input-text,
					{{WRAPPER}} .everest-forms .evf-field-container .evf-frontend-row .evf-frontend-grid .evf-field.everest-forms-invalid select,
					{{WRAPPER}} .everest-forms .evf-field-container .evf-frontend-row .evf-frontend-grid .evf-field.everest-forms-invalid textarea' => 'border-color: {{VALUE}};',
				],
			]
        );
        
		$this->add_control(
			'response_validation_bg',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error' => 'background: {{VALUE}};',
				],
			]
        );		
        
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'response_validation_border',
				'label' => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error',
			]
        );
        
		$this->add_responsive_control(
			'response_validation_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-everest-forms .everest-forms label.evf-error' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );
        
        $this->end_controls_tab();
        
        $this->end_controls_tabs();
        
        $this->end_controls_section();
        
		$this->start_controls_section(
            'section_extra_option_styling',
            [
                'label' => esc_html__('Extra Option', 'bdthemes-element-pack'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
        
		$this->add_responsive_control(
            'content_max_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Maximum Width', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-everest-forms .everest-forms' => 'max-width: {{SIZE}}{{UNIT}}',
				],
            ]
        );

		$this->end_controls_section();

	}

	private function get_shortcode() {
		$settings = $this->get_settings_for_display();

		if (!$settings['everest_form']) {
			return '<div class="bdt-alert bdt-alert-warning">'.__('Please select a Everest Forms From Setting!', 'bdthemes-element-pack').'</div>';
		}

		$attributes = [
			'id' => $settings['everest_form'],
		];

		$this->add_render_attribute( 'shortcode', $attributes );

		$shortcode   = [];
		$shortcode[] = sprintf( '[everest_form %s]', $this->get_render_attribute_string( 'shortcode' ) );

		return implode("", $shortcode);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

        $this->add_render_attribute( 'everest_wrapper', 'class', 'bdt-everest-forms' );
        
        // if ( $settings['custom_radio_checkbox'] == 'yes' ) {
        //     $this->add_render_attribute( 'everest_wrapper', 'class', 'bdt-custom-radio-checkbox' );
        // }
		
		?>

		<div <?php echo $this->get_render_attribute_string('everest_wrapper'); ?>>

			<?php echo do_shortcode( $this->get_shortcode() ); ?>

		</div>

		<?php
	}

	public function render_plain_content() {
		echo $this->get_shortcode();
	}
}
