<?php 
/*
Widget Name: Circle Menu
Description: Circle Menu
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
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Circle_Menu extends Widget_Base {
		
	public function get_name() {
		return 'tp-circle-menu';
	}

    public function get_title() {
        return esc_html__('Circle Menu', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-circle-o-notch theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }

	public function get_keywords() {
		return ['circle', 'bubble menu', 'menu', 'list', 'tp', 'theplus'];
	}

    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Circle Menu', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'icon_layout_open',
			[
				'label' => esc_html__( 'Icon Layout', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'circle',
				'options' => [
					'circle' => esc_html__( 'Circle', 'theplus' ),
					'straight'  => esc_html__( 'Straight', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'icon_layout_straight_style',
			[
				'label' => esc_html__( 'Menu Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1' => esc_html__( 'Style 1', 'theplus' ),
					'style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
				],
			]
		);
		
		$this->add_control(
			'icon_direction',
			[
				'label' => esc_html__( 'Icon Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom-right',
				'options' => [
					'top'  => esc_html__( 'Top', 'theplus' ),					
					'right'  => esc_html__( 'Right', 'theplus' ),					
					'bottom'  => esc_html__( 'Bottom', 'theplus' ),					
					'left'  => esc_html__( 'Left', 'theplus' ),					
					'top-right'  => esc_html__( 'Top Right', 'theplus' ),					
					'top-left'  => esc_html__( 'Top Left', 'theplus' ),					
					'bottom-right'  => esc_html__( 'Bottom Right', 'theplus' ),					
					'bottom-left'  => esc_html__( 'Bottom Left', 'theplus' ),					
					'top-half'  => esc_html__( 'Top Half', 'theplus' ),					
					'right-half'  => esc_html__( 'Right Half', 'theplus' ),					
					'bottom-half'  => esc_html__( 'Bottom Half', 'theplus' ),					
					'left-half'  => esc_html__( 'Left Half', 'theplus' ),					
					'full'  => esc_html__( 'Full', 'theplus' ),		
					'none' => esc_html__( 'None', 'theplus' ),
				],
				'condition'    => [
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		$this->add_control(
			'layout_straight_menu_direction',
			[
				'label' => esc_html__( 'Menu Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
				'top'  => esc_html__( 'Top', 'theplus' ),					
					'right'  => esc_html__( 'Right', 'theplus' ),					
					'bottom'  => esc_html__( 'Bottom', 'theplus' ),					
					'left'  => esc_html__( 'Left', 'theplus' ),
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		
		$repeater->add_control(
			'tooltip_menu_title',
			[
				'label' => esc_html__( 'Tooltip Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'loop_image_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image using this option.','theplus'),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),					
				],
			]
		);
		
		$repeater->add_control(
			'loop_select_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_select_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'loop_image_icon' => 'image',
				],
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
					'loop_image_icon' => 'icon',
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
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome',
				],	
			]
		);
		$repeater->add_control(
			'loop_icon_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome_5',
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
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'icon_mind',
				],
			]
		);
		$repeater->add_control(
			'loop_icon_link_type',
			[
				'label' => esc_html__( 'Select Link Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,				
				'default' => 'url',
				'options' => [
					'url'  => esc_html__( 'URL', 'theplus' ),
					'email' => esc_html__( 'Email', 'theplus' ),
					'phone' => esc_html__( 'Phone', 'theplus' ),
					'nolink' => esc_html__( 'No Link', 'theplus' ),
				],
				'separator' => 'before',
			]
		);
		$repeater->add_control(
			'icons_url',
			[
				'label' => esc_html__( 'Url', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'show_external' => true,
				'default' => [
					'url' => '#',
					'is_external' => false,
					'nofollow' => false,
					],
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_icon_link_type' => 'url',
				],
			]
		);
		$repeater->add_control(
			'email',
			[
				'label' => esc_html__( 'Email', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Email', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_icon_link_type' => 'email',
				],
			]
		);
		$repeater->add_control(
			'phone',
			[
				'label' => esc_html__( 'Phone', 'theplus' ),
				'type' => \Elementor\Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Enter Phone', 'theplus' ),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_icon_link_type' => 'phone',
				],
			]
		);
		$repeater->start_controls_tabs( 'tabs_title_style' );
		$repeater->start_controls_tab(
			'tab_title_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$repeater->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}} .menu-tooltip-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon svg,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}} .menu-tooltip-title svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'render_type' => 'ui',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}} .menu-tooltip-title',
			]
		);
		$repeater->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}} .menu-tooltip-title' => 'border-color: {{VALUE}}',
				],
			]
		);
		$repeater->end_controls_tab();
		
		$repeater->start_controls_tab(
			'tab_title_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$repeater->add_control(
			'icon_hover_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu_icon,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu-tooltip-title' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu_icon svg,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu-tooltip-title svg' => 'fill: {{VALUE}}',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_hover_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'render_type' => 'ui',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon:hover,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu-tooltip-title',
			]
		);
		$repeater->add_control(
			'icon_border_hover_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu_icon,{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu.menu-style-2 .plus-circle-menu-list{{CURRENT_ITEM}}:hover .menu-tooltip-title' => 'border-color: {{VALUE}}',
				],
			]
		);
		$repeater->end_controls_tab();
		$repeater->end_controls_tabs();
		$repeater->add_control(
			'tooltip_default_hover',
			[
				'label' => esc_html__( 'Tooltip Visibility', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Default', 'theplus' ),
				'label_off' => esc_html__( 'Hover', 'theplus' ),				
				'default' => 'no',
			]
		);
		$repeater->add_control(
            'tooltip_text_rotate',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Tooltip Text Rotate', 'theplus'),
				'size_units' => ['deg'],
				'range' => [
					'deg' => [
						'min' => 0,
						'max' => 360,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'deg',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon .menu-tooltip-title' => 'transform: translateY(-50%) rotate({{SIZE}}{{UNIT}})',
				],
            ]
        );
		$repeater->add_control(
            'tooltip_text_top',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Tooltip Text Top', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon .menu-tooltip-title' => 'top:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$repeater->add_control(
            'tooltip_text_left',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Tooltip Text Left', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list{{CURRENT_ITEM}} .menu_icon .menu-tooltip-title' => 'left:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$repeater->add_control(
			'tooltip_text_arrow',
			[
				'label' => esc_html__( 'Arrow Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'arrow-left',
				'options' => [
					'arrow-left'  => esc_html__( 'Left', 'theplus' ),
					'arrow-right' => esc_html__( 'Right', 'theplus' ),
					'arrow-top' => esc_html__( 'Top', 'theplus' ),
					'arrow-bottom' => esc_html__( 'Bottom', 'theplus' ),
					'arrow-none' => esc_html__( 'None', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'circle_menu_list',
			[
				'label' => esc_html__( 'Menu List', 'theplus' ),
				'type' => \Elementor\Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),			
				'default' => [
					[
						'tooltip_menu_title' => esc_html__( 'Facebook', 'theplus' ),
						'loop_image_icon' => 'icon',
						'loop_icon_style' => 'font_awesome',
						'loop_icon_fontawesome' => 'fa fa-facebook',
					],
					[
						'tooltip_menu_title' => esc_html__( 'Twitter', 'theplus' ),
						'loop_image_icon' => 'icon',
						'loop_icon_style' => 'font_awesome',
						'loop_icon_fontawesome' => 'fa fa-twitter',
					],
					[
						'tooltip_menu_title' => esc_html__( 'Instagram', 'theplus' ),
						'loop_image_icon' => 'icon',
						'loop_icon_style' => 'font_awesome',
						'loop_icon_fontawesome' => 'fa fa-instagram',
					],
					[
						'tooltip_menu_title' => esc_html__( 'Linkedin', 'theplus' ),
						'loop_image_icon' => 'icon',
						'loop_icon_style' => 'font_awesome',
						'loop_icon_fontawesome' => 'fa fa-linkedin',
					],
				],
				'title_field' => '{{{ loop_image_icon }}}',
			]
		);
		
		$this->end_controls_section();
		/* Circle Menu List*/
		/* Toggle Icon */
		$this->start_controls_section(
			'icon_toggle',
			[
				'label' => esc_html__( 'Toggle Icon', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'loop_image_main_icon',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image using this option.','theplus'),
				'default' => 'icon',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),					
				],
			]
		);
		$this->add_control(
            'loop_max_main_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Max Width Svg', 'theplus'),
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition' => [
					'loop_image_main_icon' => 'svg',
					'loop_svg_main_icon' => ['img'],
				],
            ]
        );
		$this->add_control(
			'loop_select_main_image',
			[
				'label' => esc_html__( 'Use Image As icon', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_image_main_icon' => 'image',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'loop_select_main_image_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'loop_image_main_icon' => 'image',
				],
			]
		);
		$this->add_control(
			'loop_icon_main_style',
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
					'loop_image_main_icon' => 'icon',
				],
			]
		);
		$this->add_control(
			'loop_icon_main_fontawesome',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICON,
				'default' => 'fa fa-home',
				'condition' => [
					'loop_image_main_icon' => 'icon',
					'loop_icon_main_style' => 'font_awesome',
				],	
			]
		);
		$this->add_control(
			'loop_icon_main_fontawesome_5',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-plus',
					'library' => 'solid',
				],
				'condition' => [
					'loop_image_main_icon' => 'icon',
					'loop_icon_main_style' => 'font_awesome_5',
				],
			]
		);
		$this->add_control(
			'loop_icons_main_mind',
			[
				'label' => esc_html__( 'Icon Library', 'theplus' ),
				'type' => Controls_Manager::SELECT2,
				'default' => '',
				'label_block' => true,
				'options' => theplus_icons_mind(),
				'condition' => [
					'loop_image_main_icon' => 'icon',
					'loop_icon_main_style' => 'icon_mind',
				],
			]
		);
		
		$this->add_control(
			'toggle_open_icon_style',
			[
				'label' => esc_html__( 'Menu Open Icon Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,				
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'style 3', 'theplus' ),					
				],
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/* Toggle Icon */
		
		/* Icon Position*/
		$this->start_controls_section(
			'icon_position_section',
			[
				'label' => esc_html__( 'Icon Position', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'main_icon_position',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'absolute',
				'options' => [
					'absolute'  => esc_html__( 'Absolute', 'theplus' ),					
					'fixed'  => esc_html__( 'Fixed', 'theplus' ),
				],
			]
		);		
		$this->start_controls_tabs( 'circle_icon_position' );
		/*desktop  start*/
		$this->start_controls_tab( 'normal',
			[
				'label' => esc_html__( 'Desktop', 'theplus' ),
			]
		);		
		$this->add_control(
			'd_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),				
			]
		);

		$this->add_control(
			'd_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_left_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
			]
		);
		$this->add_control(
			'd_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 20,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_right_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),				
			]
		);
		$this->add_control(
			'd_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'd_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
			]
		);
		$this->add_control(
			'd_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					'd_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		/*desktop end*/
		/*tablet start*/
		$this->start_controls_tab( 'tablet',
			[
				'label' => esc_html__( 'Tablet', 'theplus' ),
			]
		);
		$this->add_control(
			't_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			't_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_left_auto' => [ 'yes' ],
				],
			]
		);
		
		$this->add_control(
			't_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_right_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					't_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			't_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],					
				],
				'separator' => 'after',
				'condition'    => [
					't_responsive' => [ 'yes' ],
					't_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		/*tablet end*/
		/*mobile start*/
		$this->start_controls_tab( 'mobile',
			[
				'label' => esc_html__( 'Mobile', 'theplus' ),
			]
		);
		$this->add_control(
			'm_responsive', [
				'label'   => esc_html__( 'Responsive Values', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			'm_left_auto', [
				'label'   => esc_html__( 'Left (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_xposition', [
				'label' => esc_html__( 'Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_left_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_right_auto',[
				'label'   => esc_html__( 'Right (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_rightposition',[
				'label' => esc_html__( 'Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_right_auto' => [ 'yes' ],
				],
			]
		);
		
		$this->add_control(
			'm_top_auto', [
				'label'   => esc_html__( 'Top (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_yposition', [
				'label' => esc_html__( 'Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_top_auto' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_bottom_auto', [
				'label'   => esc_html__( 'Bottom (Auto / %)', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( '%', 'theplus' ),
				'label_off' => esc_html__( 'Auto', 'theplus' ),
				'condition'    => [
					'm_responsive' => [ 'yes' ],
				],
			]
		);
		$this->add_control(
			'm_pos_bottomposition', [
				'label' => esc_html__( 'Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => '%',
					'size' => '',
				],
				'range' => [
					'%' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'condition'    => [
					'm_responsive' => [ 'yes' ],
					'm_bottom_auto' => [ 'yes' ],
				],
			]
		);
		$this->end_controls_tab();
		/*mobile end*/
		$this->end_controls_tabs();
		$this->end_controls_section();
		/* Icon Position*/
		
		/* Extra Options*/
		$this->start_controls_section(
			'extra_option_section',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		
		$this->add_control(
			'angle_start',
			[
				'label' => esc_html__( 'Angle Start', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'condition'    => [
					'icon_direction' => [ 'none' ],
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		$this->add_control(
			'angle_end',
			[
				'label' => esc_html__( 'Angle End', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 90,
				],
				'condition'    => [
					'icon_direction' => [ 'none' ],
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		$this->add_responsive_control(
			'circle_radius',
			[
				'label' => esc_html__( 'Circle Radius', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 360,
						'step' => 5,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		
		$this->add_control(
			'icon_delay',
			[
				'label' => esc_html__( 'Icon Delay', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 7000,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1000,
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		$this->add_control(
			'icon_speed',
			[
				'label' => esc_html__( 'Menu Open Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 10000,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 500,
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		$this->add_control(
			'icon_step_in',
			[
				'label' => esc_html__( 'Icon Step In', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -20,
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		$this->add_control(
			'icon_step_out',
			[
				'label' => esc_html__( 'Icon Step Out', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		
		$this->add_control(
			'layout_straight_menu_gap', [
				'label' => esc_html__( 'Menu Between Gap', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
				],
			]
		);
		$this->add_control(
			'layout_straight_menu_transition_duration',
			[
				'label' => esc_html__( 'Menu Open Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'ms' ],
				'range' => [
					'ms' => [
						'min' => 0,
						'max' => 10000,
						'step' => 50,
					],
				],
				'default' => [
					'unit' => 'ms',
					'size' => 1000,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu .plus-circle-menu-list:not(.plus-circle-main-menu-list)' => 'transition-duration:{{SIZE}}{{UNIT}}',
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
				],
			]
		);
		$this->add_control(
			'icon_transition',
			[
				'label' => esc_html__( 'Icon Transition', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'ease',
				'options' => [
					'ease'  => esc_html__( 'Ease', 'theplus' ),					
					'linear'  => esc_html__( 'Linear', 'theplus' ),											
					'ease-in'  => esc_html__( 'Ease In', 'theplus' ),											
					'ease-out'  => esc_html__( 'Ease Out', 'theplus' ),											
					'ease-in-out'  => esc_html__( 'Ease In Out', 'theplus' ),											
					'cubic-bezier(n,n,n,n)'  => esc_html__( 'Cubic Bezier', 'theplus' ),
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper.layout-straight .plus-circle-menu .plus-circle-menu-list:not(.plus-circle-main-menu-list)' => 'transition-timing-function:{{VALUE}}',
				],
			]
		);
		$this->add_control(
			'icon_trigger',
			[
				'label' => esc_html__( 'Icon Trigger', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'hover',
				'options' => [
					'hover'  => esc_html__( 'Hover', 'theplus' ),					
					'click'  => esc_html__( 'Click', 'theplus' ),											
				],
				'condition'    => [					
					'icon_layout_open' => [ 'circle' ],
				],
			]
		);
		$this->end_controls_section();
		/* extra options*/
		/*Style tag*/
		
		/* Icon Style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Icon Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'repeater_icon_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
					
				],
				'default' => [
					'unit' => 'px',
					'size' => 20,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon i' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'repeater_circle_width',
			[
				'label' => esc_html__( 'Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:not(.plus-circle-main-menu-list)' => 'width: calc({{SIZE}}{{UNIT}} - 5px ) !important;height: calc({{SIZE}}{{UNIT}} - 5px) !important;line-height: calc({{SIZE}}{{UNIT}} - 5px) !important;',
				],
			]
		);

		$this->add_responsive_control(
			'repeater_icon_image_width',
			[
				'label' => esc_html__( 'Image Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 90,
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon img' => 'width: {{SIZE}}{{UNIT}};, height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		
		$this->add_control(
			'repeater_icon_border',
			[
				'label' => esc_html__( 'Icon Border', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
			]
		);
		
		$this->add_control(
			'icon_border_radius_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'condition' => [
					'repeater_icon_border' => 'yes',					
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon' => 'border-style: {{VALUE}};',
				],
			]
		);
		
		$this->add_responsive_control(
			'repeater_icon_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],				
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [					
					'repeater_icon_border' => 'yes',					
				],
			]
		);
		
		$this->start_controls_tabs( 'tabs_icon_border_style' );
		$this->start_controls_tab(
			'tab_icon_border_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'repeater_icon_border' => 'yes',					
				],
			]
		);
		$this->add_control(
			'icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'repeater_icon_border' => 'yes',					
				],
			]
		);
		$this->add_responsive_control(
			'icon_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu_icon',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_icon_border_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [					
					'repeater_icon_border' => 'yes',					
				],
			]
		);
		$this->add_control(
			'icon_border_hover_color',
			[
				'label' => esc_html__( 'Border Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#252525',
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu_icon' => 'border-color: {{VALUE}};',
				],
				'condition' => [					
					'repeater_icon_border' => 'yes',					
				],
			]
		);
		$this->add_responsive_control(
			'icon_border_hover_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu_icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'icon_box_shadow_hover',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu_icon',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Toggle Icon Style*/
		$this->start_controls_section(
            'section_toggle_styling',
            [
                'label' => esc_html__('Toggle Icon Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'toggle_size',
			[
				'label' => esc_html__( 'Icon Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 15,
				],
				
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon img' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon svg' => 'width:{{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
			'toggle_icon_width',
			[
				'label' => esc_html__( 'Toggle Icon Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'toggle_image_width',
			[
				'label' => esc_html__( 'Image Width', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'circle_menu_border_option', [
				'label'   => esc_html__( 'Circle Menu Border', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
			]
		);
		$this->add_control(
			'toggle_icon_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'condition' => [
					'repeater_icon_border' => 'yes',					
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon' => 'border-style: {{VALUE}};',
				],
				'condition'    => [
					'circle_menu_border_option' => [ 'yes' ],
				],
			]
		);
		$this->add_responsive_control(
			'toggle_icon_border_width',
			[
				'label' => esc_html__( 'Icon Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [
					'circle_menu_border_option' => [ 'yes' ],
				],
			]
		);
		$this->start_controls_tabs( 'toggle_icon_main_style' );
		$this->start_controls_tab(
			'toggle_icon_main_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_color',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list a.main_menu_icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list a.main_menu_icon svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper .plus-circle-main-menu-list.style-3 a.main_menu_icon .close-toggle-icon,{{WRAPPER}} .plus-circle-menu-wrapper .plus-circle-main-menu-list.style-3 a.main_menu_icon .close-toggle-icon:before' => 'background-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list a.main_menu_icon',
			]
		);
		
		$this->add_control(
			'toggle_icon_border_color',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list a.main_menu_icon' => 'border-color: {{VALUE}}',
				],
				'condition'    => [
					'circle_menu_border_option' => [ 'yes' ],
				],
			]
		);
		$this->add_responsive_control(
			'toggle_icon_border_radius_normal',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_icon_box_shadow_normal',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .main_menu_icon',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'toggle_icon_main_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'icon_hover_color',
			[
				'label' => esc_html__( 'Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover a.main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon' => 'color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover a.main_menu_icon svg,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon svg' => 'fill: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper .plus-circle-main-menu-list.style-3:hover a.main_menu_icon .close-toggle-icon,{{WRAPPER}} .plus-circle-menu-wrapper .plus-circle-main-menu-list.style-3:hover a.main_menu_icon .close-toggle-icon:before' => 'background-color: {{VALUE}}',
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'icon_hover_background',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover a.main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon',
			]
		);
		$this->add_control(
			'icon_hover_border',
			[
				'label' => esc_html__( 'Icon Hover Border', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover a.main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon' => 'border-color: {{VALUE}}',
				],
				'condition'    => [
					'circle_menu_border_option' => [ 'yes' ],
				],
			]
		);
		$this->add_responsive_control(
			'toggle_icon_border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .main_menu_icon img,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'toggle_icon_box_shadow_hover',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .main_menu_icon,{{WRAPPER}} .plus-circle-menu-inner-wrapper .circleMenu-open .plus-circle-menu-list a.main_menu_icon',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Toggle Icon Style*/
		$this->start_controls_section(
			'icon_tooltip_text_style',
			[
				'label' => esc_html__( 'Tooltip Text', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tooltip_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
					'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT
				],
				'selector' => '{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list .menu-tooltip-title',
			]
		);
		$this->add_control(
			'straight_text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu-tooltip-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'straight_text_border_option', [
				'label'   => esc_html__( 'Circle Menu Border', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Yes', 'theplus' ),
				'label_off' => esc_html__( 'No', 'theplus' ),
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'straight_text_border_style',
			[
				'label' => esc_html__( 'Border Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => theplus_get_border_style(),
				'condition' => [
					'repeater_icon_border' => 'yes',					
				],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu-tooltip-title' => 'border-style: {{VALUE}};',
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
					'straight_text_border_option' => [ 'yes' ],
				],
			]
		);
		$this->add_responsive_control(
			'straight_text_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu-tooltip-title' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'    => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
					'straight_text_border_option' => [ 'yes' ],
				],
			]
		);
		$this->start_controls_tabs( 'tabs_straight_text_style' );
		$this->start_controls_tab(
			'tab_straight_text_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'tooltip_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list .menu-tooltip-title' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'straight_text_border',
			[
				'label' => esc_html__( 'Border Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list .menu-tooltip-title' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'tooltip_text_normal_bgcolor',
			[
				'label' => esc_html__( 'Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list .menu-tooltip-title' => 'background-color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list.arrow-bottom .menu-tooltip-title:before' => 'border-top-color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list.arrow-top .menu-tooltip-title:before' => 'border-bottom-color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list.arrow-left .menu-tooltip-title:before' => 'border-right-color: {{VALUE}}',
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list.arrow-right .menu-tooltip-title:before' => 'border-left-color: {{VALUE}}',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tooltip_text_shadow',
				'selector' => '{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list .menu-tooltip-title',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'straight_text_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'straight_text_hover_color',
			[
				'label' => esc_html__( 'Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list:hover .menu-tooltip-title' => 'color: {{VALUE}}',
				],
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'straight_text_hover_border',
			[
				'label' => esc_html__( 'Hover Border', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu-tooltip-title' => 'border-color: {{VALUE}}',
				],
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_responsive_control(
			'straight_text_border_radius_hover',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu-tooltip-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_control(
			'straight_text_hover_bgcolor',
			[
				'label' => esc_html__( 'Background Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#000',
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-wrapper li.plus-circle-menu-list:hover .menu-tooltip-title' => 'background-color: {{VALUE}}',					
				],
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'straight_text_shadow_hover',
				'selector' => '{{WRAPPER}} .plus-circle-menu-inner-wrapper .plus-circle-menu-list:hover .menu-tooltip-title',
				'condition' => [
					'icon_layout_open' => [ 'straight' ],
					'icon_layout_straight_style' => 'style-2',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'tooltip_display_desktop',
			[
				'label' => esc_html__( 'Visibility Desktop', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tooltip_display_tablet',
			[
				'label' => esc_html__( 'Visibility Tablet', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->add_control(
			'tooltip_display_mobile',
			[
				'label' => esc_html__( 'Visibility Mobile', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Hide', 'theplus' ),
				'label_off' => esc_html__( 'Show', 'theplus' ),				
				'default' => 'no',
			]
		);
		$this->end_controls_section();
		/*Tooltip Text*/
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
				'type' => \Elementor\Controls_Manager::SWITCHER,
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
			'show_bg_overlay_color',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'icon_layout_open','operator' => '==','value'    => 'straight',
								],	
								[
									'name'     => 'icon_layout_open','operator' => '==','value'    => 'circle',
									'name'     => 'icon_trigger','operator' => '==','value'    => 'click',
								],	
							],
						],
					],
				],
			]
		);
		$this->add_control(
			'show_bg_overlay_color_value',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .plus-circle-menu-inner-wrapper .show-bg-overlay.activebg' => 'background: {{VALUE}}',					
				],
				'conditions'   => [
					'terms' => [
						[
							'relation' => 'or',
							'terms'    => [
								[
									'name'     => 'icon_layout_open','operator' => '==','value'    => 'straight',
								],	
								[
									'name'     => 'icon_layout_open','operator' => '==','value'    => 'circle',
									'name'     => 'icon_trigger','operator' => '==','value'    => 'click',
								],	
							],
						],
					],
				],
				'condition' => [
					'show_bg_overlay_color' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Extra Option Style*/

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation.php';
	}
	
	 protected function render() {
		$settings = $this->get_settings_for_display();		

		$icon_direction = $settings['icon_direction'];
		$icon_layout_open = $settings['icon_layout_open'];
		$show_scroll_window_offset = ($settings['show_scroll_window_offset']=='yes') ? 'scroll-view' : '';
		$scroll_top_offset_value = ($settings['show_scroll_window_offset']=='yes') ? 'data-scroll-view="'.esc_attr($settings['scroll_top_offset_value']["size"]).'"' : '';

		$show_bg_overlay_color = ($settings['show_bg_overlay_color']=='yes') ? 'show-bg-overlay' : '';
		$show_bg_overlay_color_value = ($settings['show_bg_overlay_color']=='yes') ? 'data-overlay-color="'.esc_attr($settings['show_bg_overlay_color_value']).'"' : '';

		$icon_layout_straight_style = ($settings['icon_layout_open']=='straight') ? 'menu-'.esc_attr($settings['icon_layout_straight_style']) : '';
		$layout_straight_menu_direction = ($settings['icon_layout_open']=='straight') ? 'menu-direction-'.esc_attr($settings['layout_straight_menu_direction']) : '';

		$tooltip_display_desktop = ($settings["tooltip_display_desktop"]=='yes') ? 'tooltip_desktop_hide' : '';
		$tooltip_display_tablet = ($settings["tooltip_display_tablet"]=='yes') ? 'tooltip_tablet_hide' : '';
		$tooltip_display_mobile = ($settings["tooltip_display_mobile"]=='yes') ? 'tooltip_mobile_hide' : '';

		if($icon_layout_open == 'circle'){
			$circle_radius = !empty($settings['circle_radius']['size']) ? $settings['circle_radius']['size'] : 150;
			$circle_radius_tablet = !empty($settings['circle_radius_tablet']['size']) ? $settings['circle_radius_tablet']['size'] : 150;
			$circle_radius_mobile = !empty($settings['circle_radius_mobile']['size']) ? $settings['circle_radius_mobile']['size'] : 150;
			
			$icon_delay = $settings['icon_delay']['size'];
			$icon_speed = $settings['icon_speed']['size'];
			$icon_step_in = $settings['icon_step_in']['size'];
			$icon_step_out = $settings['icon_step_out']['size'];
		}
		$icon_transition = $settings['icon_transition'];
		$icon_trigger = $settings['icon_trigger'];
		$loop_image_main_icon = $settings['loop_image_main_icon'];
		$loop_icon_main_style = $settings['loop_icon_main_style'];
		$loop_icon_main_fontawesome = $settings['loop_icon_main_fontawesome'];		
		$loop_icons_main_mind = $settings['loop_icons_main_mind'];
		$main_icon_position = $settings['main_icon_position'];
		
		$toggle_open_icon_style = $settings['toggle_open_icon_style'];
		
		if($icon_layout_open=='circle'){
			if($icon_direction =='none'){
				$angle_start = $settings['angle_start']['size'];
				$angle_end = $settings['angle_end']['size'];
			}else{
				$angle_start = 0;
				$angle_end = 0;
			}
		}
		if($main_icon_position == 'absolute'){
			$position_class = 'circle_menu_position_abs';
		}else if($main_icon_position == 'fixed'){
			$position_class = 'circle_menu_position_fix';
		}
		
		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
		
		$main_toggle_click='';
		$uid=uniqid("circle_menu");
		$circle_menu ='<div class="plus-circle-menu-wrapper '.esc_attr($uid).' layout-'.esc_attr($icon_layout_open).' '.esc_attr($show_scroll_window_offset).' '.esc_attr($animated_class).' " '.$animation_attr.' data-uid='.esc_attr($uid).' '.$scroll_top_offset_value.' '.$show_bg_overlay_color_value.'>';
		$circle_menu .='<div class="plus-circle-menu-inner-wrapper ">';
			if(!empty($settings['show_bg_overlay_color']) && $settings['show_bg_overlay_color']=='yes'){
				$circle_menu .='<div id="show-bg-overlay" class="show-bg-overlay"></div>';
			}
			$circle_menu .='<ul class="plus-circle-menu circleMenu-closed '.$position_class.' '.esc_attr($layout_straight_menu_direction).' '.esc_attr($icon_layout_straight_style).' '.esc_attr($tooltip_display_desktop).' '.esc_attr($tooltip_display_tablet).' '.esc_attr($tooltip_display_mobile).'">';
			
				
				$main_toggle_click .= '<li class="plus-circle-main-menu-list plus-circle-menu-list '.esc_attr($toggle_open_icon_style).'">';
					if(!empty($loop_icon_main_style) && $loop_image_main_icon == 'icon' && $loop_icon_main_style=='font_awesome'){
						$icons_main='<i class="fa '.$loop_icon_main_fontawesome.'  toggle-icon-wrap" ></i>';
					}else if(!empty($loop_icon_main_style) && $loop_image_main_icon == 'icon' && $loop_icon_main_style=='icon_mind'){
						$icons_main='<i class="'.$loop_icons_main_mind.' toggle-icon-wrap" ></i>';
					}else if(!empty($loop_icon_main_style) && $loop_image_main_icon == 'icon' && $loop_icon_main_style=='font_awesome_5'){
						$loop_icon_main_fontawesome_5 = $settings['loop_icon_main_fontawesome_5'];
						ob_start();
						\Elementor\Icons_Manager::render_icon( $loop_icon_main_fontawesome_5, [ 'aria-hidden' => 'true' ]);
						$icons_main = ob_get_contents();
						ob_end_clean();	
					}else if(!empty($settings["loop_select_main_image"]["url"]) && $loop_image_main_icon == 'image'){
							$loop_select_main_image=$settings["loop_select_main_image"]["id"];				
							$loop_select_main_image_Src = tp_get_image_rander( $loop_select_main_image,$settings['loop_select_main_image_thumbnail_size'], [ 'class' => 'toggle-icon-wrap' ] );
						$icons_main= $loop_select_main_image_Src;
					}else{
						$icons_main='';
					}
					$close_toggle='';
					if($toggle_open_icon_style=='style-3'){
						$close_toggle='<span class="close-toggle-icon"></span>';
					}
					$icon_bg2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['icon_background_image'],$settings['icon_hover_background_image']) : '';
					$main_toggle_click .= '<a href="#" class="main_menu_icon '.$icon_bg2.'" style="cursor:pointer;">'.$icons_main.$close_toggle.'</a>';
				$main_toggle_click .= '</li>';

				$circle_menu .=$main_toggle_click;
				
				$ij=2;
				
					if ( $settings['circle_menu_list'] ) {
						foreach (  $settings['circle_menu_list'] as $item ) {
							$arrow_text=$item['tooltip_text_arrow'];
							$tooltip_default_hover = ($item["tooltip_default_hover"]=='yes') ? 'tooltip-default-show' : '';
							$target =$nofollow ='';
							if(!empty($item['loop_icon_link_type']) && $item['loop_icon_link_type']=='email'){
								$icon_url='mailto:'.$item['email'];
							}else if(!empty($item['loop_icon_link_type']) && $item['loop_icon_link_type']=='phone'){
								$icon_url='tel:'.$item['phone'];
							}else if(!empty($item['icons_url']['url'])){
								$target = $item['icons_url']['is_external'] ? ' target="_blank"' : '';
								$nofollow = $item['icons_url']['nofollow'] ? ' rel="nofollow"' : '';
								$icon_url=$item['icons_url']['url'];
							}else{
							$target = ' target="_blank"';
								$nofollow = ' rel="nofollow"';
								$icon_url='#';
							}
							if(!empty($item['loop_icon_link_type']) && $item['loop_icon_link_type'] != 'nolink'){
								$nolink=' href="'.esc_url($icon_url).'" '.$target.' '.$nofollow;
							}else{
								$nolink='';
							}
							$circle_menu .= '<li class="plus-circle-menu-list elementor-repeater-item-' . $item['_id'] . ' '.esc_attr($arrow_text).' '.esc_attr($tooltip_default_hover).'">';
							
							if(!empty($item['loop_image_icon'])){								
									$tooltip_title='';
									if(!empty($item["tooltip_menu_title"])){
										$icon_bg1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['icon_background_image'],$item['icon_hover_background_image']) : '';
										$tooltip_title = '<span class="menu-tooltip-title '.$icon_bg1.'">'.$item["tooltip_menu_title"].'</span>';
									}
									if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
										$loop_imgSrc='';
										if(!empty($item["loop_select_image"]["url"])){	  
											$loop_select_image=$item["loop_select_image"]["id"];				
											$loop_imgSrc = tp_get_image_rander( $loop_select_image,$item['loop_select_image_thumbnail_size'], [ 'class' => 'img' ] );
										}
										$icon_bg = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['icon_background_image'],$item['icon_hover_background_image']) : '';
										if($icon_layout_open=='straight' && $icon_layout_straight_style=='menu-style-2'){
											$circle_menu .='<a '.$nolink.' class="menu_icon '.$icon_bg.'" >'.$tooltip_title.'</a>';
										}else{											
											$circle_menu .='<a '.$nolink.' class="menu_icon '.$icon_bg.'">'.$loop_imgSrc.$tooltip_title.'</a>';
										}
										
									}else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){		
										
										if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome'){
											$icons=$item["loop_icon_fontawesome"];
										}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='icon_mind'){
											$icons=$item["loop_icons_mind"];
										}else if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
											ob_start();
											\Elementor\Icons_Manager::render_icon( $item['loop_icon_fontawesome_5'], [ 'aria-hidden' => 'true' ]);
											$icons = ob_get_contents();
											ob_end_clean();
										}else{
											$icons='';
										}
										
										if($icon_layout_open=='straight' && $icon_layout_straight_style=='menu-style-2'){
											$circle_menu .='<a '.$nolink.' class="menu_icon" >'.$tooltip_title.'</a>';
										}else{
											$icon_bg4 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($item['icon_background_image'],$item['icon_hover_background_image']) : '';
											if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome_5'){
												$circle_menu .= '<a '.$nolink.' class="menu_icon '.$icon_bg4 .'" ><span>'.$icons.'</span>'.$tooltip_title.'</a>';
											}else{
												$circle_menu .= '<a '.$nolink.' class="menu_icon '.$icon_bg4 .'" ><i class=" '.esc_attr($icons).' " ></i>'.$tooltip_title.'</a>';
											}											
										}
									}
							}

							$circle_menu .= '</li>';
							$ij++;
						}
					}
				
			$circle_menu .='</ul>';
		$circle_menu .='</div>';
		$circle_menu .='</div>';
		$circle_menu_js='';
		if($icon_layout_open=='circle'){			
				$circle_menu_js ='jQuery(document).ready(function(i){						
					jQuery(".'.esc_attr($uid).' .plus-circle-menu").circleMenu({			
						direction: "'.esc_attr($icon_direction).'",
						angle:{start:'.esc_attr($angle_start).', end:'.esc_attr($angle_end).'},
						circle_radius: '.esc_attr($circle_radius).',
						circle_radius_tablet: '.esc_attr($circle_radius_tablet).',
						circle_radius_mobile: '.esc_attr($circle_radius_mobile).',
						delay:'.esc_attr($icon_delay).',			
						item_diameter:0,
						speed: '.esc_attr($icon_speed).',
						step_in: '.esc_attr($icon_step_in).',
						step_out: '.esc_attr($icon_step_out).',
						transition_function: "'.esc_attr($icon_transition).'",
						trigger: "'.esc_attr($icon_trigger).'"
					});
				});';
				$circle_menu .= wp_print_inline_script_tag($circle_menu_js);
		}
		$circle_menu .='<style>';
						$rpos='auto';$bpos='auto';$ypos='auto';$xpos='auto';
								if($settings['d_left_auto']=='yes'){
									if(!empty($settings['d_pos_xposition']['size']) || $settings['d_pos_xposition']['size']=='0'){
										$xpos=$settings['d_pos_xposition']['size'].$settings['d_pos_xposition']['unit'];
									}
								}
								if($settings['d_top_auto']=='yes'){
									if(!empty($settings['d_pos_yposition']['size']) || $settings['d_pos_yposition']['size']=='0'){
										$ypos=$settings['d_pos_yposition']['size'].$settings['d_pos_yposition']['unit'];
									}
								}
								if($settings['d_bottom_auto']=='yes'){
									if(!empty($settings['d_pos_bottomposition']['size']) || $settings['d_pos_bottomposition']['size']=='0'){
										$bpos=$settings['d_pos_bottomposition']['size'].$settings['d_pos_bottomposition']['unit'];
									}
								}
								if($settings['d_right_auto']=='yes'){
									if(!empty($settings['d_pos_rightposition']['size']) || $settings['d_pos_rightposition']['size']=='0'){
										$rpos=$settings['d_pos_rightposition']['size'].$settings['d_pos_rightposition']['unit'];
									}
								}
								
								$circle_menu.='.'.esc_attr($uid).' .plus-circle-menu{margin: 0 auto !important;margin-top:'.esc_attr($ypos).' !important;bottom:'.esc_attr($bpos).';left:'.esc_attr($xpos).';right:'.esc_attr($rpos).';}';
								if(!empty($rpos) && $rpos=='0%' && !empty($xpos) && $xpos=='0%'){
									$circle_menu.='.'.esc_attr($uid).'.layout-circle .plus-circle-menu{left: calc('.esc_attr($xpos).' - '.intval($settings["toggle_icon_width"]["size"]).$settings["toggle_icon_width"]["unit"].' );}';
								}
								if(!empty($ypos) && $ypos=='auto'){
									$circle_menu.='.'.esc_attr($uid).' .plus-circle-menu{top: auto;}';
								}
							
							if(!empty($settings['t_responsive']) && $settings['t_responsive']=='yes'){
								$tablet_xpos='auto';$tablet_ypos='auto';$tablet_bpos='auto';$tablet_rpos='auto';
								if($settings['t_left_auto']=='yes'){
									if(!empty($settings['t_pos_xposition']['size']) || $settings['t_pos_xposition']['size']=='0'){
										$tablet_xpos=$settings['t_pos_xposition']['size'].$settings['t_pos_xposition']['unit'];
									}
								}
								if($settings['t_top_auto']=='yes'){
									if(!empty($settings['t_pos_yposition']['size']) || $settings['t_pos_yposition']['size']=='0'){
										$tablet_ypos=$settings['t_pos_yposition']['size'].$settings['t_pos_yposition']['unit'];
									}
								}
								if($settings['t_bottom_auto']=='yes'){
									if(!empty($settings['t_pos_bottomposition']['size']) || $settings['t_pos_bottomposition']['size']=='0'){
										$tablet_bpos=$settings['t_pos_bottomposition']['size'].$settings['t_pos_bottomposition']['unit'];
									}
								}
								if($settings['t_right_auto']=='yes'){
									if(!empty($settings['t_pos_rightposition']['size']) || $settings['t_pos_rightposition']['size']=='0'){
										$tablet_rpos=$settings['t_pos_rightposition']['size'].$settings['t_pos_rightposition']['unit'];
									}
								}
								
								$circle_menu.='@media (min-width:601px) and (max-width:990px){.'.esc_attr($uid).' .plus-circle-menu{margin: 0 auto !important;margin-top:'.esc_attr($tablet_ypos).' !important;bottom:'.esc_attr($tablet_bpos).';left:'.esc_attr($tablet_xpos).';right:'.esc_attr($tablet_rpos).';}';
								if(!empty($tablet_rpos) && $tablet_rpos=='0%' && !empty($tablet_xpos) && $tablet_xpos=='0%'){
									$circle_menu.='.'.esc_attr($uid).'.layout-circle .plus-circle-menu{left: calc('.esc_attr($tablet_xpos).' - '.intval($settings["toggle_icon_width"]["size"]).$settings["toggle_icon_width"]["unit"].' );}';
								}
								if(!empty($tablet_ypos) && $tablet_ypos=='auto'){
									$circle_menu.='.'.esc_attr($uid).' .plus-circle-menu{top: auto;}';
								}
								$circle_menu.='}';
							}
							if(!empty($settings['m_responsive']) && $settings['m_responsive']=='yes'){
								$mobile_xpos='auto';$mobile_ypos='auto';$mobile_bpos='auto';$mobile_rpos='auto';
								if($settings['m_left_auto']=='yes'){
									if(!empty($settings['m_pos_xposition']['size']) || $settings['m_pos_xposition']['size']=='0'){
										$mobile_xpos=$settings['m_pos_xposition']['size'].$settings['m_pos_xposition']['unit'];
									}
								}
								if($settings['m_top_auto']=='yes'){
									if(!empty($settings['m_pos_yposition']['size']) || $settings['m_pos_yposition']['size']=='0'){
										$mobile_ypos=$settings['m_pos_yposition']['size'].$settings['m_pos_yposition']['unit'];
									}
								}
								if($settings['m_bottom_auto']=='yes'){
									if(!empty($settings['m_pos_bottomposition']['size']) || $settings['m_pos_bottomposition']['size']=='0'){
										$mobile_bpos=$settings['m_pos_bottomposition']['size'].$settings['m_pos_bottomposition']['unit'];
									}
								}
								if($settings['m_right_auto']=='yes'){
									if(!empty($settings['m_pos_rightposition']['size']) || $settings['m_pos_rightposition']['size']=='0'){
										$mobile_rpos=$settings['m_pos_rightposition']['size'].$settings['m_pos_rightposition']['unit'];
									}
								}
								$circle_menu.='@media (max-width:600px){.'.esc_attr($uid).' .plus-circle-menu{margin: 0 auto !important; margin-top:'.esc_attr($mobile_ypos).' !important;bottom:'.esc_attr($mobile_bpos).';left:'.esc_attr($mobile_xpos).';right:'.esc_attr($mobile_rpos).';}';
								if(!empty($mobile_rpos) && $mobile_rpos=='0%' && !empty($mobile_xpos) && $mobile_xpos=='0%'){
									$circle_menu.='.'.esc_attr($uid).'.layout-circle .plus-circle-menu{left: calc('.esc_attr($mobile_xpos).' - '.intval($settings["toggle_icon_width"]["size"]).$settings["toggle_icon_width"]["unit"].' );}';
								}
								if(!empty($mobile_ypos) && $mobile_ypos=='auto'){
									$circle_menu.='.'.esc_attr($uid).' .plus-circle-menu{top: auto;}';
								}
								$circle_menu.='}';
							}
							if($icon_layout_open=='straight'){
								$value=0;
								$i=2;
								if($ij>1){
									while($i < $ij){
										
										if( $settings['layout_straight_menu_direction'] == 'right'){
											$value = $settings["layout_straight_menu_gap"]["size"] + $value + $settings["toggle_icon_width"]["size"];
											$circle_menu .= '.'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-right .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'), .'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-right .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'){
												left: '.esc_attr($value).'px;
											}';
										}
										if( $settings['layout_straight_menu_direction'] == 'bottom'){
											$value = $settings["layout_straight_menu_gap"]["size"] + $value;
											$circle_menu .= '.'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-bottom .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'), .'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-bottom .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'){
												top: '.esc_attr($value).'px;
											}';
										}
										if( $settings['layout_straight_menu_direction'] == 'left'){
											$value = $settings["layout_straight_menu_gap"]["size"] + $value;
											$circle_menu .= '.'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-left .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'), .'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-left .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'){
												right: '.esc_attr($value).'px;
											}';
										}
										if( $settings['layout_straight_menu_direction'] == 'top'){
											$value = $settings["layout_straight_menu_gap"]["size"] + $value;
											$circle_menu .= '.'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-top .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'), .'.esc_attr($uid).'.plus-circle-menu-wrapper.layout-straight .plus-circle-menu.circleMenu-open.menu-direction-top .plus-circle-menu-list:not(.plus-circle-main-menu-list):nth-child('.esc_attr($i).'){
												bottom: '.esc_attr($value).'px;
											}';
										}
									$i++;
									}
								}
							}
							$circle_menu .='</style>';							
		
		echo $circle_menu;

		
	}
	
    protected function content_template() {
		
    }
}