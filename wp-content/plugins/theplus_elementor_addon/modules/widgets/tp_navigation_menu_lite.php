<?php 
/*
Widget Name: TP Navigation Menu Lite
Description: Style of header navigation bar menu
Author: theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Box_Shadow;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Navigation_Menu_Lite extends Widget_Base {

	public $TpDoc = THEPLUS_TPDOC;
		
	public function get_name() {
		return 'tp-navigation-menu-lite';
	}

    public function get_title() {
        return __('Navigation Menu Lite', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-bars theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-header');
    }

	public function get_custom_help_url() {
		$DocUrl = $this->TpDoc . "navigation-menu-lite";

		return esc_url($DocUrl);
	}

	public function get_keywords() {
		return [ 'menu', 'navigation', 'header','menu bar','nav' ];
	}
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'navbar_sections',
			[
				'label' => __( 'Navigation Bar', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'navbar_menu_type',
			[
				'label' => __( 'Menu Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'horizontal',
				'options' => [
					'horizontal'  => __( 'Horizontal Menu', 'theplus' ),					
					'vertical' => __( 'Vertical Menu', 'theplus' ),
				],
			]
		);
		$this->add_control('how_it_works_vertical',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "create-a-vertical-navigation-menu-in-elementor-for-free/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> How it works <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				 'condition' => [
					'navbar_menu_type' => ['vertical']
				],
			]
		);
		$this->add_control(
			'navbar',
			[
				'label' => __( 'Select Menu', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => l_theplus_navigation_menulist(),
			]
		);
		$this->add_control(
			'menu_hover_click',
			[
				'label' => __( 'Menu Hover/Click', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover'  => __( 'Hover Sub-Menu', 'theplus' ),					
					'click' => __( 'Click Sub-Menu', 'theplus' ),
				],
			]
		);
		$this->add_control('how_it_works_hovermenu',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "open-elementor-submenu-dropdown-on-hover-for-free/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> How it works <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'menu_hover_click' => ['hover']
				],
			]
		);
		$this->add_control('how_it_works_clickmenu',
			[
				'label' => wp_kses_post( "<a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "open-elementor-submenu-dropdown-on-click-for-free/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> How it works <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'condition' => [
					'menu_hover_click' => ['click']
				],
			]
		);
		$this->add_control(
			'menu_transition',
			[
				'label' => __( 'Menu Effects', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => __( 'Style 1', 'theplus' ),					
					'style-2' => __( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->end_controls_section();
		
		$this->start_controls_section(
            'section_extra_options',
            [
                'label' => __('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		
		$this->add_control(
			'nav_alignment',
			[
				'label' => __( 'Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'text-left' => [
						'title' => __( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'text-center' => [
						'title' => __( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'text-right' => [
						'title' => __( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'separator' => 'before',
				'default' => 'text-center',
				'toggle' => true,
				'label_block' => false,
			]
		);
		$this->end_controls_section();
		/*mobile menu content*/
		$this->start_controls_section(
            'section_mobile_menu_options',
            [
                'label' => __('Mobile Menu', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );
		$this->add_control(
			'show_mobile_menu',
			[
				 'label' => wp_kses_post( "Responsive Mobile Menu <a class='tp-docs-link' href='" . esc_url($this->TpDoc) . "create-an-elementor-hamburger-toggle-menu-for-mobile-for-free/?utm_source=wpbackend&utm_medium=elementoreditor&utm_campaign=widget' target='_blank' rel='noopener noreferrer'> <i class='eicon-help-o'></i> </a>", 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),				
				'default' => 'yes',
			]
		);
		$this->add_control(
			'open_mobile_menu',
			[
				'label' => __( 'Open Mobile Menu', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1500,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 991,
				],
				'condition' => [					
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_menu_toggle_style',
			[
				'label' => __( 'Toggle Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => __( 'Style 1', 'theplus' ),
				],
				'condition' => [					
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_toggle_alignment',
			[
				'label' => __( 'Toggle Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => __( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => __( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'separator' => 'before',
				'default' => 'flex-end',
				'toggle' => true,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-nav-toggle.mobile-toggle' => 'justify-content: {{VALUE}}',
				],
				'condition' => [					
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_nav_alignment',
			[
				'label' => __( 'Mobile Navigation Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'separator' => 'before',
				'default' => 'flex-start',
				'toggle' => true,
				'label_block' => false,
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu-content .nav li a' => 'text-align: {{VALUE}}',
				],
				'condition' => [					
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_menu_content',
			[
				'label' => __( 'Menu Content', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal-menu',
				'options' => [
					'normal-menu'  => __( 'Normal Menu', 'theplus' ),
					'template-menu'  => __( 'Template Menu', 'theplus' ),
				],
				'condition' => [					
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_navbar',
			[
				'label' => __( 'Select Menu', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => l_theplus_navigation_menulist(),
				'condition' => [
					'show_mobile_menu' => 'yes',
					'mobile_menu_content' => 'normal-menu',
				],
			]
		);
		$this->add_control(
			'mobile_navbar_template_pro',
			[
				'label' => esc_html__( 'Unlock more possibilities', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => theplus_pro_ver_notice(),
				'classes' => 'plus-pro-version',
				'condition'    => [
					'show_mobile_menu' => 'yes',
					'mobile_menu_content' => "template-menu",
				],
			]
		);
		$this->end_controls_section();
		/*mobile menu content*/
		/*Main Menu Style*/
		$this->start_controls_section(
			'main_menu_styling',
			[
				'label' => __( 'Main Menu', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_menu_typography',
				'label' => __( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a',
			]
		);
		$this->add_responsive_control(
			'main_menu_outer_padding',
			[
				'label' => __( 'Outer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '5',
							'right' => '5',
							'bottom' => '5',
							'left' => '5',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'main_menu_inner_padding',
			[
				'label' => __( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '10',
							'right' => '5',
							'bottom' => '10',
							'left' => '5',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-2 .plus-navigation-menu .navbar-nav > li.dropdown > a:before' => 'right: calc({{RIGHT}}{{UNIT}} + 3px);',
				],
			]
		);
		$this->add_control(
			'main_menu_indicator_style',
			[
				'label' => __( 'Main Menu Indicator Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => __( 'None', 'theplus' ),
					'style-1'  => __( 'Style 1', 'theplus' ),
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_main_menu_style' );
		$this->start_controls_tab(
			'tab_main_menu_normal',
			[
				'label' => __( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'main_menu_normal_color',
			[
				'label' => __( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'main_menu_normal_icon_color',
			[
				'label' => __( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-1 .plus-navigation-menu .navbar-nav > li.dropdown > a:after' => 'color: {{VALUE}}',
				],
				'condition' => [					
					'main_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'main_menu_border',
			[
				'label' => __( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'main_menu_normal_border_style',
			[
				'label'   => __( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'theplus' ),
					'solid'  => __( 'Solid', 'theplus' ),
					'dotted' => __( 'Dotted', 'theplus' ),
					'dashed' => __( 'Dashed', 'theplus' ),
					'groove' => __( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'main_menu_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'main_menu_normal_border_color',
			[
				'label' => __( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'border-color: {{VALUE}};',
				],
				'condition' => [					
					'main_menu_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'main_menu_normal_border_width',
			[
				'label' => __( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'main_menu_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'main_menu_normal_radius',
			[
				'label'      => __( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'main_menu_normal_bg_options',
			[
				'label' => __( 'Normal Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'main_menu_normal_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a',
				
			]
		);
		$this->add_control(
			'main_menu_normal_shadow_options',
			[
				'label' => __( 'Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'main_menu_normal_shadow',
				'selector' => '{{WRAPPER}} .plus-navigation-menu .navbar-nav>li>a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_main_menu_hover',
			[
				'label' => __( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'main_menu_hover_color',
			[
				'label' => __( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:hover > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'main_menu_hover_icon_color',
			[
				'label' => __( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-1 .plus-navigation-menu .navbar-nav > li.dropdown:hover > a:after' => 'color: {{VALUE}}',
				],
				'condition' => [					
					'main_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'main_menu_hover_border_color',
			[
				'label' => __( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:hover > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [					
					'main_menu_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'main_menu_hover_radius',
			[
				'label'      => __( 'Hover Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:hover > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'main_menu_hover_bg_options',
			[
				'label' => __( 'Hover Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'main_menu_hover_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:hover > a',
				
			]
		);
		$this->add_control(
			'main_menu_hover_shadow_options',
			[
				'label' => __( 'Hover Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'main_menu_hover_shadow',
				'selector' => '{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:hover > a',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_main_menu_active',
			[
				'label' => __( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'main_menu_active_color',
			[
				'label' => __( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.current_page_item > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'main_menu_active_icon_color',
			[
				'label' => __( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-1 .plus-navigation-menu .navbar-nav > li.dropdown.active > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-1 .plus-navigation-menu .navbar-nav > li.dropdown:focus > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.main-menu-indicator-style-1 .plus-navigation-menu .navbar-nav > li.dropdown.current_page_item > a:after' => 'color: {{VALUE}}',
				],
				'condition' => [					
					'main_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'main_menu_active_border_color',
			[
				'label' => __( 'Active Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.current_page_item > a' => 'border-color: {{VALUE}};',
				],
				'condition' => [					
					'main_menu_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'main_menu_active_radius',
			[
				'label'      => __( 'Active Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.current_page_item > a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'main_menu_active_bg_options',
			[
				'label' => __( 'Active Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'main_menu_active_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.current_page_item > a',
				
			]
		);
		$this->add_control(
			'main_menu_active_shadow_options',
			[
				'label' => __( 'Active Shadow Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'main_menu_active_shadow',
				'selector' => '{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav > li.current_page_item > a',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Main Menu Style*/
		/*Sub Menu Style*/
		$this->start_controls_section(
			'sub_menu_styling',
			[
				'label' => __( 'Sub Menu', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sub_menu_typography',
				'label' => __( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu > li > a',
			]
		);
		$this->add_control(
			'sub_menu_outer_options',
			[
				'label' => __( 'Sub-Menu Outer Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'sub_menu_outer_padding',
			[
				'label' => __( 'Outer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '0',
							'right' => '0',
							'bottom' => '0',
							'left' => '0',
							'isLinked' => true
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu .dropdown-menu' => 'margin-top: {{TOP}}{{UNIT}};',
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu .dropdown-menu' => 'left: calc(100% + {{RIGHT}}{{UNIT}});',
				],
			]
		);
		$this->add_control(
			'sub_menu_outer_border',
			[
				'label' => __( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		$this->add_control(
			'sub_menu_outer_border_style',
			[
				'label'   => __( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => __( 'None', 'theplus' ),
					'solid'  => __( 'Solid', 'theplus' ),
					'dotted' => __( 'Dotted', 'theplus' ),
					'dashed' => __( 'Dashed', 'theplus' ),
					'groove' => __( 'Groove', 'theplus' ),
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu' => 'border-style: {{VALUE}};',
				],
				'condition' => [
					'sub_menu_outer_border' => 'yes',
				],
			]
		);
		$this->add_control(
			'sub_menu_outer_border_color',
			[
				'label' => __( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu' => 'border-color: {{VALUE}};',
				],
				'condition' => [					
					'sub_menu_outer_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'sub_menu_outer_border_width',
			[
				'label' => __( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 1,
					'right'  => 1,
					'bottom' => 1,
					'left'   => 1,
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'sub_menu_outer_border' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'sub_menu_outer_radius',
			[
				'label'      => __( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sub_menu_outer_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu',
				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'sub_menu_outer_shadow',
				'selector' => '{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu',
				'separator' => 'after',
			]
		);
		$this->add_control(
			'sub_menu_inner_options',
			[
				'label' => __( 'Sub-Menu Inner Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'sub_menu_inner_padding',
			[
				'label' => __( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '10',
							'right' => '15',
							'bottom' => '10',
							'left' => '15',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu:not(.menu-vertical) .nav li.dropdown .dropdown-menu > li,{{WRAPPER}} .plus-navigation-menu.menu-vertical .nav li.dropdown .dropdown-menu > li a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}  !important;',
				],
			]
		);
		$this->add_control(
			'sub_menu_indicator_style',
			[
				'label' => __( 'Sub Menu Indicator Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => __( 'None', 'theplus' ),
					'style-1'  => __( 'Style 1', 'theplus' ),
					'style-2'  => __( 'Style 2', 'theplus' ),
				],
				'separator' => 'after',
			]
		);
		$this->start_controls_tabs( 'tabs_sub_menu_style' );
		$this->start_controls_tab(
			'tab_sub_menu_normal',
			[
				'label' => __( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'sub_menu_normal_color',
			[
				'label' => __( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu > li > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'sub_menu_normal_icon_color',
			[
				'label' => __( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-1 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a:before,{{WRAPPER}}  .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu > a:before' => 'border-color: {{VALUE}};background: 0 0;',
				],
				'condition' => [					
					'sub_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'sub_menu_normal_bg_options',
			[
				'label' => __( 'Normal Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sub_menu_normal_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu > li',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sub_menu_hover',
			[
				'label' => __( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'sub_menu_hover_color',
			[
				'label' => __( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu > li:hover > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'sub_menu_hover_icon_color',
			[
				'label' => __( 'Hover Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-1 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a:before,{{WRAPPER}}  .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:hover > a:before' => 'border-color: {{VALUE}};background: 0 0;',
				],
				'condition' => [					
					'sub_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'sub_menu_hover_bg_options',
			[
				'label' => __( 'Hover Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sub_menu_hover_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .nav li.dropdown .dropdown-menu > li:hover',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_sub_menu_active',
			[
				'label' => __( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'sub_menu_active_color',
			[
				'label' => __( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li.active > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li:focus > a,{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li.current_page_item > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'sub_menu_active_icon_color',
			[
				'label' => __( 'Active Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-1 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.active > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-1 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:focus > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-1 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.current_page_item > a:after' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.active > a:before,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:focus > a:before,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.current_page_item > a:before,{{WRAPPER}}  .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.active > a:after,{{WRAPPER}}  .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:focus > a:after,{{WRAPPER}}  .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.current_page_item > a:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.active > a:before,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:focus > a:before,{{WRAPPER}} .plus-navigation-wrap .plus-navigation-inner.sub-menu-indicator-style-2 .plus-navigation-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.current_page_item > a:before' => 'border-color: {{VALUE}};background: 0 0;',
				],
				'condition' => [					
					'sub_menu_indicator_style!' => 'none',
				],
			]
		);
		$this->add_control(
			'sub_menu_active_bg_options',
			[
				'label' => __( 'Active Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sub_menu_active_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li.active,{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li:focus,{{WRAPPER}} .plus-navigation-menu .navbar-nav li.dropdown .dropdown-menu > li.current_page_item',
				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Mobile Menu Style*/
		$this->start_controls_section(
			'mobile_nav_options_styling',
			[
				'label' => __( 'Mobile Menu Style', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_mobile_menu' => 'yes',
				],
			]
		);
		$this->add_control(
			'mobile_nav_toggle_options',
			[
				'label' => __( 'Toggle Navigation Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mobile_nav_toggle_height',
			[
				'label' => __( 'Toggle Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-nav-toggle.mobile-toggle' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->start_controls_tabs( 'tab_toggle_nav_style' );
		$this->start_controls_tab(
			'tab_toggle_nav_normal',
			[
				'label' => __( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'toggle_nav_color',
			[
				'label' => __( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .mobile-plus-toggle-menu ul.toggle-lines li.toggle-line' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_toggle_nav_active',
			[
				'label' => __( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'toggle_nav_active_color',
			[
				'label' => __( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .mobile-plus-toggle-menu:not(.collapsed) ul.toggle-lines li.toggle-line' => 'background: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mobile_main_menu_options',
			[
				'label' => __( 'Mobile Main Menu Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mobile_main_menu_typography',
				'label' => __( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-mobile-menu .navbar-nav>li>a',
			]
		);
		$this->add_responsive_control(
			'mobile_main_menu_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .navbar-nav li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'mobile_main_menu_inner_padding',
			[
				'label' => __( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '10',
							'right' => '10',
							'bottom' => '10',
							'left' => '10',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .navbar-nav>li>a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',					
				],
			]
		);
		$this->start_controls_tabs( 'tabs_mobile_main_menu_style' );
		$this->start_controls_tab(
			'tab_mobile_main_menu_normal',
			[
				'label' => __( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'mobile_main_menu_normal_color',
			[
				'label' => __( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .navbar-nav>li>a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_main_menu_normal_icon_color',
			[
				'label' => __( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown > a:after' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_main_menu_normal_bg_options',
			[
				'label' => __( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mobile_main_menu_normal_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav>li>a',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mobile_main_menu_active',
			[
				'label' => __( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'mobile_main_menu_active_color',
			[
				'label' => __( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.active > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li:focus > a,{{WRAPPER}} .plus-mobile-menu .navbar-nav > li.current_page_item > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_main_menu_active_icon_color',
			[
				'label' => __( 'Active Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown.active > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown:focus > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown.current_page_item > a:after' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_main_menu_active_bg_options',
			[
				'label' => __( 'Active Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mobile_main_menu_active_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown.active > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown:focus > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav > li.dropdown.current_page_item > a',
				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mobile_menu_border_color',
			[
				'label' => __( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'separator' => ['before','after'],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-nav-toggle .plus-mobile-menu .navbar-nav li a,
					{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li a' => 'border-bottom-color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_sub_menu_options',
			[
				'label' => __( 'Mobile Sub Menu Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'mobile_sub_menu_typography',
				'label' => __( 'Typography', 'theplus' ),
				'selector' => '{{WRAPPER}} .plus-mobile-menu .nav li.dropdown .dropdown-menu > li > a',
			]
		);
		$this->add_responsive_control(
			'mobile_sub_menu_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .nav li.dropdown .dropdown-menu li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',					
				],
			]
		);
		$this->add_responsive_control(
			'mobile_sub_menu_inner_padding',
			[
				'label' => __( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px' ],
				'default' => [
							'top' => '10',
							'right' => '10',
							'bottom' => '10',
							'left' => '15',
							'isLinked' => false 
				],
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .nav li.dropdown .dropdown-menu > li > a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',					
				],
			]
		);
		$this->start_controls_tabs( 'tabs_mobile_sub_menu_style' );
		$this->start_controls_tab(
			'tab__mobile_sub_menu_normal',
			[
				'label' => __( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'mobile_sub_menu_normal_color',
			[
				'label' => __( 'Normal Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-mobile-menu .nav li.dropdown .dropdown-menu > li > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_sub_menu_normal_icon_color',
			[
				'label' => __( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .nav li.dropdown .dropdown-menu > li > a:after' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_sub_menu_normal_bg_options',
			[
				'label' => __( 'Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mobile_sub_menu_normal_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .nav li.dropdown .dropdown-menu > li > a',
				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mobile_sub_menu_active',
			[
				'label' => __( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'mobile_sub_menu_active_color',
			[
				'label' => __( 'Active Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.active > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li:focus > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.current_page_item > a' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_sub_menu_active_icon_color',
			[
				'label' => __( 'Active Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.active > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu:focus > a:after,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav ul.dropdown-menu > li.dropdown-submenu.current_page_item > a:after' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'mobile_sub_menu_active_bg_options',
			[
				'label' => __( 'Active Background Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mobile_sub_menu_active_bg_color',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.active > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li:focus > a,{{WRAPPER}} .plus-navigation-wrap .plus-mobile-menu .navbar-nav li.dropdown .dropdown-menu > li.current_page_item > a',
				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Mobile Menu Style*/
		/*Extra Options Style*/
		$this->start_controls_section(
			'extra_options_styling',
			[
				'label' => __( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'main_menu_hover_style',
			[
				'label' => __( 'Main Menu Hover Effects', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'  => __( 'None', 'theplus' ),
					'style-1'  => __( 'Style 1', 'theplus' ),
					'style-2'  => __( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'border-height',
			[
				'label' => __( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 30,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:after,{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:before' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'main_menu_hover_style' => ['style-2']
				],
			]
		);
		$this->add_control(
			'alignment-border-adjust',
			[
				'label' => __( 'Alignment Border Adjust', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:after,{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:before' => 'bottom : {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'main_menu_hover_style' => ['style-2']
				],
			]
		);
		$this->add_control(
			'main_menu_hover_style_1_color',
			[
				'label' => __( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-1 > li > a:before,{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:after,{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'main_menu_hover_style' => ['style-1','style-2']
				],
			]
		);
		$this->add_control(
			'main_menu_hover_style_2_color',
			[
				'label' => __( 'Hover Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#222',
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:hover:after,{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-2 > li > a:hover:before' => 'background: {{VALUE}}',
				],
				'condition' => [
					'main_menu_hover_style' => ['style-2']
				],
			]
		);
		$this->add_control(
			'main_menu_hover_style_1_width',
			[
				'label' => __( 'Border Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-navigation-menu .navbar-nav.menu-hover-style-1 > li > a:before' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'main_menu_hover_style' => 'style-1',
				],
			]
		);
		$this->add_control(
			'main_menu_hover_inverse',
			[
				'label' => __( 'On Hover Inverse Effect Main Menu', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'main_menu_hover_inverse_pro',
			[
				'label' => esc_html__( 'Unlock more possibilities', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => theplus_pro_ver_notice(),
				'classes' => 'plus-pro-version',
				'condition'    => [
					'main_menu_hover_inverse' => 'yes',
				],
			]
		);
		$this->add_control(
			'sub_menu_hover_inverse',
			[
				'label' => __( 'On Hover Inverse Effect Sub Menu', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sub_menu_hover_inverse_pro',
			[
				'label' => esc_html__( 'Unlock more possibilities', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => theplus_pro_ver_notice(),
				'classes' => 'plus-pro-version',
				'condition'    => [
					'sub_menu_hover_inverse' => 'yes',
				],
			]
		);
		$this->add_control(
			'main_menu_last_open_sub_menu',
			[
				'label' => __( 'Main Menu Last Open Sub-menu Left', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Show', 'theplus' ),
				'label_off' => __( 'Hide', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'main_menu_last_open_sub_menu_pro',
			[
				'label' => esc_html__( 'Unlock more possibilities', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'description' => theplus_pro_ver_notice(),
				'classes' => 'plus-pro-version',
				'condition'    => [
					'main_menu_last_open_sub_menu' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		include THEPLUS_PATH. 'modules/widgets/theplus-needhelp.php';
	}
	
	 protected function render() {
		$menu_attr ='';
        $settings = $this->get_settings_for_display();
		$nav_alignment=$settings["nav_alignment"];
		$menu_hover_click='menu-'.$settings["menu_hover_click"];
		$navbar_menu_type='menu-'.$settings["navbar_menu_type"];
		$menu_attr .=' data-menu_transition="'.esc_attr($settings["menu_transition"]).'"';
		$main_menu_hover_style='menu-hover-'.$settings["main_menu_hover_style"];
		$nav_menu    = ! empty( $settings['navbar'] ) ? wp_get_nav_menu_object( $settings['navbar'] ) : false;
		$mobile_navbar    = ! empty( $settings['mobile_navbar'] ) ? wp_get_nav_menu_object( $settings['mobile_navbar'] ) : false;		
		
		$main_menu_indicator_style='main-menu-indicator-'.$settings['main_menu_indicator_style'];
		$sub_menu_indicator_style='sub-menu-indicator-'.$settings['sub_menu_indicator_style'];
		
		$mobile_menu_toggle_style=$settings["mobile_menu_toggle_style"];
		$navbar_attr = [];
	    if ( ! $nav_menu ) {
	    	return;
	    }

		$nav_menu_args=array(
			'menu'           => $nav_menu,
			'theme_location'    => 'default_navmenu',
			'depth'             => 8,
			'container'         => 'div',
			'container_class'   => 'plus-navigation-menu '.$navbar_menu_type,
			'menu_class'        => 'nav navbar-nav yamm '.$main_menu_hover_style,
			'fallback_cb'       => false,
			'walker'            => new L_Theplus_Navigation_NavWalker
		);
		if($settings["show_mobile_menu"]=='yes' && $settings["mobile_menu_content"]=='normal-menu' && !empty($settings["mobile_navbar"])){
			$mobile_nav_menu_args=array(
				'menu'           => $mobile_navbar,
				'theme_location'    => 'mobile_navmenu',
				'depth'             => 5,
				'container'         => 'div',
				'container_class'   => 'plus-mobile-menu',
				'menu_class'        => 'nav navbar-nav',
				'fallback_cb'       => false,
				'walker'            => new L_Theplus_Navigation_NavWalker
			);
		}
		$uid=uniqid("nav-menu");
		?>
		
		<div class="plus-navigation-wrap <?php echo esc_attr($nav_alignment); ?> <?php echo esc_attr($uid); ?>">
			<div class="plus-navigation-inner <?php echo esc_attr($menu_hover_click); ?> <?php echo esc_attr($main_menu_indicator_style); ?> <?php echo esc_attr($sub_menu_indicator_style); ?> " <?php echo $menu_attr; ?>>
				<div id="theplus-navigation-normal-menu" class="collapse navbar-collapse navbar-ex1-collapse">
					<?php wp_nav_menu( apply_filters( 'widget_nav_menu_args', $nav_menu_args, $nav_menu, $settings ) ); ?>	
				</div>
				
				<?php if($settings["show_mobile_menu"]=='yes' && !empty($mobile_menu_toggle_style)){ ?>
				
					<div class="plus-mobile-nav-toggle navbar-header mobile-toggle">
						<div class="mobile-plus-toggle-menu plus-collapsed toggle-<?php echo esc_attr($mobile_menu_toggle_style); ?>" data-target="#plus-mobile-nav-toggle-<?php echo esc_attr($uid); ?>">
							<?php if($mobile_menu_toggle_style=='style-1'){ ?>
							<ul class="toggle-lines">
								<li class="toggle-line"></li>
								<li class="toggle-line"></li>
							</ul>
							<?php } ?>
						</div>
					</div>
					<div id="plus-mobile-nav-toggle-<?php echo esc_attr($uid); ?>" class="collapse navbar-collapse navbar-ex1-collapse plus-mobile-menu-content">
						<?php if($settings["mobile_menu_content"]=='normal-menu' && !empty($settings["mobile_navbar"])){ ?>
							<?php wp_nav_menu( apply_filters( 'widget_nav_menu_args', $mobile_nav_menu_args, $nav_menu, $settings ) ); ?>	
						<?php } ?>						
					</div>
				<?php } ?>
				
			</div>
		</div>
		 
		<?php 
		$css_rule='';		
		if($settings["show_mobile_menu"]=='yes' && !empty($settings['open_mobile_menu']['size'])){
			$open_mobile_menu=($settings['open_mobile_menu']['size']).$settings['open_mobile_menu']['unit'];
			$close_mobile_menu=($settings['open_mobile_menu']['size']+1).$settings['open_mobile_menu']['unit'];
			
			$css_rule .='@media (min-width:'.esc_attr($close_mobile_menu).'){.plus-navigation-wrap.'.esc_attr($uid).' #theplus-navigation-normal-menu{display: block!important;}.plus-navigation-wrap.'.esc_attr($uid).' #plus-mobile-nav-toggle-'.esc_attr($uid).'.collapse.in{display:none;}}';
			
			$css_rule .='@media (max-width:'.esc_attr($open_mobile_menu).'){.plus-navigation-wrap.'.esc_attr($uid).' #theplus-navigation-normal-menu{display:none !important;}.plus-navigation-wrap.'.esc_attr($uid).' .plus-mobile-nav-toggle.mobile-toggle{display: -webkit-flex;display: -moz-flex;display: -ms-flex;display: flex;-webkit-align-items: center;-moz-align-items: center;-ms-align-items: center;align-items: center;-webkit-justify-content: flex-end;-moz-justify-content: flex-end;-ms-justify-content: flex-end;justify-content: flex-end;}}';
		}else{
			$css_rule .='.plus-navigation-wrap.'.esc_attr($uid).' #theplus-navigation-normal-menu{display: block!important;}';
		}
		echo '<style>'.$css_rule.'</style>';
	}
	
    protected function content_template() {
	
    }

}