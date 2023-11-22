<?php

namespace ElementPack\Modules\TimeZone\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Background;
use ElementPack\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit();
}

class Time_Zone extends Module_Base {

	public function get_name() {
		return 'bdt-time-zone';
	}

	public function get_title() {
		return BDTEP . esc_html__( 'Time Zone', 'bdthemes-element-pack' );
	}

	public function get_icon() {
		return 'bdt-wi-time-zone';
	}

	public function get_categories() {
		return [ 'element-pack' ];
	}

	public function get_keywords() {
		return [ 'time', 'zone', 'digital', 'analog', 'clock', 'timezone' ];
	}

	public function get_style_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'ep-styles' ];
		} else {
			return [ 'ep-time-zone' ];
		}
	}

	public function get_script_depends() {
		if ( $this->ep_is_edit_mode() ) {
			return [ 'jclock', 'ep-scripts' ];
		} else {
			return [ 'jclock', 'ep-time-zone' ];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/WOMIk_FVRz4';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_additional',
			[ 
				'label' => esc_html__( 'Time Zone', 'bdthemes-element-pack' ),
			]
		);

		$this->add_control(
			'select_style',
			[ 
				'label'       => esc_html__( 'Select Style', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::SELECT,
				'options'     => [ 
					'digital'  => esc_html__( 'Digital', 'bdthemes-element-pack' ),
					'analog_1' => esc_html__( 'Analog ', 'bdthemes-element-pack' ),
				],
				'default'     => 'digital',
				'render_type' => 'template',
			]
		);

		$this->add_control(
			'select_gmt',
			[ 
				'label'   => esc_html__( 'Select GMT', 'bdthemes-element-pack' ),
				'type'    => Controls_Manager::SELECT2,
				'options' => [ 
					'local'  => 'Local GMT',
					'-0'     => 'UT or UTC - GMT -0',
					'+1'     => 'CET - GMT+1',
					'+2'     => 'EET - GMT+2',
					'+3'     => 'MSK - GMT+3',
					'+4'     => 'SMT - GMT+4',
					'+5'     => 'PKT - GMT+5',
					'+5.5'   => 'IND - GMT+5.5',
					'+6'     => 'OMSK / BD - GMT+6',
					'+7'     => 'CXT - GMT+7',
					'+8'     => 'CST / AWST / WST - GMT+8',
					'+9'     => 'JST - GMT+9',
					'+10'    => 'EAST - GMT+10',
					'+11'    => 'SAKT - GMT+11',
					'+12'    => 'IDLE  - GMT+12',
					'+13'    => 'NZDT  - GMT+13',
					'-1'     => 'WAT  - GMT-1',
					'-2'     => 'AT  - GMT-2',
					'-3'     => 'ART  - GMT-3',
					'-4'     => 'AST  - GMT-4',
					'-5'     => 'EST  - GMT-5',
					'-6'     => 'CST  - GMT-6',
					'-7'     => 'MST  - GMT-7',
					'-8'     => 'PST  - GMT-8',
					'-9'     => 'AKST  - GMT-9',
					'-10'    => 'HST  - GMT-10',
					'-11'    => 'NT  - GMT-11',
					'-12'    => 'IDLW  - GMT-12',
					'custom' => 'Custom GMT',
				],
				'default' => [ '+1' ],

			]
		);

		//show_clock_second 
		$this->add_control(
			'show_clock_second',
			[ 
				'label'     => esc_html__( 'Show Second Hand', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);

		$this->add_control(
			'local_gmt_note',
			[ 
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => esc_html__( 'Country name will not work dynamically on Local GMT. So in this case, you may deactivate Show Country Option.', 'bdthemes-element-pack' ),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => [ 
					'select_gmt' => 'local',
				]
			]
		);

		$this->add_control(
			'input_gmt',
			[ 
				'label'       => esc_html__( 'Custom GMT ', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'example: +6',
				'default'     => '+6',
				'condition'   => [ 
					'select_gmt' => 'custom',
				],
			]
		);

		$this->add_control(
			'time_hour',
			[ 
				'label'     => esc_html__( 'Time Format', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					'12h' => esc_html__( '12 Hours', 'bdthemes-element-pack' ),
					'24h' => esc_html__( '24 Hours', 'bdthemes-element-pack' ),
				],
				'default'   => '12h',
				'condition' => [ 
					'select_style' => 'digital',
				],
			]
		);

		$this->add_control(
			'show_date',
			[ 
				'label'        => esc_html__( 'Show Date', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off'    => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'select_date_format',
			[ 
				'label'     => esc_html__( 'Date Format', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SELECT,
				'options'   => [ 
					'%m/%d/%y'  => 'mm/dd/yy',
					'%m/%d/%Y'  => 'mm/dd/yyyy',
					'%m-%d-%y'  => 'mm-dd-yy',
					'%m-%d-%Y'  => 'mm-dd-yyyy',
					'%m %d %y'  => 'mm dd yy',
					'%m %d %Y'  => 'mm dd yyyy',

					'%d/%m/%y'  => 'dd/mm/yy',
					'%d/%m/%Y'  => 'dd/mm/yyyy',
					'%d-%m-%Y'  => 'dd-mm-yyyy',
					'%d %m %Y'  => 'dd mm yyyy',

					'%d/%y'     => 'dd/yy',
					'%d-%y'     => 'dd-yy',
					'%d%y'      => 'dd yy',
					'%d/%Y'     => 'dd/yyyy',
					'%d-%Y'     => 'dd-yyyy',
					'%d %Y'     => 'dd yyyy',

					'%b %d, %y' => 'mm dd, yy',
					'%b %d, %Y' => 'mm dd, yyyy',

					'%d %b, %y' => 'dd mm yy',

					'%y %b %d'  => 'yy mm dd',
					'%Y %b %d'  => 'yyyy mm dd',

					'%d %b, %Y' => 'dd mm, yyyy',
					'%b-%d-%Y'  => 'mm-dd-yyyy',

					'%a, %d %b' => 'day-dd-m',

					'custom'    => 'Custom Format',
				],
				'default'   => '%d %b, %Y',
				'condition' => [ 
					'show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'input_date_format',
			[ 
				'label'       => esc_html__( 'Custom Date Format', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'Type date format here', 'bdthemes-element-pack' ),
				'default'     => '%a, %d %b',
				'condition'   => [ 
					'select_date_format' => 'custom',
				],
			]
		);

		$this->add_control(
			'show_country',
			[ 
				'label'        => esc_html__( 'Show Country', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::SWITCHER,
				'label_on'     => esc_html__( 'Show', 'bdthemes-element-pack' ),
				'label_off'    => esc_html__( 'Hide', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);

		$this->add_control(
			'input_country',
			[ 
				'label'       => esc_html__( 'Type Country name ', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__( 'example: Bangladesh', 'bdthemes-element-pack' ),
				'default'     => esc_html__( 'Bangladesh', 'bdthemes-element-pack' ),
				'condition'   => [ 
					'show_country' => 'yes',
				],
			]
		);

		$this->add_control(
			'timer_layout',
			[ 
				'label'      => esc_html__( 'Layout', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::CHOOSE,
				'options'    => [ 
					'top'    => [ 
						'title' => esc_html__( 'Top', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-top',
					],
					'bottom' => [ 
						'title' => esc_html__( 'Bottom', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'default'    => 'top',
				'toggle'     => false,
				'conditions' => [ 
					'relation' => 'or',
					'terms'    => [ 
						[ 
							'name'  => 'show_country',
							'value' => 'yes',
						],
						[ 
							'name'  => 'show_date',
							'value' => 'yes',
						],
					],
				],
				'separator'  => 'before',
				'render_type' => 'template',
			]
		);

		$this->add_responsive_control(
			'text_align',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'left'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-timer' => 'text-align: {{VALUE}};',
				],
				'condition' => [ 
					'select_style' => 'digital',
				],
			]
		);
		
		$this->add_responsive_control(
			'analog_clock_alignment',
			[ 
				'label'     => esc_html__( 'Alignment', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [ 
					'start'   => [ 
						'title' => esc_html__( 'Left', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [ 
						'title' => esc_html__( 'Center', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-center',
					],
					'end'  => [ 
						'title' => esc_html__( 'Right', 'bdthemes-element-pack' ),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-wrap' => 'align-items: {{VALUE}}; text-align: {{VALUE}}; justify-content: {{VALUE}};',
				],
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);

		$this->end_controls_section();

		//Style

		$this->start_controls_section(
			'section_style_time',
			[ 
				'label'     => esc_html__( 'Time', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'select_style' => 'digital',
				],
			]
		);

		$this->add_control(
			'time_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone .bdt-time-zone-time' => 'color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'time_typography',
				'selector' => '{{WRAPPER}} .bdt-time-zone .bdt-time-zone-time',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_date',
			[ 
				'label'     => esc_html__( 'Date', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_date' => 'yes',
				],

			]
		);

		$this->add_control(
			'date_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone .bdt-time-zone-date' => 'color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'date_typography',
				'selector' => '{{WRAPPER}} .bdt-time-zone .bdt-time-zone-date',
			]
		);

		$this->add_responsive_control(
			'date_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-time-zone .bdt-time-zone-date' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_country',
			[ 
				'label'     => esc_html__( 'Country', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'show_country' => 'yes',
				],
			]
		);

		$this->add_control(
			'country_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone .bdt-time-zone-country' => 'color: {{VALUE}};',
				],

			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'country_typography',
				'selector' => '{{WRAPPER}} .bdt-time-zone .bdt-time-zone-country',
			]
		);

		$this->add_responsive_control(
			'country_margin',
			[ 
				'label'      => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%' ],
				'selectors'  => [ 
					'{{WRAPPER}} .bdt-time-zone .bdt-time-zone-country' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		//circle style
		$this->start_controls_section(
			'section_style_circle',
			[ 
				'label'     => esc_html__( 'Circle', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);
		//circle size
		$this->add_responsive_control(
			'circle_clock_size',
			[ 
				'label'     => esc_html__( 'Size', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [ 
					'px' => [ 
						'min' => 100,
						'max' => 500,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-circle' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		//background popover control
		$this->add_control(
			'circle_background_color',
			[ 
				'label'        => esc_html__( 'Background Color', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
			]
		);
		$this->start_popover();
		//color
		$this->add_control(
			'circle_background_color_color',
			[ 
				'label'       => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [ 
					'circle_background_color' => 'yes',
				],
				'render_type' => 'ui',
				'selectors'   => [ 
					'{{WRAPPER}}' => '--ep-circle-bg-color: {{VALUE}};'
				],
			]
		);
		//second color
		$this->add_control(
			'circle_background_color_second_color',
			[ 
				'label'       => esc_html__( 'Second Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [ 
					'circle_background_color' => 'yes',
				],
				'render_type' => 'ui',
				'selectors'   => [ 
					'{{WRAPPER}}' => '--ep-circle-bg-second-color: {{VALUE}};'
				],
			]
		);
		//end popover
		$this->end_popover();
		//border color popover control
		$this->add_control(
			'circle_border_color',
			[ 
				'label'        => esc_html__( 'Border Color', 'bdthemes-element-pack' ),
				'type'         => Controls_Manager::POPOVER_TOGGLE,
				'label_off'    => __( 'None', 'bdthemes-element-pack' ),
				'label_on'     => __( 'Custom', 'bdthemes-element-pack' ),
				'return_value' => 'yes',
				'separator'    => 'before',
			]
		);
		$this->start_popover();
		//color
		$this->add_control(
			'circle_border_color_color',
			[ 
				'label'       => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [ 
					'circle_border_color' => 'yes',
				],
				'render_type' => 'ui',
				'selectors'   => [ 
					'{{WRAPPER}}' => '--ep-circle-border-color: {{VALUE}};'
				],
			]
		);
		//second color
		$this->add_control(
			'circle_border_color_second_color',
			[ 
				'label'       => esc_html__( 'Second Color', 'bdthemes-element-pack' ),
				'type'        => Controls_Manager::COLOR,
				'condition'   => [ 
					'circle_border_color' => 'yes',
				],
				'render_type' => 'ui',
				'selectors'   => [ 
					'{{WRAPPER}}' => '--ep-circle-border-second-color: {{VALUE}};'
				],
			]
		);
		$this->end_popover();
		//circle width
		$this->add_responsive_control(
			'circle_clock_width',
			[ 
				'label'     => esc_html__( 'Border Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-circle' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		// border radius
		$this->add_responsive_control(
			'circle_clock_border_radius',
			[ 
				'label'     => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-circle' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->end_controls_section();

		//dial style
		$this->start_controls_section(
			'section_style_dial',
			[ 
				'label'     => esc_html__( 'Dial', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);
		//tabs dial control
		$this->start_controls_tabs( 'tabs_dial_style' );
		//normal dial style
		$this->start_controls_tab(
			'tab_dial_style_hour',
			[ 
				'label' => esc_html__( 'Hour', 'bdthemes-element-pack' ),
			]
		);
		//color
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'hour_background',
				'selector' => '{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-hour',
				'exclude'  => [ 'image' ],
			]
		);
		//end dial style
		$this->end_controls_tab();
		//dial style minute
		$this->start_controls_tab(
			'tab_dial_style_minute',
			[ 
				'label' => esc_html__( 'Minute', 'bdthemes-element-pack' ),
			]
		);
		//color
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'minute_background',
				'selector' => '{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-minute',
				'exclude'  => [ 'image' ],
			]
		);
		//end dial style
		$this->end_controls_tab();
		//dial style second
		$this->start_controls_tab(
			'tab_dial_style_second',
			[ 
				'label' => esc_html__( 'Second', 'bdthemes-element-pack' ),
			]
		);
		//color
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'second_background',
				'selector' => '{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-second',
				'exclude'  => [ 'image' ],
			]
		);
		//end dial style
		$this->end_controls_tab();
		//dial style point
		$this->start_controls_tab(
			'tab_dial_style_point',
			[ 
				'label' => esc_html__( 'Point', 'bdthemes-element-pack' ),
			]
		);
		//color
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[ 
				'name'     => 'point_background',
				'selector' => '{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-face:after',
				'exclude'  => [ 'image' ],
			]
		);
		//end dial style
		$this->end_controls_tab();
		//end tabs dial control
		$this->end_controls_tabs();

		$this->end_controls_section();

		//am/pm style
		$this->start_controls_section(
			'section_style_am_pm',
			[ 
				'label'     => esc_html__( 'AM/PM', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);

		//text color
		$this->add_control(
			'circle_clock_text_color',
			[ 
				'label'     => esc_html__( 'AM/PM Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-am-pm' => 'color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);
		//test typography
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[ 
				'name'     => 'circle_clock_text_typography',
				'selector' => '{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-am-pm',
			]
		);

		$this->end_controls_section();

		//Graduations style
		$this->start_controls_section(
			'section_style_graduations',
			[ 
				'label'     => esc_html__( 'Graduations', 'bdthemes-element-pack' ),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [ 
					'select_style' => 'analog_1',
				],
			]
		);

		// color
		$this->add_control(
			'circle_clock_graduations_color',
			[ 
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:before, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:before' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_responsive_control(
			'circle_clock_graduations_width',
			[ 
				'label'     => esc_html__( 'Width', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 3,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:before' => 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:before' => 'height: {{SIZE}}{{UNIT}};',
				],

			]
		);
		$this->add_responsive_control(
			'circle_clock_graduations_height',
			[ 
				'label'     => esc_html__( 'Height', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 6,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 40,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:before' => 'height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:before' => 'width: {{SIZE}}{{UNIT}};',
				],

			]
		);
		//border radius
		$this->add_responsive_control(
			'circle_clock_graduations_border_radius',
			[ 
				'label'     => esc_html__( 'Border Radius', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::SLIDER,
				'default'   => [ 
					'size' => 0,
				],
				'range'     => [ 
					'px' => [ 
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' => [ 
					'{{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-y:before, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:after, {{WRAPPER}} .bdt-time-zone-style-analog_1 .bdt-clock-time-graduations-x:before' => 'border-radius: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

	}

	protected function render() {
		$settings = $this->get_settings_for_display();

		if ( $settings['select_gmt'] == 'custom' ) {
			$select_gmt = $settings['input_gmt'];
		} else {
			$select_gmt = $settings['select_gmt'];
		}

		if ( $settings['show_country'] == 'yes' ) {
			$country = $settings['input_country'];
		} else {
			$country = 'emptyCountry';
		}

		if ( $settings['show_date'] == 'yes' ) {
			if ( $settings['select_date_format'] == 'custom' ) {
				$dateFormat = $settings['input_date_format'];
			} else {
				$dateFormat = $settings['select_date_format'];
			}
		} else {
			$dateFormat = 'emptyDate';
		}

		$this->add_render_attribute(
			[ 
				'bdt_time_zone_data' => [ 
					'data-settings' => [ 
						wp_json_encode(
							array_filter( [ 
								"id"          => 'bdt-time-zone-data-' . $this->get_id(),
								"clock_style" => $settings['select_style'],
								"gmt"         => $select_gmt,
								"timeHour"    => $settings['time_hour'],
								"country"     => $country,
								"dateFormat"  => $dateFormat,
							] )
						),
					],
				],
			]
		);

		$this->add_render_attribute( 'bdt_time_zone_data', 'class', [ 
			'bdt-time-zone',
			'bdt-time-zone-style-' . $settings['select_style'],
			'bdt-time-zone-' . $settings['timer_layout'],
		] );

		?>

		<div <?php echo $this->get_render_attribute_string( 'bdt_time_zone_data' ); ?>>
			<?php if ( 'analog_1' !== $settings['select_style'] ) : ?>
				<div class="bdt-time-zone-timer  " id="bdt-time-zone-data-<?php echo $this->get_id(); ?>" <?php echo $this->get_render_attribute_string( 'bdt_time_zone_data' ); ?>>
				</div>
			<?php else : ?>
				<div class="bdt-clock-wrap bdt-flex">
					<div class="bdt-clock-circle">
						<div class="bdt-clock-face">
							<div class="bdt-clock-hour"></div>
							<div class="bdt-clock-minute"></div>
							<?php if ( 'yes' == $settings['show_clock_second'] ) : ?>
								<div class="bdt-clock-second"></div>
							<?php endif; ?>
						</div>
						<div class="bdt-clock-am-pm"></div>
						<div class="bdt-clock-time-graduations-y"></div>
						<div class="bdt-clock-time-graduations-x"></div>
					</div>
					<?php if ( 'yes' == $settings['show_date'] or 'yes' == $settings['show_country'] ) : ?>
					<div class="bdt-country-date-wrap">
						<?php
						if ( 'yes' == $settings['show_country'] && ! empty( $settings['input_country'] ) ) {
							echo '<div class="bdt-time-zone-country">' . esc_html( $settings['input_country'] ) . '</div>';
						}
						if ( 'yes' == $settings['show_date'] ) {
							echo '<div class="bdt-time-zone-date"></div>';
						}
						?>
					</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}