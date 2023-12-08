<?php
namespace ElementPack\Modules\Navbar\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use ElementPack\Modules\Navbar\ep_menu_walker;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Navbar extends Module_Base {

	public function get_name() {
		return 'bdt-navbar';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Navbar', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-navbar';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'navbar', 'menu' ];
	}

	public function get_style_depends() {
       	if ($this->ep_is_edit_mode()) {
           return ['ep-styles'];
		} else {
			return [ 'ep-font', 'ep-navbar' ];
		}
  	}

	public function get_custom_help_url() {
		return 'https://youtu.be/ZXdDAi9tCxE';
	}

	protected function register_controls() {
		
		$this->start_controls_section(
			'section_navbar_content',
			[
				'label' => esc_html__( 'Navbar', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'navbar',
			[
				'label'   => esc_html__( 'Select Menu', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT,
				'options' => element_pack_get_menu(),
				'default' => 0,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label'   => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'flex-end'  => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-container' => 'justify-content: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_offset',
			[
				'label' => esc_html__( 'Offset', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => -150,
						'max' => 150,
					],
				],
				'size_units' => [ 'px' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav' => 'transform: translateX({{SIZE}}{{UNIT}});',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_height',
			[
				'label' => esc_html__( 'Menu Height', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 150,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'menu_parent_arrow',
			[
				'label'        => __( 'Parent Indicator', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-navbar-parent-indicator-',
			]
		);

		$this->add_control(
			'auto_hiding_menu',
			[
				'label'        => __( 'Auto Hiding Menu', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'dropdown_content',
			[
				'label' => esc_html__( 'Dropdown', 'bdthemes-element-pack' ),
			]
		);

		$this->add_responsive_control(
			'dropdown_align',
			[
				'label'     => esc_html__( 'Dropdown Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-center',
					],
					'right'  => [
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-h-align-right',
					],
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_link_align',
			[
				'label'   => esc_html__( 'Item Alignment', 'bdthemes-element-pack' ),
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
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_padding',
			[
				'label'      => esc_html__( 'Dropdown Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_width',
			[
				'label' => esc_html__( 'Dropdown Width', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 350,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'dropdown_additional',
			[
				'label' => esc_html__( 'Additional', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'dropdown_delay_show',
			[
				'label' => esc_html__( 'Delay Show', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
			]
		);

		$this->add_control(
			'dropdown_delay_hide',
			[
				'label' => esc_html__( 'Delay Hide', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
				'default' => ['size' => 800],
			]
		);

		$this->add_control(
			'dropdown_duration',
			[
				'label' => esc_html__( 'Dropdown Duration', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
					],
				],
				'default' => ['size' => 200],
			]
		);

		$this->add_control(
			'dropdown_offset',
			[
				'label' => esc_html__( 'Dropdown Offset', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_menu_style',
			[
				'label' => esc_html__( 'Navbar', 'bdthemes-element-pack' ),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'navbar_style',
			[
				'label'   => __( 'Navbar Style', 'bdthemes-element-pack' ) . BDTEP_UC,
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => __( 'Default', 'bdthemes-element-pack' ),
					'1' => __( 'Style 1', 'bdthemes-element-pack' ),
					'2' => __( 'Style 2', 'bdthemes-element-pack' ),
					'3' => __( 'Style 3', 'bdthemes-element-pack' ),
					'4' => __( 'Style 4', 'bdthemes-element-pack' ),
				],
				'prefix_class' => 'bdt-navbar-style-',
			]
		);

		$this->start_controls_tabs( 'menu_link_styles' );

		$this->start_controls_tab( 'menu_link_normal', [ 'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ) ] );

		$this->add_control(
			'menu_link_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_link_background',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_spacing',
			[
				'label' => esc_html__( 'Column Gap', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav' => 'margin-left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-navbar-nav > li' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'menu_bottom_spacing',
			[
				'label' => esc_html__( 'Row Gap', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav' => 'margin-bottom: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-navbar-nav > li' => 'margin-bottom: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-navbar-nav > li > a',
			]
		);

		$this->add_control(
			'menu_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography_normal',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-navbar-nav > li > a',
			]
		);

		$this->add_control(
			'menu_parent_arrow_color',
			[
				'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-nav > li.bdt-parent a:after' => 'color: {{VALUE}};',
				],
				'condition' => ['menu_parent_arrow' => 'yes'],
			]
		);
		//auto hiding menu shadow
		$this->add_control(
			'auto_hiding_menu_both_side_color',
			[
				'label'     => esc_html__( 'Auto Hiding Menu Shadow', 'bdthemes-element-pack' ) . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-auto-hide-menu .bdt-cd-secondary-nav::before' => 'background: linear-gradient(to right, {{VALUE}}, rgba(37,40,61,0));',
					'{{WRAPPER}} .bdt-auto-hide-menu .bdt-cd-secondary-nav::after' => 'background: linear-gradient(to left, {{VALUE}}, rgba(37,40,61,0));',
				],
				'condition' => ['auto_hiding_menu' => 'yes'],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab( 'menu_link_hover', [ 'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ) ] );

		$this->add_control(
			'navbar_hover_style_color',
			[
				'label'     => esc_html__( 'Style Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li:hover > a:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-navbar-nav > li:hover > a:after'  => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navbar_style!' => '',
				],
			]
		);

		$this->add_control(
			'menu_link_color_hover',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'link_background_hover',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_border_color_hover',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li > a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography_hover',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-navbar-nav > li > a:hover',
			]
		);

		$this->add_control(
			'menu_parent_arrow_color_hover',
			[
				'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-nav > li.bdt-parent a:hover::after' => 'color: {{VALUE}};',
				],
				'condition' => ['menu_parent_arrow' => 'yes'],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab( 'menu_link_active', [ 'label' => esc_html__( 'Active', 'bdthemes-element-pack' ) ] );

		$this->add_control(
			'navbar_active_style_color',
			[
				'label'     => esc_html__( 'Style Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a:before' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a:after'  => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navbar_style!' => '',
				],
			]
		);

		$this->add_control(
			'menu_hover_color_active',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'menu_hover_background_color_active',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'menu_border_active',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a',
			]
		);

		$this->add_control(
			'menu_border_radius_active',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'menu_typography_active',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-navbar-nav > li.bdt-active > a',
			]
		);

		$this->add_control(
			'menu_parent_arrow_color_active',
			[
				'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-nav > li.bdt-parent.bdt-active a:after' => 'color: {{VALUE}};',
				],
				'condition' => ['menu_parent_arrow' => 'yes'],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'dropdown_color',
			[
				'label' => esc_html__( 'Dropdown', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SECTION,
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'dropdown_background',
			[
				'label'     => esc_html__( 'Dropdown Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->start_controls_tabs( 'dropdown_link_styles' );

		$this->start_controls_tab( 'dropdown_link_normal', [ 'label' => esc_html__( 'Normal', 'bdthemes-element-pack' ) ] );

		$this->add_control(
			'dropdown_link_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_link_background',
			[
				'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'dropdown_link_spacing',
			[
				'label' => esc_html__( 'Gap', 'bdthemes-element-pack' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
					],
				],
				'size_units' => [ 'px'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li + li' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'dropdown_link_padding',
			[
				'label'      => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'dropdown_link_border',
				'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
				'default'  => '1px',
				'selector' => '{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a',
			]
		);

		$this->add_control(
			'dropdown_link_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_link_typography',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a',
			]
		);

		$this->add_control(
			'dropdown_parent_arrow_color',
			[
				'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-dropdown-nav > li.bdt-parent a:after' => 'color: {{VALUE}};',
				],
				'condition' => ['menu_parent_arrow' => 'yes'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'dropdown_link_hover', [ 'label' => esc_html__( 'Hover', 'bdthemes-element-pack' ) ] );

		$this->add_control(
			'dropdown_link_hover_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a:hover' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_control(
			'dropdown_link_hover_bg_color',
			[
				'label'     => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_border_hover_color',
			[
				'label'     => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'dropdown_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'dropdown_typography_hover',
				'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .bdt-navbar-dropdown-nav > li > a:hover',
			]
		);

		$this->add_control(
			'dropdown_parent_arrow_color_hover',
			[
				'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-dropdown-nav > li.bdt-parent a:hover::after' => 'color: {{VALUE}};',
				],
				'condition' => ['menu_parent_arrow' => 'yes'],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab( 'dropdown_link_active', [ 'label' => esc_html__( 'Active', 'bdthemes-element-pack' ) ] );

			$this->add_control(
				'dropdown_active_color',
				[
					'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-navbar-dropdown-nav > li.bdt-active > a' => 'color: {{VALUE}};',
					],
				]
			);

			$this->add_control(
				'dropdown_active_bg_color',
				[
					'label'     => esc_html__( 'Background', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}} .bdt-navbar-dropdown-nav > li.bdt-active > a' => 'background-color: {{VALUE}};',
					],
				]
			);

			$this->add_group_control(
				Group_Control_Border::get_type(),
				[
					'name'     => 'dropdown_active_border',
					'label'    => esc_html__( 'Border', 'bdthemes-element-pack' ),
					'default'  => '1px',
					'selector' => '{{WRAPPER}} .bdt-navbar-dropdown-nav > li.bdt-active > a',
				]
			);

			$this->add_control(
				'dropdown_active_radius',
				[
					'label'      => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
					'type'       => Controls_Manager::DIMENSIONS,
					'size_units' => [ 'px', '%' ],
					'selectors'  => [
						'{{WRAPPER}} .bdt-navbar-dropdown-nav > li.bdt-active > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					],
				]
			);
			$this->add_group_control(
				Group_Control_Typography::get_type(),
				[
					'name'     => 'dropdown_typography_active',
					'label'    => esc_html__( 'Typography', 'bdthemes-element-pack' ),
					//'scheme'   => Schemes\Typography::TYPOGRAPHY_1,
					'selector' => '{{WRAPPER}} .bdt-navbar-dropdown-nav > li.bdt-active > a',
				]
			);

			$this->add_control(
				'dropdown_parent_arrow_color_active',
				[
					'label'     => esc_html__( 'Parent Indicator Color', 'bdthemes-element-pack' ),
					'type'      => Controls_Manager::COLOR,
					'selectors' => [
						'{{WRAPPER}}.bdt-navbar-parent-indicator-yes .bdt-navbar-dropdown-nav > li.bdt-parent.bdt-active a:after' => 'color: {{VALUE}};',
					],
					'condition' => ['menu_parent_arrow' => 'yes'],
				]
			);


		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render() {

		$settings = $this->get_settings_for_display();
		$id       = 'bdt-navbar-' . $this->get_id();
		
		if (!$settings['navbar']) {
			element_pack_alert(__('Please select a Menu From Setting!', 'bdthemes-element-pack'));
		}

		$nav_menu = ! empty( $settings['navbar'] ) ? wp_get_nav_menu_object( $settings['navbar'] ) : false;

		if ( ! $nav_menu ) {
			return;
		}

		$nav_menu_args = array(
			'fallback_cb'    => false,
			'container'      => false,
			'menu_class'     => 'bdt-navbar-nav',
			'items_wrap'     => '<ul class="%2$s">%3$s</ul>',
			'theme_location' => 'default_navmenu', // creating a fake location for better functional control
			'menu'           => $nav_menu,
			'echo'           => true,
			'depth'          => 0,
			'walker'        => new ep_menu_walker
		);

		$this->add_render_attribute(
			[
				'navbar-attr' => [
					'class' => [
						'bdt-navbar-container',
						'bdt-navbar',
						'bdt-navbar-transparent'
					],
					'data-bdt-navbar' => [
						wp_json_encode(array_filter([
							"align"      => $settings["dropdown_align"] ? $settings["dropdown_align"] : "left",
							"delay-show" => $settings["dropdown_delay_show"]["size"] ? $settings["dropdown_delay_show"]["size"] : false,
							"delay-hide" => $settings["dropdown_delay_hide"]["size"] ? $settings["dropdown_delay_hide"]["size"] : false,
							"offset"     => $settings["dropdown_offset"]["size"] ? $settings["dropdown_offset"]["size"] : false,
							"duration"   => $settings["dropdown_duration"]["size"] ? $settings["dropdown_duration"]["size"] : false
						]))
					]
				]
			]
		);

		if ('yes' == $settings['auto_hiding_menu']) {
			$this->add_render_attribute('navbar-wrap', 'class', 'bdt-auto-hide-menu');
			$this->add_render_attribute('navbar-attr', 'class', 'bdt-cd-secondary-nav');
		}
		$this->add_render_attribute('navbar-wrap', 'class', 'bdt-navbar-wrapper');

		?>
		<div <?php echo $this->get_render_attribute_string( 'navbar-wrap' ); ?>>
			<nav <?php echo $this->get_render_attribute_string( 'navbar-attr' ); ?>>
				<?php wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings ) ); ?>
			</nav>
		</div>
		<?php
	}
}