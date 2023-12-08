<?php 
/*
Widget Name: Scroll Navigation
Description: navigation bar Scrolling Effect scroll event.
Author: Theplus
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
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Scroll_Navigation extends Widget_Base {
		
	public function get_name() {
		return 'tp-scroll-navigation';
	}

    public function get_title() {
        return esc_html__('Scroll Navigation', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-sort theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	
    protected function register_controls() {
		/* Scroll Navigation Menu List Start*/
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Scroll Navigation', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'scroll_navigation_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),					
					'style-2' => esc_html__( 'Style 2', 'theplus' ),					
					'style-3' => esc_html__( 'Style 3', 'theplus' ),					
					'style-4' => esc_html__( 'Style 4', 'theplus' ),					
					'style-5' => esc_html__( 'Style 5', 'theplus' ),					
				],
			]
		);
		$this->add_control(
			'scroll_navigation_direction',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [									
					'left'  => esc_html__( 'Middle Left', 'theplus' ),
					'right'  => esc_html__( 'Middle Right', 'theplus' ),
					'top'  => esc_html__( 'Top', 'theplus' ),					
					'top_left'  => esc_html__( 'Top Left', 'theplus' ),					
					'top_right'  => esc_html__( 'Top Right', 'theplus' ),
					'bottom'  => esc_html__( 'Bottom', 'theplus' ),	
					'bottom_left'  => esc_html__( 'Bottom Left', 'theplus' ),	
					'bottom_right'  => esc_html__( 'Bottom Right', 'theplus' ),	
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-1','style-3','style-5'],
				],
			]
		);
		$this->add_control(
			'scroll_navigation_direction_st4',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [									
					'left'  => esc_html__( 'Middle Left', 'theplus' ),
					'right'  => esc_html__( 'Middle Right', 'theplus' ),					
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		
		$this->add_control(
			'scroll_navigation_direction_inner',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'p_center',
				'options' => [									
					'p_left'  => esc_html__( 'Left', 'theplus' ),
					'p_right'  => esc_html__( 'Right', 'theplus' ),
					'p_center'  => esc_html__( 'Center', 'theplus' ),
				],
				'condition'    => [
				'scroll_navigation_direction' => ['top','bottom'],
				'scroll_navigation_style!' => ['style-2','style-4'],
				],
			]
		);
		$this->add_control(
			'scroll_navigation_display_counter',
			[
				'label' => esc_html__( 'Display Counter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [					
					'scroll_navigation_style' => ['style-2','style-4'],
				],
				
			]
		);
		$this->add_control(
			'scroll_navigation_display_counter_style',
			[
				'label' => esc_html__( 'Counter Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'number-normal',
				'options' => [									
					'number-normal'  => esc_html__( 'Normal', 'theplus' ),
					'decimal-leading-zero'  => esc_html__( 'Decimal Leading Zero', 'theplus' ),
					'upper-alpha'  => esc_html__( 'Upper Alpha', 'theplus' ),
					'lower-alpha'  => esc_html__( 'Lower Alpha', 'theplus' ),
					'lower-roman'  => esc_html__( 'Lower Roman', 'theplus' ),
					'upper-roman'  => esc_html__( 'Upper Roman', 'theplus' ),
					'lower-greek'  => esc_html__( 'Lower Greek', 'theplus' ),
					
				],
				'condition'    => [
					'scroll_navigation_display_counter' => 'yes',
					'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_control(
			'scroll_navigation_tooltip_display_style',
			[
				'label' => esc_html__( 'Tooltip Display Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'on-hover',
				'options' => [									
					'on-hover'  => esc_html__( 'On Hover', 'theplus' ),
					'on-active-section'  => esc_html__( 'On Active Section', 'theplus' ),
					'on-default'  => esc_html__( 'Default', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'scroll_navigation_section_id',
			[
				'label' => esc_html__( 'Section Id', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'section-id',
			]
		);
		$repeater->add_control(
			'display_tool_tip',
			[
				'label' => esc_html__( 'Tooltip', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				
			]
		);
		$repeater->add_control(
			'tooltip_menu_title',
			[
				'label' => esc_html__( 'Tooltip Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => ['active'   => true,],
				'condition' => [				
					'display_tool_tip' => 'yes',
				],
			]
		);
		
		$repeater->add_control(
			'display_tool_tip_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'loop_icon_style',
			[
				'label' => esc_html__( 'Icon Font', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'font_awesome',
				'options' => [
					'font_awesome'  => esc_html__( 'Font Awesome', 'theplus' ),
					'font_awesome_5'  => esc_html__( 'Font Awesome 5', 'theplus' ),
					'icon_mind' => esc_html__( 'Icons Mind', 'theplus' ),
				],
				'condition' => [
					'display_tool_tip_icon' => 'yes',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-bank',
				'condition' => [
					'loop_icon_style' => 'font_awesome',	
					
					'display_tool_tip_icon' => 'yes',
				],	
			]
		);
		$repeater->add_control(
			'loop_icons_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [					
					'loop_icon_style' => 'icon_mind',
					'display_tool_tip_icon' => 'yes',
				],
			]
		);	
		$repeater->add_control(
			'icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'separator' => 'before',
				'condition' => [
					'loop_icon_style' => 'font_awesome_5',
					'display_tool_tip_icon' => 'yes',
				],	
			]
		);
		
		$this->add_control(
			'scroll_navigation_menu_list',
			[
				'label' => esc_html__( 'Scroll Navigation List', 'theplus' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),			
				'default' => [
					[
						'loop_image_icon' => 'icon',
						'loop_icon_style' => 'font_awesome',
						'loop_icon_fontawesome' => 'fa fa-dot-circle-o',
					],
					
				],	
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pagescroll_connection',
			[
				'label' => esc_html__( 'Page Scroll Connection', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'pagescroll_type',
			[
				'label' => esc_html__( 'Page Scroll Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fullpage',
				'options' => [
				'fullpage'  => esc_html__( 'Full Page', 'theplus' ),
					'pagepiling' => esc_html__( 'Page Piling', 'theplus' ),
					'multiscroll' => esc_html__( 'Multi Scroll', 'theplus' ),
				],
				'condition'    => [
					'pagescroll_connection' => 'yes',
				],
			]
		);
		$this->add_control(
			'pagescroll_connect_id',
			[
				'label' => esc_html__( 'Page Scroll Connect ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition'    => [
					'pagescroll_connection' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/* Scroll Navigation Menu List End*/
		/* Scroll Navigation Style Start*/
		/* Scroll Navigation Style start*/
		$this->start_controls_section(
            'section_navigation_styling',
            [
                'label' => esc_html__('Navigation Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );						
		$this->add_responsive_control(
			'navigation_icon_height_width',
			[
				'label' => esc_html__( 'Icon Height/Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 25,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot,{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot:hover,{{WRAPPER}} .theplus-scroll-navigation a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot:before,{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot:hover:before,{{WRAPPER}} .theplus-scroll-navigation a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner' => 'min-width: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-1','style-2','style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'navigation_icon_font_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 120,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot' => 'font-size: {{SIZE}}{{UNIT}};', 
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot svg' => 'width: {{SIZE}}{{UNIT}};height:auto', 
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot' => 'line-height: {{SIZE}}{{UNIT}};', 
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner' => 'min-width: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-5'],
				],
			]
		);
		$this->add_responsive_control(
			'navigation_icon_spacing_top_bottom__margin',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
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
					'{{WRAPPER}} .theplus-scroll-navigation.s_n_top a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_bottom a.theplus-scroll-navigation__item' => 'margin-right: {{SIZE}}{{UNIT}};margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_direction' => ['top','bottom'],
				'scroll_navigation_style!' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'navigation_icon_spacing_left_right_st24__margin',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
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
					'{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item._mPS2id-h,{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item._mPS2id-h' => 'margin-top: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'navigation_icon_spacing_other_all_margin',
			[
				'label' => esc_html__( 'Icon Spacing', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.s_n_top_left a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_top_right a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_bottom_left a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_bottom_right a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_left a.theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.s_n_right a.theplus-scroll-navigation__item' => 'margin-top: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_direction' => ['left','right','top_left','top_right','bottom_left','bottom_right'],
				'scroll_navigation_direction_st4' => ['left','right'],
				'scroll_navigation_style!' => ['style-2','style-4'],
				],
			]
		);
		$this->start_controls_tabs( 'scroll_navigation_icon_style' );
		$this->start_controls_tab(
			'scroll_navigation_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),				
			]
		);
		$this->add_control(
			'scroll_navigation_icon_color_normal',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__dot:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'scroll_navigation_icon_border_normal',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__dot:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot',
					'condition'    => [
						'scroll_navigation_style!' => ['style-5'],
					],
			]
		);
		$this->add_control(
			'navigation_icon_width',
			[
				'label' => esc_html__( 'Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-4'],
				],
			]
		);
		$this->add_control(
			'navigation_icon_height',
			[
				'label' => esc_html__( 'Icon Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-4'],
				],
			]
		);
		$this->end_controls_tab();		
		$this->start_controls_tab(
			'scroll_navigation_icon_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),				
			]
		);
		$this->add_control(
			'scroll_navigation_icon_color_hover',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-1 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__dot:hover:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot:hover i,
					{{WRAPPER}} .theplus-scroll-navigation.style-5 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot i' => 'color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot:hover svg,
					{{WRAPPER}} .theplus-scroll-navigation.style-5 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'scroll_navigation_icon_border_hover',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-1 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__dot:hover:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot:before,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-5 a.theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot',
					'condition'    => [
						'scroll_navigation_style!' => ['style-5'],
					],
			]
		);
		$this->add_control(
			'navigation_icon_width_hover',
			[
				'label' => esc_html__( 'Hover Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot:hover,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__item._mPS2id-h.highlight .theplus-scroll-navigation__dot' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-4'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_nav_icon_style_shadow',
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__dot:before',
				'condition' => [					
					'scroll_navigation_style' => 'style-2',
				],				
			]
		);
		$this->end_controls_section();
		/* Scroll Navigation Style End*/
		/* Scroll Navigation Icon Background Style start*/
		$this->start_controls_section(
            'section_navigation_background_styling',
            [
                'label' => esc_html__('Navigation Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_control(
			'scroll_nav_icon_background_style',
			[
				'label' => esc_html__( 'Navigation Background', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->start_controls_tabs( 'scroll_nav_icon_background' , [
			'condition' => [
				'scroll_nav_icon_background_style' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'scroll_nav_icon_background_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'scroll_nav_icon_background_normal',
				'label' => esc_html__( 'Icon Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot',
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scroll_nav_icon_background_hover',
			[
				'label' => esc_html__( 'hover', 'theplus' ),
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'scroll_nav_icon_background_hover',
				'label' => esc_html__( 'Icon Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight .theplus-scroll-navigation__dot',
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'navigation_icon_inner_padding',
			[
				'label' => esc_html__( 'Navigation Inner Padding', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner .theplus-scroll-navigation__item' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
						'scroll_nav_icon_background_style' => 'yes',
						'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_responsive_control(
			'navigation_icon_inner_padding_st5',
			[
				'label' => esc_html__( 'Navigation Inner Padding', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot' => 'padding: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
						'scroll_nav_icon_background_style' => 'yes',
						'scroll_navigation_style' => ['style-5'],
				],
			]
		);
		$this->add_control(
			'scroll_nav_icon_background_border_heading',
			[
				'label' => esc_html__( 'Icon Background Border', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->start_controls_tabs( 'scroll_nav_icon_background_border'  , [
			'condition' => [
				'scroll_nav_icon_background_style' => 'yes',
			],
		]);
		$this->start_controls_tab(
			'scroll_nav_icon_background_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'scroll_nav_icon_background_border__normal',
				'label' => esc_html__( 'Border', 'theplus' ),				
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner  .theplus-scroll-navigation__dot',
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'scroll_nav_icon_background_border_radious_normal',
			[
				'label' => esc_html__( 'Icon Background Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,
					{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner  .theplus-scroll-navigation__dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'scroll_nav_icon_background_border_hover',
			[
				'label' => esc_html__( 'hover', 'theplus' ),
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_nav_icon_background_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::COLOR,					
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight .theplus-scroll-navigation__dot' => 'border-color: {{VALUE}}',
				],	
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],
			]
		);
		$this->add_responsive_control(
			'scroll_nav_icon_background_border_radious_hover',
			[
				'label' => esc_html__( 'Icon Background Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner a.theplus-scroll-navigation__item:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__dot:hover,
				{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item.highlight .theplus-scroll-navigation__dot' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'scroll_nav_icon_background_style' => 'yes',
				],				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_nav_icon_background_shadow',
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation.style-1 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-2 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-3 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-4 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item,{{WRAPPER}} .theplus-scroll-navigation.style-5 .theplus-scroll-navigation__inner .theplus-scroll-navigation__item .theplus-scroll-navigation__dot',
				
			]
		);
		$this->end_controls_section();
		/* Scroll Navigation Icon Background Style end*/
		/* Scroll Navigation Tooltip Start*/
		$this->start_controls_section(
            'section_navigation_tooltip_styling',
            [
                'label' => esc_html__('Tooltip', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'navigation_tooltip_margin',
			[
				'label' => esc_html__( 'Navigation Tooltip Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner .tooltiptext' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'navigation_tooltip_padding',
			[
				'label' => esc_html__( 'Navigation Tooltip Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner .tooltiptext' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				
			]
		);
		$this->add_responsive_control(
			'scroll_navigation_tooltip_align',
			[
				'label' => esc_html__( 'Alignment', 'theplus' ),
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
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'prefix_class' => 'text-%s',
				'separator' => 'after',
			]
		);		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'navigation_tooltip_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot span.tooltiptext',
			]
		);
		$this->add_responsive_control(
			'navigation_tooltip_iconsize',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot span.tooltiptext' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot span.tooltiptext svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'navigation_tooltip_font_color_normal',
			[
				'label' => esc_html__( 'Font Color Normal', 'theplus' ),
				'type' => Controls_Manager::COLOR,					
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation__dot .tooltiptext' => 'color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation__dot .tooltiptext svg' => 'fill: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'navigation_tooltip_font_color_hover',
			[
				'label' => esc_html__( 'Font Color Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,					
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation__dot .tooltiptext:hover' => 'color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation__dot .tooltiptext:hover svg' => 'fill: {{VALUE}}',
				],				
			]
		);
		$this->add_control(
			'navigation_tooltip_background_color',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot .tooltiptext' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot span.tooltiptext:after' => 'border-right-color:{{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'navigation_tooltip_height',
			[
				'label' => esc_html__( 'Tooltip Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px'],
				'range' => [
					'px' => [
						'min' => 35,
						'max' => 200,
						'step' => 1,
					],
				],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__dot span.tooltiptext' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'scroll_nav_tooltip_arrow',
			[
				'label' => esc_html__( 'Tooltip Arrow', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',				
			]
		);		
		/*tooltip shadow start*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_nav_tooltip_shadow',
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation__dot span.tooltiptext',
				
			]
		);
		/*tooltip shadow end*/
		$this->add_responsive_control(
			'scroll_nav_tooltip_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation__dot .tooltiptext' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
				'separator' => 'after',
			]
		);
		$this->end_controls_section();
		/* Scroll Navigation Tooltip End*/
		/* Scroll Navigation Display Counter Start*/
		$this->start_controls_section(
            'section_navigation_dispaly_counter_styling',
            [
                'label' => esc_html__('Display Counter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
            ]
        );
		$this->add_responsive_control(
			'navigation_dispaly_counter_margin',
			[
				'label' => esc_html__( 'Navigation Display Counter Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_greek:after' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
				'separator' => 'after',
				
			]
		);
		$this->add_control(
			'navigation_dispaly_counter_size',
			[
				'label' => esc_html__( 'Counter Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_greek:after' => 'font-size: {{SIZE}}{{UNIT}};'
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		$this->add_control(
			'navigation_dispaly_counter_color_normal',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-2.s_n_left .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_right .theplus-scroll-navigation__dot.lower_greek:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.number_normal:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.decimal_leading_zero:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_alpha:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.upper_roman:after,
					{{WRAPPER}} .theplus-scroll-navigation.style-4.s_n_left .theplus-scroll-navigation__dot.lower_greek:after' => 'color: {{VALUE}}',
				],
				'condition'    => [
				'scroll_navigation_style' => ['style-2','style-4'],
				],
			]
		);
		$this->end_controls_section();
		/* Scroll Navigation Display Counter Start*/
		/*background option*/
		$this->start_controls_section(
            'section_bg_option_styling',
            [
                'label' => esc_html__('Whole Background Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'navigation_icon_padding',
			[
				'label' => esc_html__( 'Whole Navigation Offset', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'scroll_nav_background_padding',
			[
				'label' => esc_html__( 'Whole Navigation Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		/*background background option start*/
		$this->add_control(
			'scroll_nav_background_style',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'scroll_nav_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner',
				'condition' => [					
					'scroll_nav_background_style' => 'yes',
				],
			]
		);		
		/*background background option end*/
		/*background border option start*/
		$this->add_control(
			'scroll_nav_background_border',
			[
				'label' => esc_html__( 'Box Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'scroll_nav_background_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner',
				'condition' => [					
					'scroll_nav_background_border' => 'yes',
				],
			]
			
		);
		$this->add_responsive_control(
			'scroll_nav_background_border_radious',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'scroll_nav_background_border' => 'yes',
				],
				'separator' => 'after',
			]
		);
				
		/*background border option end*/
		/*background shadow start*/
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_nav_background_shadow',
				'selector' => '{{WRAPPER}} .theplus-scroll-navigation .theplus-scroll-navigation__inner',
				
			]
		);
		/*background shadow end*/
		
		$this->end_controls_section();
		/*background option end*/
		/*Extra Option Style*/
		$this->start_controls_section(
			'extra_option_style_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_control(
			'show_scroll_window_offset',
			[
				'label' => esc_html__( 'Show Menu Scroll Offset', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'scroll_top_offset_value',
			[
				'label' => esc_html__( 'Scroll Top Offset Value', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 'px',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 5000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'condition' => [
					'show_scroll_window_offset' => 'yes',
				],
			]
		);
		$this->add_control(
			'scroll_top_offset',
			[
				'label' => esc_html__( 'Section Top Offset', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => 'px',
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 700,
						'step' => 10,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
			]
		);
		$this->add_control(
			'disable_scroll_navigation',
			[
				'label' => esc_html__( 'Hide Scroll Navigation', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1024,
						'step' => 5,
					],
				],			
			]
		);
		$this->end_controls_section();
		/*Extra Option Style*/
		/* Scroll Navigation Style End*/
	}
	
	 protected function render() {
		$settings = $this->get_settings_for_display();			
		$scroll_navigation_style = $settings['scroll_navigation_style'];
		$scroll_navigation_direction = $settings['scroll_navigation_direction'];
		$scroll_navigation_direction_st4 = $settings['scroll_navigation_direction_st4'];
		$scroll_navigation_direction_inner = $settings['scroll_navigation_direction_inner'];
		$scroll_navigation_display_counter_style = $settings['scroll_navigation_display_counter_style'];	
		$scroll_navigation_tooltip_display_style = $settings['scroll_navigation_tooltip_display_style'];	
		$scroll_navigation_menu_list = $settings['scroll_navigation_menu_list'];
	
		$scroll_style='';
		if($scroll_navigation_style == 'style-1'){
			$scroll_style = 'style-1';
		}else if($scroll_navigation_style == 'style-2'){
			$scroll_style = 'style-2';
		}else if($scroll_navigation_style == 'style-3'){
			$scroll_style = 'style-3';
		}else if($scroll_navigation_style == 'style-4'){
			$scroll_style = 'style-4';
		}else if($scroll_navigation_style == 'style-5'){
			$scroll_style = 'style-5';
		}
		
		if($scroll_navigation_direction == 'top'){
			$direction_class = 's_n_top';
		}else if($scroll_navigation_direction == 'top_left'){
			$direction_class = 's_n_top_left';
		}else if($scroll_navigation_direction == 'top_right'){
			$direction_class = 's_n_top_right';
		}else if($scroll_navigation_direction == 'bottom'){
			$direction_class = 's_n_bottom';
		}else if($scroll_navigation_direction == 'bottom_left'){
			$direction_class = 's_n_bottom_left';
		}else if($scroll_navigation_direction == 'bottom_right'){
			$direction_class = 's_n_bottom_right';
		}else if($scroll_navigation_direction == 'left'){
			$direction_class = 's_n_left';
		}else if($scroll_navigation_direction == 'right'){
			$direction_class = 's_n_right';
		}
		
		if($scroll_navigation_direction_st4 == 'left'){
			$direction_class = 's_n_left';
		}else if($scroll_navigation_direction_st4 == 'right'){
			$direction_class = 's_n_right';
		}
		
		$position_class='';
		if($scroll_navigation_direction_inner == 'p_left'){
			$position_class = 'po_left';
		}else if($scroll_navigation_direction_inner == 'p_right'){
			$position_class = 'po_right';
		}else if($scroll_navigation_direction_inner == 'p_center'){
			$position_class = 'po_center';
		}
		
		$display_counter_class='';
		if($scroll_navigation_display_counter_style == 'number-normal'){
			$display_counter_class = 'number_normal';
		}else if($scroll_navigation_display_counter_style == 'decimal-leading-zero'){
			$display_counter_class = 'decimal_leading_zero';
		}else if($scroll_navigation_display_counter_style == 'upper-alpha'){
			$display_counter_class = 'upper_alpha';
		}else if($scroll_navigation_display_counter_style == 'lower-alpha'){
			$display_counter_class = 'lower_alpha';
		}else if($scroll_navigation_display_counter_style == 'lower-roman'){
			$display_counter_class = 'lower_roman';
		}else if($scroll_navigation_display_counter_style == 'upper-roman'){
			$display_counter_class = 'upper_roman';
		}else if($scroll_navigation_display_counter_style == 'lower-greek'){
			$display_counter_class = 'lower_greek';
		}
		
		$display_tooltip_style_class='';
		if($scroll_navigation_tooltip_display_style == 'on-hover'){
			$display_tooltip_style_class = 'on_hover';
		}else if($scroll_navigation_tooltip_display_style == 'on-active-section'){
			$display_tooltip_style_class = 'on_active_section';
		}else if($scroll_navigation_tooltip_display_style == 'on-default'){
			$display_tooltip_style_class = 'on_default';
		}
		
		$tooltip_arrow='';
		if($settings['scroll_nav_tooltip_arrow'] == 'yes'){
			$tooltip_arrow = 'sn_t_a_e';
		}else if($settings['scroll_nav_tooltip_arrow'] == 'no'){
			$tooltip_arrow = 'sn_t_a_d';
		}
		$show_scroll_window_offset = ($settings['show_scroll_window_offset']=='yes') ? 'scroll-view' : '';
		$scroll_top_offset_value = (!empty($settings['scroll_top_offset_value']["size"]) && $settings['show_scroll_window_offset']=='yes') ? 'data-scroll-view="'.$settings['scroll_top_offset_value']["size"].'"' : 'data-scroll-view="100"';
		
		$scroll_top_offset = (!empty($settings['scroll_top_offset']["size"])) ? 'data-scroll-top-offset="'.$settings['scroll_top_offset']["size"].'"' : 0;
		
		if(!empty($settings['pagescroll_connection']) && $settings['pagescroll_connection']=='yes'){
			$full_page_connect = 'data-pagescroll="yes"';
			$id = (!empty($settings["pagescroll_connect_id"])) ? 'id="tp-sc-'.esc_attr($settings["pagescroll_connect_id"]).'"' : '';
			$full_page_connect .=(!empty($settings["pagescroll_type"])) ? ' data-pagescroll-type="'.esc_attr($settings["pagescroll_type"]).'"' : '';
		}else{
			$full_page_connect = 'data-pagescroll="no"';
			$id ='';
		}
		
		if ( $settings['scroll_navigation_menu_list'] ) {
			$uid=uniqid('scroll');
			$scroll_navigation = '<div '.$id.' class="theplus-scroll-navigation '.esc_attr($uid).' '.esc_attr($scroll_style).' '.esc_attr($direction_class).' '.esc_attr($position_class).' '.esc_attr($show_scroll_window_offset).'" '.$scroll_top_offset_value.' '.$scroll_top_offset.' data-uid="'.esc_attr($uid).'" '.$full_page_connect.'>';			
			$scroll_navigation .='<div class="theplus-scroll-navigation__inner">';		
			
			foreach (  $settings['scroll_navigation_menu_list'] as $item ) {
				$scroll_navigation .= '<a href="#'.esc_attr($item['scroll_navigation_section_id']).'" class="theplus-scroll-navigation__item _mPS2id-h" >';
				$tooltip_menu_title=$tooltip_title=$tooltip_icon=$icons='';
				
						$s_icon_img =$st_5_icon=$icons='';
						if($item["loop_icon_style"]=='font_awesome'){
							$icons = $item["loop_icon_fontawesome"];
						}else if($item["loop_icon_style"]=='icon_mind'){
							$icons = $item["loop_icons_mind"];
						}else if($item['loop_icon_style']=='font_awesome_5'){
							ob_start();
							\Elementor\Icons_Manager::render_icon( $item['icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
							$icons = ob_get_contents();
							ob_end_clean();		
						}
						
						if(!empty($icons)){
							if($item['loop_icon_style']=='font_awesome_5' && !empty($item['loop_icon_style'])){
								$s_icon_img .='<span class="scroll-tooltip-icon">'.$icons.'</span>';
							}else{
								$s_icon_img = '<i class=" '.esc_attr($icons).' scroll-tooltip-icon "></i>';
							}
							
						}
				
					if($scroll_navigation_style=='style-5'){
						if(!empty($s_icon_img)){
							$st_5_icon = $s_icon_img;
						}else{
							$st_5_icon = '<i class=" fa fa-home scroll-tooltip-icon "></i>';
						}
						$s_icon_img ='';						

					}
					
					if(!empty($item["tooltip_menu_title"] || $icons)){
						$tooltip_title = '<span class="tooltiptext '.esc_attr($direction_class).' '.esc_attr($tooltip_arrow).' '.esc_attr($settings['scroll_navigation_tooltip_align']).' '.esc_attr($display_tooltip_style_class).'">'.$s_icon_img.' '.esc_html($item["tooltip_menu_title"]).'</span>';
					}
					
				$scroll_navigation .= '<div class="theplus-scroll-navigation__dot '.esc_attr($display_counter_class).'">'.$st_5_icon.$tooltip_title.'</div>';
				$scroll_navigation .= '</a>';
			}			
			$scroll_navigation .= '</div>';
			$scroll_navigation .= '</div>';

			$css_rule='';
			if(!empty($settings['disable_scroll_navigation']['size'])){
				$disable_scroll_navigation=($settings['disable_scroll_navigation']['size']).$settings['disable_scroll_navigation']['unit'];				
				$css_rule .='@media (max-width:'.esc_attr($disable_scroll_navigation).'){.theplus-scroll-navigation.'.esc_attr($uid).'{display:none;}}';
				$scroll_navigation .='<style>'.esc_attr($css_rule).'</style>';
			}
			

			echo $scroll_navigation;
		} 
	}
	
    protected function content_template() {
		
    }
}