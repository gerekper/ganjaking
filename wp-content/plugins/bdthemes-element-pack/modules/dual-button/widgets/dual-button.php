<?php
namespace ElementPack\Modules\DualButton\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;

use Elementor\Icons_Manager;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class DualButton extends Module_Base {
	public function get_name() {
		return 'bdt-dual-button';
	}

	public function get_title() {
		return BDTEP . __( 'Dual Button', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-dual-button';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'dual', 'button', 'link', 'double' ];
	}

	public function get_style_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['ep-styles'];
        } else {
            return [ 'ep-dual-button' ];
        }
    }

	public function get_custom_help_url() {
		return 'https://youtu.be/7hWWqHEr6s8';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_button',
			[
				'label' => __( 'Button', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'dual_button_size',
			[
				'label'   => __( 'Button Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'xs' => __( 'Extra Small', 'bdthemes-element-pack' ),
					'sm' => __( 'Small', 'bdthemes-element-pack' ),
					'md' => __( 'Medium', 'bdthemes-element-pack' ),
					'lg' => __( 'Large', 'bdthemes-element-pack' ),
					'xl' => __( 'Extra Large', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'start' => [
						'title' => __( 'Left', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-center',
					],
					'end' => [
						'title' => __( 'Right', 'bdthemes-element-pack' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-element-align-wrapper' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label' => __( 'Button Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'%' => [
						'max' => 100,
						'min' => 20,
					],
					'px' => [
						'max' => 1200,
						'min' => 300,
					],
				],
				'size_units' => ['%', 'px'],
				'default' => [
					'size' => 40,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => 80,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-dual-button'  => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'show_middle_text',
			[
				'label' => __( 'Middle Text', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'middle_text',
			[
				'label'       => __( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => __( 'or', 'bdthemes-element-pack' ),
				'placeholder' => __( 'or', 'bdthemes-element-pack' ),
				'condition'   => [
					'show_middle_text' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'dual_button_gap',
			[
				'label'   => __( 'Button Gap', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 5,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 50,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-btn-a' => 'margin-right: {{SIZE}}px;',
				],
				'condition' => [
					'show_middle_text!' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button_a',
			[
				'label' => __( 'Button A', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_text',
			[
				'label'       => __( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => __( 'Click Me', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Click Me', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_link',
			[
				'label'       => __( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => __( 'https://your-link.com', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'add_custom_a_attributes',
			[
				'label'     => __( 'Add Custom Attributes', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_a_attributes',
			[
				'label' => __( 'Custom Attributes', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => ['add_custom_a_attributes' => 'yes']
			]
		);

		$this->add_control(
			'button_a_onclick',
			[
				'label' => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'button_a_onclick_event',
			[
				'label'       => __( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'button_a_onclick' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'button_a_align',
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
			]
		);

		$this->add_control(
			'button_a_select_icon',
			[
				'label'       => __( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_a_icon',
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'button_a_icon_align',
			[
				'label'   => __( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __( 'Left', 'bdthemes-element-pack' ),
					'right'  => __( 'Right', 'bdthemes-element-pack' ),
					'top'    => __( 'Top', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'button_a_select_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_a_icon_indent',
			[
				'label' => __( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
					'default' => [
						'size' => 8,
					],
				'condition' => [
					'button_a_select_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a .bdt-flex-align-right'  => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-a .bdt-flex-align-left'   => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-a .bdt-flex-align-top'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-a .bdt-flex-align-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_css_id_a',
			[
				'label' => __( 'Button ID', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button_b',
			[
				'label' => __( 'Button B', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_text',
			[
				'label'       => __( 'Text', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => [ 'active' => true ],
				'default'     => __( 'Read More', 'bdthemes-element-pack' ),
				'placeholder' => __( 'Read More', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_link',
			[
				'label'       => __( 'Link', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::URL,
				'dynamic'     => [ 'active' => true ],
				'placeholder' => __( 'https://your-link.com', 'bdthemes-element-pack' ),
				'default'     => [
					'url' => '#',
				],
			]
		);

		$this->add_control(
			'add_custom_b_attributes',
			[
				'label'     => __( 'Add Custom Attributes', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'custom_b_attributes',
			[
				'label' => __( 'Custom Attributes', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::TEXTAREA,
				'dynamic' => [
					'active' => true,
				],
				'placeholder' => __( 'key|value', 'bdthemes-element-pack' ),
				'description' => sprintf( __( 'Set custom attributes for the price table button tag. Each attribute in a separate line. Separate attribute key from the value using %s character.', 'bdthemes-element-pack' ), '<code>|</code>' ),
				'classes' => 'elementor-control-direction-ltr',
				'condition' => ['add_custom_b_attributes' => 'yes']
			]
		);

		$this->add_control(
			'button_b_size',
			[
				'label'   => __( 'Button Size', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'md',
				'options' => [
					'xs' => __( 'Extra Small', 'bdthemes-element-pack' ),
					'sm' => __( 'Small', 'bdthemes-element-pack' ),
					'md' => __( 'Medium', 'bdthemes-element-pack' ),
					'lg' => __( 'Large', 'bdthemes-element-pack' ),
					'xl' => __( 'Extra Large', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->add_control(
			'button_b_onclick',
			[
				'label' => esc_html__( 'OnClick', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'button_b_onclick_event',
			[
				'label'       => __( 'OnClick Event', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'myFunction()',
				'description' => sprintf( __('For details please look <a href="%s" target="_blank">here</a>'), 'https://www.w3schools.com/jsref/event_onclick.asp' ),
				'condition' => [
					'button_b_onclick' => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'button_b_align',
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
			]
		);

		$this->add_control(
			'button_b_select_icon',
			[
				'label'       => __( 'Icon', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'button_b_icon',
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'button_b_icon_align',
			[
				'label'   => __( 'Icon Position', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'   => __( 'Left', 'bdthemes-element-pack' ),
					'right'  => __( 'Right', 'bdthemes-element-pack' ),
					'top'    => __( 'Top', 'bdthemes-element-pack' ),
					'bottom' => __( 'Bottom', 'bdthemes-element-pack' ),
				],
				'condition' => [
					'button_b_select_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_b_icon_indent',
			[
				'label' => __( 'Icon Spacing', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
					'default' => [
						'size' => 8,
					],
				'condition' => [
					'button_b_select_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b .bdt-flex-align-right'  => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-b .bdt-flex-align-left'   => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-b .bdt-flex-align-top'    => 'margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-btn-b .bdt-flex-align-bottom' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_css_id_b',
			[
				'label' => __( 'Button ID', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],
				'default' => '',
				'title' => __( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'bdthemes-element-pack' ),
				'description' => __( 'Please make sure the ID is unique and not used elsewhere on the page this form is displayed. This field allows <code>A-z 0-9</code> & underscore chars without spaces.', 'bdthemes-element-pack' ),
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_content_style',
			[
				'label' => __( 'Button', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs( 'tabs_dual_button_style' );

		$this->start_controls_tab(
			'tab_dual_button_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_border_style',
			[
				'label'   => __( 'Border Style', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'   => __( 'None', 'bdthemes-element-pack' ),
					'solid'  => __( 'Solid', 'bdthemes-element-pack' ),
					'dotted' => __( 'Dotted', 'bdthemes-element-pack' ),
					'dashed' => __( 'Dashed', 'bdthemes-element-pack' ),
					'groove' => __( 'Groove', 'bdthemes-element-pack' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button a' => 'border-style: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_border_width',
			[
				'label'      => __( 'Border Width', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default'    => [
					'top'    => 3,
					'right'  => 3,
					'bottom' => 3,
					'left'   => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'button_border_style!' => 'none'
				]
			]
		);

		$this->add_responsive_control(
			'dual_button_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dual_button_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a',
			]
		);

		$this->add_responsive_control(
			'dual_button_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dual_button_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-dual-button a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_dual_button_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'dual_button_hover_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'dual_button_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style_a',
			[
				'label' => __( 'Button A', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_a_effect',
			[
				'label'   => __( 'Effect', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'a',
				'options' => [
					'a' => __( 'Effect A', 'bdthemes-element-pack' ),
					'b' => __( 'Effect B', 'bdthemes-element-pack' ),
					'c' => __( 'Effect C', 'bdthemes-element-pack' ),
					'd' => __( 'Effect D', 'bdthemes-element-pack' ),
					'e' => __( 'Effect E', 'bdthemes-element-pack' ),
					'f' => __( 'Effect F', 'bdthemes-element-pack' ),
					'g' => __( 'Effect G', 'bdthemes-element-pack' ),
					'h' => __( 'Effect H', 'bdthemes-element-pack' ),
					'i' => __( 'Effect I', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_a_style' );

		$this->start_controls_tab(
			'tab_button_a_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_a_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-btn-a, 
								{{WRAPPER}} .bdt-btn-a.bdt-effect-i .bdt-btn-content-wrap:after,
								{{WRAPPER}} .bdt-btn-a.bdt-effect-i .bdt-btn-content-wrap:before',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_a_border_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-btn-a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_a_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666',
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_style!' => 'none'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_a_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a.bdt-btn-a',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_a_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_hover_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_a_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-btn-a:after, 
								{{WRAPPER}} .bdt-btn-a:hover,
								{{WRAPPER}} .bdt-btn-a.bdt-effect-i,
								{{WRAPPER}} .bdt-btn-a.bdt-effect-h:after',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_a_hover_border_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-btn-a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_a_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_style!' => 'none'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_a_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a.bdt-btn-a:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_style_b',
			[
				'label' => __( 'Button B', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_b_effect',
			[
				'label'   => __( 'Effect', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'a',
				'options' => [
					'a' => __( 'Effect A', 'bdthemes-element-pack' ),
					'b' => __( 'Effect B', 'bdthemes-element-pack' ),
					'c' => __( 'Effect C', 'bdthemes-element-pack' ),
					'd' => __( 'Effect D', 'bdthemes-element-pack' ),
					'e' => __( 'Effect E', 'bdthemes-element-pack' ),
					'f' => __( 'Effect F', 'bdthemes-element-pack' ),
					'g' => __( 'Effect G', 'bdthemes-element-pack' ),
					'h' => __( 'Effect H', 'bdthemes-element-pack' ),
					'i' => __( 'Effect I', 'bdthemes-element-pack' ),
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_b_style' );

		$this->start_controls_tab(
			'tab_button_b_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_b_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-btn-b, 
							   {{WRAPPER}} .bdt-btn-b.bdt-effect-i .bdt-btn-content-wrap:after, 
							   {{WRAPPER}} .bdt-btn-b.bdt-effect-i .bdt-btn-content-wrap:before',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_b_border_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-btn-b' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_b_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#666',
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_style!' => 'none'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_b_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a.bdt-btn-b',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_b_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_hover_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_b_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-btn-b:after,
								{{WRAPPER}} .bdt-btn-b:hover, 
								{{WRAPPER}} .bdt-btn-b.bdt-effect-i,
								{{WRAPPER}} .bdt-btn-b.bdt-effect-h:after
								',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'button_b__hover_border_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-btn-b:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_b_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_style!' => 'none'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_b_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button a.bdt-btn-b:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button_a_icon',
			[
				'label'     => __( 'Button A Icon', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'button_a_select_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_a_icon_style' );

		$this->start_controls_tab(
			'tab_button_a_icon_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_icon_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a .bdt-a-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-btn-a .bdt-a-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_a_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_a_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner',
			]
		);

		$this->add_control(
			'button_a_icon_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_a_icon_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_a_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner',
			]
		);

		$this->add_responsive_control(
			'button_a_icon_size',
			[
				'label' => __( 'Icon Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-button .bdt-a-icon .bdt-ep-button-a-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_a_icon_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_a_icon_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-a:hover .bdt-a-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-btn-a:hover .bdt-a-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_a_icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-ep-button:hover .bdt-a-icon .bdt-ep-button-a-icon-inner',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'button_a_icon_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_a_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-button:hover .bdt-a-icon .bdt-ep-button-a-icon-inner' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button_b_icon',
			[
				'label'     => __( 'Button B Icon', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'button_b_select_icon[value]!' => '',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_b_icon_style' );

		$this->start_controls_tab(
			'tab_button_b_icon_normal',
			[
				'label' => __( 'Normal', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_icon_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b .bdt-btn-b-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-btn-b .bdt-btn-b-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_b_icon_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_b_icon_border',
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner',
			]
		);

		$this->add_control(
			'button_b_icon_padding',
			[
				'label'      => __( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'button_b_icon_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_b_icon_shadow',
				'selector' => '{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner',
			]
		);

		$this->add_responsive_control(
			'button_b_icon_size',
			[
				'label' => __( 'Icon Size', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 10,
						'max'  => 100,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-button .bdt-btn-b-icon .bdt-b-icon-inner' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_b_icon_hover',
			[
				'label' => __( 'Hover', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'button_b_icon_hover_color',
			[
				'label'     => __( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-btn-b:hover .bdt-btn-b-icon' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-btn-b:hover .bdt-btn-b-icon svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'button_b_icon_hover_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .bdt-ep-button:hover .bdt-btn-b-icon .bdt-b-icon-inner',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'button_b_icon_hover_border_color',
			[
				'label'     => __( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_b_icon_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-ep-button:hover .bdt-btn-b-icon .bdt-b-icon-inner' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_middle_text',
			[
				'label'      => __( 'Middle Text', 'bdthemes-element-pack' ),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'show_middle_text',
							'value'    => 'yes',
						],
						[
							'name'     => 'middle_text',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]

		);

		$this->add_control(
			'middle_text_color',
			[
				'label'     => __( 'Text Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-dual-button span' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'middle_text_background',
				'types'    => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .bdt-dual-button span',
				'separator' => 'after',
			]
		);

		$this->add_responsive_control(
			'middle_text_radius',
			[
				'label'      => __( 'Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-dual-button span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'middle_text_shadow',
				'selector' => '{{WRAPPER}} .bdt-dual-button span',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'middle_text_typography',
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-dual-button span',
			]
		);

		$this->end_controls_section();
	}

	public function render_text_a($settings) {

		$this->add_render_attribute( 'content-wrapper-a', 'class', 'bdt-btn-content-wrap' );

		if ( 'left' == $settings['button_a_icon_align'] or 'right' == $settings['button_a_icon_align'] ) {
			$this->add_render_attribute( 'content-wrapper-a', 'class', 'bdt-flex bdt-flex-middle' );
		}
		$this->add_render_attribute( 'content-wrapper-a', 'class', 'bdt-flex-' . $settings['button_a_align'] );

		$this->add_render_attribute( 'content-wrapper-a', 'class', ( 'top' == $settings['button_a_icon_align'] ) ? 'bdt-flex bdt-flex-column' : '' );
		$this->add_render_attribute( 'content-wrapper-a', 'class', ( 'bottom' == $settings['button_a_icon_align'] ) ? 'bdt-flex bdt-flex-column-reverse' : '' );
		$this->add_render_attribute( 'content-wrapper-a', 'data-text', esc_attr($settings['button_a_text']));

		$this->add_render_attribute( 'button-a-text', 'class', 'bdt-btn-text' );

		if ( ! isset( $settings['button_a_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['button_a_icon'] = 'fas fa-arrow-left';
		}

		$migrated  = isset( $settings['__fa4_migrated']['button_a_select_icon'] );
		$is_new    = empty( $settings['button_a_icon'] ) && Icons_Manager::is_migration_allowed();

		?>
		<div <?php echo $this->get_render_attribute_string( 'content-wrapper-a' ); ?>>
			<?php if ( ! empty( $settings['button_a_select_icon']['value'] ) ) : ?>
				<div class="bdt-btn-icon bdt-a-icon bdt-flex-align-<?php echo esc_attr($settings['button_a_icon_align']); ?>">
					<div class="bdt-ep-button-a-icon-inner">
					
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $settings['button_a_select_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
					else : ?>
						<i class="<?php echo esc_attr( $settings['button_a_icon'] ); ?>" aria-hidden="true"></i>
					<?php endif; ?>

					</div>
				</div>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string( 'button-a-text' ); ?>><?php echo wp_kses( $settings['button_a_text'], element_pack_allow_tags('title') ); ?></div>
		</div>
		<?php
	}

	public function render_text_b($settings) {

		$this->add_render_attribute( 'content-wrapper-b', 'class', 'bdt-btn-content-wrap' );

		if ( 'left' == $settings['button_b_icon_align'] or 'right' == $settings['button_b_icon_align'] ) {
			$this->add_render_attribute( 'content-wrapper-b', 'class', 'bdt-flex bdt-flex-middle' );
		}
		$this->add_render_attribute( 'content-wrapper-b', 'class', 'bdt-flex-' . $settings['button_b_align'] );

		$this->add_render_attribute( 'content-wrapper-b', 'class', ( 'top' == $settings['button_b_icon_align'] ) ? 'bdt-flex bdt-flex-column' : '' );
		$this->add_render_attribute( 'content-wrapper-b', 'class', ( 'bottom' == $settings['button_b_icon_align'] ) ? 'bdt-flex bdt-flex-column-reverse' : '' );
		$this->add_render_attribute( 'content-wrapper-b', 'data-text', esc_attr($settings['button_b_text']));

		$this->add_render_attribute( 'button-b-text', 'class', 'bdt-btn-text' );

		if ( ! isset( $settings['button_b_icon'] ) && ! Icons_Manager::is_migration_allowed() ) {
			// add old default
			$settings['button_b_icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset( $settings['__fa4_migrated']['button_b_select_icon'] );
		$is_new    = empty( $settings['button_b_icon'] ) && Icons_Manager::is_migration_allowed();

		?>
		<div <?php echo $this->get_render_attribute_string( 'content-wrapper-b' ); ?>>
			<?php if ( ! empty( $settings['button_b_select_icon']['value'] ) ) : ?>
				<div class="bdt-btn-icon bdt-btn-b-icon bdt-flex-align-<?php echo esc_attr($settings['button_b_icon_align']); ?>">
					<div class="bdt-b-icon-inner">
					
					<?php if ( $is_new || $migrated ) :
						Icons_Manager::render_icon( $settings['button_b_select_icon'], [ 'aria-hidden' => 'true', 'class' => 'fa-fw' ] );
					else : ?>
						<i class="<?php echo esc_attr( $settings['button_b_icon'] ); ?>" aria-hidden="true"></i>
					<?php endif; ?>

					</div>
				</div>
			<?php endif; ?>
			<div <?php echo $this->get_render_attribute_string( 'button-b-text' ); ?>><?php echo wp_kses( $settings['button_b_text'], element_pack_allow_tags('title') ); ?></div>
		</div>
		<?php
	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		$this->add_render_attribute( 'wrapper', 'class', 'bdt-dual-button bdt-ep-button-wrapper bdt-element' );

		if ( ! empty( $settings['button_a_link']['url'] ) ) {
			$this->add_link_attributes( 'button_a', $settings['button_a_link'] );
		}

		if ( ! empty( $settings['button_b_link']['url'] ) ) {
			$this->add_link_attributes( 'button_b', $settings['button_b_link'] );
		}

		if ( 'yes' === $settings['button_a_onclick'] ) {
			$this->add_render_attribute( 'button_a', 'onclick', $settings['button_a_onclick_event'] );
		}

		if ( 'yes' === $settings['button_b_onclick'] ) {
			$this->add_render_attribute( 'button_b', 'onclick', $settings['button_b_onclick_event'] );
		}

		$this->add_render_attribute( 'button_a', 'class', 'bdt-btn-a bdt-ep-button' );
		$this->add_render_attribute( 'button_a', 'class', 'bdt-effect-' . esc_attr($settings['button_a_effect']) );
		$this->add_render_attribute( 'button_a', 'class', 'bdt-ep-button-size-' . esc_attr($settings['dual_button_size']) );

		if ( ! empty( $settings['button_css_id_a'] ) ) {
			$this->add_render_attribute( 'button_a', 'id', $settings['button_css_id_a'] );
		}

		if ( $settings['add_custom_a_attributes'] and ! empty( $settings['custom_a_attributes'] ) ) {
			$attributes = explode( "\n", $settings['custom_a_attributes'] );

			$reserved_attr = [ 'href', 'target' ];

			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute ) ) {
					$attr = explode( '|', $attribute, 2 );
					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					if ( ! in_array( strtolower( $attr[0] ), $reserved_attr ) ) {
						$this->add_render_attribute( 'button_a', trim( $attr[0] ), trim( $attr[1] ) );
					}
				}
			}
		}

		$this->add_render_attribute( 'button_b', 'class', 'bdt-btn-b bdt-ep-button' );		
		$this->add_render_attribute( 'button_b', 'class', 'bdt-effect-' . esc_attr($settings['button_b_effect']) );
		$this->add_render_attribute( 'button_b', 'class', 'bdt-ep-button-size-' . esc_attr($settings['dual_button_size']) );

		if ( ! empty( $settings['button_css_id_b'] ) ) {
			$this->add_render_attribute( 'button_b', 'id', $settings['button_css_id_b'] );
		}

		if ( $settings['add_custom_b_attributes'] and ! empty( $settings['custom_b_attributes'] ) ) {
			$attributes = explode( "\n", $settings['custom_b_attributes'] );

			$reserved_attr = [ 'href', 'target' ];

			foreach ( $attributes as $attribute ) {
				if ( ! empty( $attribute ) ) {
					$attr = explode( '|', $attribute, 2 );
					if ( ! isset( $attr[1] ) ) {
						$attr[1] = '';
					}

					if ( ! in_array( strtolower( $attr[0] ), $reserved_attr ) ) {
						$this->add_render_attribute( 'button_b', trim( $attr[0] ), trim( $attr[1] ) );
					}
				}
			}
		}

		?>
		<div class="bdt-element-align-wrapper bdt-flex">
			<div <?php echo $this->get_render_attribute_string( 'wrapper' ); ?>>
				<a <?php echo $this->get_render_attribute_string( 'button_a' ); ?>>
					<?php $this->render_text_a($settings); ?>
				</a>

				<?php if ( 'yes' === $settings['show_middle_text'] ) : ?>
					<span><?php echo esc_attr($settings['middle_text']); ?></span>
				<?php endif; ?>

				<a <?php echo $this->get_render_attribute_string( 'button_b' ); ?>>
					<?php $this->render_text_b($settings); ?>
				</a>
			</div>
		</div>
		<?php
	}
}
