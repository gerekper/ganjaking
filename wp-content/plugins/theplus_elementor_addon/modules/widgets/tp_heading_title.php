<?php
/*
Widget Name: Heading Title 
Description: Creative Heading Options.
Author: Theplus
Author URI: https://posimyth.com
*/
namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Text_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Theplus_Ele_Heading_Title extends Widget_Base {
		
	public function get_name() {
		return 'tp-heading-title';
	}

    public function get_title() {
        return esc_html__('Heading Title', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-header theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }
	
	protected function register_controls() {
		/*tab Layout */
		$this->start_controls_section(
			'heading_title_layout_section',
			[
				'label' => esc_html__( 'Content', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control(
            'heading_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Style', 'theplus'),
                'default' => 'style_1',
                'options' => [
                    'style_1' => esc_html__('Modern', 'theplus'),
                    'style_2' => esc_html__('Simple', 'theplus'),
                    'style_4' => esc_html__('Classic', 'theplus'),
                    'style_5' => esc_html__('Double Border', 'theplus'),
                    'style_6' => esc_html__('Vertical Border', 'theplus'),
                    'style_7' => esc_html__('Dashing Dots', 'theplus'),
                    'style_8' => esc_html__('Unique', 'theplus'),
                    'style_9' => esc_html__('Stylish', 'theplus'),
                    'style_10' => esc_html__('Animated Split', 'theplus'),
                ],
            ]
        );
		$this->add_control(
            'ani_split_type', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Type', 'theplus'),
                'default' => 'words',
                'options' => [
                    'words' => esc_html__('Word', 'theplus'),
                    'chars' => esc_html__('Character', 'theplus'),
                    'lines' => esc_html__('Line', 'theplus'),
                ],
				'condition' => [
					'heading_style' => 'style_10',
				],
            ]
        );
		$this->add_control(
			'select_heading',
			[
				'label' => esc_html__( 'Select Heading', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'default',
				'options' => [
					'default'  => esc_html__( 'Default', 'theplus' ),
					'page_title' => esc_html__( 'Page Title', 'theplus' ),					
				],
				'condition' => [
					'heading_style!' => 'style_10',
				],
			]
		);
		$this->add_control(
            'title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Heading Title', 'theplus'),
                'label_block' => true,
                'default' => esc_html__('Heading', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [					
					'select_heading' => 'default',
				],
            ]
        );		
		$this->add_control(
            'sub_title',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Sub Title', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => esc_html__('Sub Title', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'heading_style!' => 'style_10',
				],
            ]
        );
		$this->add_control(
            'title_s',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Extra Title', 'theplus'),
                'label_block' => true,
                'separator' => 'before',
                'default' => esc_html__('Title', 'theplus'),
				'dynamic' => [
					'active'   => true,
				],
				'condition' => [
					'heading_style' => 'style_1',
				],
            ]
        );
		$this->add_control(
            'heading_s_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Extra Title Position', 'theplus'),
                'default' => 'text_after',
                'options' => [
                    'text_after' => esc_html__('Postfix', 'theplus'),
                    'text_before' => esc_html__('Prefix', 'theplus'),
                ],
				'condition' => [
					'heading_style' => 'style_1',
				],
            ]
        );
		$this->add_responsive_control(
			'sub_title_align',
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
					'justify' => [
						'title' => esc_html__( 'Justify', 'theplus' ),
						'icon' => 'eicon-text-align-justify',
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				'prefix_class' => 'text-%s',
				'default' => 'center',
				'separator' => 'before',
				'condition' => [
					'heading_style!' => 'style_6',
				],
			]
		);
		$this->add_control(
			'heading_title_subtitle_limit',
			[
				'label' => esc_html__( 'Heading Title & Sub Title Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'heading_style!' => 'style_10',
				],
			]
		);
		$this->add_control(
			'display_heading_title_limit',
			[
				'label' => esc_html__( 'Heading Title Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
            'display_heading_title_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Limit on', 'theplus'),
                'default' => 'char',
                'options' => [
                    'char' => esc_html__('Character', 'theplus'),
                    'word' => esc_html__('Word', 'theplus'),                    
                ],
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_heading_title_limit'    => 'yes',
				],
            ]
        );
		$this->add_control(
			'display_heading_title_input',
			[
				'label' => esc_html__( 'Heading Title Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,				
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_heading_title_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
			'display_title_3_dots',
			[
				'label' => esc_html__( 'Display Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_heading_title_limit'    => 'yes',
				],
			]
		);
		
		$this->add_control(
			'display_sub_title_limit',
			[
				'label' => esc_html__( 'Sub Title Limit', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
            'display_sub_title_by', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Limit on', 'theplus'),
                'default' => 'char',
                'options' => [
                    'char' => esc_html__('Character', 'theplus'),
                    'word' => esc_html__('Word', 'theplus'),                    
                ],
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_sub_title_limit'    => 'yes',
				],
            ]
        );
		$this->add_control(
			'display_sub_title_input',
			[
				'label' => esc_html__( 'Sub Title Count', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,				
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_sub_title_limit'    => 'yes',
				],
			]
		);
		$this->add_control(
			'display_sub_title_3_dots',
			[
				'label' => esc_html__( 'Display Dots', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'condition'   => [
					'heading_style!' => 'style_10',
					'heading_title_subtitle_limit'    => 'yes',
					'display_sub_title_limit'    => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*tab style/Layout*/		
		
		/*tab animation*/
		$this->start_controls_section(
			'heading_title_animation_section',
			[
				'label' => esc_html__( 'Animated Split Text Options', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
				'condition'   => [
					'heading_style' => 'style_10',					
				],
			]
		);
		$this->add_control(
            'hst_animation_type', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Animation Effect', 'theplus'),
                'default' => 'default',
                'options' => [
                    'default' => esc_html__('Default', 'theplus'),
                    'Power0.easeNone' => esc_html__('Power 0 easeNone', 'theplus'),
                    'Power1.easeInOut' => esc_html__('Power 1 easeInOut', 'theplus'),
                    'Power1.easeIn' => esc_html__('Power 1 easeIn', 'theplus'),
                    'Power1.easeOut' => esc_html__('Power 1 easeOut', 'theplus'),
                    'Power2.easeInOut' => esc_html__('Power 2 easeInOut', 'theplus'),
                    'Power2.easeIn' => esc_html__('Power 2 easeIn', 'theplus'),
                    'Power2.easeOut' => esc_html__('Power 2 easeOut', 'theplus'),
                    'Power3.easeInOut' => esc_html__('Power 3 easeInOut', 'theplus'),
                    'Power3.easeIn' => esc_html__('Power 3 easeIn', 'theplus'),
                    'Power3.easeOut' => esc_html__('Power3 easeOut', 'theplus'),
                    'Power4.easeInOut' => esc_html__('Power 4 easeInOut', 'theplus'),
                    'Power4.easeIn' => esc_html__('Power 4 easeIn', 'theplus'),
                    'Power4.easeOut' => esc_html__('Power 4 easeOut', 'theplus'),
                    'Back.easeOut' => esc_html__('Back easeOut', 'theplus'),
                    'Elastic.easeInOut' => esc_html__('Elastic easeInOut', 'theplus'),
                    'Elastic.easeIn' => esc_html__('Elastic easeIn', 'theplus'),
                    'Elastic.easeOut' => esc_html__('Elastic easeOut', 'theplus'),
                    'Bounce.easeInOut' => esc_html__('Bounce easeInOut', 'theplus'),
                    'Bounce.easeIn' => esc_html__('Bounce easeIn', 'theplus'),
                    'Bounce.easeOut' => esc_html__('Bounce easeOut', 'theplus'),
                ],
            ]
        );
		$this->add_control(
			'hst_animation_x', [
				'label' => esc_html__( 'X', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -700,
						'max' => 700,
						'step' => 10,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_y', [
				'label' => esc_html__( 'Y', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 100,
				],
				'range' => [
					'px' => [
						'min' => -700,
						'max' => 700,
						'step' => 10,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_z', [
				'label' => esc_html__( 'Z', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -700,
						'max' => 700,
						'step' => 10,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_scale', [
				'label' => esc_html__( 'Scale', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_rotation', [
				'label' => esc_html__( 'Rotate', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -700,
						'max' => 700,
						'step' => 10,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_speed', [
				'label' => esc_html__( 'Speed', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 1,
				],
				'range' => [
					'px' => [
						'min' => 0.01,
						'max' => 3,
						'step' => 0.01,
					],
				],
			]
		);
		$this->add_control(
			'hst_animation_delay', [
				'label' => esc_html__( 'Delay', 'theplus' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'unit' => 'px',
					'size' => 0.02,
				],
				'range' => [
					'px' => [
						'min' => 0.01,
						'max' => 1,
						'step' => 0.01,
					],
				],
			]
		);		
		$this->end_controls_section();
		/*tab animation*/

		/*tab style*/
		$this->start_controls_section(
            'section_animatedtextstyling',
            [
                'label' => esc_html__('Animated Split Text', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'heading_style' => 'style_10',
				],
            ]
        );
		$this->add_control(
            'ast_title', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Title Tag', 'theplus'),
                'default' => 'div',
                'options' => theplus_get_tags_options('a'),
            ]
        );
		$this->add_control(
			'ast_title_link',
			[
				'label' => esc_html__( 'Heading Title Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'after',
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'ast_title' => 'a',
				],
			]
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'anitext_typography',
                'label' => esc_html__('Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .heading.style-10 .sub-style',
            ]
        );
		$this->add_control(
			'title_anitext_n',
			[
				'label' => esc_html__( 'Color', 'theplus' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .heading.style-10 .sub-style' => 'color: {{VALUE}};',
				],
			]
		);
		$this->end_controls_section();
		/*tab style/Layout*/

		/*tab style*/
		$this->start_controls_section(
            'section_styling',
            [
                'label' => esc_html__('Separator Settings', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'heading_style!' => ['style_1','style_2','style_8','style_10'],
				],
            ]
        );
		$this->add_control('input_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%'],
				'selectors' => [
					'{{WRAPPER}} .seprator ' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
            'double_color',
            [
                'label' => esc_html__('Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#4d4d4d',
                'selectors' => [
                    '{{WRAPPER}} .heading.style-5 .heading-title:before,{{WRAPPER}} .heading.style-5 .heading-title:after' => 'background: {{VALUE}};',
                ],
				'condition'    => [
					'heading_style' => 'style_5',
				],
            ]
        );
		$this->add_control(
            'double_top',
			[
				'label' => esc_html__( 'Top Separator Height', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'min' => -50,
				'step' => 1,
				'default' => 6,
				'condition'    => [
					'heading_style' => 'style_5',
				],
				'selectors' => [
                    '{{WRAPPER}} .heading.style-5 .heading-title:before' => 'height: {{VALUE}}px;',
                ],
				
			]
        );
		$this->add_control(
            'double_bottom',
			[
				'label' => esc_html__( 'Bottom Separator Height', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'min' => -50,
				'step' => 1,
				'default' => 2,
				'condition'    => [
					'heading_style' => 'style_5',
				],
				'selectors' => [
                    '{{WRAPPER}} .heading.style-5 .heading-title:after' => 'height: {{VALUE}}px;',
                ],
				
			]
        );
		$this->add_control(
			'sep_img',
			[
				'label' => esc_html__( 'Separator With Image', 'theplus' ),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => '',
				],
				'condition'    => [
					'heading_style' => 'style_4',
				],
			]
		);
		$this->add_control(
            'sep_clr',
            [
                'label' => esc_html__('Separator Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#4099c3',
                'selectors' => [
                    '{{WRAPPER}} .heading .title-sep' => 'border-color: {{VALUE}};',
                ],
				'condition'    => [
					'heading_style' => ['style_4','style_9'],
				],
            ]
        );
		$this->add_responsive_control(
            'sep_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator Width', 'theplus'),
				'size_units' => [ '%', 'px' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'range' => [
					'' => [
						'min' => 0,
						'max' => 100,
						'step' => 2,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
                    '{{WRAPPER}} .heading .title-sep,{{WRAPPER}} .heading .seprator' => 'width: {{SIZE}}{{UNIT}};',
                ],
				'condition'    => [
					'heading_style' => ['style_4','style_9'],
				],
            ]
        );
		$this->add_control(
            'dot_color',
            [
                'label' => esc_html__('Separator Dot Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#ca2b2b',
                'selectors' => [
					'{{WRAPPER}} .heading .sep-dot' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .heading.style-7 .head-title:after' => 'color: {{VALUE}}; text-shadow: 15px 0 {{VALUE}}, -15px 0 {{VALUE}};',
                ],
				'condition'    => [
					'heading_style' => ['style_7','style_9'],
				],
            ]
        );
		$this->add_control(
            'seprator_dot_offfset',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Offset', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'condition'    => [
					'heading_style' => ['style_7','style_9'],
				],
				'selectors' => [
					'{{WRAPPER}} .heading.style-7 .head-title:after,{{WRAPPER}} .heading.style-9 .sep-dot' => 'top: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_responsive_control(
            'sep_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Separator Height', 'theplus'),
				'size_units' => [ 'px' ],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'range' => [
					'' => [
						'min' => 0,
						'max' => 10,
						'step' => 1,
					],
				],
				'render_type' => 'ui',
				'selectors' => [
                    '{{WRAPPER}} .heading .title-sep' => 'border-width: {{SIZE}}{{UNIT}};',
                ],
				'condition'    => [
					'heading_style' => 'style_4',
				],
            ]
        );
		$this->add_control(
            'top_clr_width',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Width', 'theplus'),
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 20,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 2,
				],
				'render_type' => 'ui',
				'condition'    => [
					'heading_style' => 'style_6',
				],
				'selectors' => [
					'{{WRAPPER}} .heading .vertical-divider' => 'width: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
            'top_clr_height',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Height', 'theplus'),
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
					'size' => 30,
				],
				'render_type' => 'ui',
				'condition'    => [
					'heading_style' => 'style_6',
				],
				'selectors' => [
					'{{WRAPPER}} .heading .vertical-divider' => 'height: {{SIZE}}{{UNIT}}',
				],
            ]
        );
		$this->add_control(
            'top_clr',
            [
                'label' => esc_html__('Separator Vertical Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '#1e73be',
                'selectors' => [
                    '{{WRAPPER}} .heading .vertical-divider' => 'background-color: {{VALUE}};',
                ],
				'condition'    => [
					'heading_style' => 'style_6',
					'top_clr_bg_color!' => 'yes',
				],
            ]
        );
		$this->add_control(
            'top_clr_bg_color',
            [
				'label'   => esc_html__( 'Background', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition'    => [
					'heading_style' => 'style_6',
				],
			]			
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'top_clr_bg_color_select',
				'label' => esc_html__( 'Color', 'theplus' ),
				'types' => [ 'classic', 'gradient'],
				'selector' => '{{WRAPPER}} .heading .vertical-divider',
				'condition'    => [
					'heading_style' => 'style_6',
					'top_clr_bg_color' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*tab style*/
		/*tab Main Title Style*/
		$this->start_controls_section(
            'section_title_styling',
            [
                'label' => esc_html__('Main Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'condition'   => [
					'heading_style!' => 'style_10',	
					'title!' => '',				
				],								
            ]
        );
		$this->add_control(
            'title_h', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Title Tag', 'theplus'),
                'default' => 'h2',
                'options' => theplus_get_tags_options('a'),
            ]
        );
		$this->add_control(
			'title_link',
			[
				'label' => esc_html__( 'Heading Title Link', 'theplus' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
				],
				'separator' => 'after',
				'placeholder' => esc_html__( 'https://www.demo-link.com', 'theplus' ),
				'condition' => [
					'title_h' => 'a',
				],
			]
		);	
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'title_typography',
                'label' => esc_html__('Title Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .heading .heading-title',
            ]
        );
		$this->add_control(
			'title_color',
			[
				'label' => esc_html__( 'Title Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
				'toggle' => true,
			]
		);
		$this->add_control(
			'title_solid_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .heading .heading-title' => 'color: {{VALUE}};',
				],
				'default' => '#313131',
				'condition'    => [
					'title_color' => ['solid'],
				],
			]
		);
		$this->add_control(
            'title_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition'    => [
					'title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition'    => [
					'title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition'    => [
					'title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition'    => [
					'title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition'    => [
					'title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'title_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .heading .heading-title' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'title_color' => ['gradient'],
					'title_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control('title_gradient_position', 
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .heading .heading-title' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{title_gradient_color1.VALUE}} {{title_gradient_color1_control.SIZE}}{{title_gradient_color1_control.UNIT}}, {{title_gradient_color2.VALUE}} {{title_gradient_color2_control.SIZE}}{{title_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'title_color' => [ 'gradient' ],
					'title_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_group_control(
			Group_Control_Text_Shadow::get_type(),
			[
				'name' => 'title_shadow',
				'selectors' => '{{WRAPPER}} .heading .heading-title',
				'separator' => 'before',
			]
		);
		$this->add_responsive_control('s_maintitle_pg',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition' => [
					'layout!' => [ 'carousel' ]
				],
				'selectors' => [
					'{{WRAPPER}} .heading_style .head-title .heading-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'heading_style' => [ 'style_1', 'style_2' ],
				],
			]
		);
		$this->add_control(
            'special_effect',
            [
				'label'   => esc_html__( 'Special Effect', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'heading_style' => [ 'style_1','style_2','style_8' ],
				],
			]			
		);
		$this->add_group_control(
			\Theplus_Overlay_Special_Effect_Group::get_type(),
			 [
				'label' => esc_html__( 'Overlay Color', 'theplus' ),
				'name'  => 'overlay_spcial',
				'condition' => [
					'special_effect' => 'yes',
				],
			]
		);
		$this->end_controls_section();
		/*tab Title Style*/
		/*tab Sub Title Style*/
		$this->start_controls_section(
            'section_sub_title_styling',
            [
                'label' => esc_html__('Sub Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,				
				'condition'   => [
					'heading_style!' => 'style_10',	
					'sub_title!' => '',		
				],
            ]
        );
		$this->add_control(
            'sub_title_tag', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Subtitle Tag', 'theplus'),
                'default' => 'h3',
                'options' => theplus_get_tags_options(),
            ]
        );
		
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'sub_title_typography',
                'label' => esc_html__('Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .heading .heading-sub-title',
            ]
        );
		$this->add_control(
			'sub_title_color',
			[
				'label' => esc_html__( 'Subtitle Title Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
				'toggle' => true,
			]
		);
		$this->add_control(
			'sub_title_solid_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .heading .heading-sub-title' => 'color: {{VALUE}};',
				],
				'default' => '#313131',
				'condition'    => [
					'sub_title_color' => ['solid'],
				],
			]
		);
		$this->add_control(
            'sub_title_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition'    => [
					'sub_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'sub_title_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition'    => [
					'sub_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'sub_title_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition'    => [
					'sub_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'sub_title_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition'    => [
					'sub_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'sub_title_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition'    => [
					'sub_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'sub_title_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .heading .heading-sub-title' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{sub_title_gradient_color1.VALUE}} {{sub_title_gradient_color1_control.SIZE}}{{sub_title_gradient_color1_control.UNIT}}, {{sub_title_gradient_color2.VALUE}} {{sub_title_gradient_color2_control.SIZE}}{{sub_title_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'sub_title_color' => ['gradient'],
					'sub_title_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'sub_title_gradient_position', 
			[
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .heading .heading-sub-title' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{sub_title_gradient_color1.VALUE}} {{sub_title_gradient_color1_control.SIZE}}{{sub_title_gradient_color1_control.UNIT}}, {{sub_title_gradient_color2.VALUE}} {{sub_title_gradient_color2_control.SIZE}}{{sub_title_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'sub_title_color' => [ 'gradient' ],
					'sub_title_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_responsive_control('s_subtitle_pg',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'condition' => [
					'layout!' => ['carousel']
				],
				'selectors' => [
					'{{WRAPPER}} .heading_style .heading-sub-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'heading_style' => [ 'style_1', 'style_2' ],
				],
			]
		);
		$this->end_controls_section();
		/*tab Extra Title Style*/
		/*tab Ex Title Style*/
		$this->start_controls_section(
            'section_extra_title_styling',
            [
                'label' => esc_html__('Extra Title', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition'    => [
					'heading_style' => 'style_1',
					'title_s!' => '',
				],
            ]
        );
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'ex_title_typography',
                'label' => esc_html__('Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .heading .title-s',
            ]
        );
		$this->add_control(
			'ex_title_color',
			[
				'label' => esc_html__( 'Extra Title Color', 'theplus' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'solid' => [
						'title' => esc_html__( 'Solid', 'theplus' ),
						'icon' => 'eicon-paint-brush',
					],
					'gradient' => [
						'title' => esc_html__( 'Gradient', 'theplus' ),
						'icon' => 'eicon-barcode',
					],
				],
				'label_block' => false,
				'default' => 'solid',
				'toggle' => true,
			]
		);
		$this->add_control(
			'ex_title_solid_color',
			[
				'label'     => esc_html__( 'Color', 'theplus' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .heading .title-s' => 'color: {{VALUE}};',
				],
				'default' => '#313131',
				'condition'    => [
					'ex_title_color' => ['solid'],
				],
			]
		);
		$this->add_control(
            'ex_title_gradient_color1',
            [
                'label' => esc_html__('Color 1', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'orange',
				'condition'    => [
					'ex_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'ex_title_gradient_color1_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 1 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 0,
				],
				'render_type' => 'ui',
				'condition'    => [
					'ex_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'ex_title_gradient_color2',
            [
                'label' => esc_html__('Color 2', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => 'cyan',
				'condition'    => [
					'ex_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'ex_title_gradient_color2_control',
            [
                'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Color 2 Location', 'theplus'),
				'size_units' => [ '%' ],
				'default' => [
					'unit' => '%',
					'size' => 100,
				],
				'render_type' => 'ui',
				'condition'    => [
					'ex_title_color' => 'gradient',
				],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'ex_title_gradient_style', [
                'type' => Controls_Manager::SELECT,
                'label' => esc_html__('Gradient Style', 'theplus'),
                'default' => 'linear',
                'options' => theplus_get_gradient_styles(),
				'condition'    => [
					'ex_title_color' => 'gradient',
					],
				'of_type' => 'gradient',
            ]
        );
		$this->add_control(
            'ex_title_gradient_angle', [
				'type' => Controls_Manager::SLIDER,
				'label' => esc_html__('Gradient Angle', 'theplus'),
				'size_units' => [ 'deg' ],
				'default' => [
					'unit' => 'deg',
					'size' => 180,
				],
				'range' => [
					'deg' => [
						'step' => 10,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .heading .title-s' => 'background-color: transparent; background-image: linear-gradient({{SIZE}}{{UNIT}}, {{ex_title_gradient_color1.VALUE}} {{ex_title_gradient_color1_control.SIZE}}{{ex_title_gradient_color1_control.UNIT}}, {{ex_title_gradient_color2.VALUE}} {{ex_title_gradient_color2_control.SIZE}}{{ex_title_gradient_color2_control.UNIT}})',
				],
				'condition'    => [
					'ex_title_color' => ['gradient'],
					'ex_title_gradient_style' => ['linear']
				],
				'of_type' => 'gradient',
			]
        );
		$this->add_control(
            'ex_title_gradient_position', [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Position', 'theplus'),
				'options' => theplus_get_position_options(),
				'default' => 'center center',
				'selectors' => [
					'{{WRAPPER}} .heading .title-s' => 'background-color: transparent; background-image: radial-gradient(at {{VALUE}}, {{ex_title_gradient_color1.VALUE}} {{ex_title_gradient_color1_control.SIZE}}{{ex_title_gradient_color1_control.UNIT}}, {{ex_title_gradient_color2.VALUE}} {{ex_title_gradient_color2_control.SIZE}}{{ex_title_gradient_color2_control.UNIT}})',
				],
				'condition' => [
					'ex_title_color' => [ 'gradient' ],
					'ex_title_gradient_style' => 'radial',
				],
				'of_type' => 'gradient',
			]
        );
		$this->end_controls_section();
		/*tab Extra Title Style*/
		
		
		/*tab Setting option*/
		$this->start_controls_section(
            'section_settings_option_styling',
            [
                'label' => esc_html__('Advanced', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'heading_style!' => 'style_10',
				],
            ]
        );
		
		$this->add_control(
            'position',
            [
				'type' => Controls_Manager::SELECT,
				'label' => esc_html__('Title Position', 'theplus'),
				'default' => 'after',
				'options' => [
					'before' => esc_html__('Before Title', 'theplus'),
					'after' => esc_html__('After Title', 'theplus'),
				],
			]
		);
		$this->add_control(
            'mobile_center_align',
            [
				'type' => Controls_Manager::SWITCHER,
				'label' => esc_html__('Center Alignment In Mobile', 'theplus'),
				'default' => 'no',				
			]
		);
		$this->end_controls_section();
		/*tab Extra Title Style*/
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
	protected function limit_words($string, $word_limit){
		$words = explode(" ",$string);
		return implode(" ",array_splice($words,0,$word_limit));
	}
	
	protected function render() {

	$settings = $this->get_settings_for_display();
	
		$heading_style = (!empty($settings["heading_style"]) ? $settings["heading_style"] : 'style_1');
		$heading_title_text='';
		if(!empty($settings["select_heading"]) && $settings["select_heading"] == "page_title"){
			$heading_title_text = get_the_title();
		}else if(!empty($settings["title"])){
			if((!empty($settings['display_heading_title_limit']) && $settings['display_heading_title_limit'] == 'yes') && !empty($settings['display_heading_title_input'])){
				if(!empty($settings['display_heading_title_by'])){
					if($settings['display_heading_title_by'] == 'char'){												
						$heading_title_text = substr($settings['title'],0,$settings['display_heading_title_input']);								
					}else if($settings['display_heading_title_by']=='word'){
						$heading_title_text = $this->limit_words($settings['title'],$settings['display_heading_title_input']);					
					}
				}
				if($settings['display_heading_title_by'] == 'char'){
					if(strlen($settings["title"]) > $settings['display_heading_title_input']){
						if(!empty($settings['display_title_3_dots']) && $settings['display_title_3_dots']=='yes'){
							$heading_title_text .='...';
						}
					}
				}else if($settings['display_heading_title_by'] == 'word'){
					if(str_word_count($settings["title"]) > $settings['display_heading_title_input']){
						if(!empty($settings['display_title_3_dots']) && $settings['display_title_3_dots']=='yes'){
							$heading_title_text .='...';
						}
					}
				}		
				
			}else{				
				$heading_title_text = $settings["title"];
			}
		}
		
		$imgSrc=$sub_gradient_cass =$title_s_gradient_cass =$title_gradient_cass ='';
		if(!empty($settings["sep_img"]["url"])){
			$image_id=$settings["sep_img"]["id"];				
			$imgSrc= tp_get_image_rander( $image_id,'full');
		}
		
		if($settings["title_color"] == "gradient") {
			$title_gradient_cass = 'heading-title-gradient';
		}
		if($settings["ex_title_color"] == "gradient") {
			$title_s_gradient_cass = 'heading-title-gradient';
		}
		if($settings["sub_title_color"] == "gradient") {
			$sub_gradient_cass = 'heading-title-gradient';
		}

		$style_class = '';
		if($heading_style == "style_1"){
			$style_class = 'style-1';
		}else if($heading_style == "style_2"){
			$style_class = 'style-2';
		}else if($heading_style == "style_4"){
			$style_class = 'style-4';
		}else if($heading_style == "style_5"){
			$style_class = 'style-5';
		}else if($heading_style == "style_6"){
			$style_class = 'style-6';
		}else if($heading_style == "style_7"){
			$style_class = 'style-7';
		}else if($heading_style == "style_8"){
			$style_class = 'style-8';
		}else if($heading_style == "style_9"){
			$style_class = 'style-9';
		}else if($heading_style == "style_10"){
			$style_class = 'style-10';
		}
		else if($heading_style == "style_11"){
			$style_class = 'style-11';
		}

		/*--On Scroll View Animation ---*/
			include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';

		/*--Plus Extra ---*/
			$PlusExtra_Class = "";
			include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

			$uid = uniqid('heading_style');
			$heading ='<div class="heading heading_style '.esc_attr($uid).' '.esc_attr($style_class).' '.$animated_class.'" '.$animation_attr.'>';
			
				$mobile_center='';
				if(!empty($settings["mobile_center_align"]) && $settings["mobile_center_align"] == 'yes'){
					if ($heading_style == "style_1" || $heading_style == "style_2" || $heading_style == "style_4" || $heading_style == "style_5"  || $heading_style == "style_7" || $heading_style == "style_9"){
						$mobile_center = 'heading-mobile-center';
					}			
				}

				$ani_split_type = !empty($settings['ani_split_type']) ? $settings['ani_split_type'] : 'words';

				$annimtypedtaattr = $cls = $htaattrbunch= '';
				if($heading_style == "style_10" && !empty($ani_split_type)){
					$ani_split_typesel = $ani_split_type;					 
					 if($ani_split_type==='lines'){
					 	$ani_split_typesel = 'lines,chars';
					 }
					$annimtypedtaattr .= ' data-animsplit-type="'.$ani_split_typesel.'"';
					$cls = $ani_split_type;


					/*data attribute*/
					$hst_animation_type = !empty($settings["hst_animation_type"]) ? $settings["hst_animation_type"] : 'default';
					$hst_animation_x = !empty($settings["hst_animation_x"]) ? $settings["hst_animation_x"]["size"] : 0;
					$hst_animation_y = !empty($settings["hst_animation_y"]) ? $settings["hst_animation_y"]["size"] : 100;
					$hst_animation_z = !empty($settings["hst_animation_z"]) ? $settings["hst_animation_z"]["size"] : 0;
					$hst_animation_scale = !empty($settings["hst_animation_scale"]) ? $settings["hst_animation_scale"]["size"] : 0;
					$hst_animation_rotation = !empty($settings["hst_animation_rotation"]) ? $settings["hst_animation_rotation"]["size"] : 0;
					$hst_animation_speed = !empty($settings["hst_animation_speed"]) ? $settings["hst_animation_speed"]["size"] : 1;
					$hst_animation_delay = !empty($settings["hst_animation_delay"]) ? $settings["hst_animation_delay"]["size"] : 0.02;

					$htaattr =[
						'effect' => $hst_animation_type,
						'x' => $hst_animation_x,
						'y' => $hst_animation_y,
						'z' => $hst_animation_z,
						'scale' => $hst_animation_scale,
						'rotation' => $hst_animation_rotation,
						'speed' => $hst_animation_speed,
						'delay' => $hst_animation_delay,
					];
					$htaattrbunch= 'data-aniattrht = '.json_encode($htaattr);
				}
				
				$ast_title = !empty($settings['ast_title']) ? $settings['ast_title'] : 'div';

				if ($heading_style == "style_10" && !empty($heading_title_text)){
					/*link*/
					if ( ! empty( $settings['ast_title_link']['url'] ) ) {
						$this->add_render_attribute( 'ast_ani_link', 'href', $settings['ast_title_link']['url'] );
						if ( $settings['ast_title_link']['is_external'] ) {
							$this->add_render_attribute( 'ast_ani_link', 'target', '_blank' );
						}
						if ( $settings['ast_title_link']['nofollow'] ) {
							$this->add_render_attribute( 'ast_ani_link', 'rel', 'nofollow' );
						}
					}
					/*link*/

					$heading .='<'.esc_attr(theplus_validate_html_tag($ast_title)).' '.$this->get_render_attribute_string( "ast_ani_link" ).' class="sub-style '.esc_attr($cls).'" '.$annimtypedtaattr.' '.$htaattrbunch.'>';					
					$heading .= $heading_title_text;					
				}else{
					$heading .='<div class="sub-style '.esc_attr($cls).'" '.$annimtypedtaattr.' '.$htaattrbunch.'>';
				}

				if ($heading_style == "style_6"){
					$heading .='<div class="vertical-divider top"> </div>';
				}
					$title_con=$s_title_con=$title_s_before='';

					if($heading_style == "style_1" ){
						$title_s_before .= '<span class="title-s '.esc_attr($title_s_gradient_cass).'"> '.wp_kses_post($settings["title_s"]).' </span>';
					}

						if(!empty($heading_title_text)){
							$reveal_effects=$effect_attr='';
							if ($heading_style == "style_1" || $heading_style == "style_2" || $heading_style == "style_8"){
								if(!empty($settings["special_effect"]) && $settings["special_effect"] == 'yes'){
									$effect_rand_no = uniqid('reveal');
									$color_1 = (!empty($settings["overlay_spcial_effect_color_1"])) ? $settings["overlay_spcial_effect_color_1"] : '#313131';
									$color_2 = (!empty($settings["overlay_spcial_effect_color_2"])) ? $settings["overlay_spcial_effect_color_2"] : '#ff214f';
									$effect_attr .=' data-reveal-id="'.esc_attr($effect_rand_no).'" ';
									$effect_attr .=' data-effect-color-1="'.esc_attr($color_1).'" ';
									$effect_attr .=' data-effect-color-2="'.esc_attr($color_2).'" ';
									$reveal_effects=' pt-plus-reveal '.esc_attr($effect_rand_no).' ';
								}
							}
	
							if ( !empty( $settings['title_link']['url'] ) && $settings["title_h"] == 'a') {
									$this->add_render_attribute( 'titlehref', 'href' ,$settings['title_link']['url'] );
								if ( $settings['title_link']['is_external'] ) {
									$this->add_render_attribute( 'titlehref', 'target', '_blank' );
								}
								if ( $settings['title_link']['nofollow'] ) {
									$this->add_render_attribute( 'titlehref', 'rel', 'nofollow' );
								}
							}
			
							$title_con ='<div class="head-title '.esc_attr($mobile_center).'" > ';
								$title_con .='<'.esc_attr(theplus_validate_html_tag($settings["title_h"])).' '.$this->get_render_attribute_string( "titlehref" ).' class="heading-title '.esc_attr($mobile_center).' '.esc_attr($reveal_effects).' '.esc_attr($title_gradient_cass).'" '.$effect_attr.'  data-hover="'.esc_attr($heading_title_text).'">';
								if($settings["heading_s_style"] == "text_before"){
									$title_con.= $title_s_before.$heading_title_text;
								}else{
									$title_con.= $heading_title_text.$title_s_before;
								}
								$title_con .='</'.esc_attr(theplus_validate_html_tag($settings["title_h"])).'>';

								if ($heading_style == "style_4" || $heading_style == "style_9"){
									$title_con .= '<div class="seprator sep-l" >';
										$title_con .= '<span class="title-sep sep-l" ></span>';
										if ($heading_style == "style_9" ){
											$title_con .= '<div class="sep-dot">.</div>';
										}else{	
											if(!empty($imgSrc)){  
												$title_con .= '<div class="sep-mg">'.$imgSrc.'</div>';
											}
										}
									$title_con .='<span class="title-sep sep-r" ></span>';
									$title_con .='</div>';
								}
							$title_con .='</div>';
						}
						$sub_title_dis='';
						if(!empty($settings["sub_title"])){
							if((!empty($settings['display_sub_title_limit']) && $settings['display_sub_title_limit'] == 'yes') && !empty($settings['display_sub_title_input'])){
									if(!empty($settings['display_sub_title_by'])){
										if($settings['display_sub_title_by'] == 'char'){
											$sub_title_dis = substr($settings['sub_title'],0,$settings['display_sub_title_input']);										
										}else if($settings['display_sub_title_by'] == 'word'){
											$sub_title_dis = $this->limit_words($settings['sub_title'],$settings['display_sub_title_input']);					
										}
									}

									if($settings['display_sub_title_by'] == 'char'){
										if(strlen($settings["sub_title"]) > $settings['display_heading_title_input']){
											if(!empty($settings['display_sub_title_3_dots']) && $settings['display_sub_title_3_dots']=='yes'){
												$sub_title_dis .='...';
											}
										}
									}else if($settings['display_sub_title_by'] == 'word'){
										if(str_word_count($settings["sub_title"]) > $settings['display_heading_title_input']){
											if(!empty($settings['display_sub_title_3_dots']) && $settings['display_sub_title_3_dots']=='yes'){
												$sub_title_dis .='...';
											}
										}
									}
	
								}else{
									$sub_title_dis = $settings['sub_title'];
								}
							$s_title_con ='<div class="sub-heading">';
								$s_title_con .='<'.esc_attr(theplus_validate_html_tag($settings["sub_title_tag"])).' class="heading-sub-title '.esc_attr($mobile_center).' '.esc_attr($sub_gradient_cass).'"> '.wp_kses_post($sub_title_dis).' </'.esc_attr(theplus_validate_html_tag($settings["sub_title_tag"])).'>';
							$s_title_con .='</div>';
						}
						if($settings["position"] == "before"){
							$heading .= $s_title_con.$title_con;
						}if($settings["position"] == "after"){
							$heading .= $title_con.$s_title_con;
						}
				if ($heading_style == "style_6"){
					$heading .='<div class="vertical-divider bottom"> </div>';
				}

				if ($heading_style == "style_10" && !empty($heading_title_text)){
					$heading.='</'.esc_attr(theplus_validate_html_tag($ast_title)).'>';
				}else{
					$heading.='</div>';
				}
			$heading.='</div>';

		echo $before_content.$heading.$after_content;
	}
    protected function content_template() {
	
    }
}