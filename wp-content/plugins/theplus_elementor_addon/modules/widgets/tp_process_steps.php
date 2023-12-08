<?php 
/*
Widget Name: Process/Steps
Description: Process/Steps
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
use TheplusAddons\Theplus_Element_Load;
use Elementor\Group_Control_Image_Size;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Process_Steps extends Widget_Base {
		
	public function get_name() {
		return 'tp-process-steps';
	}

    public function get_title() {
        return esc_html__('Process/Steps', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-ellipsis-h theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	public function get_keywords() {
		return ['process', 'steps', 'sequence','process bar'];
	}
	
    protected function register_controls() {
		/*process steps section start*/
		$this->start_controls_section(
			'section_process_steps',
			[
				'label' => esc_html__( 'Process/Steps', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'ps_style',
			[
				'label' => esc_html__( 'Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style_1',
				'options' => [
					'style_1'  => esc_html__( 'Vertical', 'theplus' ),					
					'style_2' => esc_html__( 'Horizontal', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'pro_ste_display_counter',
			[
				'label' => esc_html__( 'Display Counter', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'pro_ste_display_counter_style',
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
					'custom-text'  => esc_html__( 'Custom Text', 'theplus' ),
				],
				'condition'    => [
					'pro_ste_display_counter' => 'yes',					
				],
			]
		);
		$this->add_control(
			'pro_ste_display_special_bg',
			[
				'label' => esc_html__( 'Special Background', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'pro_ste_display_info_box',
			[
				'label' => esc_html__( 'Normal Layout In Mobile', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'ps_style' => 'style_2',					
				],				
			]
		);
		$this->add_responsive_control(
			'img_st2_align',
			[
				'label' => esc_html__( 'Circle Alignment', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Left', 'theplus' ),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'theplus' ),
						'icon' => 'eicon-text-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'Right', 'theplus' ),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2.mobile .tp-process-steps-wrapper .tp-ps-left-imt' => 'justify-content: {{VALUE}};',
				],
				'condition'    => [
					'ps_style' => 'style_2',					
					'pro_ste_display_info_box' => 'yes',					
				],
			]
		);	
		$this->add_responsive_control(
			'content_st2_align',
			[
				'label' => esc_html__( 'Content Alignment', 'theplus' ),
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
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'default' => 'center',				
				'condition'    => [
					'ps_style' => 'style_2',					
					'pro_ste_display_info_box' => 'yes',					
				],
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2.mobile .tp-process-steps-wrapper .tp-ps-right-content' => 'text-align: {{VALUE}};',
				],
			]
		);		
		$this->add_control(
			'default_active',
			[
				'label' => esc_html__( 'Default Active', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [					
					'0'  => esc_html__( '1', 'theplus' ),
					'1'  => esc_html__( '2', 'theplus' ),
					'2'  => esc_html__( '3', 'theplus' ),
					'3'  => esc_html__( '4', 'theplus' ),
					'4'  => esc_html__( '5', 'theplus' ),
					'5'  => esc_html__( '6', 'theplus' ),
					'6'  => esc_html__( '7', 'theplus' ),
					'7'  => esc_html__( '8', 'theplus' ),
					'8'  => esc_html__( '9', 'theplus' ),
					'9'  => esc_html__( '10', 'theplus' ),
					'50'  => esc_html__( 'None', 'theplus' ),
					'custom'  => esc_html__( 'Custom', 'theplus' ),
				],
			]
		);
		$this->add_control(
			'default_active_custom',
			[
				'label' => esc_html__( 'Custom', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 100,
				'step' => 1,
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'default_active' => 'custom',
				],
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'loop_title',
			[
				'label' => esc_html__( 'Title', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'dynamic' => ['active'   => true,],
			]
		);
		$repeater->add_control(
			'loop_content_desc',
			[
				'label' => esc_html__( 'Description', 'theplus' ),
				'type' => Controls_Manager::WYSIWYG,
				'default' => esc_html__('I am text block. Click edit button to change this text. Lorem ipsum dolor sit amet, consectetur adipiscing elit.', 'theplus' ),
			]
		);		
		$repeater->add_control(
			'loop_image_icon',
			[
			'label' => esc_html__( 'Select Icon', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'description' => esc_html__('You can select Icon, Custom Image or Text using this option.','theplus'),
				'default' => 'icon',
				'separator' => 'before',
				'options' => [
					''  => esc_html__( 'None', 'theplus' ),
					'icon' => esc_html__( 'Icon', 'theplus' ),
					'image' => esc_html__( 'Image', 'theplus' ),
					'text' => esc_html__( 'Text', 'theplus' ),
					'lottie' => esc_html__( 'Lottie', 'theplus' ),
				],
			]
		);
		$repeater->add_control(
			'lottieUrl',
			[
				'label' => esc_html__( 'Lottie URL', 'theplus' ),
				'type' => Controls_Manager::URL,				
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => ['loop_image_icon' => 'lottie'],
			]
		);
		$repeater->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Lottie Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'condition' => ['loop_image_icon' => 'lottie'],
			]
		);
		$repeater->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Lottie Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
				'condition' => ['loop_image_icon' => 'lottie'],
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
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'loop_image_icon' => 'image',
				],
			]
		);
		$repeater->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name' => 'thumbnail',
				'default' => 'full',
				'separator' => 'none',
				'separator' => 'after',
				'exclude' => [ 'custom' ],
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
				'type' => Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-star',
					'library' => 'solid',
				],
				'condition' => [
					'loop_image_icon' => 'icon',
					'loop_icon_style' => 'font_awesome',
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
			'loop_select_text',
			[
				'label' => esc_html__( 'Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'The Plus', 'theplus' ),
				'dynamic' => ['active'   => true,],
				'condition' => [
					'loop_image_icon' => 'text',
				],
			]
		);
		$repeater->add_control(
			'loop_icn_link',
			[
				'label' => esc_html__( 'Icon Link', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',								
			]
		);
		$repeater->add_control(
			'loop_url_link',
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
			'sep_pre_ste_background_n_head',
			[
				'label' => 'Normal Background Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sep_pre_ste_background',
				'types'     => [ 'classic', 'gradient' ],			
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper{{CURRENT_ITEM}} .tp-ps-left-imt .tp-ps-icon-img',
			]
		);
		$repeater->add_control(
			'sep_pre_ste_background_h_head',
			[
				'label' => 'Hover Background Option',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'sep_pre_ste_background_h',
				'types'     => [ 'classic', 'gradient' ],			
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover{{CURRENT_ITEM}} .tp-ps-left-imt .tp-ps-icon-img,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active{{CURRENT_ITEM}} .tp-ps-left-imt .tp-ps-icon-img',
			]
		);
		$repeater->add_control(
			'dis_counter_custom_text_head',
			[
				'label' => 'Display Counter Custom Text',
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',				
			]
		);
		$repeater->add_control(
			'dis_counter_custom_text',
			[
				'label' => esc_html__( 'Custom Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Step', 'theplus' ),
				'dynamic' => ['active'   => true,],				
			]
		);
		$this->add_control(
            'loop_content',
            [
				'label' => esc_html__( 'Process/Steps', 'theplus' ),
                'type' => Controls_Manager::REPEATER,
                'default' => [
                    [
                        'loop_title' => 'The Plus 1',                       
                    ],
					[
                        'loop_title' => 'The Plus 2',
                    ],
					[
                        'loop_title' => 'The Plus 3',
                    ],					
                ],
                'separator' => 'before',
				'fields' => $repeater->get_controls(),
                'title_field' => '{{{ loop_title }}}',				
            ]
        );
		$this->add_control(
			'connection_switch',
			[
				'label' => esc_html__( 'Carousel Anything Connection', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',				
			]
		);
		$this->add_control(
			'connection_unique_id',
			[
				'label' => esc_html__( 'Connection Carousel ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition' => [					
					'connection_switch' => 'yes',
				],				
			]
		);
		$this->add_control(
			'connection_hover_click',
			[
				'label' => esc_html__( 'Effect on', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'con_pro_hover',
				'options' => [
					'con_pro_hover'  => esc_html__( 'Hover', 'theplus' ),
					'con_pro_click' => esc_html__( 'Click', 'theplus' ),
				],
				'condition' => [					
					'connection_switch' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*process steps section start*/
		
		/* style section start*/
		/*title style start*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Title Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_control(
            'heading_tag', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Tag', 'theplus'),
                'default' => 'h6',
                'options' => [
                    'h1' => esc_html__('H1', 'theplus'),
                    'h2' => esc_html__('H2', 'theplus'),
                    'h3' => esc_html__('H3', 'theplus'),
                    'h4' => esc_html__('H4', 'theplus'),
                    'h5' => esc_html__('H5', 'theplus'),
                    'h6' => esc_html__('H6', 'theplus'),
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'title_typography',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-title',
			]
		);
		$this->add_control(
			'title_text_color_n',
			[
				'label' => esc_html__( 'Text Color Normal', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_text_color_h',
			[
				'label' => esc_html__( 'Text Color Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-content .tp-pro-step-title,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-content .tp-pro-step-title' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*title style end*/
		
		/*description style start*/
		$this->start_controls_section(
            'section_description_styling',
            [
                'label' => esc_html__('Description Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'description_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-pro-step-desc, .tp-process-steps-widget.style_1 .tp-pro-step-desc p' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'description_typography',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-desc,{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-desc p,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-desc span',
			]
		);
		$this->add_control(
			'title_description_color_n',
			[
				'label' => esc_html__( 'Description Color Normal', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-desc,{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-content .tp-pro-step-desc p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'title_description_color_h',
			[
				'label' => esc_html__( 'Description Color Hover', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-content .tp-pro-step-desc,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-content .tp-pro-step-desc,{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-content .tp-pro-step-desc p,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-content .tp-pro-step-desc p' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*description style end*/
		
		/*Icon/Image style start*/
		$this->start_controls_section(
            'section_icon_image_styling',
            [
                'label' => esc_html__('Icon/Image Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		
		$this->add_control(
			'tab_icon_heading',
			[
				'label' => esc_html__( 'Icon Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$this->add_responsive_control(
            'tab_icon_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Icon Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-icon-img i' => 'font-size:{{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-icon-img svg' => 'width: {{SIZE}}{{UNIT}};height:{{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_tab_icon' );
		$this->start_controls_tab(
			'tab_tab_icon_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_icon_color_n',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tab_icon_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_icon_color_h',
			[
				'label' => esc_html__( 'Icon Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img i,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img svg,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img svg' => 'fill: {{VALUE}};',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		
		$this->add_control(
			'tab_image_heading',
			[
				'label' => esc_html__( 'Image Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'tab_image_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 500,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img .tp-icon-img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
			'tab_image_border_radius_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-ps-icon-img.tp-pro-step-icon-img .tp-icon-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		
		$this->add_control(
			'tab_text_heading',
			[
				'label' => esc_html__( 'Text Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'tab_text_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img .tp-ps-text',
			]
		);
		$this->start_controls_tabs( 'tabs_tab_text' );
		$this->start_controls_tab(
			'tab_tab_text_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_text_color_n',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img .tp-ps-text' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tab_text_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'tab_text_color_h',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img .tp-ps-text,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img .tp-ps-text' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		$this->add_control(
			'tab_bg_heading',
			[
				'label' => esc_html__( 'Background Options', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
            'tab_bg_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Background Size', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 500,
						'step' => 2,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 90,
				],
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img' => 'width: {{SIZE}}{{UNIT}};height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:after' => 'width: calc({{SIZE}}{{UNIT}} + 20px);height:calc({{SIZE}}{{UNIT}} + 20px);',
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:before' => 'width: calc({{SIZE}}{{UNIT}} + 40px);height:calc({{SIZE}}{{UNIT}} + 40px);',
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt:after,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper .tp-ps-left-imt:after' => 'left:calc(({{SIZE}}{{UNIT}} /2 ) - ({{seprator_border_width_n.SIZE}}px));',
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt' => 'margin-right: calc(({{SIZE}}{{UNIT}}/1.3));',
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-right-content' => 'width: calc((100% - ({{SIZE}}{{UNIT}} * 2)));',
				],
            ]
        );
		$this->add_responsive_control(
            'pro_ste_minimum_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Minimum Height of Content', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
				'separator' => 'before',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper,{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt:after,{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper' => 'min-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .tp-process-steps-widget.style_2.mobile .tp-process-steps-wrapper,{{WRAPPER}} .tp-process-steps-widget.style_2.mobile .tp-process-steps-wrapper .tp-ps-left-imt:after' => 'min-width: {{SIZE}}{{UNIT}};',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_tab_bg' );
		$this->start_controls_tab(
			'tab_tab_bg_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_bg_background_n',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_bg_border_n',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img',
			]
		);
		$this->add_responsive_control(
			'tab_bg_border_radius_n',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img:first-child,
					{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_bg__shadow_n',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img:first-child,
				{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-special-bg:after',
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_tab_bg_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'tab_bg_background_h',
				'label' => esc_html__( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'tab_bg_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img',
			]
		);
		$this->add_responsive_control(
			'tab_bg_border_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img:first-child,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-special-bg:after,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img:first-child,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-special-bg:after' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'tab_bg__shadow_h',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img:first-child,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-special-bg:after,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img:first-child,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-special-bg:before,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-special-bg:after',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		
		
		$this->start_controls_tabs( 'tabs_transform' );
		$this->start_controls_tab(
			'tab_transform',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'transform_n',
			[
				'label' => esc_html__( 'Transform css', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img .tp-ps-text,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img .tp-icon-img,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img i' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;-webkit-transition: all .3s ease-in-out;
					-moz-transition: all .3s ease-in-out;-o-transition: all .3s ease-in-out;transition: all .3s ease-in-out;'
				],
			]
		);
		$this->add_control(
			'overlay_color_n',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-icon-img' => 'box-shadow: {{VALUE}} 0 0 0 100px inset;',
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_transform_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'transform_h',
			[
				'label' => esc_html__( 'Transform css', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'rotate(10deg) scale(1.1)', 'theplus' ),
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img .tp-ps-text,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img .tp-icon-img,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-icon-img i,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img .tp-ps-text,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img .tp-icon-img,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-icon-img i' => 'transform: {{VALUE}};-ms-transform: {{VALUE}};-moz-transform: {{VALUE}};-webkit-transform: {{VALUE}};transform-style: preserve-3d;-ms-transform-style: preserve-3d;-moz-transform-style: preserve-3d;-webkit-transform-style: preserve-3d;'
				],
			]
		);
		$this->add_control(
			'overlay_color_h',
			[
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-icon-img,
					{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-icon-img' => 'box-shadow: {{VALUE}} 0 0 0 100px inset;',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->add_control(
			'tab_bg_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'tab_bg_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'tab_bg_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'tab_bg_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-icon-img' => '-webkit-backdrop-filter:grayscale({{tab_bg_bf_grayscale.SIZE}})  blur({{tab_bg_bf_blur.SIZE}}{{tab_bg_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{tab_bg_bf_grayscale.SIZE}})  blur({{tab_bg_bf_blur.SIZE}}{{tab_bg_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'tab_bg_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();
		/*Icon/Image style end*/
		/*lottie style start*/
		$this->start_controls_section(
            'section_lottie_styling',
            [
                'label' => esc_html__('Lottie', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control(
			'lottieWidth',
			[
				'label' => esc_html__( 'Width', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
			]
		);
		$this->add_responsive_control(
			'lottieHeight',
			[
				'label' => esc_html__( 'Height', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 700,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 40,
				],
			]
		);
		$this->add_responsive_control(
			'lottieSpeed',
			[
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 10,
                        'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
			]
		);
		$this->add_control(
			'lottieLoop',
			[
				'label' => esc_html__( 'Loop Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'lottiehover',
			[
				'label' => esc_html__( 'Hover Animation', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*lottie style end*/
		/*Separator/Line style start*/
		$this->start_controls_section(
            'section_seprator_styling',
            [
                'label' => esc_html__('Separator/Line Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );			
		$this->start_controls_tabs( 'tabs_seprator' );
		$this->start_controls_tab(
			'tab_seprator_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'seprator_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt:after,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-ps-left-imt:before,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper .tp-ps-left-imt:after' => 'border-color:{{VALUE}};',
				],
			]
		);
	
		$this->add_control(
			'seprator_border_style_n',
			[
				'label' => esc_html__( 'Border Type', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
                    'solid' => esc_html__('Solid', 'theplus'),
                    'dashed' => esc_html__('Dashed', 'theplus'),
                    'dotted' => esc_html__('Dotted', 'theplus'),
                    'groove' => esc_html__('Groove', 'theplus'),
                    'inset' => esc_html__('Inset', 'theplus'),
                    'outset' => esc_html__('Outset', 'theplus'),
                    'ridge' => esc_html__('Ridge', 'theplus'),
                    'border_img_custom' => esc_html__('Custom', 'theplus'),
                ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt:after,
{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-ps-left-imt:before,
{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper .tp-ps-left-imt:after' => 'border-style: {{VALUE}};',
				],				
			]
		);
		$this->add_responsive_control(
            'seprator_border_width_n',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Border Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper .tp-ps-left-imt:before,
					{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt:after' => 'border-width: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [					
					'seprator_border_style_n!' => 'border_img_custom',
				],
            ]
        );
		$this->add_control(
			'seprator_cusom_img',
			[
				'label' => esc_html__( 'Separator/Line Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'media_type' => 'image',
				'default' => [
					'url' => '',
				],
				'condition' => [
					'seprator_border_style_n' => 'border_img_custom',					
				],
			]
		);
		$this->add_responsive_control(
            'seprator_main_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator/Line Offset', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_1.tp_ps_sep_img .tp-sep-custom-img-inner,
					{{WRAPPER}} .tp-process-steps-widget.style_2.tp_ps_sep_img .tp-sep-custom-img-inner' => 'left: {{SIZE}}{{UNIT}} !important;position:relative;',
				],
				'condition' => [					
					'seprator_border_style_n' => 'border_img_custom',
				],
            ]
        );
		$this->add_responsive_control(
            'seprator_main_size',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator/Line Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [					
					'{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper .tp-ps-left-imt:before' => 'width: {{SIZE}}{{UNIT}} !important; right: calc((-{{SIZE}}{{UNIT}} / 2) - 10px)!important;',
					'{{WRAPPER}} .tp-process-steps-widget.style_1.tp_ps_sep_img .tp-sep-custom-img-inner' => 'max-height: {{SIZE}}{{UNIT}} !important;',
				],
				'condition' => [
					'ps_style!' => 'style_1',					
					'seprator_border_style_n!' => 'border_img_custom',					
				],
            ]
        );
		$this->add_responsive_control(
            'seprator_img_height_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Image Maximum Size', 'theplus'),
				'size_units' => [ 'px','%' ],
				'range' => [
					'px' => [
						'min' => 1,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
						'step' => 1,
					],
				],
				'separator' => 'after',
				'render_type' => 'ui',
				'selectors' => [					
					'{{WRAPPER}} .tp-process-steps-widget.style_1.tp_ps_sep_img .tp-sep-custom-img-inner' => 'max-height: {{SIZE}}{{UNIT}} !important;',
					'{{WRAPPER}} .tp-process-steps-widget.style_2.tp_ps_sep_img .tp-sep-custom-img-inner' => 'width: {{SIZE}}{{UNIT}} !important;height:auto !important;max-width: {{SIZE}}{{UNIT}} !important;'
				],
				'condition' => [
					'seprator_border_style_n' => 'border_img_custom',					
				],
            ]
        );
		
		$this->add_responsive_control(
			'seprator_cusom_img_button_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget.tp_ps_sep_img .tp-process-steps-wrapper .separator_custom_img img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
				'condition' => [
					'seprator_border_style_n' => 'border_img_custom',					
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_seprator_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'seprator_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper:hover .tp-ps-left-imt:after,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper:hover .tp-ps-left-imt:before,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper:hover .tp-ps-left-imt:after,
					{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper.active .tp-ps-left-imt:after,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper.active .tp-ps-left-imt:before,
					{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper.active .tp-ps-left-imt:after' => 'border-color:{{VALUE}};',
				],
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*Separator/Line style end*/
		
		/*display counter start*/
		$this->start_controls_section(
            'section_display_counter_styling',
            [
                'label' => esc_html__('Display Counter Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'pro_ste_display_counter' => 'yes',					
				],
            ]
        );
		$this->add_responsive_control(
			'display_counter_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-dc:after' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'pro_ste_display_counter_style' => ['custom-text','number-normal']
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
            'display_counter_left_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -300,
						'max' => 300,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-dc' => 'margin-left: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'display_counter_top_offset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Top Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
						'step' => 1,
					],
				],				
				'render_type' => 'ui',
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-ps-left-imt .tp-ps-dc' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'display_counter_typography',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				'separator' => 'before',
			]
		);		
		$this->start_controls_tabs( 'tabs_display_counter' );
		$this->start_controls_tab(
			'tab_display_counter_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'display_counter_color',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'display_counter_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'display_counter_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'display_counter_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
					{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'display_counter_shadow',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper .tp-ps-dc.dc_custom_text .ds_custom_text_label',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_display_counter_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'display_counter_color_h',
			[
				'label' => esc_html__( 'Text Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-dc.dc_custom_text .ds_custom_text_label' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'display_counter_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'display_counter_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'display_counter_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-dc:after,
					{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-dc:after,
					{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-dc.dc_custom_text .ds_custom_text_label' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'display_counter_shadow_h',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper:hover .tp-ps-dc.dc_custom_text .ds_custom_text_label,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-left-imt .tp-ps-dc:after,
				{{WRAPPER}} .tp-process-steps-wrapper.active .tp-ps-dc.dc_custom_text .ds_custom_text_label',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();
		/*display counter end*/
		
		/*Content Background Style start*/
		$this->start_controls_section(
            'section_content_bg_styling',
            [
                'label' => esc_html__('Content Background Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
            ]
        );
		$this->add_responsive_control(
			'content_bg_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
			]
		);
		$this->add_responsive_control(
			'content_bg_margin_st2',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2  .tp-process-steps-wrapper .tp-ps-right-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'after',
				'condition' => [
					'ps_style' => 'style_2',					
				],
			]
		);
		$this->add_responsive_control(
            'content_bg_margin_right',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Left Content Right Margin', 'theplus'),
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
					'{{WRAPPER}} .tp-process-steps-widget.style_1 .tp-process-steps-wrapper .tp-ps-left-imt' => 'margin-right: {{SIZE}}{{UNIT}} !important',
				],
				'condition' => [
					'ps_style' => 'style_1',					
				],
            ]
        );		
		$this->start_controls_tabs( 'tabs_content_bg' );
		$this->start_controls_tab(
			'tab_content_bg_n',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_bg_border',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'content_bg_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_bg_shadow',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_content_bg_h',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'content_background_h',
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-right-content,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-right-content',
			]
		);
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'content_bg_border_h',
				'label' => esc_html__( 'Border', 'theplus' ),
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-right-content,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-right-content',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'content_bg_radius_h',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-right-content,
					{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-right-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',				
				],	
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_bg_shadow_h',
				'selector' => '{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper:hover .tp-ps-right-content,
				{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper.active .tp-ps-right-content',				
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'content_bg_bf',
			[
				'label' => esc_html__( 'Backdrop Filter', 'theplus' ),
				'type' => Controls_Manager::POPOVER_TOGGLE,
				'label_off' => __( 'Default', 'theplus' ),
				'label_on' => __( 'Custom', 'theplus' ),
				'return_value' => 'yes',
			]
		);
		$this->add_control(
			'content_bg_bf_blur',
			[
				'label' => esc_html__( 'Blur', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 100,
						'min' => 1,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 10,
				],
				'condition'    => [
					'content_bg_bf' => 'yes',
				],
			]
		);
		$this->add_control(
			'content_bg_bf_grayscale',
			[
				'label' => esc_html__( 'Grayscale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget .tp-process-steps-wrapper .tp-ps-right-content' => '-webkit-backdrop-filter:grayscale({{content_bg_bf_grayscale.SIZE}})  blur({{content_bg_bf_blur.SIZE}}{{content_bg_bf_blur.UNIT}}) !important;backdrop-filter:grayscale({{content_bg_bf_grayscale.SIZE}})  blur({{content_bg_bf_blur.SIZE}}{{content_bg_bf_blur.UNIT}}) !important;',
				 ],
				'condition'    => [
					'content_bg_bf' => 'yes',
				],
			]
		);
		$this->end_popover();
		$this->end_controls_section();

		$this->start_controls_section( 'Horizontal_bg_section',
            [
                'label' => esc_html__( 'Horizontal Background', 'theplus' ),
                'tab' => Controls_Manager::TAB_STYLE,	
				'condition' => [
					'ps_style' => 'style_2',	
				],			
            ]
        );
		$this->add_responsive_control( 'Horizontal_bg_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ps_style' => 'style_2',				
				],
			]
		);
		$this->add_responsive_control( 'Horizontal_bg_margin_st22',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em'],				
				'selectors' => [
					'{{WRAPPER}} .tp-process-steps-widget.style_2 .tp-process-steps-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'ps_style' => 'style_2',					
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

	}
    protected function render() {
		$settings = $this->get_settings_for_display();
		
		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';
		/*--Plus Extra ---*/

		$pro_ste_display_counter_style = $settings['pro_ste_display_counter_style'];		
		$uid=uniqid('proste');
		
		$display_counter_class=$display_special_bg=$responsive_class=$seprator_cusom_img_class='';
		
		if($pro_ste_display_counter_style == 'number-normal'){
			$display_counter_class = 'number_normal';
		}else if($pro_ste_display_counter_style == 'decimal-leading-zero'){
			$display_counter_class = 'decimal_leading_zero';
		}else if($pro_ste_display_counter_style == 'upper-alpha'){
			$display_counter_class = 'upper_alpha';
		}else if($pro_ste_display_counter_style == 'lower-alpha'){
			$display_counter_class = 'lower_alpha';
		}else if($pro_ste_display_counter_style == 'lower-roman'){
			$display_counter_class = 'lower_roman';
		}else if($pro_ste_display_counter_style == 'upper-roman'){
			$display_counter_class = 'upper_roman';
		}else if($pro_ste_display_counter_style == 'lower-greek'){
			$display_counter_class = 'lower_greek';
		}else if($pro_ste_display_counter_style == 'custom-text'){
			$display_counter_class = 'dc_custom_text';
		}
		
		if(!empty($settings['seprator_border_style_n']) && $settings['seprator_border_style_n'] == 'border_img_custom'){
			$seprator_cusom_img_class = 'tp_ps_sep_img';
		}
		
		if(!empty($settings['pro_ste_display_special_bg']) && $settings['pro_ste_display_special_bg'] == 'yes'){
			$display_special_bg = 'tp-ps-special-bg';
		}
		$mobile_class='';
		if(!empty($settings['pro_ste_display_info_box']) && $settings['pro_ste_display_info_box']=='yes'){
			$mobile_class = 'mobile';
		}
		
		$connect_carousel =$connection_hover_click='';
		if(!empty($settings["connection_unique_id"])){
			$connect_carousel='tpca_'.$settings["connection_unique_id"];
			$uid="tptab_".$settings["connection_unique_id"];
			$connection_hover_click=$settings["connection_hover_click"];
		}
			
		if(!empty($settings["loop_content"])) {
			$output = '<div id="'.esc_attr($uid).'" class="tp-process-steps-widget '.esc_attr($settings['ps_style']).' '.esc_attr($seprator_cusom_img_class).' '.esc_attr($mobile_class).' '.esc_attr($animated_class).'" '.$animation_attr.' data-connection="'.esc_attr($connect_carousel).'" data-eventtype="'.esc_attr($connection_hover_click).'">';	
				$loop_content=$settings["loop_content"];
				$index=0;					
				foreach($loop_content as $index => $item) {
					$ps_count = $index;
					$on_load_class='';
					$default_active=$settings['default_active'];
					
					if((!empty($settings['default_active']) && $settings['default_active']=='custom') && (!empty($settings['default_active_custom']) && $ps_count==($settings['default_active_custom'] - 1))){							
						$on_load_class = 'active';
					}else if($ps_count==$default_active && $default_active !='custom'){							
						$on_load_class = 'active';		
					}
					$list_title=$description=$title_a_start=$title_a_end=$list_img='';
					
					/*link*/
					if ( ! empty( $item['loop_url_link']['url'] ) ) {
						$this->add_render_attribute( 'loop_box_link'.$index, 'href', $item['loop_url_link']['url'] );
						if ( $item['loop_url_link']['is_external'] ) {
							$this->add_render_attribute( 'box_link'.$index, 'target', '_blank' );
						}
						if ( $item['loop_url_link']['nofollow'] ) {
							$this->add_render_attribute( 'box_link'.$index, 'rel', 'nofollow' );
						}
					}
					/*link*/
					
					/*tile*/
					if(!empty($item['loop_title'])){							
						if (!empty($item['loop_url_link']['url'])){
							$title_a_start = '<a '.$this->get_render_attribute_string( "loop_box_link".$index ).'>';
							$title_a_end = '</a>';
						}
						
						$heading_tag = !empty($settings['heading_tag']) ? $settings['heading_tag'] : 'h6';
						$list_title = $title_a_start.'<'.theplus_validate_html_tag($heading_tag).' class="tp-pro-step-title">'.$item['loop_title'].'</'.theplus_validate_html_tag($heading_tag).'>'.$title_a_end;							
					}
					/*tile*/
					
					/*description*/					
					if(!empty($item['loop_content_desc'])){
							$description='<div class="tp-pro-step-desc"> '.wp_kses_post($item['loop_content_desc']).' </div>';
					}	
					/*description*/
					
					/*icon-image-text*/
					if(!empty($item['loop_image_icon'])){
						/*image*/
						if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'image'){
							$loop_imgSrc1='';									
								if(!empty($item["loop_select_image"]["url"])){
									$loop_select_image=$item['loop_select_image']['id'];										
									$loop_imgSrc1 = tp_get_image_rander( $loop_select_image,$item['thumbnail_size'], [ 'class' => 'tp-icon-img' ] );
								}
								$list_img ='<div class="tp-ps-icon-img tp-pro-step-icon-img" >'.$loop_imgSrc1.'</div>';
						}
						/*image*/							
						/*icon*/
						else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'icon'){		
							if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='icon_mind'){
								$list_img='<i class=" '.esc_attr($item["loop_icons_mind"]).' tp-icon-fi" ></i>';
							}else{
								$list_img='';
							}
						}
						/*icon*/
						/*text*/
						else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'text'){
							$list_img='<span class="tp-ps-text">'.esc_attr($item['loop_select_text']).'</span>';
						}
						/*lottie*/
						else if(isset($item['loop_image_icon']) && $item['loop_image_icon'] == 'lottie'){
							$ext = pathinfo($item['lottieUrl']['url'], PATHINFO_EXTENSION);			
							if($ext!='json'){
								$list_img .= '<h3 class="theplus-posts-not-found">'.esc_html__("Opps!! Please Enter Only JSON File Extension.",'theplus').'</h3>';
							}else{
								$lottieWidth = isset($settings['lottieWidth']['size']) ? $settings['lottieWidth']['size'] : 40;
								if(!empty($item['lottieWidth']['size'])){
									$lottieWidth = isset($item['lottieWidth']['size']) ? $item['lottieWidth']['size'] : 40;
								}
								
								$lottieHeight = isset($settings['lottieHeight']['size']) ? $settings['lottieHeight']['size'] : 40;
								if(!empty($item['lottieHeight']['size'])){
									$lottieHeight = isset($item['lottieHeight']['size']) ? $item['lottieHeight']['size'] : 40;
								}
								
								$lottieSpeed = isset($settings['lottieSpeed']['size']) ? $settings['lottieSpeed']['size'] : 1;
								$lottieLoop = isset($settings['lottieLoop']) ? $settings['lottieLoop'] : 'no';
								$lottiehover = isset($settings['lottiehover']) ? $settings['lottiehover'] : 'no';
								$lottieLoopValue='';
								if(!empty($settings['lottieLoop']) && $settings['lottieLoop']=='yes'){
									$lottieLoopValue ='loop'; 
								}
								$lottieAnim='autoplay';
								if(!empty($settings['lottiehover']) && $settings['lottiehover']=='yes'){
									$lottieAnim ='hover'; 
								}
								$list_img .= '<lottie-player src="'.esc_url($item['lottieUrl']['url']).'" style="width: '.esc_attr($lottieWidth).'px; height: '.esc_attr($lottieHeight).'px;" '.esc_attr($lottieLoopValue).'  speed="'.esc_attr($lottieSpeed).'" '.esc_attr($lottieAnim).'></lottie-player>';
							}
						}
					}
					/*icon-image-text*/
					if(!empty($item["loop_icon_style"]) && $item["loop_icon_style"]=='font_awesome'){
						ob_start();
						\Elementor\Icons_Manager::render_icon( $item['loop_icon_fontawesome'], [ 'aria-hidden' => 'true' ]);
						$list_img = ob_get_contents();
						ob_end_clean();						
					}
					
					$display_counter='';
					if(!empty($settings['pro_ste_display_counter']) && $settings['pro_ste_display_counter']=='yes'){
						$display_counter = '<div class="tp-ps-dc '.esc_attr($display_counter_class).'">';
							if($settings['pro_ste_display_counter']=='yes' && $settings['pro_ste_display_counter_style']=='custom-text'){
								$display_counter .= '<span class="ds_custom_text_label">'.esc_attr($item['dis_counter_custom_text']).'</span>';
							}
						$display_counter .= '</div>';
					}
					
					$dis_sep_custom_img='';
					if(!empty($settings['seprator_border_style_n']) && $settings['seprator_border_style_n'] == 'border_img_custom'){	
						if(!empty($settings['seprator_cusom_img']['url'])){
							$seprator_cusom_img=$settings["seprator_cusom_img"]["id"];
							$sepimg1= tp_get_image_rander( $seprator_cusom_img,'full', [ 'class' => 'tp-sep-custom-img-inner' ] );
							$dis_sep_custom_img='<span class="separator_custom_img">'.$sepimg1.'</span>';
						}
					}						
				
					$output .= '<div class="tp-process-steps-wrapper elementor-repeater-item-' .esc_attr($item['_id']) . ' elementor-ps-content-'.esc_attr($ps_count).' '.esc_attr($on_load_class).'" data-index="'.esc_attr($ps_count).'">';
						if(!empty($settings['ps_style'])){
							if($settings['ps_style']=='style_1' || $settings['ps_style']=='style_2'){							
								$output .= '<div class="tp-ps-left-imt '.esc_attr($display_special_bg).'">';	
												
								$icn_a_start = '<span class="tp-ps-icon-img '.esc_attr($display_special_bg).'">';
								$icn_a_end = '</span>';
								if(!empty($item['loop_icn_link'] && $item['loop_icn_link'] == 'yes')){					
									if (!empty($item['loop_url_link']['url'])){
										$icn_a_start = '<a class="tp-ps-icon-img '.esc_attr($display_special_bg).'" '.$this->get_render_attribute_string( "loop_box_link".$index ).'>';
										$icn_a_end = '</a>';
									}
								}
								if(!empty($list_img)){
									$output .= $dis_sep_custom_img.$icn_a_start.$list_img.$icn_a_end.$display_counter;
								}
								$output .= '</div>';									
								$output .= '<div class="tp-ps-right-content">';
									$output .= '<span class="tp-ps-content">'.$list_title.' '.$description.'</span>';
								$output .= '</div>';
							}
						}
					$output .= '</div>';
					$index++;
				}
				
			$output .= '</div>';				
			echo $before_content.$output.$after_content;
		}
	}
}