<?php 
/*
Widget Name: Page Scroll
Description: Page Scroll
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Page_Scroll extends Widget_Base {
		
	
	public function get_name() {
		return 'tp-page-scroll';
	}

    public function get_title() {
        return esc_html__('Page Scroll', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-rocket theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-creatives');
    }
	public function get_keywords() {
		return ['one page scroll', 'full page js', 'page piling', 'page pilling', 'multi scroll', 'page scroll', 'scroll'];
	}
    protected function register_controls() {
		$this->start_controls_section(
			'section_page_scroll',
			[
				'label' => esc_html__( 'Page Scroll', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
			'page_scroll_opt',
			[
				'label' => esc_html__( 'Option', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'tp_full_page',
				'options' => [
					'tp_full_page'  => esc_html__( 'Full Page', 'theplus' ),
					'tp_page_pilling'  => esc_html__( 'Page Piling', 'theplus' ),					
					'tp_multi_scroll'  => esc_html__( 'Multi Scroll', 'theplus' ),
					'tp_horizontal_scroll'  => esc_html__( 'Horizontal Scroll', 'theplus' ),
				],
			]
		);
		$this->end_controls_section();
		
		/*full page & page pilling content start*/
		$this->start_controls_section(
			'full_pagepilling_content_templates',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'		=> [
					'page_scroll_opt' => ['tp_page_pilling','tp_full_page'],
				],
			]
		);
		$this->add_control(
			'fit_screen_note',
			[
				'label' => esc_html__( 'Make sure your templates have full width On and It will suitable to screen height.', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);
		$repeater = new \Elementor\Repeater();
		$repeater->add_control(
			'fp_content_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
			]
		);
		$repeater->add_control(
			'fp-slideid',
			[
				'label' => esc_html__( 'Slide Id', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'slideid', 'theplus' ),
			]
		);
		$this->add_control(
			'fp_content',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
			]
		);
		$this->end_controls_section();
		/*full page & page pilling content end*/
		/*Horizontal Scroll content end*/
		$this->start_controls_section(
			'hscroll_content_template',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'		=> [
					'page_scroll_opt' => 'tp_horizontal_scroll',
				],
			]
		);
		$this->add_control(
			'hscroll_screen_note',
			[
				'label' => esc_html__( 'We recommend you to make "Full height" from elementor template to have proper look in all screens.', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
			]
		);
		$hscroll_repeater = new \Elementor\Repeater();
		$hscroll_repeater->add_control(
			'hslide_template',
			[
				'label'       => esc_html__( 'Elementor Templates', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => 'true',
			]
		);
		
		$hscroll_repeater->add_control(
            'hslide_id', [
                'label' => esc_html__('Slide/Anchor ID', 'theplus'),
                'type' => Controls_Manager::TEXT,
                'label_block' => true,
            ]
        );
		$hscroll_repeater->add_control(
            'hslide_width',
            [
                'label' => esc_html__('Slide Width', 'theplus'),
                'type' => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'rem', 'vw'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 10,
                    ],
					'vw' => [
                        'min' => 0,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                    'rem' => [
                        'min' => 0,
                        'max' => 100,
                        'step' => 1,
                    ],
                ],
                'default' => [
                    'unit' => 'vw',
                    'size' => 100,
                ],
				'separator' => 'before',
            ]
        );
		$hscroll_repeater->add_control(
            'hslide_align',
            [
                'label' => esc_html__('Slide Align', 'theplus'),
                'type' => \Elementor\Controls_Manager::CHOOSE,
                'options' => [
                    'top' => [
                        'title' => esc_html__('Top', 'theplus'),
                        'icon' => 'eicon-v-align-top',
                    ],
                    'center' => [
                        'title' => esc_html__('Middle', 'theplus'),
                        'icon' => 'eicon-v-align-middle',
                    ],
                    'bottom' => [
                        'title' => esc_html__('Bottom', 'theplus'),
                        'icon' => 'eicon-v-align-bottom',
                    ],
                ],
                'default' => 'center',
                'toggle' => false,
            ]
        );
		$hscroll_repeater->add_control(
            'hslide_padding',
            [
                'label' => esc_html__('Slide Padding', 'theplus'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'isLinked' => true,
                ],
				'selectors' => [
					'{{WRAPPER}}.tp-page-scroll-wrapper .tp_hscroll_slide{{CURRENT_ITEM}}' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
            ]
        );

        $hscroll_repeater->add_control(
            'hslide_margin',
            [
                'label' => esc_html__('Slide Margin', 'theplus'),
                'type' => Controls_Manager::DIMENSIONS,
                'default' => [
                    'top' => 'auto',
                    'right' => '',
                    'bottom' => 'auto',
                    'left' => '',
                    'isLinked' => true,
                ],
                'allowed_dimensions' => 'horizontal',
				'selectors' => [
					'{{WRAPPER}}.tp-page-scroll-wrapper .tp_hscroll_slide{{CURRENT_ITEM}}' => 'margin: auto {{RIGHT}}{{UNIT}} auto {{LEFT}}{{UNIT}}',
				],
            ]
        );
		$hscroll_repeater->add_control(
			'hscroll_bg_scrolling',
			[
				'label' => esc_html__( 'Scroll page section change background on view section', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$hscroll_repeater->add_control(
			'scroll_bg_color', [
				'label' => esc_html__('Background Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
			]
		);
		$hscroll_repeater->add_control(
			'scroll_bg_image', [
				'label' => 'Background Image <b>(Works only in On Scroll Background Animation)</b>',
				'type' => Controls_Manager::MEDIA,
				'dynamic' => ['active'   => true,],
				'default' => [ 'url' => '',	],
				'separator' => 'before',
			]
		);
		$hscroll_repeater->add_control(
			'hscroll_overlay_bg_scrolling',
			[
				'label' => esc_html__( 'Overlay Background Scroll Bg Image', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$hscroll_repeater->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'scroll_bg_overlay',
				'label' => esc_html__( 'Overlay Color On Image', 'theplus' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '.scroll{{ID}} {{CURRENT_ITEM}}.plus-section-bg-scrolling:after',
				'description' => 'Overlay Color On Image <b>(Works only in On Scroll Background Animation)</b>',
			]
		);
		$this->add_control(
			'hscroll_content',
			[
				'label' => '',
				'type' => Controls_Manager::REPEATER,
				'fields' => $hscroll_repeater->get_controls(),
				'title_field' => '{{{ hslide_id }}}',
			]
		);
		$this->add_control(
            'hscroll_bg',
            [
                'label' => esc_html__('Horizontal Scroll Background', 'theplus'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('On', 'theplus'),
                'label_off' => esc_html__('Off', 'theplus'),                
				'separator'	=> 'before',
            ]
        );
		$this->add_control(
			'hscroll_page_full',
			[
				'label' => esc_html__( 'Position', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'inherit',
				'options' => [
					'inherit'  => esc_html__( 'Inherit', 'theplus' ),
					'relative' => esc_html__( 'Relative', 'theplus' ),
				],
				'description' => esc_html__( 'Note : Use this field, If your background isn\'t work the way it should be.', 'theplus' ),
				'condition' => [
					'hscroll_bg' => 'yes',
				],
			]
		);
		$this->add_control(
			'hscroll_bg_duration',
			[
				'label' => esc_html__( 'Background Transition Duration', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 's' ],
				'range' => [
					's' => [
						'min' => 0,
						'max' => 2,
						'step' => 0.1,
					],
				],
				'default' => [
					'unit' => 's',
					'size' => 0.7,
				],
				'condition' => [
					'hscroll_bg' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*Horizontal Scroll content end*/
		$this->start_controls_section(
            'settings_section',
            [
                'label' => esc_html__('Extra Options', 'theplus'),
                'tab' => Controls_Manager::TAB_CONTENT,
				'condition'		=> [
					'page_scroll_opt' => 'tp_horizontal_scroll',
				],
            ]
        );

        $this->add_control(
            'hscroll_speed',
            [
                'label' => esc_html__('Horizontal Scroll Speed', 'theplus'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0.1,
                        'max' => 3.5,
                        'step' => 0.01,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 0.8,
                ],
            ]
        );
		$this->add_control(
            'hscroll_sec_id_transition',
            [
                'label' => esc_html__('Section Id/Anchor Transition Duration (ms)', 'theplus'),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 5000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => 700,
                ],
            ]
        );
		$this->add_control(
            'hscroll_draggable',
            [
                'label' => esc_html__('Mouse Draggable Scrollbar', 'theplus'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('On', 'theplus'),
                'label_off' => esc_html__('Off', 'theplus'),                
				'separator'	=> 'before',
            ]
        );
		$this->add_control(
            'hscroll_scrollbar',
            [
                'label' => esc_html__('Scrollbar Rail', 'theplus'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'yes',
                'label_on' => esc_html__('On', 'theplus'),
                'label_off' => esc_html__('Off', 'theplus'),                
				'separator'	=> 'before',
            ]
        );
		$this->add_control(
            'hscroll_footer',
            [
                'label' => esc_html__('Horizontal Scroll Footer', 'theplus'),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'no',
                'label_on' => esc_html__('On', 'theplus'),
                'label_off' => esc_html__('Off', 'theplus'),                
				'separator'	=> 'before',
            ]
        );
		$this->add_control(
			'hscroll_footer_height',
			[
				'label' => esc_html__( 'Footer Height', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],				
				'condition' => [
					'hscroll_footer' => 'yes',
				],
			]
		);		
		$this->add_control(
            'hscroll_responsive',
            [
                'label' => esc_html__('Horizontal Scroll Responsive', 'theplus'),
                'type' => Controls_Manager::SWITCHER,
                'default' => '',
                'label_on' => esc_html__('On', 'theplus'),
                'label_off' => esc_html__('Off', 'theplus'),                
				'separator'	=> 'before',
            ]
        );
		$this->add_control(
            'hscroll_res_minwidth',
            [
				'label' => esc_html__('Min Width', 'theplus'),
				'type' => Controls_Manager::SLIDER,                
                'description' => esc_html__('Enter Minimum Screen Width(px) from which you want to turn off Horizontal Scroll.', 'theplus'),
                'size_units' => ['px'],
                'range' => [
                    'px' => [
                        'min' => 0,
                        'max' => 2000,
                        'step' => 10,
                    ],
                ],
                'default' => [
                    'unit' => 'px',
                    'size' => '767',
                ],
                'condition' => [
                    'hscroll_responsive' => 'yes',
                ]
            ]
        );
		
		
        $this->end_controls_section();
		/*multi scroll content start*/
		$this->start_controls_section(
			'multi_scroll_content_templates',
			[
				'label' => esc_html__( 'Multi Scroll Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'		=> [
					'page_scroll_opt' => 'tp_multi_scroll',
				],
			]
		);
		$this->add_control(
			'template_full_height_text',
		  	[
                'label'         => esc_html__( 'Make sure your templates have full width On and It will suitable to screen height.', 'theplus' ),
		     	'type'          => Controls_Manager::RAW_HTML,		     	
		  	]
		);
		
		 $multiscroll_repeater = new REPEATER();
		 
		 $multiscroll_repeater->add_control(
			'fp-slideid',
			[
				'label' => esc_html__( 'Slide Id', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
				],				
				'placeholder' => esc_html__( 'Enter Slide Id', 'theplus' ),
			]
		);
		
		$multiscroll_repeater->add_control(
			'multi_left_template',
			[
				'label'       => esc_html__( 'Left Template', 'theplus' ),
				'type'        => Controls_Manager::SELECT,
				'default'     => '0',
				'options'     => theplus_get_templates(),
				'label_block' => false,
			]
		);		
		 $multiscroll_repeater->add_control(
			'multi_right_template',
		  	[
		     	'label'			=> esc_html__( 'Right Template', 'theplus' ),
		     	'type'          => Controls_Manager::SELECT2,
		     	'options'     => theplus_get_templates(),
		     	'label_block'      => false,
		  	]
		);
		 $this->add_control(
			'multi_left_right_repeater',
           [
               'label'          => esc_html__( 'Sections', 'theplus' ),
               'type'           => Controls_Manager::REPEATER,
               'fields' => $multiscroll_repeater->get_controls(),   
           ]
       );
        
        $this->end_controls_section();						
		
		/*full page dots settings start*/
		$this->start_controls_section('dots_settings',
            [
                'label'     => esc_html__('Dots', 'theplus'),
				'condition' => [
					'page_scroll_opt!' => 'tp_horizontal_scroll',
				],
            ]
        );		
		$this->add_control(
			'show_dots',
			[
				'label' => esc_html__( 'Dots', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				'description'   => esc_html__('Works only on the frontend', 'theplus'),
				'condition' => [
					'page_scroll_opt!' => 'tp_multi_scroll',
				],
			]
		);		
		$this->add_control('nav_postion',
            [
                'label'         => esc_html__('Dots Positions', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'right'   => esc_html__('Right', 'theplus'),
                    'left'   => esc_html__('Left', 'theplus'),
                ],
                'default'       => 'right',
				'condition' => [					
					'show_dots' => 'yes',
					'page_scroll_opt!' => 'tp_multi_scroll',
				],
            ]
        );
		$this->add_control('nav_dots_tooltips',
            [
                'label'         => esc_html__('Dots Tooltips Text', 'theplus'),
                'type'          => Controls_Manager::TEXT,
                'description'   => esc_html__('Add Multiple text separated by comma \',\'','theplus'),
                'condition'     => [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling'],
                    'show_dots'   => 'yes'					
                ]
            ]
        );
		/*navigation multi scroll*/
		
		$this->add_control(
			'multi_navigation_dots',
            [
                'label'         => esc_html__('Navigation Dots', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,
				'default'		=> 'yes',
				'condition'		=> [
					'page_scroll_opt' => 'tp_multi_scroll',
				],
                
            ]
        );		
		$this->add_control(
			'multi_navigation_dots_pos',
            [
                'label'         => esc_html__('Dots Horizontal Position', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'left'  => esc_html__('Left', 'theplus'),
                    'right' => esc_html__('Right', 'theplus'),
                ],
                'default'       => 'right',
                'condition'     => [
					'page_scroll_opt' => 'tp_multi_scroll',
                    'multi_navigation_dots'   => 'yes'
                ]
            ]
        );
		$this->add_control('multi_navigation_verti_dots_pos',
            [
                'label'         => esc_html__('Dots Vertical Position', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'top'   => esc_html__('Top', 'theplus'),
                    'middle'=> esc_html__('Middle', 'theplus'),
                    'bottom'=> esc_html__('Bottom', 'theplus'),
                ],
                'default'       => 'middle',
                'condition'     => [
					'page_scroll_opt' => 'tp_multi_scroll',
                    'multi_navigation_dots'   => 'yes'
                ]
            ]
        );
		$this->add_control(
			'multi_dots_tooltips',
            [
                'label'         => esc_html__('Dots Tooltip\'s Text', 'theplus'),
                'type'          => Controls_Manager::TEXT,
                'description'   => esc_html__('Add Multiple text separated by comma \',\'','theplus'),
                'condition'     => [
					'page_scroll_opt' => 'tp_multi_scroll',
                    'multi_navigation_dots'   => 'yes'
                ]
            ]
        );
		/*navigation multi scroll*/
		
		$this->add_control(
			'scroll_nav_connection',
			[
				'label' => esc_html__( 'Scroll Navigation Connection', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->add_control(
			'scrollnav_connect_id',
			[
				'label' => esc_html__( 'Scroll Navigation Connect ID', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'condition'    => [
					'scroll_nav_connection' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*full page dots settings end*/
		
		/*Next previous settings end*/
		$this->start_controls_section('next_previous_settings',
            [
                'label'     => esc_html__('Next Previous Button', 'theplus'),
				'condition' => [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling','tp_multi_scroll'],
				],
            ]
        );
		$this->add_control(
			'show_next_prev',
			[
				'label' => esc_html__( 'Next Previous Button', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',				
			]
		);
		$this->add_control('next_prev_style',
            [
                'label'         => esc_html__('Style', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'style-1'   => esc_html__('Style 1', 'theplus'),
                    'style-2'   => esc_html__('Style 2', 'theplus'),
                    'style-3'   => esc_html__('Style 3', 'theplus'),
                    'custom'   => esc_html__('Custom', 'theplus'),
                ],
                'default'       => 'style-1',
				'condition' => [
					'show_next_prev' => 'yes',
				],
            ]
        );
		$this->add_control(
			'nav_prev_image',
			[
				'label' => esc_html__( 'Previous Icon', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'next_prev_style' => 'custom',
				],
			]
		);
		$this->add_control(
			'nav_next_image',
			[
				'label' => esc_html__( 'Next Icon', 'theplus' ),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'condition' => [
					'next_prev_style' => 'custom',
				],
			]
		);
		$this->add_control(
			'np_gap',
			[
				'label' => esc_html__( 'Gap', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .fp-nxt-prev .fp-nav-btn.fp-nav-next' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_next_prev' => 'yes',
				],
			]
		);
		$this->add_control('next_prev_direction',
            [
                'label'         => esc_html__('Direction', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'horizontal'   => esc_html__('Horizontal', 'theplus'),
                    'vertical'   => esc_html__('Vertical', 'theplus'),
                ],
                'default'       => 'horizontal',
				'condition' => [
					'show_next_prev' => 'yes',
				],
            ]
        );
		$this->add_control('fp_nav_position',
            [
                'label'         => esc_html__('Position', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'bottom-center'   => esc_html__('bottom-center', 'theplus'),
                    'bottom-left'   => esc_html__('bottom-left', 'theplus'),
                    'bottom-right'   => esc_html__('bottom-right', 'theplus'),
                    'left-top'   => esc_html__('left-top', 'theplus'),
                    'left-center'   => esc_html__('left-center', 'theplus'),
                    'right-top'   => esc_html__('right-top', 'theplus'),
                    'right-center'   => esc_html__('right-center', 'theplus'),
                ],
                'default'       => 'bottom-center',
				'condition' => [
					'show_next_prev' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'fp_offset_left',
			[
				'label' => esc_html__( 'Offset Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 5,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],				
				'selectors'  => [
					'{{WRAPPER}} .fp-nxt-prev.bottom-left,{{WRAPPER}} .fp-nxt-prev.left-top,{{WRAPPER}} .fp-nxt-prev.left-center' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_next_prev' => 'yes','fp_nav_position' => ['bottom-left','left-top','left-center'],],
			]
		);
		$this->add_responsive_control(
			'fp_offset_right',
			[
				'label' => esc_html__( 'Offset Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'{{WRAPPER}} .fp-nxt-prev.bottom-right,{{WRAPPER}} .fp-nxt-prev.right-top,{{WRAPPER}} .fp-nxt-prev.right-center' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_next_prev' => 'yes','fp_nav_position' => ['bottom-right','right-top','right-center'],],
			]
		);
		$this->add_responsive_control(
			'fp_offset_bottom',
			[
				'label' => esc_html__( 'Offset Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'{{WRAPPER}} .fp-nxt-prev.bottom-left,{{WRAPPER}} .fp-nxt-prev.bottom-right,{{WRAPPER}} .fp-nxt-prev.bottom-center' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_next_prev' => 'yes','fp_nav_position' => ['bottom-left','bottom-center','bottom-right'],],
			]
		);
		$this->add_responsive_control(
			'fp_offset_top',
			[
				'label' => esc_html__( 'Offset Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'{{WRAPPER}} .fp-nxt-prev.left-top,{{WRAPPER}} .fp-nxt-prev.right-top' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_next_prev' => 'yes','fp_nav_position' => ['left-top','right-top'],],
			]
		);
		$this->add_control(
			'next_size',
			[
				'label'         => esc_html__('Next Icon Size', 'theplus'),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'small'   => esc_html__('Small', 'theplus'),
					'medium'   => esc_html__('Medium', 'theplus'),
					'large'   => esc_html__('Large', 'theplus'),					
				],
				'default'       => 'medium',
				'separator' => 'before',
				'condition' => [
					'show_next_prev' => 'yes',
					'next_prev_style!' => ['style-3','custom'],
				],
			]
        );
		$this->add_control(
			'prev_size',
			[
				'label'         => esc_html__('Prev Icon Size', 'theplus'),
				'type'          => Controls_Manager::SELECT,
				'options'       => [
					'small'   => esc_html__('Small', 'theplus'),
					'medium'   => esc_html__('Medium', 'theplus'),
					'large'   => esc_html__('Large', 'theplus'),					
				],
				'default'       => 'medium',
				'condition' => [
					'show_next_prev' => 'yes',
					'next_prev_style!' => ['style-3','custom'],
				],
			]
        );
		$this->add_control(
			'nxt_txt',
			[
				'label' => esc_html__( 'Next Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Next',
				'placeholder' => esc_html__( 'Next', 'theplus' ),
				'condition' => [
					'show_next_prev' => 'yes',
					'next_prev_style' => 'style-3',
				],
			]
		);
		$this->add_control(
			'prev_txt',
			[
				'label' => esc_html__( 'Previous Button Text', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Prev',
				'placeholder' => esc_html__( 'Prev', 'theplus' ),
				'condition' => [
					'show_next_prev' => 'yes',
					'next_prev_style' => 'style-3',
				],
			]
		);
		$this->end_controls_section();
		/*Next previous settings end*/
				
		/*paginate settings start*/
		$this->start_controls_section('section_show_paginate',
            [
                'label'     => esc_html__('Paginate', 'theplus'),
				'condition' => [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling','tp_multi_scroll'],
				],
            ]
        );
		$this->add_control(
			'show_paginate',
			[
				'label' => esc_html__( 'Show Paginate', 'theplus' ),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
			]
		);
		$this->add_control('paginate_style',
            [
                'label'         => esc_html__('Pagination Styles', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'fadeIn'   => esc_html__('FadeIn', 'theplus'),
                    'fadeInDown'   => esc_html__('FadeInDown', 'theplus'),
                    'fadeInUp'   => esc_html__('FadeInUp', 'theplus'),
                    'flipInX'   => esc_html__('FlipInX', 'theplus'),
                    'flipInY'   => esc_html__('FlipInY', 'theplus'),
                    'rotateInDownRight'   => esc_html__('RotateInDownRight', 'theplus'),
                    'rotateInUpRight'   => esc_html__('RotateInUpRight', 'theplus'),
                    'zoomIn'   => esc_html__('ZoomIn', 'theplus'),
                    'rollIn'   => esc_html__('RollIn', 'theplus'),
                    'bounceIn'   => esc_html__('BounceIn', 'theplus'),                    
                ],
                'default'       => 'fadeIn',
				'condition' => [
					'show_paginate' => 'yes',
				],
            ]
        );
		$this->add_control('paginate_position',
            [
                'label'         => esc_html__('Pagination Position', 'theplus'),
                'type'          => Controls_Manager::SELECT,
                'options'       => [
                    'bottom-center'   => esc_html__('Bottom Center', 'theplus'),                               
                    'bottom-left'   => esc_html__('Bottom Left', 'theplus'),                               
                    'bottom-right'   => esc_html__('Bottom Right', 'theplus'),                               
                    'left-top'   => esc_html__('Left Top', 'theplus'),                               
                    'left-center'   => esc_html__('Left Center', 'theplus'),                               
                    'right-top'   => esc_html__('Right Top', 'theplus'),                               
                    'right-center'   => esc_html__('Right Center', 'theplus'), 
                ],
                'default'       => 'bottom-left',
				'condition' => [
					'show_paginate' => 'yes',
				],
            ]
        );
		$this->add_responsive_control(
			'paginate_offset_left',
			[
				'label' => esc_html__( 'Offset Left', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 5,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],				
				'selectors'  => [
					'.ps{{ID}}.fullpage-nav-paginate.bottom-left,.ps{{ID}}.fullpage-nav-paginate.left-top,.ps{{ID}}.fullpage-nav-paginate.left-center' => 'left: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_paginate' => 'yes','paginate_position' => ['bottom-left','left-top','left-center'],],
			]
		);
		$this->add_responsive_control(
			'paginate_offset_right',
			[
				'label' => esc_html__( 'Offset Right', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'.ps{{ID}}.fullpage-nav-paginate.bottom-right,.ps{{ID}}.fullpage-nav-paginate.right-top,.ps{{ID}}.fullpage-nav-paginate.right-center' => 'right: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_paginate' => 'yes','paginate_position' =>  ['bottom-right','right-top','right-center'],],
			]
		);
		$this->add_responsive_control(
			'paginate_offset_bottom',
			[
				'label' => esc_html__( 'Offset Bottom', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'.ps{{ID}}.fullpage-nav-paginate.bottom-left,.ps{{ID}}.fullpage-nav-paginate.bottom-right,.ps{{ID}}.fullpage-nav-paginate.bottom-center' => 'bottom: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_paginate' => 'yes','paginate_position' =>  ['bottom-left','bottom-center','bottom-right'],],
			]
		);
		$this->add_responsive_control(
			'paginate_offset_top',
			[
				'label' => esc_html__( 'Offset Top', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => ['min' => 0,'max' => 1000,'step' => 1,],
					'%' => ['min' => 0,'max' => 100,],
				],
				'default' => ['unit' => '%','size' => 7,],
				'selectors'  => [
					'.ps{{ID}}.fullpage-nav-paginate.left-top,.ps{{ID}}.fullpage-nav-paginate.right-top' => 'top: {{SIZE}}{{UNIT}};',
				],
				'condition' => ['show_paginate' => 'yes','paginate_position' =>  ['left-top','right-top'],],
			]
		);
		$this->end_controls_section();
		/*paginate settings end*/
		
		/*header footer selection start*/
		$this->start_controls_section('section_show_header_footer_opt',
            [
                'label'     => esc_html__('Footer Options', 'theplus'),
				'condition' => [
					'page_scroll_opt' => ['tp_full_page'],
				],
            ]
        );
		$this->add_control(
			'tp_show_header_footer_note',
			[
				'label' => esc_html__( 'Footer template count in Paginate.', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,				
			]
		);			
		$this->add_control(
			'tp_show_footer',
			[
				'label' => esc_html__( 'Footer', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
			]
		);
		$this->end_controls_section();
		/*header footer selection end*/
		
		/*extra option start*/
		$this->start_controls_section('section_show_extra_opt',
            [
                'label'     => esc_html__('Extra Option', 'theplus'),
				'condition' => [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling'],
				],
            ]
        );
		$this->add_control(
			'tp_direction',
			[
				'label' => esc_html__( 'Direction', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'vertical',
				'options' => [
					'vertical'  => esc_html__( 'Vertical', 'theplus' ),
					'horizontal'  => esc_html__( 'Horizontal', 'theplus' ),					
				],
				'separator' => 'after',
				'condition' => [
					'page_scroll_opt!' => ['tp_full_page'],
				],
			]
		);
		$this->add_control(
			'tp_fp_hide_hash_id',
			[
				'label' => esc_html__( 'Hide Hash/id in URL', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'after',
				'condition' => [
					'page_scroll_opt!' => ['tp_page_pilling'],
				],
			]
		);
		$this->add_control(
			'tp_keyboard_scrolling',
			[
				'label' => esc_html__( 'Keyboard Scrolling', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'yes',
				
			]
		);
		$this->add_control(
			'tp_reset_scrolling',
			[
				'label' => esc_html__( 'Reset Scroll Top', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'page_scroll_opt' => ['tp_full_page'],
				],
				
			]
		);
		$this->add_control(
			'tp_overflow_scrolling',
			[
				'label' => esc_html__( 'Content Overflow Scroll', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'page_scroll_opt' => ['tp_full_page'],
				],
				
			]
		);
		$this->add_control(
			'tp_scrolling_speed',
			[
				'label' => esc_html__( 'Scrolling Speed', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 5,
				'max' => 5000,
				'step' => 5,
				'default' => 700,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_loop_bottom',
			[
				'label' => esc_html__( 'Loop Bottom', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'description'   => esc_html__('Scrolling down in the last section should scroll to the first one or not.','theplus'),
				'separator' => 'before',
			]
		);
		$this->add_control(
			'tp_loop_top',
			[
				'label' => esc_html__( 'Loop Top', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'description'   => esc_html__('Scrolling up in the first section should scroll to the last one or not.','theplus'),
				'default' => 'no',
			]
		);
		$this->add_control(
			'tp_tablet_off',
			[
				'label' => esc_html__( 'Page Pilling in Tablet', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),				
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'page_scroll_opt!' => ['tp_full_page'],
				],
			]
		);
		$this->add_control(
			'tp_mobile_off',
			[
				'label' => esc_html__( 'Page Pilling in Mobile', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'condition' => [
					'page_scroll_opt!' => ['tp_full_page'],					
				],
			]
		);
		$this->add_control(
			'tp_continuous_vertical',
			[
				'label' => esc_html__( 'Continuous Vertical', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'page_scroll_opt!' => ['tp_page_pilling'],
				],
			]
		);
		$this->add_control(
			'tp_responsive_width',
			[
				'label' => esc_html__( 'Responsive Width', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',				
				'condition' => [
					'page_scroll_opt!' => ['tp_page_pilling'],
				],
			]
		);
		$this->add_control(
			'res_width_value',
			[
				'label' => esc_html__( 'Responsive Width', 'theplus' ),
				'type' => \Elementor\Controls_Manager::NUMBER,
				'min' => 300,
				'max' => 2000,
				'step' => 5,
				'default' => 0,
				'description'   => esc_html__('ex. 900 < Scroll Normal Site','theplus'),
				'condition' => [
					'page_scroll_opt!' => ['tp_page_pilling'],
					'tp_responsive_width' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*extra option end*/
		
		/*extra option multi scroll start*/
		$this->start_controls_section(
			'section_multi_extra_opt',
            [
                'label'     => esc_html__('Extra Option', 'theplus'),
				'condition'		=> [
					'page_scroll_opt' => 'tp_multi_scroll',
				],
            ]
        );
		$this->add_control(
			'multi_left_width',
            [
                'label'         => esc_html__('Left Section Width', 'theplus'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => '%',
                'default'       => [
                    'size'  => 50
                ]
            ]
        );
		$this->add_control(
			'multi_right_width',
            [
                'label'         => esc_html__('Right Section Width', 'theplus'),
                'type'          => Controls_Manager::SLIDER,
                'size_units'    => '%',
                'default'       => [
                    'size'  => 50
                ]
            ]
        );
		$this->add_control(
			'multi_keyboard_scrolling',
            [
                'label'         => esc_html__('Keyboard Scrolling', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,
            ]
        );
        
        $this->add_control(
			'multi_loop_top',
            [
                'label'         => esc_html__('Loop Top', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,
                'description'   => esc_html__('Scrolling up in the first section should scroll to the last one or not.','theplus')
                
            ]
        );
        
        $this->add_control(
			'multi_loop_bottom',
            [
                'label'         => esc_html__('Loop Bottom', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,
                'description'   => esc_html__('Scrolling down in the last section should scroll to the first one or not.','theplus')
                
            ]
        );
        
        $this->add_control(
			'multi_scrolling_speed',
            [
                'label'         => esc_html__('Scroll Speed', 'theplus'),
                'type'          => Controls_Manager::NUMBER,
                'title'         => esc_html__('Set scrolling speed in seconds, default: 700', 'theplus'),
                'default'       => 700,               
            ]
        );        
        $this->add_control(
			'multi_scroll_responsive_tabs',
            [
                'label'         => esc_html__('Disable on Tablet', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,                
                'default'       => 'no'
            ]
        );
        
        $this->add_control('multi_scroll_responsive_mobile',
            [
                'label'         => esc_html__('Disable on Mobiles', 'theplus'),
                'type'          => Controls_Manager::SWITCHER,                
                'default'       => 'no'
            ]
        );
		$this->end_controls_section();
		/*extra option multi scroll end*/
		/************** style tag start*****************/
		/*hscroll style*/
		$this->start_controls_section(
            'section_hscroll_styling',
            [
                'label' => esc_html__('Horizontal Scroll Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'page_scroll_opt' => 'tp_horizontal_scroll',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'hscroll_normal_background',
				'label' => __( 'Background', 'theplus' ),
				'types' => [ 'classic', 'gradient' ],
				'selector' => '{{WRAPPER}} .tp_horizontal_scroll .tp_hscroll_root',
				'fields_options' => [
					'hscroll_normal_background_position' => 'center center',
				],
			]
		);
		$this->end_controls_section();
		/*hscroll style*/
		/*dot style start*/
		$this->start_controls_section(
            'section_dot_styling',
            [
                'label' => esc_html__('Dot Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling','tp_multi_scroll'],
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_dot_style' );
		$this->start_controls_tab(
			'tab_dot_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);	
		$this->add_control(
			'dots_color_n',
			[
				'label' => esc_html__( 'Dots Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#fp-nav ul li a span' => 'background: {{VALUE}}',
					'#pp-nav ul li a span,#multiscroll-nav ul li a span' => 'border:1px solid {{VALUE}} !important',
				],
			]
		);		
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_dot_active',
			[
				'label' => esc_html__( 'Active', 'theplus' ),
				'condition'		=> [
					'page_scroll_opt!' => ['tp_full_page'],
				],
			]
		);
		$this->add_control(
			'dots_color_h',
			[
				'label' => esc_html__( 'Dots Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#pp-nav ul li .active span,#multiscroll-nav ul li .active span' => 'background: {{VALUE}}',					
				],
			]
		);		
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_control(
			'dots_tt_head',
			[
				'label' => esc_html__( 'Tooltip Text Option', 'theplus' ),
				'type' => \Elementor\Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_responsive_control(
			'dots_text_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px'],				
				'selectors' => [
					'#fp-nav ul li .fp-tooltip,#pp-nav ul li .pp-tooltip,#multiscroll-nav ul li .multiscroll-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'dots_text_typo_n',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '#fp-nav ul li .fp-tooltip,#pp-nav ul li .pp-tooltip,#multiscroll-nav ul li .multiscroll-tooltip',
				
			]
		);
		$this->add_control(
			'dots_text_color_n',
			[
				'label' => esc_html__( 'Tooltip Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#fp-nav ul li .fp-tooltip,#pp-nav ul li .pp-tooltip,#multiscroll-nav ul li .multiscroll-tooltip' => 'color: {{VALUE}}',
				],
			]
		);
		$this->add_control(
			'dots_text_bg_color_n',
			[
				'label' => esc_html__( 'Tooltip Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#fp-nav ul li .fp-tooltip,#pp-nav ul li .pp-tooltip,#multiscroll-nav ul li .multiscroll-tooltip' => 'background: {{VALUE}}',
				],
			]
		);
		$this->add_responsive_control(
			'dots_tt_border',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'#fp-nav ul li .fp-tooltip,#pp-nav ul li .pp-tooltip,#multiscroll-nav ul li .multiscroll-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->end_controls_section();
		/*dot style end*/
		/*next previous buton start*/
		$this->start_controls_section(
            'section_nxt_prv_styling',
            [
                'label' => esc_html__('Next Previous Button Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling','tp_multi_scroll'],
					'next_prev_style!' => ['custom'],
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_nxt_prv_style' );
		$this->start_controls_tab(
			'tab_np_normal',
			[
				'label' => esc_html__( 'Normal', 'theplus' ),
			]
		);
		$this->add_control(
			'np_icon_color_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-1 .fp-nav-btn,
					#ps{{ID}}.fp-nxt-prev.style-2 .fp-nav-btn,
					#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_st3_n_color_n',
			[
				'label' => esc_html__( 'Next Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn.fp-nav-next' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style' => ['style-3'],
				],
			]
		);		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'np_st3_n_size_n',
				'selector' => '#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn.fp-nav-next',
				'condition' => [
					'next_prev_style' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_st3_p_color_n',
			[
				'label' => esc_html__( 'Previous Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn.fp-nav-prev' => 'color: {{VALUE}}',
				],
				'separator' => 'before',
				'condition'		=> [
					'next_prev_style' => ['style-3'],
				],
			]
		);
		
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'np_st3_p_size_n',
				'selector' => '#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn.fp-nav-prev',
				'condition' => [
					'next_prev_style' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_icon_bg_n',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'{{WRAPPER}} .fp-nxt-prev.style-1 .fp-nav-btn,
					{{WRAPPER}} .fp-nxt-prev.style-2 .fp-nav-btn' => 'background: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->add_responsive_control(
			'np_icon_n_br',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .fp-nxt-prev.style-1 .fp-nav-btn,{{WRAPPER}} .fp-nxt-prev.style-2 .fp-nav-btn' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'np_icon_n_bx_sdw',
				'selector' => '{{WRAPPER}} .fp-nxt-prev.style-1 .fp-nav-btn,{{WRAPPER}} .fp-nxt-prev.style-2 .fp-nav-btn',
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_np_hover',
			[
				'label' => esc_html__( 'Hover', 'theplus' ),
			]
		);
		$this->add_control(
			'np_icon_color_h',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-1 .fp-nav-btn:hover,
					#ps{{ID}}.fp-nxt-prev.style-2 .fp-nav-btn:hover' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_st3_n_color_h',
			[
				'label' => esc_html__( 'Next Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn:hover.fp-nav-next' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_st3_p_color_h',
			[
				'label' => esc_html__( 'Previous Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'#ps{{ID}}.fp-nxt-prev.style-3 .fp-nav-btn:hover.fp-nav-prev' => 'color: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'np_icon_bg_h',
			[
				'label' => esc_html__( 'Background', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .fp-nxt-prev.style-1 .fp-nav-btn:hover,
					{{WRAPPER}} .fp-nxt-prev.style-2 .fp-nav-btn:hover' => 'background: {{VALUE}}',
				],
				'condition'		=> [
					'next_prev_style!' => ['style-3'],
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();		
		$this->end_controls_section();
		$this->start_controls_section(
            'section_nxt_prv_custom',
            [
                'label' => esc_html__('Next Previous Custom Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [					
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling'],			
					'next_prev_style' => ['custom'],
				],
            ]
        );
		$this->add_control(
			'fp_nxt_btn',
			[
				'label' => esc_html__( 'Next Button Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 50,
				],
				'selectors' => [
					'{{WRAPPER}} .fp-nxt-prev.custom .fp-nav-btn.fp-nav-next' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'fp_prev_btn',
			[
				'label' => esc_html__( 'Preview Button Size', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', '%' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 1,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 35,
				],
				'selectors' => [
					'{{WRAPPER}} .fp-nxt-prev.custom .fp-nav-btn.fp-nav-prev' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->end_controls_section();
		/*next previous buton end*/
		$this->start_controls_section(
            'section_paginate_custom',
            [
                'label' => esc_html__('Paginate Style', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'		=> [				
					'page_scroll_opt' => ['tp_full_page','tp_page_pilling','tp_multi_scroll'],
					'show_paginate' => 'yes',
				],
            ]
        );
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'paginate_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.ps{{ID}}.fullpage-nav-paginate .slide-nav',
			]
		);
		$this->add_control(
			'paginate_color',
			[
				'label' => esc_html__( 'Current Paginate Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'.ps{{ID}}.fullpage-nav-paginate .slide-nav' => 'color: {{VALUE}}',
				],
				'separator' => 'after',
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'paginate_last_typography',
				'label' => esc_html__( 'Typography', 'theplus' ),
				'global' => [
                    'default' => Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector' => '.ps{{ID}}.fullpage-nav-paginate .total-page-nav',
			]
		);
		$this->add_control(
			'paginate_last_color',
			[
				'label' => esc_html__( 'Total Paginate Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,				
				'selectors' => [
					'.ps{{ID}}.fullpage-nav-paginate .total-page-nav' => 'color: {{VALUE}}',
				],
			]
		);
		$this->end_controls_section();
		/*next previous buton end*/	
		/************** style tag end*****************/
	}
	
	
	protected function render() {
		$settings = $this->get_settings_for_display();
		$page_scroll_opt = $settings['page_scroll_opt'];
		$uid_widget=uniqid("ps");
		$id = $this->get_id();
		$data_attr='';
        $widget_id = 'ps'.$this->get_id();
		
        $multi_navigation_dots = ( 'yes' == $settings['multi_navigation_dots'] ) ? true : false;        
        $dots_text = explode(',', $settings['multi_dots_tooltips'] );
		$nav_dots_text = explode(',', $settings['nav_dots_tooltips'] );
		$top_loop = ( 'yes' == $settings['multi_loop_top'] ) ? true : false;        
        $bottom_loop = ( 'yes' == $settings['multi_loop_bottom'] ) ? true : false;
		
		if((($settings['show_paginate']=='yes' && $page_scroll_opt=='tp_full_page') 
			|| ($settings['show_paginate']=='yes' && $page_scroll_opt=='tp_page_pilling') 
			|| ($settings['show_paginate']=='yes' && $page_scroll_opt=='tp_multi_scroll')) 
			&& 
			((!empty($settings['show_paginate']) && $page_scroll_opt=='tp_full_page') 
			|| (!empty($settings['show_paginate']) && $page_scroll_opt=='tp_page_pilling')
			|| (!empty($settings['show_paginate']) && $page_scroll_opt=='tp_multi_scroll')
			)){
			$data_attr .=' data-show_paginate="on"';
			$data_attr .=' data-paginate_style="'.$settings['paginate_style'].'"';
			$data_attr .=' data-paginate_position="'.$widget_id.' '.$settings['paginate_position'].'"';			
		}else{
			$data_attr .=' data-show_paginate="off"';
		}
		if(!empty($settings['tp_reset_scrolling']) && $settings['tp_reset_scrolling']=='yes' && $page_scroll_opt=='tp_full_page'){
			$data_attr .=' data-scrolloverflow="yes"';
		}

		if(!empty($settings['tp_overflow_scrolling']) && $settings['tp_overflow_scrolling']=='yes' && $page_scroll_opt=='tp_full_page'){
			$data_attr .=' data-scrolloverflowscroll="yes"';
		}
		

		if((!empty($page_scroll_opt) && $page_scroll_opt=='tp_full_page') && (!empty($settings['tp_show_footer']) && $settings['tp_show_footer']=='yes')){
			$data_attr .=' data-show_footer="yes"';			
		}else{
			$data_attr .=' data-show_footer="no"';
		}
		
		$show_next_prev = $settings['show_next_prev'];
		$next_prev_style = $settings['next_prev_style'];
		$fp_nav_position = $settings['fp_nav_position'];		
		$next_prev_direction = $settings['next_prev_direction'];		
		$nav_prev_image = (!empty($settings['nav_prev_image']['url'])) ? $settings['nav_prev_image']['url'] : '';
		$nav_next_image = (!empty($settings['nav_next_image']['url'])) ? $settings['nav_next_image']['url'] : '';
		$prev_size = (!empty($settings['prev_size']) && $settings['prev_size']) ? $settings['prev_size'] : '';
		$nxt_txt = (!empty($settings['nxt_txt'])) ? $settings['nxt_txt'] : '';
		$prev_txt = (!empty($settings['prev_txt'])) ? $settings['prev_txt'] : '';		
		$next_size = $settings['next_size'];
		
		
        $multi_scroll_options = [
			'multi_id'            => esc_attr( $id ),			
            'dots'          => $multi_navigation_dots,
            'dotsTooltips'      => $dots_text,
            'dotsPosition'       => $settings['multi_navigation_dots_pos'],
            'dotsVertical'       => $settings['multi_navigation_verti_dots_pos'],
            'loopTop'       => $top_loop,
            'loopBottom'       => $bottom_loop,        
            'disable_tablet'      => ( $settings['multi_scroll_responsive_tabs'] == 'yes' ) ? 'yes' : 'no',            
            'disable_mobile'      => ( $settings['multi_scroll_responsive_mobile'] == 'yes' ) ? 'yes' : 'no',            
			'keyboardScrolling'      => ( $settings['multi_keyboard_scrolling'] == 'yes' ) ? true : false,
			'scrollingSpeed' => ($settings["multi_scrolling_speed"]) ? $settings["multi_scrolling_speed"] : 700,
			'leftSide'     => !empty( $settings['multi_left_width']['size'] ) ? $settings['multi_left_width']['size'] : 50,
            'rightSide'    => !empty( $settings['multi_right_width']['size'] ) ? $settings['multi_right_width']['size'] : 50,
        ];
        
        $this->add_render_attribute( 'multi_scroll_wrapper', 'class', 'theplus-multiscroll-wrap' );
        $this->add_render_attribute( 'multi_scroll_inner', 'class', array( 'theplus-multiscroll-inner' ) );
		$this->add_render_attribute( 'multi_scroll_inner', 'id', 'theplus-multiscroll-' . $id );        
        
        $this->add_render_attribute('multi_right_template', 'class', [ 'theplus-multiscroll-temp', 'theplus-multiscroll-right-temp', 'theplus-multiscroll-temp-' . $id ] );
        $this->add_render_attribute('multi_left_template', 'class', [ 'theplus-multiscroll-temp', 'theplus-multiscroll-left-temp', 'theplus-multiscroll-temp-' . $id ] );
        
        
        $this->add_inline_editing_attributes('left_side_text', 'advanced');
        $this->add_inline_editing_attributes('right_side_text', 'advanced');
        $this->add_render_attribute('left_side_text', 'class', 'theplus-multiscroll-left-text');
        $this->add_render_attribute('right_side_text', 'class', 'theplus-multiscroll-right-text');
        $templates = $settings['multi_left_right_repeater'];
		
		/*Full Page*/
		$full_page_content='';
		$full_page_anchors=array();
		$fullpage_opt=array();
		if($page_scroll_opt=='tp_full_page'){
			if(!empty($settings["fp_content"])) {
				$i=1;
				foreach($settings["fp_content"] as $item) {
					if(!empty($item["fp_content_template"])){
						$slideid =(!empty($item['fp-slideid'])) ? $item['fp-slideid'] : 'fp_'.$id.'_'.$i;
						$full_page_anchors[] = $slideid;
						
						$full_page_content .='<div class="section">';
							$full_page_content .=Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($item["fp_content_template"]);
						$full_page_content .='</div>';
						
						$i++;
					}
				}
			}
			
			if(!empty($full_page_anchors)){				
				$fullpage_opt['anchors']=$full_page_anchors;				
			}
			$fullpage_opt["navigationTooltips"] = false;			
			
			$fullpage_opt["scrollingSpeed"] = ($settings["tp_scrolling_speed"]!='') ? $settings["tp_scrolling_speed"]: 700;
			$fullpage_opt["responsiveWidth"] = ($settings["res_width_value"]!='') ? $settings["res_width_value"]: 0;
			$fullpage_opt["loopBottom"] = ( 'yes' == $settings['tp_loop_bottom'] ) ? true : false;
			$fullpage_opt["lockAnchors"] = ( 'yes' == $settings['tp_fp_hide_hash_id'] ) ? true : false;
			$fullpage_opt["loopTop"] = ( 'yes' == $settings['tp_loop_top'] ) ? true : false;
			$fullpage_opt["continuousVertical"] = ( 'yes' == $settings['tp_continuous_vertical'] ) ? true : false;			
			$fullpage_opt["keyboardScrolling"] = ( 'yes' == $settings['tp_keyboard_scrolling'] ) ? true : false;
			
			if(($settings['show_dots']=='yes' && $page_scroll_opt=='tp_full_page') && (!empty($settings['show_dots']) && $page_scroll_opt=='tp_full_page')){
				//$data_attr .=' data-nav="true"';
				$fullpage_opt["navigation"] = true;
				$fullpage_opt["navigationPosition"] = (!empty($settings['nav_postion']) && $settings['nav_postion']=='left') ? 'left' : 'right';
				$fullpage_opt['navigationTooltips']=$nav_dots_text;				
			}else{
				$fullpage_opt["navigation"] = false;
			}
			
			$data_fullpage=json_encode($fullpage_opt);
			$data_attr .= ' data-full-page-opt=\'' . $data_fullpage . '\'';
			
			if(!empty($settings['scroll_nav_connection']) && $settings['scroll_nav_connection']=='yes' && !empty($settings['scrollnav_connect_id'])){
				$data_attr .= ' data-scroll-nav-id="tp-sc-'.esc_attr($settings['scrollnav_connect_id']).'"';
			}
		}
		
		/*Page Piling*/
		$page_piling_content='';
		$page_piling_anchors=array();
		$page_piling_opt=array();
		if($page_scroll_opt=='tp_page_pilling'){
			$i=1;
			if(!empty($settings["fp_content"])) {
				$page_piling_content .= '<div id="pagepiling">';
					foreach($settings["fp_content"] as $item) {
						if(!empty($item["fp_content_template"])){
							$slideid =(!empty($item['fp-slideid'])) ? $item['fp-slideid'] : 'fp_'.$id.'_'.$i;
							$page_piling_anchors[] = $slideid;
						
							$page_piling_content .= '<div class="section pp_section">';
								$page_piling_content .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($item["fp_content_template"]);
							$page_piling_content .='</div>';
						}
						$i++;						
					}
				$page_piling_content .='</div>';
			}
			
			if(!empty($page_piling_anchors)){				
				$page_piling_opt['anchors']=$page_piling_anchors;
			}
			
			$page_piling_opt["scrollingSpeed"] = ($settings["tp_scrolling_speed"]!='') ? $settings["tp_scrolling_speed"]: 700;			
			$page_piling_opt["loopBottom"] = ( 'yes' == $settings['tp_loop_bottom'] ) ? true : false;
			$page_piling_opt["loopTop"] = ( 'yes' == $settings['tp_loop_top'] ) ? true : false;			
			
			$page_piling_opt["pp_tablet_off"] = ( 'yes' == $settings['tp_tablet_off'] ) ? 'yes' : 'no';
			$page_piling_opt["pp_mobile_off"] = ( 'yes' == $settings['tp_mobile_off'] ) ? 'yes' : 'no';
			
			$page_piling_opt["keyboardScrolling"] = ( 'yes' == $settings['tp_keyboard_scrolling'] ) ? true : false;
			$page_piling_opt['direction']=(!empty($settings["tp_direction"])) ? $settings["tp_direction"] : 'vertical';
			
		
			if(($settings['show_dots']=='yes' && $page_scroll_opt=='tp_page_pilling') && (!empty($settings['show_dots']) && $page_scroll_opt=='tp_page_pilling')){				
				$page_piling_opt["navigation"]["display"] = true;
				$page_piling_opt["navigation"]["position"] = (!empty($settings['nav_postion']) && $settings['nav_postion']=='left') ? 'left' : 'right';
				$page_piling_opt["navigation"]["tooltips"]=$nav_dots_text;
			}else{				
				$page_piling_opt["navigation"]["display"] = false;
				$page_piling_opt["navigation"]["position"] ='';
				$page_piling_opt["navigation"]["tooltips"]='';
			}
		
			$pagepiling_opt=json_encode($page_piling_opt);
			$data_attr .= ' data-page-piling-opt=\'' . $pagepiling_opt . '\'';
			
			if(!empty($settings['scroll_nav_connection']) && $settings['scroll_nav_connection']=='yes' && !empty($settings['scrollnav_connect_id'])){
				$data_attr .= ' data-scroll-nav-id="tp-sc-'.esc_attr($settings['scrollnav_connect_id']).'"';
			}
		}
		
		//Multiscroll
		$multiscroll_anchors = array();
		$multi_scroll_opt=array();
		if($page_scroll_opt=='tp_multi_scroll'){
			if(!empty($settings["multi_left_right_repeater"])) {
				$i=1;
				foreach($settings["multi_left_right_repeater"] as $item) {
					if(!empty($item["multi_left_template"]) && !empty($item["multi_right_template"])){
						$slideid =(!empty($item['fp-slideid'])) ? $item['fp-slideid'] : 'fp_'.$id.'_'.$i;
						$multiscroll_anchors[] = $slideid;
					}
					$i++;
				}
			}
			
			if(!empty($multiscroll_anchors)){
				$multi_scroll_opt['anchors']=$multiscroll_anchors;
			}
			
			$multiscroll_opt=json_encode($multi_scroll_opt);
			
			$data_attr .= ' data-multi-scroll-opt=\'' . $multiscroll_opt . '\'';
			if(!empty($settings['scroll_nav_connection']) && $settings['scroll_nav_connection']=='yes' && !empty($settings['scrollnav_connect_id'])){
				$data_attr .= ' data-scroll-nav-id="tp-sc-'.esc_attr($settings['scrollnav_connect_id']).'"';
			}
		}
		
		$pp_tab_off=$pp_mob_off='';
		if((!empty($settings['tp_tablet_off']) && $settings['tp_tablet_off']=='yes') || (!empty($settings['multi_scroll_responsive_tabs']) && $settings['multi_scroll_responsive_tabs']=='yes')){
			$pp_tab_off = 'tp_tablet_off';
		}
		
		if((!empty($settings['tp_mobile_off']) && $settings['tp_mobile_off']=='yes') || (!empty($settings['multi_scroll_responsive_mobile']) && $settings['multi_scroll_responsive_mobile']=='yes')){
			$pp_mob_off = 'tp_mobile_off';
		}
		
		
		//Horizontal Scroll HScroll
		$hslides = array();
		$hslide_width = array();
		$hslide_loop = $hslide_id = '';
		if($page_scroll_opt=='tp_horizontal_scroll'){
			$i=0;
			if(!empty($settings["hscroll_content"])) {
				foreach($settings["hscroll_content"] as $slide){
					$slide_template = $slide['hslide_template'];
					
					$slide_id = $i;
					$slide_id2 = $slide_id;
					if ($slide_id == 0) {
						$slide_id2 = 'first';
					}
					if ($slide_id == count($settings['hscroll_content']) - 1) {
						$slide_id2 = 'last';
					}
					
					$margin_style ='';
					if($slide["hslide_align"]== 'top'){
						$margin_style = 'margin-top:0; margin-bottom:0;';
					}
					if($slide["hslide_align"]== 'bottom'){
						$margin_style = 'margin-bottom:0;';
					}
					
					if(!empty($slide_template)){
						$width = $slide['hslide_width']['size'].$slide['hslide_width']['unit'];
						$hslide_margin_l = ($slide["hslide_margin"]['left']) ? $slide["hslide_margin"]['left'] : 0;
						$hslide_margin_r = ($slide["hslide_margin"]['right']) ? $slide["hslide_margin"]['right'] : 0;
						$hslide_width[] = $width.' + '.($hslide_margin_l + $hslide_margin_r).$slide["hslide_margin"]['unit'];
						if (isset($slide['hslide_id']) && $slide['hslide_id'] != '') {
							$hslide_id .= "'#$slide[hslide_id]': 'tp_hscroll_slide_$slide_id', ";
						}
						$slide_html = Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($slide_template);
					
						$hslide_loop .='<div id="tp_hscroll_slide_'.esc_attr($slide_id).'" class="tp_hscroll_slide tp_hscroll_slide_'.esc_attr($slide_id2).' elementor-repeater-item-' . esc_attr($slide['_id']) . '" data-slide-width="'.esc_attr($width).'" style="width: '.esc_attr($width).';'.$margin_style.'" >';
							$hslide_loop .= $slide_html;
						$hslide_loop .='</div>';
						$i++;
					}
				}
			}
			
			$hscroll_speed = $settings['hscroll_speed']['size'];
			$hscroll_sec_id_transition = $settings['hscroll_sec_id_transition']['size'];
			$hscroll_responsive = $settings['hscroll_responsive'] === 'yes';
			$hscroll_scrollbar = $settings['hscroll_scrollbar'] === 'yes';
			$hscroll_draggable = $settings['hscroll_draggable'] === 'yes';
			$hscroll_res_minwidth = 0;
			$hscroll_footer_height = !empty($settings['hscroll_footer_height']['size']) ? $settings['hscroll_footer_height']['size'].$settings['hscroll_footer_height']['unit'] : '';	
									
			if (!empty($hscroll_responsive)) {
				$hscroll_res_minwidth = $settings['hscroll_res_minwidth']['size'];
			}
			
			$style_script_rules = '';
			$style_script_rules .= '<style>.tp_hscroll_root{width: calc('.implode(' + ', $hslide_width).');}
				.tp_hscroll_footer {
					display: flex;
					background: transparent;
					border: 0;
					top: 0;
					z-index: 0;
					position: absolute;
				}
				</style>';
			$style_script_rules .= '<script type="text/javascript">
				(function($) {
					$(document).ready(function() {
						if($(".tp_hscroll_scrollable").length){
							$("body").addClass("tp-horizontal-scroll");
							var scrollbar = "'.$hscroll_scrollbar.'";
							if(scrollbar!=true){
								$("body").addClass("tp-scroll-bar");
							}
						}
						var hscroll_elem = $.jInvertScroll([".tp_hscroll_scrollable"],
							{
							height: ($(".tp_hscroll_root").width() / '.$hscroll_speed.'),
							footer_height: $(".tp_hscroll_footer").height(),
							onScroll: function(percent) {
								var pageWrapper = $(".plus-scroll-sections-bg");
								if(pageWrapper.length){
									var parent_id=pageWrapper.parent().data("elementor-id");
									var  windowidth = $(window).width();
									var arry_index = [];
									if($(".elementor .elementor-inner").length){
										var abc = ".elementor-"+parent_id+"> .elementor-inner > .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide";
									}else{
										var abc = ".elementor-"+parent_id+"> .elementor-inner > .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide";
									}
									$(abc).each(function(){
										var el = $(this);
										var el_begin = el.offset().left;
										var el_end = el.outerWidth();
										var animationEndpos = el_begin + el_end - windowidth;
										arry_index[el.index()] = animationEndpos;
									});
									
									if($("body").hasClass("rtl")){
										arry_index=arry_index.reverse();
									}

									$.each( arry_index, function( key, value ) {
										if( 0<=(value+300)){
											if($(".elementor .elementor-inner").length){
												var index = $(".elementor-"+parent_id+"> .elementor-inner > .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide.active-sec:nth-child("+(key+1)+"), .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slideactive-sec:nth-child("+(key+1)+")");
											}else{
												var index = $(".elementor-"+parent_id+"> .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide.active-sec:nth-child("+(key+1)+"), .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slideactive-sec:nth-child("+(key+1)+")");
											}
											
											index = index.index();
											if( index != key ){
												if($(".elementor .elementor-inner").length){
													$(".elementor-"+parent_id+"> .elementor-inner > .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide").removeClass("active-sec");
													$(".elementor-"+parent_id+"> .elementor-inner > .elementor-section-wrap > .elementor-element .tp_horizontal_scroll .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide").eq(key).addClass("active-sec");
												}else{
													$(".elementor-"+parent_id+"> .elementor-section-wrap > .elementor-element .tp_horizontal_scroll  .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide").removeClass("active-sec");
													$(".elementor-"+parent_id+"> .elementor-section-wrap > .elementor-element .tp_horizontal_scroll  .tp_hscroll_slide, .elementor-"+parent_id+" .tp_horizontal_scroll .tp_hscroll_slide").eq(key).addClass("active-sec");
												}												
												pageWrapper.find(".plus-section-bg-scrolling").removeClass("active_sec");
												pageWrapper.find(".plus-section-bg-scrolling:nth-child("+(key+1)+")").addClass("active_sec");
											}
											return false;
										}
									});
								}
							}
						});
						var draggable = "'.$hscroll_draggable.'";
						if(draggable!=undefined && draggable=="1"){
							var mouseclicked = false, clickX;
							$(document).on({
								"mousemove": function(e) {
									mouseclicked && dragHscrollPos(e);
								},
								"touchmove": function(e) {
									mouseclicked && dragHscrollPos(e);
								},
								"mousedown": function(e) {
									mouseclicked = true;
									clickX = e.pageX;
								},
								"touchstart": function(e) {
									var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
									mouseclicked = true;
									clickX = touch.pageX;
								},
								"touchend": function(e) {
									var touch = e.originalEvent.touches[0] || e.originalEvent.changedTouches[0];
									mouseclicked = false;
									clickX = touch.pageX;
								},
								"mouseup": function() {
									mouseclicked = false;
									$("html").css("cursor", "auto");
								}
							});
							var dragHscrollPos = function(e) {
								$("html").css("cursor", "grabbing");
								var curr = (e.pageX!=undefined) ? e.pageX : e.originalEvent.touches[0].pageX;
								$(window).scrollTop($(window).scrollTop() + ((clickX - curr)/30));
							}
							$("head").append("<style>body{-webkit-touch-callout: none;-webkit-user-select: none;-khtml-user-select: none;-moz-user-select: none;-ms-user-select: none;user-select: none;}</style>");
						}
						var hscroll_Animation = function(e){
							let window_height = $(window).height();
							let window_width = window.innerWidth
								|| document.documentElement.clientWidth
								|| document.body.clientWidth;
							let sec_id = $(this);
							let hscroll_section_id = {
								'.$hslide_id.'
							};
							if (!$(sec_id.attr("href")).selector in hscroll_section_id){
								return true;
							}
							
							//let checkID = hscroll_section_id[$(sec_id.attr("href")).selector];
							let checkID = hscroll_section_id[$(this).attr("href")];
							let hscroll_width = $(".tp_hscroll_root").width();
							let check_width = hscroll_width - window_width;
							if (check_width < 0) {
								check_width = 0;
							}
							if($("#"+checkID).length){
								var hscroll_calc = (hscroll_width / '.$hscroll_speed.') - $(".tp_hscroll_footer").height() - window_height;
								$("html, body").stop().animate({
									scrollTop: ($("#"+checkID).position().left * hscroll_calc)/check_width,
								}, '.$hscroll_sec_id_transition.');
								e.preventDefault();
							}
							return false;
						};
						var closeAnimation = function(e) {
							return true;
						};
						'.($hscroll_responsive ? '
						var hscroll_checkresponsive = function(){
							window_width = window.innerWidth
								|| document.documentElement.clientWidth
								|| document.body.clientWidth;
							if (window_width > '.$hscroll_res_minwidth.') {
								hscroll_elem.reinitialize();
								$("a[href*=\'#\']").off("click", closeAnimation);
								$("a[href*=\'#\']").on("click", hscroll_Animation);
								$(".tp_hscroll_root,.tp-page-scroll-wrapper").css({"height":"100vh","overflow":"hidden"});
								$(".tp_hscroll_root").css({"width": "","display": "flex","position": "fixed","height": "100vh","z-index": "1000"});
								$(".tp_hscroll_slide").css({"display":"inline-block"});
								$(".tp_hscroll_slide").each(function(){
									var width = $(this).data("slide-width");
									$(this).css({"width": width});
								});
								
							}else {
								hscroll_elem.destroy();
								$("body").removeClass("tp-horizontal-scroll");
								$("a[href*=\'#\']").off("click", hscroll_Animation);
								$("a[href*=\'#\']").on("click", closeAnimation);
								$(".tp_hscroll_root,.tp-page-scroll-wrapper").css({"height":"auto","overflow":"visible"});
								$(".tp_hscroll_slide").css({"display":"block","width":"100%"});
								$(".tp_hscroll_root").css({"width": "auto","display": "block","position": "relative","height": "auto","z-index": "auto"});
							}
						}
						hscroll_checkresponsive();
						$(window).on("resize",function() {
							hscroll_checkresponsive();
						});' : 'var hscroll_checkresponsive = function(){
								hscroll_elem.reinitialize();
								$("a[href*=\'#\']").off("click", closeAnimation);
								$("a[href*=\'#\']").on("click", hscroll_Animation);
						}
						hscroll_checkresponsive();
						$(window).on("resize",function() {
							hscroll_checkresponsive();
						});').'
					});
				}(jQuery));</script>';
				
				//Hscroll Bg Scrolling
				if(!empty($settings["hscroll_bg"]) && $settings["hscroll_bg"]=='yes'){
					$widget_id = 'scroll'.$this->get_id();
					$bgs =array();
					$sec_colors='';
					$i=0;
					$hscroll_loop='';
					$hscroll_bg_duration = (!empty($settings["hscroll_bg_duration"]["size"])) ? 'transition-duration:'.$settings["hscroll_bg_duration"]["size"].$settings["hscroll_bg_duration"]["unit"].';' : '';
					if(!empty($settings['hscroll_content'])){
						foreach($settings['hscroll_content'] as $item) {
							if(!empty($slide['hslide_template'])){
								$active_sec='';
								if(!empty($item['scroll_bg_color']) && empty($item['scroll_bg_image']['url'])) {
									$bgs[]=$item['scroll_bg_color'];
									if($i==0){
										$active_sec='active_sec';
									}
									
									$hscroll_loop .= '<div class="elementor-repeater-item-' . $item['_id'] . ' plus-section-bg-scrolling '.esc_attr($active_sec).'" style="background:'.esc_attr($item['scroll_bg_color']).';'.$hscroll_bg_duration.'"></div>';
								}							
								if(!empty($item['scroll_bg_image']['url'])) {
									$bgs[]=$item['scroll_bg_image']['url'];
									if($i==0){
										$active_sec='active_sec';
									}
									
									$hscroll_loop .= '<div class="elementor-repeater-item-' . $item['_id'] . ' plus-section-bg-scrolling  '.esc_attr($active_sec).'" style="background:url('.esc_url($item['scroll_bg_image']['url']).');'.$hscroll_bg_duration.'"></div>';
								}
								$i++;
							}
						}
					
						$sec_colors=htmlspecialchars(json_encode($bgs), ENT_QUOTES, 'UTF-8');
						$sec_colors ='data-bg-colors="'.$sec_colors.'"';
					}
					if(!empty($settings["hscroll_page_full"]) && $settings["hscroll_page_full"]=='relative'){
						$sec_colors .=' data-position="relative"';
					}else{
						$sec_colors .=' data-position="inherit"';
					}
					$style_script_rules .='<div class="plus-scroll-sections-bg '.esc_attr($widget_id).'" data-id="'.esc_attr($widget_id).'" '.$sec_colors.' style="'.$hscroll_bg_duration.'">'.$hscroll_loop.'</div>';
				}
		}
		
		
		echo '<div id="'.$uid_widget.'" class="tp-page-scroll-wrapper '.$uid_widget.' '.$page_scroll_opt.' '.$pp_tab_off.' '.$pp_mob_off.'" data-id="'.$uid_widget.'" data-option="'.esc_attr($page_scroll_opt).'" '.$data_attr.'>';
		
			if($page_scroll_opt=='tp_full_page'){
				echo $full_page_content;
			}
			if($page_scroll_opt=='tp_page_pilling'){
				echo $page_piling_content;
			}
			if($page_scroll_opt=='tp_horizontal_scroll'){ ?>
				<div class="tp_hscroll_footer" style="height: <?php echo $hscroll_footer_height; ?> "></div>
				<div class="tp_hscroll_full_background tp_hscroll_scrollable tp_hscroll_root" style="z-index: 0"></div>
			
				<div class='tp_hscroll_scrollable tp_hscroll_root'>
					<?php echo $hslide_loop; ?>
					<?php echo $style_script_rules; ?>
				</div>
			<?php }
			if($page_scroll_opt=='tp_multi_scroll'){ ?>
				<div <?php echo $this->get_render_attribute_string('multi_scroll_wrapper'); ?> data-settings='<?php echo wp_json_encode($multi_scroll_options); ?>'>			
					<div <?php echo $this->get_render_attribute_string('multi_scroll_inner'); ?>>
						<div class="<?php echo 'theplus-multiscroll-left-' . esc_attr($id); ?>">
							<?php foreach( $templates as $index => $section ) : ?>
							<div <?php echo $this->get_render_attribute_string('multi_left_template'); ?>>
								<?php
								
									if(!empty($section['multi_left_template']) ){
										echo Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($section['multi_left_template']);
									}
								?>
							</div>
							<?php endforeach; ?>
						</div>
						<div class="<?php echo 'theplus-multiscroll-right-' . esc_attr($id); ?>">
							<?php foreach( $templates as $index => $section ) : ?>
							<div <?php echo $this->get_render_attribute_string('multi_right_template'); ?>>
								<?php                        
									if(!empty($section['multi_right_template'])){
										echo Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display($section['multi_right_template']);
									}
								?>
							</div>
							<?php endforeach; ?>					
						</div>
					</div>
				</div>
			<?php
			}
			
			
		echo '</div>';
		
		$mob_hidden_class = '';
		if($settings['multi_scroll_responsive_mobile']=='yes'){
			$mob_hidden_class = 'ms-mobs-hidd';
		}
		
		$tabs_hidden_class = '';
		if($settings['multi_scroll_responsive_tabs']=='yes'){
			$tabs_hidden_class = 'ms-tabs-hidd';
		}
		
		
		if(!empty($show_next_prev) && $show_next_prev=='yes'){		
			echo '<div id="ps'.$id.'" class="fp-nxt-prev '.esc_attr($mob_hidden_class).' '.esc_attr($tabs_hidden_class).' '.esc_attr($next_prev_style).' '.esc_attr($fp_nav_position).' '.esc_attr($next_prev_direction).'">';
				if($next_prev_style=='style-1' || $next_prev_style=='style-2'){
					echo '<div class="fp-nav-btn fp-nav-prev '.esc_attr($prev_size).'"><i class="fa fa-angle-left" aria-hidden="true"></i></div>';
					echo '<div class="fp-nav-btn fp-nav-next '.esc_attr($next_size).'"><i class="fa fa-angle-right" aria-hidden="true"></i></div>';
				}elseif($next_prev_style=='style-3'){
					echo '<div class="fp-nav-btn fp-nav-prev">'.esc_attr($prev_txt).'</div>';
					echo '<div class="fp-nav-btn fp-nav-next">'.esc_attr($nxt_txt).'</div>';
				}elseif($next_prev_style=='custom' && !empty($nav_prev_image) && !empty($nav_next_image)){			
					echo '<div class="fp-nav-btn fp-nav-prev"><img src="'.esc_url($nav_prev_image).'"></div>';
					echo '<div class="fp-nav-btn fp-nav-next"><img src="'.esc_url($nav_next_image).'"></div>';
				}
			echo '</div>';
		}
	}
}