<?php 
/*
Widget Name: Countdown 
Description: Display countdown.
Author: Theplus
Author URI: https://posimyth.com
*/

namespace TheplusAddons\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Utils;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;

use TheplusAddons\Theplus_Element_Load;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class ThePlus_Countdown extends Widget_Base {
		
	public function get_name() {
		return 'tp-countdown';
	}

    public function get_title() {
        return esc_html__('Countdown', 'theplus');
    }

    public function get_icon() {
        return 'fa fa-clock-o theplus_backend_icon';
    }

    public function get_categories() {
        return array('plus-essential');
    }

	public function get_keywords() {
		return ['Countdown', 'Count down', 'time', 'fake number', 'tp', 'theplus'];
	}
	
    protected function register_controls() {
		
		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__( 'Countdown Date', 'theplus' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);
		$this->add_control('CDType',
			[
				'label' => esc_html__( 'Countdown Setup', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal'  => esc_html__( 'Normal Countdown', 'theplus' ),
					'scarcity' => esc_html__( 'Scarcity Countdown (Evergreen)', 'theplus' ),
					'numbers' => esc_html__( 'Fake Numbers Counter', 'theplus' ),
				],
			]
		);
		$this->add_control('CDstyle',
			[
				'label' => esc_html__( 'Countdown Style', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'style-1',
				'options' => [
					'style-1'  => esc_html__( 'Style 1', 'theplus' ),
					'style-2' => esc_html__( 'Style 2', 'theplus' ),
					'style-3' => esc_html__( 'Style 3', 'theplus' ),
				],
				'condition' => [
					'CDType' => ['normal','scarcity'],
				],
			]
		);
		$this->add_control('counting_timer',
			[
				'label' => esc_html__( 'Launch Date', 'theplus' ),
				'type' => Controls_Manager::DATE_TIME,
				'label_block' => false,
				'default'     => date( 'Y-m-d H:i', strtotime( '+1 month' ) + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) ),
				'description' => sprintf( esc_html__( 'Date set according to your timezone: %s.', 'theplus' ), Utils::get_timezone_string() ),
				'condition' => [
					'CDType' => 'normal',
				],
			]
		);
		$this->add_control('inline_style',
			[
				'label' => esc_html__( 'Inline Style', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'On', 'theplus' ),
				'label_off' => esc_html__( 'Off', 'theplus' ),
				'default' => 'no',
				'separator' => 'before',
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-1',
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section('section_downcount',
            [
                'label' => esc_html__('Content Source', 'theplus'),
				'condition' => [
					'CDType' => ['normal','scarcity'],
				],
            ]
        );

		$this->add_control('dayslabels',
			[
				'label' => esc_html__( 'Days', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
			]
		);

		$this->add_control('hourslabels',
			[
				'label'   => esc_html__( 'Hours', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
			]
		);

		$this->add_control('minuteslabels',
			[
				'label'   => esc_html__( 'Minutes', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
			]
		);

		$this->add_control('secondslabels',
			[
				'label'   => esc_html__( 'Seconds', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'default' => 'yes',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label'   => esc_html__( 'Show Labels', 'theplus' ),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_labels_tag',
			[
				'label' => esc_html__( 'Tag', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'h6',
				'options' => theplus_get_tags_options(),				
				'condition' => [
					'show_labels!' => '',
				],
			]
		);
		$this->add_control('text_days',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Days Section Text', 'theplus'),
                'label_block' => false,
                'default' => esc_html__('Days', 'theplus'),
				'condition'    => [
					'show_labels!' => '',
				],
            ]
        );
		$this->add_control('text_hours',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Hours Section Text', 'theplus'),
                'label_block' => false,
                'default' => esc_html__('Hours', 'theplus'),
				'condition'    => [
					'show_labels!' => '',
				],
            ]
        );
		$this->add_control('text_minutes',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Minutes Section Text', 'theplus'),
                'label_block' => false,
                'default' => esc_html__('Minutes', 'theplus'),
                'condition'    => [
					'show_labels!' => '',
				],
            ]
        );
		$this->add_control('text_seconds',
            [
                'type' => Controls_Manager::TEXT,
                'label' => esc_html__('Seconds Section Text', 'theplus'),
                'label_block' => false,
                'default' => esc_html__('Seconds', 'theplus'),
				'condition'    => [
					'show_labels!' => '',
				],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section('extraoption_downcount',
            [
                'label' => esc_html__('Extra Option', 'theplus'),
            ]
        );
		$this->add_control('fliptheme',
			[
				'label' => esc_html__( 'Theme Color', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'dark',
				'options' => [
					'dark'  => esc_html__( 'Dark', 'theplus' ),
					'light' => esc_html__( 'Light', 'theplus' ),
					'mix' => esc_html__( 'Mix', 'theplus' ),
				],
				'condition' => [
					'CDType' => ['normal','scarcity'],
				],
				'conditions' => [
					'relation' => 'or',
					'terms' => [
						['name' => 'CDstyle', 'operator' => '===', 'value' => 'style-2'],
					],
				],
			]
		);
		$this->add_control('flipMixtime',
			[
				'label' => esc_html__( 'Theme Change Time', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 1,
				'max' => 1000,
				'step' => 1,
				'default' => 3,
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
								[ 'name' => 'CDstyle','operator'=>'===','value' => 'style-2' ],
								[ 'name' => 'fliptheme', 'operator' => '===', 'value' => 'mix' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'CDstyle','operator'=>'===','value' => 'style-2' ],
								[ 'name' => 'fliptheme', 'operator' => '===', 'value' => 'mix' ],
							]
						],
					]
				],
			]
		);

		$this->add_control('cityminit',
			[
				'label' => esc_html__( 'Reset Time', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 99,
				'description' => 'Note : Enter time in minutes when you want to reset timer data.',
				'condition' => [
					'CDType' => 'scarcity',
				],
			]
		);
		$this->add_control('storetype',
			[
				'label' => esc_html__( 'Track User Data', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'normal',
				'options' => [
					'normal' => esc_html__( 'No', 'theplus' ),
					'cookie' => esc_html__( 'Yes(Local Storage Based)', 'theplus' ),
				],
				'description' => '<a rel="noopener noreferrer" target="_blank" href="https://docs.posimyth.com/tpae/countdown/">Understand this options in depth</a>',
				'condition' => [
					'CDType' => ['scarcity','numbers'],
				],
			]
		);

		$this->add_control('fackeLoop',
			[
				'label' => esc_html__( 'Enable Loop', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'theplus' ),
				'label_off' => esc_html__( 'Hide', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'numbers' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
							]
						],
					]
				],
			]
		);
		$this->add_control('delayminit',
			[
				'label' => esc_html__( 'Delay Minute', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 0,
				'condition' => [
					'CDType' => 'scarcity',
					'storetype' => 'cookie',
					'fackeLoop' => 'yes',
				],
			]
		);

		$this->add_control('initNum',
			[
				'label' => esc_html__( 'Initial Number', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 500,
				'condition' => [
					'CDType' => 'numbers',
				],
			]
		);
		$this->add_control('endNum',
			[
				'label' => esc_html__( 'Final Number', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'step' => 1,
				'default' => 99,
				'condition' => [
					'CDType' => 'numbers',
				],
			]
		);
		$this->add_control('numRange',
			[
				'label' => esc_html__( 'Number Range', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 1000,
				'step' => 1,
				'default' => 10,
				'condition' => [
					'CDType' => 'numbers',
				],
			]
		);
		$this->add_control('changeInterval',
			[
				'label' => esc_html__( 'Change Interval (In Seconds)', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 1,
				'condition' => [
					'CDType' => 'numbers',
				],
			]
		);
		$this->add_control('fackemassage',
			[
				'label'=>__('Fake Message','theplus'),
				'type'=>Controls_Manager::TEXTAREA,
				'rows'=>2,
				'default'=>__('Showing {visible_counter}','theplus'),
				'placeholder'=>__('Enter Total Message','theplus'),
				'condition' => [
					'CDType' => 'numbers',
				],
			]
		);
		$this->add_control('fackenote',
			[
				'label'=>esc_html__('Note : You can include Countdown Number like {visible_counter}.','theplus'),
				'type'=>Controls_Manager::HEADING,
                'condition' => [
					'CDType' => 'numbers',
				],
			]   
		);
		$this->add_control('expirytype',
			[
				'label' => esc_html__( 'After Expiry Action', 'theplus' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'return_value' => 'yes',
				'default' => 'yes',
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
							]
						],
					]
				],
			]
		);

		$this->add_control('countdownExpiry',
			[
				'label' => esc_html__( 'Select Action', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'None', 'theplus' ),
					'showmsg' => esc_html__( 'Message', 'theplus' ),
					'showtemp' => esc_html__( 'Template', 'theplus' ),
					'redirect' => esc_html__( 'Page Redirect', 'theplus' ),
				],
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
								[ 'name' => 'expirytype', 'operator'=>'===', 'value' => 'yes' ],
							]
						],
					]
				],
				
			]
		);
		$this->add_control('expiryMsg',
			[
				'label' => esc_html__( 'Expiry Message', 'theplus' ),
				'type' => Controls_Manager::TEXTAREA,
				'rows' => 4,
				'default' => esc_html__( 'Countdown Has Ended !', 'theplus' ),
				'placeholder' => esc_html__( 'Type your description here', 'theplus' ),
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showmsg' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity'],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
								[ 'name' => 'expirytype', 'operator'=>'===', 'value' => 'yes' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showmsg' ],
							]
						],
					]
				],
			]
		);
		$this->add_control('expiryRedirect',
			[
				'label' => esc_html__( 'Page Redirect Url', 'theplus' ),
				'type' => Controls_Manager::URL,
				'placeholder' => esc_html__( 'http://', 'theplus' ),
				'default' => [
					'url' => '',
					'is_external' => true,
					'nofollow' => true,
					'custom_attributes' => '',
				],
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'redirect' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
								[ 'name' => 'expirytype', 'operator'=>'===', 'value' => 'yes' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'redirect' ],
							]
						],
					]
				],
			]
		);
		$this->add_control('templates',
			[
				'label' => esc_html__( 'Template', 'theplus' ),
				'type' => Controls_Manager::SELECT,
				'default' => '0',
				'options' => theplus_get_templates(),
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showtemp' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'storetype','operator'=>'===','value' => 'cookie' ],
								[ 'name' => 'expirytype', 'operator'=>'===', 'value' => 'yes' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showtemp' ],
							]
						],
					]
				],
			]
		);

		$this->add_control(
			'cd_classbased',[
				'label'   => esc_html__( 'Class Based Section Visibility', 'theplus' ),
				'type'    =>  Controls_Manager::SWITCHER,
				'default' => 'no',
				'label_on' => esc_html__( 'Enable', 'theplus' ),
				'label_off' => esc_html__( 'Disable', 'theplus' ),
				'separator' => 'before',
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle!' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'cd_classbased_note',
			[				
				'type' => \Elementor\Controls_Manager::RAW_HTML,
				'raw' => 'for more info <a href="#" target="_blank">Click Here</a>',
				'content_classes' => 'tp-widget-description',
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'cd_classbased' => 'yes',
					'CDstyle!' => ['style-3'],
				],	
			]
		);
		$this->add_control(
			'cd_class_1',
			[
				'label' => esc_html__( 'During Countdown Class', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'During Countdown', 'theplus' ),
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'cd_classbased' => 'yes',
					'CDstyle!' => ['style-3'],
				],
			]
		);
		$this->add_control(
			'cd_class_2',
			[
				'label' => esc_html__( 'After Expiry Class', 'theplus' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'placeholder' => esc_html__( 'After Expiry', 'theplus' ),
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'cd_classbased' => 'yes',
					'CDstyle!' => ['style-3'],
				],
			]
		);
		$this->end_controls_section();

		$this->start_controls_section('section_styling',
            [
                'label' => esc_html__('Counter', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-1',
				],
            ]
        );
		$this->add_control(
            'number_text_color',
            [
                'label' => esc_html__('Counter Font Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li > span' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Typography::get_type(),
			array(
				'name' => 'numbers_typography',
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_TEXT
                ],
				'selector' => '{{WRAPPER}}  .pt_plus_countdown li > span',
				'separator' => 'after',
			)
		);
		$this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name' => 'label_typography',
                'label' => esc_html__('Label Typography', 'theplus'),
                'selector' => '{{WRAPPER}} .pt_plus_countdown li > .label-ref',
				'separator' => 'after',
				'condition'    => [
					'show_labels!' => '',
				],
            ]
        );
		$this->start_controls_tabs( 'tabs_days_style' );

		$this->start_controls_tab(
			'tab_day_style',
			[
				'label' => esc_html__( 'Days', 'theplus' ),
			]
		);
		$this->add_control(
            'days_text_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_1 .label-ref' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
            'days_border_color',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_1' => 'border-color:{{VALUE}};',
                ],
                'condition'    => [
                    'inline_style!' => 'yes',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'days_background',
				'label'     => esc_html__("Days Background",'theplus'),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_countdown li.count_1',
				
			]
		);
		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_hour_style',
			[
				'label' => esc_html__( 'Hours', 'theplus' ),
			]
		);
		$this->add_control(
            'hours_text_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_2 .label-ref' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
            'hours_border_color',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_2' => 'border-color:{{VALUE}};',
                ],
                'condition'    => [
                    'inline_style!' => 'yes',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'hours_background',
				'label'     => esc_html__("Background",'theplus'),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_countdown li.count_2',				
			]
		);
		$this->end_controls_tab();
		
		$this->start_controls_tab(
			'tab_minute_style',
			[
				'label' => esc_html__( 'Minutes', 'theplus' ),
			]
		);
		$this->add_control(
            'minutes_text_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_3 .label-ref' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
            'minutes_border_color',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_3' => 'border-color:{{VALUE}};',
                ],
                'condition'    => [
                    'inline_style!' => 'yes',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'minutes_background',
				'label'     => esc_html__("Background",'theplus'),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_countdown li.count_3',				
			]
		);
		$this->end_controls_tab();
		$this->start_controls_tab(
			'tab_second_style',
			[
				'label' => esc_html__( 'Seconds', 'theplus' ),
			]
		);
		$this->add_control(
            'seconds_text_color',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_4 .label-ref' => 'color:{{VALUE}};',
                ],
            ]
        );
		$this->add_control(
            'seconds_border_color',
            [
                'label' => esc_html__('Border Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .pt_plus_countdown li.count_4' => 'border-color:{{VALUE}};',
                ],
                'condition'    => [
                    'inline_style!' => 'yes',
                ],
            ]
        );
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'seconds_background',
				'label'     => esc_html__("Background",'theplus'),
				'types'     => [ 'classic', 'gradient' ],
				'selector'  => '{{WRAPPER}} .pt_plus_countdown li.count_4',
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->add_responsive_control(
			'counter_padding',
			[
				'label' => esc_html__( 'Padding', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .pt_plus_countdown li' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_responsive_control(
			'counter_margin',
			[
				'label' => esc_html__( 'Margin', 'theplus' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],				
				'selectors' => [
					'{{WRAPPER}} .pt_plus_countdown li' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],				
			]
		);
		$this->add_control(
			'count_border_style',
			[
				'label'   => esc_html__( 'Border Style', 'theplus' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'solid',
				'options' => [
					'none'   => esc_html__( 'None', 'theplus' ),
					'solid'  => esc_html__( 'Solid', 'theplus' ),
					'dotted' => esc_html__( 'Dotted', 'theplus' ),
					'dashed' => esc_html__( 'Dashed', 'theplus' ),
					'groove' => esc_html__( 'Groove', 'theplus' ),
				],
				'separator' => 'before',
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_countdown li' => 'border-style: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'count_border_width',
			[
				'label' => esc_html__( 'Border Width', 'theplus' ),
				'type'  => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'default' => [
					'top'    => 3,
					'right'  => 3,
					'bottom' => 3,
					'left'   => 3,
				],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_countdown li' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'count_border_style!' => 'none',
				]
			]
		);
		$this->add_control(
			'count_border_radius',
			[
				'label'      => esc_html__( 'Border Radius', 'theplus' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [
					'{{WRAPPER}} .pt_plus_countdown li' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'count_hover_shadow',
				'selector' => '{{WRAPPER}} .pt_plus_countdown li',
				'separator' => 'before',
			]			
		);
        $this->end_controls_section();

		/*style 2 start*/
		$this->start_controls_section('style2text_styling',
			[
				'label' => esc_html__('Label', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-2',					
					'show_labels' => 'yes',					
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
			[
				'name'=>'s2texttypo',
				'label'=>esc_html__('Typography','theplus'),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
				'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group .rotor-group-heading',
			]
		);
		$this->start_controls_tabs('s32_tabs');
		$this->start_controls_tab('s2_text_days',
			[
				'label'=>esc_html__('Days','theplus')
			]
		); 	
		$this->add_control('s2daytextdcr',
			[
				'label' => esc_html__('Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(1) .rotor-group-heading:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2daytextdbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(1) .rotor-group-heading:before',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'s2daytextdb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(1) .rotor-group-heading:before',
            ]
        );
        $this->add_responsive_control('s2daytextdbrs',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(1) .rotor-group-heading:before'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'s2daytextdsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(1) .rotor-group-heading:before',
            ]
        );
		$this->end_controls_tab();
        $this->start_controls_tab('s2_text_hours',
            [
                'label'=>esc_html__('Hours','theplus')
            ]
        ); 	
		$this->add_control('s2hoursnumberncr',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(2) .rotor-group-heading:before' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2daytexttbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(2) .rotor-group-heading:before',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'s2daytexttdb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(2) .rotor-group-heading:before',
            ]
        );
        $this->add_responsive_control('s2daytexttbrs',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(2) .rotor-group-heading:before'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'s2daytexttsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(2) .rotor-group-heading:before',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('s2_text_minutes',
            [
                'label'=>esc_html__('Minutes','theplus')
            ]
        ); 	
		$this->add_control('s2minutesnumberncr',
			[
				'label' => esc_html__('Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(3) .rotor-group-heading:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2daytextmtbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(3) .rotor-group-heading:before',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'s2daytextmdb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(3) .rotor-group-heading:before',
            ]
        );
        $this->add_responsive_control('s2daytextmbrs',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(3) .rotor-group-heading:before'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'s2daytextmsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(3) .rotor-group-heading:before',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('s2_text_seconds',
            [
                'label'=>esc_html__('Second','theplus')
            ]
        );
		$this->add_control('s2secondnumberncr',
			[
				'label' => esc_html__('Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(4) .rotor-group-heading:before' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2daytextmsbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(4) .rotor-group-heading:before',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'s2daytextsdb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(4) .rotor-group-heading:before',
            ]
        );
        $this->add_responsive_control('s2daytextsbrs',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(4) .rotor-group-heading:before'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'s2daytextssd',
                'selector'=>'{{WRAPPER}} .tp-countdown .rotor-group:nth-of-type(4) .rotor-group-heading:before',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		/*counter style*/
		$this->start_controls_section('style2counter_styling',
			[
				'label' => esc_html__('Counter', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-2',			
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'style2countertypo',
                'label'=>esc_html__('Typography','theplus'),
				'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown .rotor',
            ]
        );
		$this->end_controls_section();
		/*counter style*/

		$this->start_controls_section('style2dark_styling',
			[
				'label' => esc_html__('Dark Theme', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-2',
					'fliptheme' => ['dark','mix']
				],
			]
		);
		$this->start_controls_tabs('s2dark_tabs');
		$this->start_controls_tab('s2dark_normal',
			[
				'label'=>esc_html__('Normal','theplus')
			]
		); 	
		$this->add_control('s2haddingntop',
			[
				'label' => esc_html__( 'Top Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control('s2darktopncr',
			[
				'label' => esc_html__('Top Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor-leaf-front' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2darktopnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor-leaf-front',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2bordernb',
				'label'=>esc_html__('Border Top','theplus'),
				'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-dark .rotor,{{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-top,{{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-leaf-front',
			]
		);

		$this->add_control('s2haddingnbootom',
			[
				'label' => esc_html__( 'Bottom Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('s2darkbottomncr',
			[
				'label' => esc_html__('Bottom Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-bottom, {{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-leaf-rear' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2darkbottomnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-bottom, {{WRAPPER}} .flipdown.flipdown__theme-dark .rotor-leaf-rear',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'s2borderbottomnb',
                'label'=>esc_html__('Border Top','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark .rotor:after',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('s2dark_hover',
            [
                'label'=>esc_html__('Hover','theplus')
            ]
        );
		$this->add_control('s2haddihghtop',
			[
				'label' => esc_html__( 'Top Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control('s2darktophcr',
			[
				'label' => esc_html__('Top Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor-leaf-front' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'s2darktophbg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor-leaf-front',
			]
		);
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2darkborderhb',
				'label'=>esc_html__('Border Top','theplus'),
				'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor,{{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-top,{{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-leaf-front',
			]
		);

		$this->add_control('s2haddinghbootom',
			[
				'label' => esc_html__( 'Bottom Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('s2darkbottomhcr',
			[
				'label' => esc_html__('Bottom Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-bottom, {{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-leaf-rear' => 'color:{{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2darkbottomhbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-bottom, {{WRAPPER}} .flipdown.flipdown__theme-dark:hover .rotor-leaf-rear',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'middlelinehb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-dark:hover .rotor:after',
            ]
        );

		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section('style2light_styling',
			[
				'label' => esc_html__('Light Theme', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-2',
					'fliptheme' => ['light','mix']
				],
			]
		);

		$this->start_controls_tabs('s2light_tabs');
		$this->start_controls_tab('s2light_normal',
			[
				'label'=>esc_html__('Normal','theplus')
			]
		); 	
		$this->add_control('s2lighthaddingntop',
			[
				'label' => esc_html__( 'Bottom Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control('s2lighttopncr',
			[
				'label' => esc_html__('Bottom Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-bottom,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-leaf-rear' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2lighttopnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-bottom,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-leaf-rear',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2lightbordernb',
				'label'=>esc_html__('Border Bottom','theplus'),
				'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-light .rotor,{{WRAPPER}} .flipdown.flipdown__theme-light .rotor-top,{{WRAPPER}} .flipdown.flipdown__theme-light .rotor-leaf-front',
			]
		);

		$this->add_control('s2lighthaddingnbootom',
			[
				'label' => esc_html__( 'Top Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('s2lighttopncrbootom',
			[
				'label' => esc_html__('Top Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-leaf-front' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2lighttopnbgbootom',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor-leaf-front',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2lightbordernbotom',
				'label'=>esc_html__('Border Top','theplus'),
				'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light .rotor:after',
			]
		);

		$this->end_controls_tab();
		$this->start_controls_tab('s2light_hover',
			[
				'label'=>esc_html__('Hover','theplus')
			]
		); 	
		$this->add_control('s2lighthaddihghtop',
			[
				'label' => esc_html__( 'Bottom Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
			]
		);
		$this->add_control('s2lighttophcr',
			[
				'label' => esc_html__('Bottom Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-bottom,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-leaf-rear' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2lighttophbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-bottom,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-leaf-rear',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2lightborderhb',
				'label'=>esc_html__('Border Bottom','theplus'),
				'selector'=>'{{WRAPPER}} .flipdown.flipdown__theme-light:hover .rotor,{{WRAPPER}} .flipdown.flipdown__theme-light:hover .rotor-top,{{WRAPPER}} .flipdown.flipdown__theme-light:hover .rotor-leaf-front',
			]
		);

		$this->add_control('s2lighthaddinghbootom',
			[
				'label' => esc_html__( 'Top Options', 'theplus' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control('s2lighttophcrbootom',
			[
				'label' => esc_html__('Top Text Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-leaf-front' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'s2lighttophbgbootom',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-top,{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor-leaf-front',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
			[
				'name'=>'s2lightborderhbotom',
				'label'=>esc_html__('Border Top','theplus'),
				'selector'=>'{{WRAPPER}} .tp-countdown .flipdown.flipdown__theme-light:hover .rotor:after',
			]
		);

		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section('style2dot_styling',
			[
				'label' => esc_html__('Dot', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-2',
				],
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
			[
				'name'=>'s2ndotbg',
				'types'=>['classic','gradient'],
				'selector'=>'{{WRAPPER}} .tp-countdown .flipdown .rotor-group:nth-child(n+2):nth-child(-n+3):before,{{WRAPPER}} .tp-countdown .flipdown .rotor-group:nth-child(n+2):nth-child(-n+3):after,{{WRAPPER}}  .tp-countdown.countdown-style-2 .rotor-group:first-child::after,{{WRAPPER}}  .tp-countdown.countdown-style-2 .rotor-group:first-child::before',
			]
		);
		$this->end_controls_section();		
		/*style 3 end*/

		

		$this->start_controls_section('style3_styling',
			[
				'label' => esc_html__('Style 3', 'theplus'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => ['normal','scarcity'],
					'CDstyle' => 'style-3',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'s3numbertypo',
                'label'=>esc_html__('Typography','theplus'),
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-counter .progressbar-text .number',
            ]
        );
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'s3labeltypo',
                'label'=>esc_html__('Typography','theplus'),
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-counter .progressbar-text .label',
            ]
        );
		$this->add_control('strokewd1',
			[
				'label' => esc_html__( 'Stroke Width', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5,
				'step' => 1,
				'default' => 5,
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter svg > path:nth-of-type(2)' => 'stroke-width:{{VALUE}};',
				],
			]
		);
		$this->add_control('trailwd',
			[
				'label' => esc_html__( 'Trail Width', 'theplus' ),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 5,
				'step' => 1,
				'default' => 3,
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter svg > path:nth-of-type(1)' => 'stroke-width:{{VALUE}};',
				],
			]
		);
		$this->start_controls_tabs('s3_tabs');
        $this->start_controls_tab('s3_num_days',
            [
                'label'=>esc_html__('Days','theplus')
            ]
        ); 	
		$this->add_control('s3daynumberncr',
            [
                'label' => esc_html__('Counter Number Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(1) .progressbar-text .number' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3daytextncr',
            [
                'label' => esc_html__('Counter Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(1) .progressbar-text .label' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3daystrokencr',
            [
                'label' => esc_html__('Counter Stroke Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(1) svg > path:nth-of-type(1)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3daystrailnncr',
            [
                'label' => esc_html__('Counter Trail Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(1) svg > path:nth-of-type(2)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_tab();
        $this->start_controls_tab('s3_text_hours',
            [
                'label'=>esc_html__('Hours','theplus')
            ]
        );
		$this->add_control('s3hoursnumberncr',
            [
                'label' => esc_html__('Counter Number Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(2) .progressbar-text .number' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3hourstextncr',
            [
                'label' => esc_html__('Counter Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(2) .progressbar-text .label' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3hourstrokencr',
            [
                'label' => esc_html__('Counter Stroke Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(2) svg > path:nth-of-type(1)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3hourstrailncr',
            [
                'label' => esc_html__('Counter Trail Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(2) svg > path:nth-of-type(2)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('s3_text_minutes',
            [
                'label'=>esc_html__('Minutes','theplus')
            ]
        );
		$this->add_control('s3minutnumberncr',
            [
                'label' => esc_html__('Counter Number Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(3) .progressbar-text .number' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3minuttextncr',
            [
                'label' => esc_html__('Counter Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(3) .progressbar-text .label' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3miutstrokencr',
            [
                'label' => esc_html__('Counter Stroke Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(3) svg > path:nth-of-type(1)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3miutstrailncr',
            [
                'label' => esc_html__('Counter Trail Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(3) svg > path:nth-of-type(2)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('s3_text_seconds',
            [
                'label'=>esc_html__('Second','theplus')
            ]
        );
		$this->add_control('s3secondnumberncr',
			[
				'label' => esc_html__('Counter Number Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(4) .progressbar-text .number' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control('s3secondtextncr',
            [
                'label' => esc_html__('Counter Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(4) .progressbar-text .label' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3secondtrokencr',
            [
                'label' => esc_html__('Counter Stroke Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(4) svg > path:nth-of-type(1)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3secondstrailncr',
            [
                'label' => esc_html__('Counter Trail Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:nth-of-type(4) svg > path:nth-of-type(2)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->add_control('s3hoverstyle',
			[
				'label'=>__('Hover style','theplus'),
				'type'=>Controls_Manager::HEADING,
				'separator'=>'before',
			]
		);
		$this->add_control('s3numberhcr',
			[
				'label' => esc_html__('Number Color', 'theplus'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:hover .progressbar-text .number' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control('s3texthcr',
            [
                'label' => esc_html__('Text Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:hover .progressbar-text .label' => 'color: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3trokehcr',
            [
                'label' => esc_html__('Stroke Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:hover svg > path:nth-of-type(1)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->add_control('s3strailhcr',
            [
                'label' => esc_html__('Trail Color', 'theplus'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
					'{{WRAPPER}} .tp-countdown .tp-countdown-counter .counter-part:hover svg > path:nth-of-type(2)' => 'stroke: {{VALUE}};',
                ],
            ]
        );
		$this->end_controls_section();

		$this->start_controls_section('expirymessage_styling',
            [
                'label' => esc_html__('Expiry Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'conditions'=>[
					'relation'=>'or',
					'terms' => [
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'normal' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showmsg' ],
							]
						],
						[
							'terms' => [
								[ 'name' => 'CDType', 'operator'=>'===', 'value' => 'scarcity' ],
								[ 'name' => 'expirytype', 'operator'=>'===', 'value' => 'yes' ],
								[ 'name' => 'countdownExpiry', 'operator'=>'===', 'value' => 'showmsg' ],
							]
						],
					]
				],
            ]
        );
		$this->add_responsive_control('expirymessagealign',
            [
                'label'=>__('Alignment','theplus'),
                'type'=>Controls_Manager::CHOOSE,
                'options'=>[
                    'left' => [
                        'title'=>esc_html__( 'Left','theplus'),
                        'icon'=>'eicon-text-align-left',
                    ],
                    'center' => [
                        'title'=>esc_html__( 'Center','theplus'),
                        'icon'=>'eicon-text-align-center',
                    ],
                    'right' => [
                        'title'=>esc_html__('Right', 'theplus'),
                        'icon'=>'eicon-text-align-right',
                    ],
                ],
                'default'=>'center',
                'toggle'=>true,
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-countdown-expiry'=>'justify-content:{{VALUE}};',
                ],
            ]
        );
		$this->add_responsive_control('expirymessagepad',
            [
                'label'=>__('Padding','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-countdown-expiry'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control('expirymessagemar',
            [
                'label'=>__('Margin','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%','em'],
                'separator'=>'after',
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-countdown-expiry'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'expirymessagetypo',
                'label'=>esc_html__('Typography','theplus'),
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry',
            ]
        );
		$this->start_controls_tabs('expirymessag_tabs');
        $this->start_controls_tab('expirymessag_Normal',
            [
                'label'=>esc_html__('Normal','theplus')
            ]
        ); 	
		$this->add_control('expirymessagncr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-countdown-expiry'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'expirymessagnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'expirymessagngb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry',
            ]
        );
        $this->add_responsive_control('expirymessagnbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-countdown-expiry'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'expirymessagnsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry',
            ]
        );
		$this->end_controls_tab();
        $this->start_controls_tab('expirymessag_Hover',
            [
                'label'=>esc_html__('Hover','theplus')
            ]
        );
		$this->add_control('expirymessaghcr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-countdown-expiry:hover'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'expirymessaghbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry:hover',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'expirymessaggb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry:hover',
            ]
        );
        $this->add_responsive_control('expirymessaghbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-countdown-expiry:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'expirymessaghsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-countdown-expiry:hover',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section('fakemsg_styling',
            [
                'label' => esc_html__('Fake Message', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => 'numbers',
				],			
            ]
        );
		$this->add_responsive_control('fakestringalign',
            [
                'label'=>__('Alignment','theplus'),
                'type'=>Controls_Manager::CHOOSE,
                'options'=>[
                    'left' => [
                        'title'=>esc_html__( 'Left','theplus'),
                        'icon'=>'eicon-text-align-left',
                    ],
                    'center' => [
                        'title'=>esc_html__( 'Center','theplus'),
                        'icon'=>'eicon-text-align-center',
                    ],
                    'right' => [
                        'title'=>esc_html__('Right', 'theplus'),
                        'icon'=>'eicon-text-align-right',
                    ],
                ],
                'default'=>'left',
                'toggle'=>true,
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number'=>'justify-content:{{VALUE}};',
                ],
            ]
        );
		$this->add_responsive_control('fakestringpad',
            [
                'label'=>__('Padding','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_responsive_control('fakestringmar',
            [
                'label'=>__('Margin','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%','em'],
                'separator'=>'after',
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'fakestringtypo',
                'label'=>esc_html__('Typography','theplus'),
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number',
            ]
        );
		$this->start_controls_tabs('fakestring_tabs');
        $this->start_controls_tab('fakestring_Normal',
            [
                'label'=>esc_html__('Normal','theplus')
            ]
        );  
		$this->add_control('fakestringncr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-fake-number'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'fakestringnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'fakestringb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number',
            ]
        );
        $this->add_responsive_control('fakestringnbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'fakestringnsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number',
            ]
        );
		$this->end_controls_tab();
        $this->start_controls_tab('fakestring_Hover',
            [
                'label'=>esc_html__('Hover','theplus')
            ]
        );
		$this->add_control('fakestringhcr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-fake-number:hover'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'fakestringhbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'fakestrihgb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover',
            ]
        );
        $this->add_responsive_control('fakestringhbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'fakestringhsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section('fakenumber_styling',
            [
                'label' => esc_html__('Fake Number', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'CDType' => 'numbers',
				],			
            ]
        );
		$this->add_responsive_control('fakenumpad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(Group_Control_Typography::get_type(),
            [
                'name'=>'fakenumtypo',
                'label'=>esc_html__('Typography','theplus'),
                'global' => [
                    'default' => \Elementor\Core\Kits\Documents\Tabs\Global_Typography::TYPOGRAPHY_PRIMARY
                ],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter',
            ]
        );

		$this->start_controls_tabs('fakenumber_tabs');
        $this->start_controls_tab('fakenumber_normal',
            [
                'label'=>esc_html__('Normal','theplus')
            ]
        );  
		$this->add_control('fakenumberncr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'fakenumbernbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'fakenumberngb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter',
            ]
        );
        $this->add_responsive_control('fakenumbernbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'fakenumbernsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number .tp-fake-visiblecounter',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('fakenumber_hover',
			[
				'label'=>esc_html__('hover','theplus')
			]
		); 
		$this->add_control('fakenumberhcr',
			[
				'label'=>__('Color','theplus'),
				'type'=>Controls_Manager::COLOR,
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown .tp-fake-number:hover .tp-fake-visiblecounter'=>'color:{{VALUE}}'
				]
			]
		);
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'fakenumberhbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover .tp-fake-visiblecounter',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'fakenumberhgb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover .tp-fake-visiblecounter',
            ]
        );
        $this->add_responsive_control('fakenumberhbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown .tp-fake-number:hover .tp-fake-visiblecounter'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'fakenumberhsd',
                'selector'=>'{{WRAPPER}} .tp-countdown .tp-fake-number:hover .tp-fake-visiblecounter',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section('background_styling',
            [
                'label' => esc_html__('Background', 'theplus'),
                'tab' => Controls_Manager::TAB_STYLE,
            ]
        );
		$this->add_responsive_control('bgpad',
			[
				'label'=>__('Padding','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown'=>'padding:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_responsive_control('bgmar',
			[
				'label'=>__('Margin','theplus'),
				'type'=>Controls_Manager::DIMENSIONS,
				'size_units'=>['px','%'],
				'selectors'=>[
					'{{WRAPPER}} .tp-countdown'=>'margin:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs('bg_tabs');
        $this->start_controls_tab('bg_normal',
            [
                'label'=>esc_html__('Normal','theplus')
            ]
        ); 
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'bgnbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'bgnb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown',
            ]
        );
        $this->add_responsive_control('bgnbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'bgnsd',
                'selector'=>'{{WRAPPER}} .tp-countdown',
            ]
        );
		$this->end_controls_tab();
		$this->start_controls_tab('bg_hover',
			[
				'label'=>esc_html__('hover','theplus')
			]
		); 
		$this->add_group_control(Group_Control_Background::get_type(),
            [
                'name'=>'bghbg',
                'types'=>['classic','gradient'],
                'selector'=>'{{WRAPPER}} .tp-countdown:hover',
            ]
        );
		$this->add_group_control(Group_Control_Border::get_type(),
            [
                'name'=>'bghb',
                'label'=>esc_html__('Border','theplus'),
                'selector'=>'{{WRAPPER}} .tp-countdown:hover',
            ]
        );
        $this->add_responsive_control('bghbr',
            [
                'label'=>__('Border Radius','theplus'),
                'type'=>Controls_Manager::DIMENSIONS,
                'size_units'=>['px','%'],
                'selectors'=>[
                    '{{WRAPPER}} .tp-countdown:hover'=>'border-radius:{{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );
		$this->add_group_control(Group_Control_Box_Shadow::get_type(),
            [
                'name'=>'bghsd',
                'selector'=>'{{WRAPPER}} .tp-countdown:hover',
            ]
        );
		$this->end_controls_tab();
		$this->end_controls_tabs();
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
		$uid = uniqid('count_down');
		$WidgetId = $this->get_id();

		/*--On Scroll View Animation ---*/
		include THEPLUS_PATH. 'modules/widgets/theplus-widget-animation-attr.php';
		/*--Plus Extra ---*/
		$PlusExtra_Class = "plus-countdown-widget";
		include THEPLUS_PATH. 'modules/widgets/theplus-widgets-extra.php';

		$CDType = !empty($settings['CDType']) ? $settings['CDType'] : 'normal';
		$style = !empty($settings['CDstyle']) ? $settings['CDstyle'] : 'style-1';

		$offset_time = get_option('gmt_offset');
		$offsetTime = wp_timezone_string();
		$now = new \DateTime('NOW', new \DateTimeZone($offsetTime));

		$future='';
		if( !empty($settings['counting_timer']) ) {
			$future = new \DateTime($settings['counting_timer'], new \DateTimeZone($offsetTime));
		}
			$now = $now->modify("+1 second");

		if( !empty($settings['counting_timer']) ){
			$counting_timer = $settings['counting_timer'];
			$counting_timer = date('m/d/Y H:i:s', strtotime($counting_timer) );
		}else{
			$curr_date = date("m/d/Y H:i:s");
			$counting_timer = date('m/d/Y H:i:s', strtotime($curr_date . ' +1 month'));
		}
	
		$countdownExpiry = !empty($settings['countdownExpiry']) ? $settings['countdownExpiry'] : '';
		$expirymsg="";
		if($countdownExpiry == 'redirect') {	
			$expirymsg = $settings['expiryRedirect']['url'];
		}else if($countdownExpiry == 'showmsg') {
			$expirymsg = !empty($settings['expiryMsg']) ? $settings['expiryMsg'] : '';
		}else if($countdownExpiry == 'showtemp') {	
			$templates = Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['templates'] );
		}

		$DaysLabels = !empty($settings['dayslabels']) ? true : false;
		$HoursLabels = !empty($settings['hourslabels']) ? true : false;
		$MinutesLabels = !empty($settings['minuteslabels']) ? true : false;
		$SecondsLabels = !empty($settings['secondslabels']) ? true : false;

		$show_labels = !empty($settings['show_labels']) ? true : false;
		$text_days = !empty($settings['text_days']) ? $settings['text_days'] : esc_html__('Days','theplus');
		$text_hours = !empty($settings['text_hours']) ? $settings['text_hours'] : esc_html__('Hours','theplus');
		$text_minutes = !empty($settings['text_minutes']) ? $settings['text_minutes'] : esc_html__('Minutes','theplus');
		
		$text_seconds = !empty($settings['text_seconds']) ? $settings['text_seconds'] : esc_html__('Seconds','theplus');
		$show_labels_tag = !empty($settings['show_labels_tag']) ? $settings['show_labels_tag'] : 'h6';

		$fliptheme=$flipMixtime='';
		if( $style == "style-2" ) {	
			$fliptheme = $settings['fliptheme'];
			$flipMixtime = !empty($settings['flipMixtime']) ? $settings['flipMixtime'] : 3;
		}
		$expirytype = !empty($settings['expirytype']) ? 'expiry' : '';

		$normalexpiry = "";
		if($future >= $now && isset($future)) {
			$normalexpiry = true;
		}else{
			$normalexpiry = false;
		}

		$Styleclass="";
		if($CDType == 'normal' || $CDType == 'scarcity') {
			$Styleclass = "countdown-".$style;

			$CDData = array(
				'widgetid' => $WidgetId,
				'type' => $CDType,
				'style' => $style,
				'expiry' => $countdownExpiry,
				'expirymsg' => $expirymsg,

				'fliptheme' => $fliptheme,
				'flipMixtime' => $flipMixtime,

				'days' => $text_days,
				'hours' => $text_hours,
				'minutes' => $text_minutes,
				'seconds' => $text_seconds,

				'daysenable' => $DaysLabels,
				'hoursenable' => $HoursLabels,
				'minutesenable' => $MinutesLabels,
				'secondsenable' => $SecondsLabels,
			);
		}

		if($CDType == 'normal') {
			$OtherDataa = array(
				'offset' => $offset_time,
				'normalexpiry' => $normalexpiry,
				'expirytype' => 'expiry',
				'timer' => $counting_timer,
			);

			$CDData = array_merge($CDData, $OtherDataa);
		}else if($CDType == 'numbers'){
			$StoreType = !empty($settings['storetype']) ? $settings['storetype'] : "normal";
			$fakeloop = !empty($settings['fackeLoop']) ? true : false;

			$ginitnum = $gendnum = $gnumrange = $gchangeinterval = '';
			
			$ginitnum = $settings['initNum'];
			$gendnum = $settings['endNum'];
			$gnumrange = $settings['numRange'];
			$gchangeinterval = $settings['changeInterval'];

			$CDData = array(
				'widgetid' => $WidgetId,
				'type' => $CDType,
				'fakeinitminit' => $ginitnum,
				'fakeend' => $gendnum,
				'fakerange' => $gnumrange,
				'fakeinterval' => $gchangeinterval,
				'fakeloop' => $fakeloop,
				'fackeMassage' => $settings['fackemassage'],
				'storetype' => $StoreType,
			);	
			if( empty($fakeloop) ) {
				unset( $CDData["fakeloop"] );
			}
		}else if($CDType == 'scarcity'){
			$StoreType = !empty($settings['storetype']) ? $settings['storetype'] : "normal";
			$loop = !empty($settings['fackeLoop']) ? true : false;

			$scarevalue = !empty($settings['cityminit']) ? $settings['cityminit'] : 0;

			$OtherDataa = array(
				'scarminit' => $scarevalue,
				'storetype' => $StoreType,
				'fakeloop' => $loop,
				'delayminit' => !empty($settings['delayminit']) ? $settings['delayminit'] : 0,
				'expirytype' => $expirytype,
			);
			$CDData = array_merge($CDData, $OtherDataa);

			if( !empty($expirytype) ) {
				if( $countdownExpiry != 'redirect' && $countdownExpiry != 'showmsg' ) {
					unset( $CDData["expirymsg"] );
				}
			}else{
				unset( $CDData["expirytype"], $CDData["expiry"], $CDData["expirymsg"]);
			}

			if( empty($loop) ) {
				unset( $CDData["fakeloop"] );
				unset( $CDData["delayminit"] );
			}

			if( $style != "style-2" ){
				unset( $CDData["fliptheme"] );
			}else{
				if($fliptheme != 'mix'){
					unset( $CDData["flipMixtime"] );
				}
			}
		}

		if($CDType == 'normal' || $CDType == 'scarcity') {
			/*unnecessary array remove*/
			
			if($countdownExpiry == "none"){
				unset( $CDData["expirytype"] );
				unset( $CDData["expirymsg"] );
			}

			if( $style != "style-2" ){
				unset( $CDData["fliptheme"] );
				unset( $CDData["flipMixtime"] );
			}else{
				if($fliptheme != 'mix'){
					unset( $CDData["flipMixtime"] );
				}
			}
		}

		$classData='';
		$cd_classbased =  isset($settings['cd_classbased']) ? 'yes' : 'no';
		
		if(isset($cd_classbased) && $cd_classbased=='yes' && !empty($CDType ) &&  ($CDType == 'normal' || $CDType == 'scarcity')  && !empty($settings['CDstyle']) && $settings['CDstyle'] != 'style-3'){
			$cd_class_1 = !empty($settings['cd_class_1']) ? $settings['cd_class_1'] : '';
			$cd_class_2 = !empty($settings['cd_class_2']) ? $settings['cd_class_2'] : '';			
			if(!empty($cd_class_1) && !empty($cd_class_2)){				
				$classbaseddata = array(
					'duringcountdownclass' => '.'.$cd_class_1,
					'afterexpcountdownclass' => '.'.$cd_class_2,
				);
				$classData = 'data-classlist = '.htmlspecialchars(json_encode($classbaseddata), ENT_QUOTES, 'UTF-8');
			}
		}

		$CDData = htmlspecialchars(json_encode($CDData), ENT_QUOTES, 'UTF-8');

			$output = '<div class="tp-countdown tp-widget-'.esc_attr($WidgetId).' '.esc_attr($Styleclass).'" data-basic="'.esc_attr($CDData).'" '.esc_attr($classData).' >';

				if($CDType == 'normal') {
					if($future >= $now && isset($future) || $countdownExpiry == "none") {
						if($style == 'style-1') {
							$inline_style = ( !empty($settings["inline_style"]) && $settings["inline_style"] == 'yes' ) ? 'count-inline-style' : '';

							$lz1 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['days_background_image']) : '';
							$lz2 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['hours_background_image']) : '';
							$lz3 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['minutes_background_image']) : '';
							$lz4 = function_exists('tp_has_lazyload') ? tp_bg_lazyLoad($settings['seconds_background_image']) : '';

							$output .= '<ul class="pt_plus_countdown '.esc_attr($uid).' '.esc_attr($inline_style).' '.$animated_class.'" '.$animation_attr.'>';
								if( !empty($DaysLabels)){
									$output .= '<li class="count_1 '.$lz1.'">';
										$output .= '<span class="days">'.esc_html__('00','theplus').'</span>';
										if( !empty($show_labels) ){
											$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="days_ref label-ref">'.esc_html($text_days).'</'.theplus_validate_html_tag($show_labels_tag).'>';
										}
									$output .= '</li>';
								}

								if(!empty($HoursLabels)){
									$output .= '<li class="count_2 '.$lz2.'">';
										$output .= '<span class="hours">'.esc_html__('00','theplus').'</span>';
										if( !empty($show_labels) ){
											$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="hours_ref label-ref">'.esc_html($text_hours).'</'.theplus_validate_html_tag($show_labels_tag).'>';
										}
									$output .= '</li>';
								}
								
								if(!empty($MinutesLabels)){
									$output .= '<li class="count_3 '.$lz3.'">';
										$output .= '<span class="minutes">'.esc_html__('00','theplus').'</span>';
										if( !empty($show_labels) ){
											$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="minutes_ref label-ref">'.esc_html($text_minutes).'</'.theplus_validate_html_tag($show_labels_tag).'>';
										}
									$output .= '</li>';
								}
							
								if(!empty($SecondsLabels)){
									$output .= '<li class="count_4 '.$lz4.'">';
										$output .= '<span class="seconds last">'.esc_html__('00','theplus').'</span>';
										if( !empty($show_labels) ){
											$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="seconds_ref label-ref">'.esc_html($text_seconds).'</'.theplus_validate_html_tag($show_labels_tag).'>';
										}
									$output .= '</li>';
								}
							$output .= '</ul>';
						}
					}
				}else if($CDType == 'scarcity'){
					$inline_style = ( !empty($settings["inline_style"]) && $settings["inline_style"] == 'yes' ) ? 'count-inline-style' : '';
					if($style == 'style-1') {
						$output .= '<ul class="pt_plus_countdown '.esc_attr($uid).' '.esc_attr($inline_style).'">';

							if(!empty($DaysLabels)){
								$output .= '<li class="count_1">';
									$output .= '<span class="days">'.esc_html__('00','theplus').'</span>';
									if( !empty($show_labels) ){
										$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="days_ref">'.esc_html($text_days).'</'.theplus_validate_html_tag($show_labels_tag).'>';
									}
								$output .= '</li>';
							}

							if(!empty($HoursLabels)){
								$output .= '<li class="count_2">';
									$output .= '<span class="hours">'.esc_html__('00','theplus').'</span>';
									if( !empty($show_labels) ){
										$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="hours_ref">'.esc_html($text_hours).'</'.theplus_validate_html_tag($show_labels_tag).'>';
									}
								$output .= '</li>';
							}

							if(!empty($MinutesLabels)){
								$output .= '<li class="count_3">';
									$output .= '<span class="minutes">'.esc_html__('00','theplus').'</span>';
									if( !empty($show_labels) ){
										$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="minutes_ref">'.esc_html($text_minutes).'</'.theplus_validate_html_tag($show_labels_tag).'>';
									}
								$output .= '</li>';
							}

							if(!empty($SecondsLabels)){
								$output .= '<li class="count_4">';
									$output .= '<span class="seconds last">'.esc_html__('00','theplus').'</span>';
									if( !empty($show_labels) ){
										$output .= '<'.theplus_validate_html_tag($show_labels_tag).' class="seconds_ref">'.esc_html($text_seconds).'</'.theplus_validate_html_tag($show_labels_tag).'>';
									}
								$output .= '</li>';
							}
							
						$output .= '</ul>';
					}
				}
				
				if( $CDType == 'normal' || $CDType == 'scarcity' ) {
					if( $countdownExpiry == 'showtemp' ){
						$output .= "<div class='tp-expriy-template tp-hide'>"; 
							$output .= Theplus_Element_Load::elementor()->frontend->get_builder_content_for_display( $settings['templates'] );
						$output .= "</div>";
					}
				}

			$output .= '</div>';

		echo $before_content.$output.$after_content;
	}

    protected function content_template() {
	
    }
}