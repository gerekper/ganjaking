<?php 
/*
Widget Name: Mobile Menu
Description: Mobile Menu
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

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Mobile_Menu extends Widget_Base {
		
	public function get_name() {
		return 'tp-mobile-menu';
	}

    public function get_title() {
        return esc_html__('Mobile Menu', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-mobile theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-header');
    }
	
    protected function register_controls() {
		/*mobile menu section start*/
		$this->start_controls_section(
			'section_advanced_buttons',
			[
				'label' => esc_html__( 'Mobile Menu', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'mm_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1'  => esc_html__( 'Style 1', 'theplus' ),					
					'style_2' => esc_html__( 'Style 2', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'mobile_menu_pos',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'fixed',
				'options' => [
					'absolute'  => esc_html__( 'Absolute', 'theplus' ),					
					'fixed'  => esc_html__( 'Fixed', 'theplus' ),		
				],
			]
		);
		$this->add_control(
			'mobile_menu_pos_po',
			[
				'label' => esc_html__( 'Fixed Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'bottom',
				'options' => [
					'top'  => esc_html__( 'Top', 'theplus' ),					
					'bottom'  => esc_html__( 'Bottom', 'theplus' ),		
				],
				'condition' => [
					'mobile_menu_pos' => 'fixed',					
				],	
			]
		);
		$this->add_control(
			'open_mobile_menu',
			[
				'label' => esc_html__( 'Open Mobile Menu', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1500,
						'step' => 5,
					],
				],				
				'separator' => 'before',				
			]
		);
		$this->end_controls_section();
		/*mobile menu section start*/
		
		/*mobile menu 1 for style 1 & 2 section start*/
		$this->start_controls_section(
			'section_mm_st1',
			[	
				'label' => esc_html__( 'Menu 1', 'theplus' ),				
				'tab' => Controls_Manager::TAB_CONTENT,				
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'mm_st1_icon_image',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mm_st1_icon',
				'options' => [
					'mm_st1_icon'  => esc_html__( 'Icon', 'theplus' ),					
					'mm_st1_image' => esc_html__( 'Image', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'mm_st1_custom_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-calendar-alt',
					'library' => 'solid',
				],
				'condition' => [
					'mm_st1_icon_image' => 'mm_st1_icon',					
				],	
			]
		);
		$repeater->add_control(
			'mm_st1_custom_image',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'mm_st1_icon_image' => 'mm_st1_image',					
				],	
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'mmst1_ci_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'mm_st1_icon_image' => 'mm_st1_image',					
				],	
			]
		);
		$repeater->add_control(
			'mm_st1_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],				
			]
		);
		$repeater->add_control(
			'mm_st1_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'mm_st1_pin_text',
			[
				'label' => esc_html__( 'Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,				
				'separator' => 'before',
				'dynamic' => ['active'   => true,],	
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .tp-loop-inner:after' => ' content:"{{VALUE}}";',
				],
			]
		);
		$this->add_control(
            'mm_st1_content',
            [
				'label' => esc_html__( 'Menu 1', 'theplus' ),
                'type' => Controls_Manager::REPEATER,               
                'separator' => 'before',
				 'default' => [
                    [
                        'mm_st1_text' => '1',
                    ],
					[
                        'mm_st1_text' => '2',
                    ],
					[
                        'mm_st1_text' => '3',
                    ],
					[
                        'mm_st1_text' => '4',
                    ],
                ],
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ mm_st1_text }}}',				
            ]
        );
		$this->end_controls_section();
		/*mobile menu 1 for style 1 & 2 section end*/
		
		/*right menu style 2 section start*/
		$this->start_controls_section(
			'section_mm_st2_r',
			[
				'label' => esc_html__( 'Menu 2', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'mm_style' => 'style_2',
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'mm_st2_icon_image_r',
			[
				'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mm_st2_icon_r',
				'options' => [
					'mm_st2_icon_r'  => esc_html__( 'Icon', 'theplus' ),
					'mm_st2_image_r' => esc_html__( 'Image', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'mm_st2_custom_icon_r',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [					
					'value' => 'far fa-calendar-alt',
					'library' => 'solid',
				],
				'condition' => [
					'mm_st2_icon_image_r' => 'mm_st2_icon_r',
				],	
			]
		);
		$repeater->add_control(
			'mm_st2_custom_image_r',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'mm_st2_icon_image_r' => 'mm_st2_image_r',
				],	
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'mmst2_cir_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'condition' => [
					'mm_st2_icon_image_r' => 'mm_st2_image_r',
				],	
			]
		);
		$repeater->add_control(
			'mm_st2_text_r',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Plus', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],				
			]
		);		
		$repeater->add_control(
			'mm_st2_link_r',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'https://your-link.com', 'theplus' ),
				'show_external' => true,
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
				'dynamic' => [
					'active'   => true,
				],
			]
		);
		$repeater->add_control(
			'mm_st2_pin_text_r',
			[
				'label' => esc_html__( 'Pin Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,				
				'separator' => 'before',
				'dynamic' => ['active'   => true,],	
				'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .tp-loop-inner:after' => ' content:"{{VALUE}}";',
				],				
			]
		);
		$this->add_control(
            'mm_st2_content_r',
            [
				'label' => esc_html__( 'Menu 2', 'theplus' ),
                'type' => Controls_Manager::REPEATER,               
                'separator' => 'before',
				'default' => [
                    [
                        'mm_st2_text_r' => '3',
                    ],
					[
                        'mm_st2_text_r' => '4',
                    ],					
                ],
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ mm_st2_text_r }}}',
            ]
        );
		$this->end_controls_section();
		/*right menu style 2 section end*/
		
		/*extra toggle section start*/
		$this->start_controls_section(
			'section_mm_extra_toggle',
			[
				'label' => esc_html__( 'Extra Toggle', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,				
			]
		);
		$this->add_control(
			'mm_extra_toggle_switch',
			[
				'label' => esc_html__( 'Extra Toggle', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',				
			]
		);
		$this->add_control(
			'mm_extra_toggle_icon_image',
			[
				'label' => esc_html__( 'Select Toggle Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mm_ext_tgl_icon',
				'options' => [
					'mm_ext_tgl_icon'  => esc_html__( 'Icon', 'theplus' ),
					'mm_ext_tgl_image' => esc_html__( 'Image', 'theplus' ),
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],	
			]
		);
		$this->add_control(
			'mm_extra_toggle_custom_icon',
			[
				'label' => esc_html__( 'Icon', 'theplus' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'far fa-calendar-check',
					'library' => 'solid',
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',	
					'mm_extra_toggle_icon_image' => 'mm_ext_tgl_icon',
				],	
			]
		);
		$this->add_control(
			'mm_extra_toggle_custom_image',
			[
				'label' => esc_html__( 'Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'media_type' => 'image',
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_icon_image' => 'mm_ext_tgl_image',
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'mm_et_ci_thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_icon_image' => 'mm_ext_tgl_image',
				],
				'separator' => 'after',
			]
		);
		
		$this->add_control(
			'mm_extra_toggle_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'TP', 'theplus' ),
				'separator' => 'before',
				'dynamic' => ['active'   => true,],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],	
			]
		);
		$this->add_control(
			'mm_extra_toggle_link_template',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'et_link',
				'options' => [
					'et_link'  => esc_html__( 'Link', 'theplus' ),					
					'et_template' => esc_html__( 'Template', 'theplus' ),
				],
				'separator' => 'before',
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',
				],	
			]
		);
		$this->add_control(
			'mm_extra_toggle_link',
			[
				'label' => esc_html__( 'Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'default' => [
					'url' => '#',
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_link',
				],
			]
		);
		$this->add_control(
			'extra_content_template_et',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
				'condition'   => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
				],
			]
		);
		$this->add_control(
			'extra_toggle_bar_direction',
			[
				'label' => esc_html__( 'Open Content Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'right'  => esc_html__( 'Right', 'theplus' ),
					'left' => esc_html__( 'Left', 'theplus' ),
					'top' => esc_html__( 'Top', 'theplus' ),
					'bottom' => esc_html__( 'Bottom', 'theplus' ),	
				],
				'separator' => 'before',
				'condition'   => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
				],
			]
		);
		$this->add_control(
			'extra_toggle_style',
			[
				'label' => esc_html__( 'Open Content Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mm-ett-style-1',
				'options' => [
					'mm-ett-style-1'  => esc_html__( 'Style 1', 'theplus' ),					
					'mm-ett-style-2'  => esc_html__( 'Style 2', 'theplus' ),
				],
				'separator' => 'before',
				'condition'   => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
				],
			]
		);
		$this->add_control(
			'extra_content_width_option',
			[
				'label' => esc_html__( 'Content Width', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'custom',
				'options' => [
					'custom'  => esc_html__( 'Custom Width/Height', 'theplus' ),
					'fullwidth' => esc_html__( 'Full-Width/Height', 'theplus' ),
				],
				'condition'   => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
				],
			]
		);
		$this->add_responsive_control(
			'extra_content_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content.open' => 'width:calc(100% - {{SIZE}}%);height:calc(100% -  {{SIZE}}%); max-width:calc(100% - {{SIZE}}%);max-height:calc(100% - {{SIZE}}%);
					align-items: center;justify-content: center;vertical-align: middle;right: 0;left: 0;margin: 0 auto;top: 50%;transform: translateY(-50%);',
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content' => 'transition: all 0.3s ease-in-out;',
				],
				'condition'   => [										
					'extra_content_width_option' => 'fullwidth',
				],
			]
		);
		$this->add_responsive_control(
			'extra_content_width',
			[
				'label' => esc_html__( 'Custom Content Width/Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 400,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.left,{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.right' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.top,{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.bottom' => 'max-height: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [					
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
					'extra_content_width_option' => 'custom',
				],
			]
		);
		
		$this->end_controls_section();
		/*extra toggle section end*/
		
		/*extra options section start*/
		$this->start_controls_section(
			'section_mm_extra_options',
			[
				'label' => esc_html__( 'Extra Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'mm_extra_display_mode',
			[
				'label' => esc_html__( 'Display', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'swiper',
				'options' => [
					'swiper'  => esc_html__( 'Swiper', 'theplus' ),
					'columns'  => esc_html__( 'Columns', 'theplus' ),					
				],
			]
		);
		
		$this->add_control(
			'mm_extra_indicator_switch',
			[
				'label' => esc_html__( 'Active Page Indicator', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'mm_extra_indicator_type',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'line',
				'options' => [
					'line'  => esc_html__( 'Line', 'theplus' ),
					'dot'  => esc_html__( 'Dot', 'theplus' ),
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
				],
			]
		);
		$this->add_control(
			'mm_extra_indicator',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '',
				'options' => [
					''  => esc_html__( 'Select', 'theplus' ),
					'indi-top'  => esc_html__( 'Top', 'theplus' ),
					'indi-bottom'  => esc_html__( 'Bottom', 'theplus' ),
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'line',
				],
			]
		);
		$this->add_responsive_control(
            'mm_extra_indicator_dot_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -100,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.dot.active .tp-menu-link:after' => 'bottom: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'dot',
				],
            ]
        );
		$this->add_control(
			'pin_text_overflow',
			[
				'label' => esc_html__( 'Pin Text Overflow', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'hidden',
				'options' => [
					'hidden'  => esc_html__( 'Hidden', 'theplus' ),					
					'visible' => esc_html__( 'Visible', 'theplus' ),
				],
				'selectors' => [
                    '{{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-l-wrapper .tp-mm-li,
					{{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-r-wrapper .tp-mm-li,
					{{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-c-wrapper .tp-mm-c-et-li' => ' overflow:{{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*extra toggle section end*/
		
		/* style section start*/
		/*icon/image style start*/
		$this->start_controls_section(
            'mm_icon_image_styling',
            [
                'label' => esc_html__('Icon/Image Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
			'mm_icon_head',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'mm_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li i,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link i,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-menu-link i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li svg,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link svg,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_mm_icon' );
		$this->start_controls_tab(
			'tab_mm_icon_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'mm_icon_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li i,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link i,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-menu-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li svg,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link svg,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li svg' => 'fill: {{VALUE}};',
				],
			]
		);		
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mm_icon_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'mm_icon_color_a',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active i,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link i,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-menu-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active svg,{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link svg,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'mm_image_head',
			[
				'label' => esc_html__( 'Image Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'mm_image_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_mm_image' );
		$this->start_controls_tab(
			'tab_mm_image_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img',				
			]
		);
		$this->add_responsive_control(
			'mm_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_image_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mm_image_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_image_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner .tp-mm-img',				
			]
		);
		$this->add_responsive_control(
			'mm_image_radius_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner .tp-mm-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_image_shadow_a',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner .tp-mm-img',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*icon/image style end*/
		
		/*extra toggle icon/image style start*/
		$this->start_controls_section(
            'mm_et_icon_image_styling',
            [
                'label' => esc_html__('Extra Toggle Icon/Image Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],
            ]
        );	
		$this->add_control(
			'mm_et_icon_head',
			[
				'label' => esc_html__( 'Icon Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'mm_et_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link i,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-menu-link i' => 'font-size: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link svg,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li svg' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'mm_et_icon_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link i,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-menu-link i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-mm-c-wrapper .tp-menu-link svg,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li svg' => 'fill: {{VALUE}};',
				],
			]
		);	
		$this->add_control(
			'mm_et_image_head',
			[
				'label' => esc_html__( 'Image Style', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'mm_et_image_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 300,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img.tp-mm-et-img' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_et_image_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img.tp-mm-et-img',				
			]
		);
		$this->add_responsive_control(
			'mm_et_image_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img.tp-mm-et-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_et_image_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner .tp-mm-img.tp-mm-et-img',				
			]
		);
		$this->end_controls_section();
		/*extra toggle icon/image style end*/
		
		/*active page indicator start*/
		$this->start_controls_section(
            'mm_indicator_styling',
            [
                'label' => esc_html__('Active Page Indicator Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',					
				],
            ]
        );	
		$this->add_responsive_control(
            'mm_indicator_line_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active:before,{{WRAPPER}}  .tp-mobile-menu .tp-mm-li.active:after' => 'width: {{SIZE}}{{UNIT}} !important',
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'line',
				],
            ]
        );
		$this->add_responsive_control(
            'mm_indicator_line_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active:before,{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active:after' => 'border-width: {{SIZE}}{{UNIT}} !important',
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'line',
				],
            ]
        );
		$this->add_control(
			'mm_indicator_line_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active:before,{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active:after' => 'border-color: {{VALUE}} !important;',
				],
				'condition' => [					
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'line',
				],
				
			]
		);
			$this->add_responsive_control(
            'mm_indicator_dot_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Size', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.dot.active .tp-menu-link:after' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}}',
				],
				'condition'   => [
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'dot',
				],
            ]
        );
		$this->add_control(
			'mm_indicator_dot_color',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.dot.active .tp-menu-link:after' => 'background: {{VALUE}};',
				],
				'condition' => [					
					'mm_extra_indicator_switch' => 'yes',
					'mm_extra_indicator_type' => 'dot',
				],
				
			]
		);
		$this->end_controls_section();
		/*active page indicator end*/
		
		/*menu style start*/
		$this->start_controls_section(
            'mm_main_menu_styling',
            [
                'label' => esc_html__('Menu Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );			
		$this->add_responsive_control(
			'mm_main_menu_margin',
			[
				'label' => esc_html__( 'Outer Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-mm-wrapper .tp-mm-wrapper-inner .tp-mm-li,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-ul .tp-mm-et-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-l-wrapper .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-r-wrapper .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-mm-li, {{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-c-wrapper .tp-mm-c-et-li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control(
            'max_width_loop',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Menu Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-mm-wrapper .tp-mm-wrapper-inner .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-l-wrapper .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-r-wrapper .tp-mm-li' => 'max-width:{{SIZE}}{{UNIT}} !important;min-width:{{SIZE}}{{UNIT}} !important;',					
				],
				'condition' => [
					'mm_extra_display_mode' => 'swiper',					
				],
			]    
        );
		$this->add_responsive_control(
            'max_height_loop',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Menu Height', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 70,
				],
				'separator' => 'before',
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-mm-wrapper .tp-mm-wrapper-inner .tp-mm-li,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-ul .tp-mm-et-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-l-wrapper .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-r-wrapper .tp-mm-li,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-mm-li, {{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-c-wrapper .tp-mm-c-et-li' => 'max-height:{{SIZE}}{{UNIT}} !important;min-height:{{SIZE}}{{UNIT}} !important;',					
				],
				'condition' => [
					'mm_extra_display_mode' => 'swiper',					
				],
			]    
        );
		$this->add_control(
			'n_title_head',
			[
				'label' => esc_html__( 'Title Typography', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',	
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'n_title',
				'selector' => '{{WRAPPER}} .tp-mm-wrapper .tp-mm-wrapper-inner .tp-mm-li .tp-mm-st1-title,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-mm-extra-toggle,{{WRAPPER}}  .tp-mobile-menu.style_2 .tp-mm-st1-title, {{WRAPPER}}  .tp-mobile-menu.style_2 .tp-mm-extra-toggle',
			]
		);
		$this->start_controls_tabs( 'tabs_mm_main_menu' );
		$this->start_controls_tab(
			'tab_mm_main_menu_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
			$this->add_control(
			'n_title_color',
			[
				'label' => esc_html__( 'Normal Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mm-wrapper .tp-mm-wrapper-inner .tp-mm-li .tp-mm-st1-title,{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-mm-extra-toggle,{{WRAPPER}}  .tp-mobile-menu.style_2 .tp-mm-st1-title, {{WRAPPER}}  .tp-mobile-menu.style_2 .tp-mm-extra-toggle' => 'color: {{VALUE}} !important;',
				],
			]
		);
			$this->add_control(
			'et_title_color',
			[
				'label' => esc_html__( 'Extra Toggle Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mm-et-wrapper .tp-mm-et-li .tp-mm-extra-toggle,{{WRAPPER}} .tp-mobile-menu.style_2 .tp-mm-extra-toggle' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mm_main_menu_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',				
			]
		);	
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_main_menu_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'mm_main_menu_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_main_menu_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_mm_main_menu_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
			]
		);
		$this->add_control(
			'n_title_color_a',
			[
				'label' => esc_html__( 'Active Title Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-mm-st1-title' => 'color: {{VALUE}} !important;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mm_main_menu_background_a',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_main_menu_border_a',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'mm_main_menu_radius_a',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_main_menu_shadow_a',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li.active .tp-loop-inner',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*menu style end*/
		
		/*extra toggle style start*/
		$this->start_controls_section(
            'mm_extra_toggle_styling',
            [
                'label' => esc_html__('Extra Toggle Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],
            ]
        );
		$this->add_control(
            'extra_toggle_equal_height',
            [
				'label'   => esc_html__( 'Extra Toggle Equal Height/Width', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',				
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],
			]
		);
		$this->add_responsive_control(
            'extra_toggle_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Extra Toggle Width', 'theplus'),
				'size_units' => [ '%' ],
				'range' => [
					'%' => [
						'min' => 0,
						'max' => 100,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.style_1.tpet-on .tp-mm-et-wrapper,{{WRAPPER}} .tp-mobile-menu.style_2.tpet-on .tp-mm-c-wrapper' => 'width:{{SIZE}}%;',
					'{{WRAPPER}} .tp-mobile-menu.style_2.tpet-on .tp-mm-l-wrapper,{{WRAPPER}} .tp-mobile-menu.style_2.tpet-on .tp-mm-r-wrapper' => 'width:calc((100% - {{SIZE}}%)/2);',
					'{{WRAPPER}} .tp-mobile-menu.style_1.tpet-on .tp-mm-wrapper' => 'width:calc(100% - {{SIZE}}%);',
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',	
					'extra_toggle_equal_height!' => 'yes',	
				],
			]    
        );
		$this->add_responsive_control(
            'extra_toggle_width_eq',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Extra Toggle Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,
					 {{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,
					 {{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper' => 'width:{{SIZE}}{{UNIT}} !important;height: {{SIZE}}{{UNIT}} !important;max-height: {{SIZE}}{{UNIT}} !important;min-height :{{SIZE}}{{UNIT}} !important;max-width:{{SIZE}}{{UNIT}} !important;min-width:{{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',	
					'extra_toggle_equal_height' => 'yes',	
				],
			]    
        );
		$this->add_responsive_control(
            'extra_toggle_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Toggle Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 1000,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',			
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper,{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper' => 'margin-top:{{SIZE}}{{UNIT}};max-height: {{max_height_loop.SIZE}}px;',
				],
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',					
				],
			]    
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'mm_extra_toggle_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'mm_extra_toggle_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'mm_extra_toggle_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'mm_extra_toggle_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-c-wrapper .tp-loop-inner,
				{{WRAPPER}} .tp-mobile-menu .tp-mm-et-wrapper',				
			]
		);
		$this->end_controls_section();
		/*extra toggle style end*/
		
		/*extra toggle style start*/
		$this->start_controls_section(
            'mm_extra_toggle_temp_styling',
            [
                'label' => esc_html__('Extra Toggle Template Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'mm_extra_toggle_switch' => 'yes',
					'mm_extra_toggle_link_template' => 'et_template',
				],
            ]
        );	
		$this->add_control(
			'mm_extra_toggle_temp_heading',
			[
				'label' => esc_html__( 'Template', 'theplus' ),
				'type' => Controls_Manager::HEADING,				
			]
		);
		$this->add_control(
			'mm_extra_toggle_temp_overflow',
			[
				'label' => esc_html__( 'Overflow', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp-of-h',
				'options' => [
					'tp-of-v'  => esc_html__( 'Visible', 'theplus' ),
					'tp-of-h' => esc_html__( 'Hidden', 'theplus' ),					
				],
				'separator' => 'before',
			]
		);
		$this->add_control(
			'content_inner_padding',
			[
				'label' => esc_html__( 'Inner Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px','em' ],
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'extra_toggle_content_background',
				'label' => esc_html__( 'Content Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.mm-ett-style-1,
				{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.mm-ett-style-2:after',				
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'extra_toggle_content_fw_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content.open',
				'condition'   => [										
					'extra_content_width_option' => 'fullwidth',
				],
			]
		);
		$this->add_responsive_control(
			'extra_toggle_content_fw_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content.open' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'condition'   => [										
					'extra_content_width_option' => 'fullwidth',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'extra_toggle_content_fw_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .header-extra-toggle-content.full-width-content.open',	
				'condition'   => [										
					'extra_content_width_option' => 'fullwidth',
				],
			]
		);
		$this->add_control(
			'extra_toggle_close_heading_options',
			[
				'label' => esc_html__( 'Close Icon Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tab_extra_toggle_close_position',
			[
				'label' => esc_html__( 'Close Icon Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'mm-ci-top-right',
				'options' => [
					'mm-ci-top-left'  => esc_html__( 'Top Left', 'theplus' ),
					'mm-ci-top-center'  => esc_html__( 'Top Center', 'theplus' ),
					'mm-ci-top-right'  => esc_html__( 'Top Right', 'theplus' ),
					'mm-ci-bottom-left'  => esc_html__( 'Bottom Left', 'theplus' ),
					'mm-ci-bottom-center'  => esc_html__( 'Bottom Center', 'theplus' ),
					'mm-ci-bottom-right'  => esc_html__( 'Bottom Right', 'theplus' ),
					'mm-ci-auto'  => esc_html__( 'Auto', 'theplus' ),
					
				],
			]
		);
		$this->start_controls_tabs( 'tabs_extra_toggle_close_style' );
		$this->start_controls_tab(
			'tab_extra_toggle_close_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_color',
			[
				'label' => esc_html__( 'Close Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}}  .tp-mobile-menu.tpet-on .extra-toggle-close-menu:before,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:after,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:before,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_bg',
			[
				'label' => esc_html__( 'Close Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu' => 'background: {{VALUE}}',
				],
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'extra_toggle_icon_close_box_shadow',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu',
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_extra_toggle_close_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_color_hover',
			[
				'label' => esc_html__( 'Close Icon Hover Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ff5a6e',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:hover:before,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:hover:after,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:hover:before,{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu-auto.tp-mm-ca:hover:after' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_bg_hover',
			[
				'label' => esc_html__( 'Close Hover Background Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#313131',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:hover' => 'background: {{VALUE}}',
				],
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->add_control(
			'extra_toggle_icon_close_border_radius_hover',
			[
				'label' => esc_html__( 'Hover Border Radius', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'extra_toggle_icon_close_box_shadow_hover',
				'label' => esc_html__( 'Hover Box Shadow', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-close-menu:hover',
				'condition' => [
					'tab_extra_toggle_close_position!' => 'mm-ci-auto',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'extra_toggle_overlay_heading_options',
			[
				'label' => esc_html__( 'Overlay Style', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'extra_toggle_overlay_background',
				'label' => esc_html__( 'Overlay Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-mobile-menu.tpet-on .extra-toggle-content-overlay',
			]
		);
		$this->end_controls_section();
		/*extra toggle style end*/
		
		/*content background style start*/
		$this->start_controls_section(
            'mm_cb_styling',
            [
                'label' => esc_html__('Content Background Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'mm_cb__padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} tp-mobile-menu' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
					Group_Control_Background::get_type(),
					[
						'name'      => 'mm_cb_bg',
						'types'     => [ 'classic', 'gradient' ],
						'selector' => '{{WRAPPER}} .tp-mobile-menu',
					]
				);
				$this->add_group_control(
					Group_Control_Border::get_type(),
					[
						'name' => 'mm_cb_border',
						'label' => esc_html__( 'Border', 'theplus' ),
						'selector' => '{{WRAPPER}} .tp-mobile-menu',
					]
				);
				$this->add_responsive_control(
					'mm_cb_border_radius',
					[
						'label'      => esc_html__( 'Border Radius', 'theplus' ),
						'type'       => Controls_Manager::DIMENSIONS,
						'size_units' => [ 'px', '%' ],
						'selectors'  => [
							'{{WRAPPER}} .tp-mobile-menu' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};overflow: hidden;',
						],
					]
				);
				$this->add_control(
					'mm_cb_overflow',
					[
						'label' => esc_html__( 'Overflow', 'theplus' ),
						'type' => Controls_Manager::SELECT,
						'default' => 'hidden',
						'options' => [
							'hidden'  => esc_html__( 'Hidden', 'theplus' ),					
							'visible' => esc_html__( 'Visible', 'theplus' ),
						],
						'selectors'  => [
							'{{WRAPPER}} .tp-mobile-menu' => 'overflow: {{VALUE}} !important;',
						],
					]
				);
				$this->add_group_control(
					Group_Control_Box_Shadow::get_type(),
					[
						'name'     => 'mm_cb_box_shadow',
						'selector' => '{{WRAPPER}} .tp-mobile-menu',
					]
				);
		$this->end_controls_section();
		/*content background style end*/
		
		/*Pin text Option Style*/
		$this->start_controls_section(
			'pin_text_style_section',
			[
				'label' => esc_html__( 'Pin Text Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);
		$this->add_responsive_control(
			'pin_text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};line-height: 1;',
				],
			]
		);
		$this->add_responsive_control(
            'pin_text_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -400,
						'max' => 400,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'pin_text_right_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Right Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -400,
						'max' => 400,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'right: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'pin_text_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Text Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'font-size: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
			'pin_text_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'pin_text_bg',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pin_text_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'pin_text_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'pin_text_shadow',
				'selector' => '{{WRAPPER}} .tp-mobile-menu .tp-mm-li .tp-loop-inner:after',				
			]
		);
		$this->end_controls_section();
		/*Pin text Option Style end*/
		
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
				'label' => esc_html__( 'Show Mobile Menu Scroll Offset', 'theplus' ),
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
		$this->end_controls_section();
		/*Extra Option Style*/
		/* style section end*/
		
	}
	
    protected function render() {
		$settings = $this->get_settings_for_display();
		
		$uid = uniqid("mm");
		$mm_style = $settings['mm_style'];
		$close_position = $settings['tab_extra_toggle_close_position'];
		global $wp;
		$current_page_url = home_url( $wp->request );
		
		$show_scroll_window_offset = ($settings['show_scroll_window_offset'] == 'yes') ? 'scroll-view' : '';		
		$scroll_top_offset_value = (!empty($settings['scroll_top_offset_value']["size"]) && $settings['show_scroll_window_offset'] == 'yes') ? 'data-scroll-view="'.esc_attr($settings['scroll_top_offset_value']["size"]).'"' : 'data-scroll-view=""';
		
		$mobile_menu_pos = $settings['mobile_menu_pos'];
		$position_class='';
		if($mobile_menu_pos == 'absolute'){
			$position_class = 'tp-mm-absolute';
		}else if($mobile_menu_pos == 'fixed'){
			$position_class = 'tp-mm-fix';
		}		
		
		$mm_extra_toggle_switch = $settings['mm_extra_toggle_switch'];
		$extra_toggle='';
		/*extra toggle start*/
		if(!empty($mm_extra_toggle_switch) && $mm_extra_toggle_switch == 'yes'){
				$mm_extra_toggle_icon_image = $settings['mm_extra_toggle_icon_image'];
				$mm_extra_toggle_link_template = $settings['mm_extra_toggle_link_template'];
				$mm_extra_toggle_text = !empty($settings['mm_extra_toggle_text']) ? $settings['mm_extra_toggle_text'] : '';
				
				/*extra toggle icon image start*/
				$image_alt='';
				if(!empty($mm_extra_toggle_icon_image == 'mm_ext_tgl_image')){
					$mm_extra_toggle_custom_image=$settings['mm_extra_toggle_custom_image']['id'];
					$img = wp_get_attachment_image_src($mm_extra_toggle_custom_image,$settings['mm_et_ci_thumbnail_size']);
					$mm_et_ci_src = isset($img[0]) ? $img[0] : '';
					$image_alt = get_post_meta($mm_extra_toggle_custom_image, '_wp_attachment_image_alt', TRUE);
					if(!$image_alt){
						$image_alt = get_the_title($mm_extra_toggle_custom_image);
					}
					$extra_toggle_ii ='<img class="tp-mm-img tp-mm-et-img" src='.esc_url($mm_et_ci_src).' alt="'.esc_attr($image_alt).'" />';
				}else if($mm_extra_toggle_icon_image == 'mm_ext_tgl_icon'){
					ob_start();
						\Elementor\Icons_Manager::render_icon( $settings['mm_extra_toggle_custom_icon'] , [ 'aria-hidden' => 'true' ]);
						$extra_toggle_ii = ob_get_contents();
					ob_end_clean();					
				}else{
					$extra_toggle_ii='';
				}
				/*extra toggle icon image end*/
				
				/*extra toggle link template start*/
				if(($mm_extra_toggle_link_template == 'et_link' && !empty($extra_toggle_ii)) || ($mm_extra_toggle_link_template == 'et_link' && !empty($mm_extra_toggle_text))){
						if(!empty($settings['mm_extra_toggle_link']['url'])){
							$et_a_start = '<a class="tp-menu-link tp-mm-et-link" href="'.esc_url($settings['mm_extra_toggle_link']['url']).'">';
							$et_a_end = '</a>';
						}
						$extra_toggle .= wp_kses_post($et_a_start) .$extra_toggle_ii.'<span class="tp-mm-extra-toggle">'.wp_kses_post($mm_extra_toggle_text).'</span>' .wp_kses_post($et_a_end);	
				}else if(($mm_extra_toggle_link_template == 'et_template' && !empty($extra_toggle_ii)) || ($mm_extra_toggle_link_template == 'et_template' && !empty($mm_extra_toggle_text))){
					$open_direction = $settings["extra_toggle_bar_direction"];
					$extra_toggle_style = $settings["extra_toggle_style"];
					$mm_extra_toggle_temp_overflow = $settings["mm_extra_toggle_temp_overflow"];
					$fullwidth_content = ($settings['extra_content_width_option']=='fullwidth') ?  'full-width-content' : '';
					$extra_toggle .= '<a class="tp-menu-link tp-mm-et-link" >'.$extra_toggle_ii.'<span class="tp-mm-extra-toggle">'.wp_kses_post($mm_extra_toggle_text).'</span></a>';
					if(!empty($settings['tab_extra_toggle_close_position']) && $settings['tab_extra_toggle_close_position'] == 'mm-ci-auto'){
						$extra_toggle .= '<div class="extra-toggle-close-menu-auto"></div>';
					}

						$extra_toggle .='<div class="header-extra-toggle-content '.esc_attr($extra_toggle_style).' '.esc_attr($fullwidth_content).' '.esc_attr($open_direction).' '.esc_attr($mm_extra_toggle_temp_overflow).'">';
							if($settings['extra_toggle_style']=='mm-ett-style-2' && !empty($settings['extra_toggle_style'])){
								$extra_toggle .='<div class="tp-con-open-st2">';
							}
							$extra_toggle .='<div class="extra-toggle-close-menu '.esc_attr($close_position).'"></div>';
							$extra_toggle .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($settings['extra_content_template_et'] );
							if($settings['extra_toggle_style']=='mm-ett-style-2' && !empty($settings['extra_toggle_style'])){
								$extra_toggle .='</div>';
							}
						$extra_toggle .='</div>';
						$extra_toggle .='<div class="extra-toggle-content-overlay"></div>';
					}
				
				/*extra toggle link template end*/
		}
		/*extra toggle end*/
		$loop=$loop2='';
		
			$indi_class='';
			if($settings['mm_extra_indicator_type'] == 'dot'){
				$indi_class = 'dot';
			}
			
			if(!empty($settings["mm_st1_content"])) {
				$i=0;
				
				foreach($settings["mm_st1_content"] as $item) {					
					$mm_st1_icon_image = $item['mm_st1_icon_image'];
					$mm_st1_custom_icon = $item['mm_st1_custom_icon'];
					
					$mm_st1_custom_image='';
					if(!empty($item['mm_st1_custom_image'])){
						$mm_st1_custom_image = $item['mm_st1_custom_image']['id'];
						$img = wp_get_attachment_image_src($mm_st1_custom_image,$item['mmst1_ci_thumbnail_size']);
						$mm_st1_custom_image = isset($img[0]) ? $img[0] : '';
					}					
					
					$mm_st1_text = $item['mm_st1_text'];
					$mm_st1_link = $item['mm_st1_link'];
					
					$title_a_start=$title_a_end='';
					/*st1 custom icon start*/
					if($mm_st1_icon_image=='mm_st1_icon'){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $mm_st1_custom_icon , [ 'aria-hidden' => 'true' ]);
						$mm_st1_custom_icon = ob_get_contents();
						ob_end_clean();						
					}
					/*st1 custom icon end*/					
					
					$mobile_menu='';
					/*st1 title & link start*/
					if ( ! empty( $item['mm_st1_link']['url'] ) ) {
						$mm_st1_link = $item['mm_st1_link']['url'];
					}
					if(!empty($item['mm_st1_text'])){						
						$mobile_menu .= '<span class="tp-mm-st1-title">'.$item['mm_st1_text'].'</span>';
					}
					$i++;
					/*st1 title & link end*/					
					
					
					/*display st1 start*/
					if (!empty($item['mm_st1_link']['url'])){
						$title_a_start = '<a class="tp-menu-link tp-mm-normal" href="'.esc_url($mm_st1_link).'">';
						$title_a_end = '</a>';
					}else{
						$title_a_start = '<a class="tp-menu-link tp-mm-normal">';
						$title_a_end = '</a>';
					}
					$inner_class_loop='';
					if($settings['mm_extra_display_mode'] == 'columns'){						
						$inner_class_loop .= 'grid-item tp-mm-eq-col';
					}
					
					$loop .='<li class="tp-mm-li elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($inner_class_loop).' '.esc_attr($settings['mm_extra_indicator']).' '.esc_attr($indi_class).'"><div class="tp-loop-inner">'.$title_a_start.'';
						if(!empty($mm_st1_custom_icon)){
							$loop .= $mm_st1_custom_icon;
						}
						if(!empty($mm_st1_custom_image)){
							$loop .= '<img class="tp-mm-img tp-mm-st1-img" src='.esc_url($mm_st1_custom_image).' />';		
						}
						$loop .= $mobile_menu;							
					$loop .=''.$title_a_end.'</div></li>';
					/*display st1 end*/
				}		
			}
			
			if(!empty($settings["mm_st2_content_r"])) {
				$j=0;
				foreach($settings["mm_st2_content_r"] as $item) {					
					$mm_st2_icon_image_r= $item['mm_st2_icon_image_r'];
					$mm_st2_custom_icon_r= $item['mm_st2_custom_icon_r'];					
					
					$mm_st2_custom_image_r='';
					if(!empty($item['mm_st2_custom_image_r'])){
						$mm_st2_custom_image_r=$item['mm_st2_custom_image_r']['id'];
						$img = wp_get_attachment_image_src($mm_st2_custom_image_r,$item['mmst2_cir_thumbnail_size']);
						$mm_st2_custom_image_r = isset($img[0]) ? $img[0] : '';
					}
								
					$mm_st2_text_r= $item['mm_st2_text_r'];
					$mm_st2_link_r= $item['mm_st2_link_r'];					
					
					$title_a_start=$title_a_end='';
					/*st2 custom icon start*/
					if($mm_st2_icon_image_r=='mm_st2_icon_r'){
						ob_start();
							\Elementor\Icons_Manager::render_icon( $mm_st2_custom_icon_r , [ 'aria-hidden' => 'true' ]);
							$mm_st2_custom_icon_r = ob_get_contents();
						ob_end_clean();						
					}
					/*st2 custom icon end*/
					
					$mobile_menu='';
					/*st2 title & link start*/
					if ( ! empty( $item['mm_st2_link_r']['url'] ) ) {
						$mm_st2_link_r = $item['mm_st2_link_r']['url'];
					}
					if(!empty($item['mm_st2_text_r'])){						
						$mobile_menu .= '<span class="tp-mm-st1-title">'.wp_kses_post($item['mm_st2_text_r']).'</span>';
					}
					$j++;
					/*st2 title & link end*/					
					
					/*display st2 start*/
					if (!empty($item['mm_st2_link_r']['url'])){
						$title_a_start = '<a class="tp-menu-link tp-mm-normal" href="'.esc_url($mm_st2_link_r).'">';
						$title_a_end = '</a>';
					}else{
						$title_a_start = '<a class="tp-menu-link tp-mm-normal">';
						$title_a_end = '</a>';
					}
					$inner_class_loop='';
					if($settings['mm_extra_display_mode'] == 'columns'){						
						$inner_class_loop .= 'grid-item tp-mm-eq-col';
					}
					$loop2 .='<li class="tp-mm-li elementor-repeater-item-' . esc_attr($item['_id']) . ' '.esc_attr($inner_class_loop).' '.esc_attr($settings['mm_extra_indicator']).' '.esc_attr($indi_class).'"><div class="tp-loop-inner">'.$title_a_start.'';
						if(!empty($mm_st2_custom_icon_r)){
							$loop2 .= $mm_st2_custom_icon_r;
						}
						if(!empty($mm_st2_custom_image_r)){
							$loop2 .= '<img class="tp-mm-img tp-mm-st1-img" src='.esc_url($mm_st2_custom_image_r).' />';		
						}
						$loop2 .= $mobile_menu;							
					$loop2 .=''.$title_a_end.'</div></li>';
					/*display st2 end*/
				}		
			}
	
		$wrapper_main_class=$wrapper_class=$inner_class=$main_class=$et_class=$close_auto_class='';
		if($settings['mm_extra_display_mode'] == 'swiper'){			
			$wrapper_main_class .= ' swiper-container';
			$wrapper_class .= ' swiper-wrapper';
			$inner_class .= ' swiper-slide swiper-slide-active';				
		}else if($settings['mm_extra_display_mode'] == 'columns'){
			$inner_class .= 'tp-row';
			$main_class .= 'tp-column-base';
		}	
		if($mm_extra_toggle_switch=='yes'){
			$et_class .= ' tpet-on';
		}
		if(!empty($settings['tab_extra_toggle_close_position']) && $settings['tab_extra_toggle_close_position'] == 'mm-ci-auto'){
			$close_auto_class .= ' tp-mm-ca';
		}
		$mobile_menu_load ='<div class="tp-mobile-menu '.esc_attr($uid).' '.esc_attr($mm_style).' '.esc_attr($et_class).' '.esc_attr($main_class).' '.esc_attr($position_class).' '.esc_attr($settings['mobile_menu_pos_po']).' '.esc_attr($show_scroll_window_offset).' '.esc_attr($close_auto_class).'" '.$scroll_top_offset_value.' data-uid='.esc_attr($uid).' >';
		if($mm_style=='style_1'){
			$mobile_menu_load .='<div class="tp-mm-wrapper  '.esc_attr($wrapper_main_class).'">';
				$mobile_menu_load .='<div class="tp-mm-wrapper-inner '.esc_attr($wrapper_class).'">';
					$mobile_menu_load .='<ul class="tp-mm-ul '.esc_attr($inner_class).'">';
							$mobile_menu_load .= $loop;						
					$mobile_menu_load .='</ul>';
				$mobile_menu_load .='</div>';
			$mobile_menu_load .='</div>';
			if($mm_extra_toggle_switch=='yes'){
				$mobile_menu_load .='<div class="tp-mm-et-wrapper">';
					$mobile_menu_load .='<ul class="tp-mm-et-ul"><li class="tp-mm-et-li"><div class="tp-loop-inner">'.$extra_toggle.'</div></li></ul>';
				$mobile_menu_load .='</div>';
			}
		}else if($mm_style=='style_2'){
			$mobile_menu_load .='<div class="tp-mm-l-wrapper '.esc_attr($wrapper_main_class).'">';
				$mobile_menu_load .='<div class="tp-mm-l-wrapper-inner '.esc_attr($wrapper_class).'">';
					$mobile_menu_load .='<ul class="tp-mm-l-ul '.esc_attr($inner_class).'">';
							$mobile_menu_load .=$loop;						
					$mobile_menu_load .='</ul>';
				$mobile_menu_load .='</div>';
			$mobile_menu_load .='</div>';			
			
			if($mm_extra_toggle_switch=='yes'){	
				$mobile_menu_load .='<div class="tp-mm-c-wrapper">';
					$mobile_menu_load .='<ul class="tp-mm-c-et-ul"><li class="tp-mm-c-et-li"><div class="tp-loop-inner">'.$extra_toggle.'</div></li></ul>';
				$mobile_menu_load .='</div>';	
			}			
			
			$mobile_menu_load .='<div class="tp-mm-r-wrapper '.esc_attr($wrapper_main_class).'">';
				$mobile_menu_load .='<div class="tp-mm-r-wrapper-inner '.esc_attr($wrapper_class).'">';
					$mobile_menu_load .='<ul class="tp-mm-r-ul '.esc_attr($inner_class).'">';
							$mobile_menu_load .=$loop2;						
					$mobile_menu_load .='</ul>';
				$mobile_menu_load .='</div>';
			$mobile_menu_load .='</div>';
			
		}		
		$mobile_menu_load .='</div>';
		
		
		if(!empty($settings['open_mobile_menu']['size'])){
			$open_mobile_menu=($settings['open_mobile_menu']['size']).$settings['open_mobile_menu']['unit'];
			$close_mobile_menu=($settings['open_mobile_menu']['size']+1).$settings['open_mobile_menu']['unit'];			
			$css_rule ='@media (min-width:'.esc_attr($close_mobile_menu).'){.tp-mobile-menu.'.esc_attr($uid).'{display:none !important;}}';			
			$css_rule .='@media (max-width:'.esc_attr($open_mobile_menu).'){{.tp-mobile-menu.'.esc_attr($uid).'{display:flex;}}';
			echo '<style>'.esc_attr($css_rule).'</style>';
		}
	
		
		echo $mobile_menu_load;
	
	}

	protected function content_template() {
	
	}	
}