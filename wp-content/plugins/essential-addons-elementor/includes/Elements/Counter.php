<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use Essential_Addons_Elementor\Classes\Helper;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // If this file is called directly, abort.
}

class Counter extends Widget_Base {
    
    public function get_name() {
        return 'eael-counter';
    }

    public function get_title() {
        return __( 'Counter', 'essential-addons-elementor' );
    }

    public function get_categories() {
        return [ 'essential-addons-elementor' ];
    }

    public function get_keywords()
    {
        return [
            'ea counter',
            'data counter',
            'key information',
            'highlight data',
            'stats',
            'key figures',
            'numbers',
            'counter number',
            'fun facts',
            'ea',
            'essential addons'
        ];
    }

    public function get_custom_help_url()
    {
		return 'https://essential-addons.com/elementor/docs/counter/';
	}

    public function get_icon() {
        return 'eaicon-counter';
    }

    protected function register_controls() {

        /**
         *	CONTENT TAB
         */
        
        /**
         * Content Tab: Counter
         */
        $this->start_controls_section(
            'section_counter',
            [
                'label'                 => __( 'Counter', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
			'eael_icon_type',
			[
				'label'                 => esc_html__( 'Icon Type', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::CHOOSE,
				'label_block'           => false,
				'options'               => [
					'none'        => [
						'title'   => esc_html__( 'None', 'essential-addons-elementor' ),
						'icon'    => 'eicon-ban',
					],
					'icon'        => [
						'title'   => esc_html__( 'Icon', 'essential-addons-elementor' ),
						'icon'    => 'fa fa-info-circle',
					],
					'image'       => [
						'title'   => esc_html__( 'Image', 'essential-addons-elementor' ),
						'icon'    => 'eicon-image-bold',
					],
				],
				'default'               => 'none',
			]
		);
        
        $this->add_control(
            'counter_icon_new',
            [
                'label'                 => __( 'Icon', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::ICONS,
                'fa4compatibility' => 'counter_icon',
                'condition'             => [
                    'eael_icon_type'  => 'icon',
                ],
            ]
        );
        
        $this->add_control(
            'icon_image',
            [
                'label'                 => __( 'Image', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::MEDIA,
                'default'               => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
				'condition'             => [
					'eael_icon_type'  => 'image',
				],
                'ai' => [
                    'active' => false,
                ],
            ]
        );
        
        $this->add_control(
            'ending_number',
            [
                'label'                 => __( 'Number', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::NUMBER,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => __( '250', 'essential-addons-elementor' ),
                'separator'             => 'before',
            ]
        );
        
        $this->add_control(
            'number_prefix',
            [
                'label'                 => __( 'Number Prefix', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'ai' => [
					'active' => false,
				],
            ]
        );
        
        $this->add_control(
            'number_suffix',
            [
                'label'                 => __( 'Number Suffix', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'ai' => [
					'active' => false,
				],
            ]
        );

        $this->add_control(
            'counter_title',
            [
                'label'                 => __( 'Title', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::TEXT,
				'dynamic'               => [
					'active'   => true,
				],
                'default'               => __( 'Counter Title', 'essential-addons-elementor' ),
                'separator'             => 'before',
                'ai' => [
					'active' => false,
				],
            ]
        );
        
        $this->add_control(
            'title_html_tag',
            [
                'label'                => __( 'Title HTML Tag', 'essential-addons-elementor' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'div',
                'options'              => [
                    'h1'     => __( 'H1', 'essential-addons-elementor' ),
                    'h2'     => __( 'H2', 'essential-addons-elementor' ),
                    'h3'     => __( 'H3', 'essential-addons-elementor' ),
                    'h4'     => __( 'H4', 'essential-addons-elementor' ),
                    'h5'     => __( 'H5', 'essential-addons-elementor' ),
                    'h6'     => __( 'H6', 'essential-addons-elementor' ),
                    'div'    => __( 'div', 'essential-addons-elementor' ),
                    'span'   => __( 'span', 'essential-addons-elementor' ),
                    'p'      => __( 'p', 'essential-addons-elementor' ),
                ],
            ]
        );
        
        $this->add_control(
            'counter_layout',
            [
                'label'                => __( 'Layout', 'essential-addons-elementor' ),
                'type'                 => Controls_Manager::SELECT,
                'default'              => 'layout-1',
                'options'              => [
                    'layout-1'     => __( 'Layout 1', 'essential-addons-elementor' ),
                    'layout-2'     => __( 'Layout 2', 'essential-addons-elementor' ),
                    'layout-3'     => __( 'Layout 3', 'essential-addons-elementor' ),
                    'layout-4'     => __( 'Layout 4', 'essential-addons-elementor' ),
                    'layout-5'     => __( 'Layout 5', 'essential-addons-elementor' ),
                    'layout-6'     => __( 'Layout 6', 'essential-addons-elementor' ),
                ],
                'separator'             => 'before',
            ]
        );

        $this->end_controls_section();

        /**
         * Content Tab: Separators
         */
        $this->start_controls_section(
            'section_counter_separators',
            [
                'label'                 => __( 'Dividers', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_control(
            'icon_divider',
            [
                'label'                 => __( 'Icon Divider', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'On', 'essential-addons-elementor' ),
                'label_off'             => __( 'Off', 'essential-addons-elementor' ),
                'return_value'          => 'yes',
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
            ]
        );
        
        $this->add_control(
            'num_divider',
            [
                'label'                 => __( 'Number Divider', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SWITCHER,
                'default'               => 'no',
                'label_on'              => __( 'On', 'essential-addons-elementor' ),
                'label_off'             => __( 'Off', 'essential-addons-elementor' ),
                'return_value'          => 'yes',
            ]
        );
        
        $this->end_controls_section();

        /**
         * Content Tab: Settings
         */
        $this->start_controls_section(
            'section_counter_settings',
            [
                'label'                 => __( 'Settings', 'essential-addons-elementor' ),
            ]
        );
        
        $this->add_responsive_control(
            'counter_speed',
            [
                'label'                 => __( 'Counting Speed', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [ 'size' => 1500 ],
                'range'                 => [
                    'px' => [
                        'min'   => 100,
                        'max'   => 2000,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
            ]
        );
        
        $this->end_controls_section();

        /**
         * STYLE TAB
         */
        
        /**
         * Style Tab: Counter
         */
        $this->start_controls_section(
            'section_style',
            [
                'label'                 => __( 'Counter', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );
        
        $this->add_responsive_control(
			'counter_align',
			[
				'label'                 => __( 'Alignment', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::CHOOSE,
				'options'               => [
					'left'      => [
						'title' => __( 'Left', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-left',
					],
					'center'    => [
						'title' => __( 'Center', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-center',
					],
					'right'     => [
						'title' => __( 'Right', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-right',
					],
					'justify'   => [
						'title' => __( 'Justified', 'essential-addons-elementor' ),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'default'               => 'center',
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-container'   => 'text-align: {{VALUE}};',
				],
			]
		);

        $this->end_controls_section();

        /**
         * Style Tab: Icon
         */
        $this->start_controls_section(
            'section_counter_icon_style',
            [
                'label'                 => __( 'Icon', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'                  => 'counter_icon_bg',
                'label'                 => __( 'Background', 'essential-addons-elementor' ),
                'types'                 => [ 'none','classic','gradient' ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
                'selector'              => '{{WRAPPER}} .eael-counter-icon',
            ]
        );

        $this->add_control(
            'counter_icon_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .eael-counter-icon svg' => 'fill: {{VALUE}};',
                ],
                'condition'             => [
                    'eael_icon_type'  => 'icon',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_size',
            [
                'label'                 => __( 'Size', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 5,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'default'               => [
                        'unit' => 'px',
                        'size' => 40,
                ],
                'size_units'            => [ 'px', 'em' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon' => 'font-size: {{SIZE}}{{UNIT}}',
                    '{{WRAPPER}} .eael-counter-icon .eael-counter-svg-icon'=> 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .eael-counter-icon .eael-counter-svg-icon svg'=> 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
                ],
                'condition'             => [
                    'eael_icon_type'  => 'icon',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_img_width',
            [
                'label'                 => __( 'Image Width', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 10,
                        'max'   => 500,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => ['px', '%'],
                'condition'             => [
                    'eael_icon_type'  => 'image',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'counter_icon_rotation',
            [
                'label'                 => __( 'Rotation', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 360,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => '',
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon .fa, {{WRAPPER}} .eael-counter-icon img' => 'transform: rotate( {{SIZE}}deg );',
                ],
            ]
        );

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'                  => 'counter_icon_border',
				'label'                 => __( 'Border', 'essential-addons-elementor' ),
				'placeholder'           => '1px',
				'default'               => '1px',
				'selector'              => '{{WRAPPER}} .eael-counter-icon',
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
			]
		);

		$this->add_control(
			'counter_icon_border_radius',
			[
				'label'                 => __( 'Border Radius', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-icon' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_icon_padding',
			[
				'label'                 => __( 'Padding', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-icon' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_icon_margin',
			[
				'label'                 => __( 'Margin', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-icon-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                ],
			]
		);
        
        $this->add_control(
            'icon_divider_heading',
            [
                'label'                 => __( 'Icon Divider', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'icon_divider_type',
            [
            'label'                     => __( 'Divider Type', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'solid'     => __( 'Solid', 'essential-addons-elementor' ),
                    'double'    => __( 'Double', 'essential-addons-elementor' ),
                    'dotted'    => __( 'Dotted', 'essential-addons-elementor' ),
                    'dashed'    => __( 'Dashed', 'essential-addons-elementor' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_height',
            [
                'label'                 => __( 'Height', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 2,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_width',
            [
                'label'                 => __( 'Width', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 30,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'icon_divider_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'icon_divider_margin',
            [
                'label'                 => __( 'Spacing', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-icon-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'eael_icon_type!' => 'none',
                    'icon_divider'  => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Number
         */
        $this->start_controls_section(
            'section_counter_num_style',
            [
                'label'                 => __( 'Number', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'counter_num_color',
            [
                'label'                 => __( 'Number Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-number' => 'color: {{VALUE}};',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'counter_num_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'selector'              => '{{WRAPPER}} .eael-counter-number-wrap .eael-counter-number',
            ]
        );

		$this->add_responsive_control(
			'counter_num_margin',
			[
				'label'                 => __( 'Margin', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-number-wrap' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
			]
		);
        
        $this->add_control(
            'num_divider_heading',
            [
                'label'                 => __( 'Number Divider', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::HEADING,
                'separator'             => 'before',
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_control(
            'num_divider_type',
            [
                'label'                 => __( 'Divider Type', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SELECT,
                'default'               => 'solid',
                'options'               => [
                    'solid'     => __( 'Solid', 'essential-addons-elementor' ),
                    'double'    => __( 'Double', 'essential-addons-elementor' ),
                    'dotted'    => __( 'Dotted', 'essential-addons-elementor' ),
                    'dashed'    => __( 'Dashed', 'essential-addons-elementor' ),
                ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-num-divider' => 'border-bottom-style: {{VALUE}}',
                ],
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_height',
            [
                'label'                 => __( 'Height', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 2,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 20,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-num-divider' => 'border-bottom-width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_width',
            [
                'label'                 => __( 'Width', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'default'               => [
                    'size'  => 30,
                ],
                'range'                 => [
                    'px' => [
                        'min'   => 1,
                        'max'   => 1000,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 1,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-num-divider' => 'width: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );

        $this->add_control(
            'num_divider_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-num-divider' => 'border-bottom-color: {{VALUE}}',
                ],
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );
        
        $this->add_responsive_control(
            'num_divider_margin',
            [
                'label'                 => __( 'Spacing', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::SLIDER,
                'range'                 => [
                    'px' => [
                        'min'   => 0,
                        'max'   => 100,
                        'step'  => 1,
                    ],
                    '%' => [
                        'min'   => 0,
                        'max'   => 30,
                        'step'  => 1,
                    ],
                ],
                'size_units'            => [ 'px', '%' ],
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-num-divider-wrap' => 'margin-bottom: {{SIZE}}{{UNIT}}',
                ],
                'condition'             => [
                    'num_divider'  => 'yes',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Prefix
         */
        $this->start_controls_section(
            'section_number_prefix_style',
            [
                'label'                 => __( 'Prefix', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );

        $this->add_control(
            'number_prefix_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-number-prefix' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'number_prefix_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'selector'              => '{{WRAPPER}} .eael-counter-number-prefix',
                'condition'             => [
                    'number_prefix!' => '',
                ],
            ]
        );
        
        $this->end_controls_section();

        /**
         * Style Tab: Suffix
         */
        $this->start_controls_section(
            'section_number_suffix_style',
            [
                'label'                 => __( 'Suffix', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );

        $this->add_control(
            'section_number_suffix_color',
            [
                'label'                 => __( 'Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-number-suffix' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'section_number_suffix_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'selector'              => '{{WRAPPER}} .eael-counter-number-suffix',
                'condition'             => [
                    'number_suffix!' => '',
                ],
            ]
        );
        
        $this->end_controls_section();

        $this->start_controls_section(
            'section_counter_title_style',
            [
                'label'                 => __( 'Title', 'essential-addons-elementor' ),
                'tab'                   => Controls_Manager::TAB_STYLE,
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'counter_title_color',
            [
                'label'                 => __( 'Text Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-title' => 'color: {{VALUE}};',
                ],
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );

        $this->add_control(
            'counter_title_bg_color',
            [
                'label'                 => __( 'Background Color', 'essential-addons-elementor' ),
                'type'                  => Controls_Manager::COLOR,
                'default'               => '',
                'selectors'             => [
                    '{{WRAPPER}} .eael-counter-title' => 'background-color: {{VALUE}};',
                ],
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );
        
        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'                  => 'counter_title_typography',
                'label'                 => __( 'Typography', 'essential-addons-elementor' ),
                'selector'              => '{{WRAPPER}} .eael-counter-title',
                'condition'             => [
                    'counter_title!' => '',
                ],
            ]
        );

		$this->add_responsive_control(
			'counter_title_margin',
			[
				'label'                 => __( 'Margin', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-title' => 'margin-top: {{TOP}}{{UNIT}}; margin-left: {{LEFT}}{{UNIT}}; margin-right: {{RIGHT}}{{UNIT}}; margin-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'counter_title!' => '',
                ],
			]
		);

		$this->add_responsive_control(
			'counter_title_padding',
			[
				'label'                 => __( 'Padding', 'essential-addons-elementor' ),
				'type'                  => Controls_Manager::DIMENSIONS,
				'size_units'            => [ 'px', '%' ],
				'placeholder'           => [
					'top'      => '',
					'right'    => '',
					'bottom'   => '',
					'left'     => '',
				],
				'selectors'             => [
					'{{WRAPPER}} .eael-counter-title' => 'padding-top: {{TOP}}{{UNIT}}; padding-left: {{LEFT}}{{UNIT}}; padding-right: {{RIGHT}}{{UNIT}}; padding-bottom: {{BOTTOM}}{{UNIT}};',
				],
                'condition'             => [
                    'counter_title!' => '',
                ],
			]
		);
        
        $this->end_controls_section();

    }

    /**
	 * Render counter widget output on the frontend.
	 */
    protected function render() {
        $settings = $this->get_settings_for_display();
        
        $this->add_render_attribute( 'counter', 'class', 'eael-counter eael-counter-'.esc_attr( $this->get_id() ) );
        
        if ( $settings['counter_layout'] ) {
            $this->add_render_attribute( 'counter', 'class', 'eael-counter-' . $settings['counter_layout'] );
        }
        
        $this->add_render_attribute( 'counter', 'data-target', '.eael-counter-number-'.esc_attr( $this->get_id() ) );
        
        $this->add_render_attribute( 'counter-number', 'class', 'eael-counter-number eael-counter-number-'.esc_attr( $this->get_id() ) );
        
        if ( $settings['ending_number'] != '' ) {
            $this->add_render_attribute( 'counter-number', 'data-to', $settings['ending_number'] );
        }
        
        if ( $settings['counter_speed']['size'] != '' ) {
            $this->add_render_attribute( 'counter-number', 'data-speed', $settings['counter_speed']['size'] );
        }
        
        $this->add_inline_editing_attributes( 'counter_title', 'none' );
        $this->add_render_attribute( 'counter_title', 'class', 'eael-counter-title' );
        ?>
        <div class="eael-counter-container">
            <div <?php echo $this->get_render_attribute_string( 'counter' ); ?>>
                <?php if ( $settings['counter_layout'] == 'layout-1' || $settings['counter_layout'] == 'layout-5' || $settings['counter_layout'] == 'layout-6' ) { ?>
                    <?php
                        // Counter icon
                        $this->render_icon();
                    ?>
                
                    <div class="eael-counter-number-title-wrap">
                        <div class="eael-counter-number-wrap">
                            <?php
                                if ( $settings['number_prefix'] != '' ) {
                                    printf( '<span class="eael-counter-number-prefix">%1$s</span>', esc_html( $settings['number_prefix'] ) );
                                }
                            ?>
                            <div <?php echo $this->get_render_attribute_string( 'counter-number' ); ?>>
                                0
                            </div>
                            <?php
                                if ( $settings['number_suffix'] != '' ) {
                                    printf( '<span class="eael-counter-number-suffix">%1$s</span>', esc_html( $settings['number_suffix'] ) );
                                }
                            ?>
                        </div>

                        <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                            <div class="eael-counter-num-divider-wrap">
                                <span class="eael-counter-num-divider"></span>
                            </div>
                        <?php } ?>

                        <?php
                            if ( !empty( $settings['counter_title'] ) ) {
                                printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'counter_title' ) );
                                    echo Helper::eael_wp_kses( $settings['counter_title'] );
                                printf( '</%1$s>', $settings['title_html_tag'] );
                            }
                        ?>
                    </div>
                <?php } elseif ( $settings['counter_layout'] == 'layout-2' ) { ?>
                    <?php
                        // Counter icon
                        $this->render_icon();

                        if ( !empty( $settings['counter_title'] ) ) {
                            printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'counter_title' ) );
                                echo Helper::eael_wp_kses( $settings['counter_title'] );
                            printf( '</%1$s>', $settings['title_html_tag'] );
                        }
                    ?>
                
                    <div class="eael-counter-number-wrap">
                        <?php
                            if ( $settings['number_prefix'] != '' ) {
                                printf( '<span class="eael-counter-number-prefix">%1$s</span>', $settings['number_prefix'] );
                            }
                        ?>
                        <div <?php echo $this->get_render_attribute_string( 'counter-number' ); ?>>
                            0
                        </div>
                        <?php
                            if ( $settings['number_suffix'] != '' ) {
                                printf( '<span class="eael-counter-number-suffix">%1$s</span>', $settings['number_suffix'] );
                            }
                        ?>
                    </div>

                    <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                        <div class="eael-counter-num-divider-wrap">
                            <span class="eael-counter-num-divider"></span>
                        </div>
                    <?php } ?>
                <?php } elseif ( $settings['counter_layout'] == 'layout-3' ) { ?>
                    <div class="eael-counter-number-wrap">
                        <?php
                            if ( $settings['number_prefix'] != '' ) {
                                printf( '<span class="eael-counter-number-prefix">%1$s</span>', $settings['number_prefix'] );
                            }
                        ?>
                        <div <?php echo $this->get_render_attribute_string( 'counter-number' ); ?>>
                            0
                        </div>
                        <?php
                            if ( $settings['number_suffix'] != '' ) {
                                printf( '<span class="eael-counter-number-suffix">%1$s</span>', $settings['number_suffix'] );
                            }
                        ?>
                    </div>

                    <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                        <div class="eael-counter-num-divider-wrap">
                            <span class="eael-counter-num-divider"></span>
                        </div>
                    <?php } ?>
                
                    <div class="eael-icon-title-wrap">
                        <?php
                            // Counter icon
                            $this->render_icon();

                            if ( !empty( $settings['counter_title'] ) ) {
                                printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'counter_title' ) );
                                    echo Helper::eael_wp_kses( $settings['counter_title'] );
                                printf( '</%1$s>', $settings['title_html_tag'] );
                            }
                        ?>
                    </div>
                <?php } elseif ( $settings['counter_layout'] == 'layout-4' ) { ?>
                    <div class="eael-icon-title-wrap">
                        <?php
                            // Counter icon
                            $this->render_icon();

                            if ( !empty( $settings['counter_title'] ) ) {
                                printf( '<%1$s %2$s>', $settings['title_html_tag'], $this->get_render_attribute_string( 'counter_title' ) );
                                    echo Helper::eael_wp_kses( $settings['counter_title'] );
                                printf( '</%1$s>', $settings['title_html_tag'] );
                            }
                        ?>
                    </div>
                
                    <div class="eael-counter-number-wrap">
                        <?php
                            if ( $settings['number_prefix'] != '' ) {
                                printf( '<span class="eael-counter-number-prefix">%1$s</span>', $settings['number_prefix'] );
                            }
                        ?>
                        <div <?php echo $this->get_render_attribute_string( 'counter-number' ); ?>>
                            0
                        </div>
                        <?php
                            if ( $settings['number_suffix'] != '' ) {
                                printf( '<span class="eael-counter-number-suffix">%1$s</span>', $settings['number_suffix'] );
                            }
                        ?>
                    </div>

                    <?php if ( $settings['num_divider'] == 'yes' ) { ?>
                        <div class="eael-counter-num-divider-wrap">
                            <span class="eael-counter-num-divider"></span>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
        </div><!-- .eael-counter-container -->
        <?php
    }
    
    /**
	 * Render counter icon output on the frontend.
     */
    private function render_icon() {
        $settings = $this->get_settings_for_display();
        $icon_migrated = isset($settings['__fa4_migrated']['counter_icon_new']);
        $icon_is_new = empty($settings['counter_icon']);
        
        if ( $settings['eael_icon_type'] == 'icon' ) { ?>
            <span class="eael-counter-icon-wrap">
                <span class="eael-counter-icon">
                    <?php if ($icon_is_new || $icon_migrated) {
                        echo '<span class="eael-counter-svg-icon">';
                        Icons_Manager::render_icon( $settings['counter_icon_new'] );
                        echo '</span>';
                     } else { ?>
                        <span class="<?php echo esc_attr( $settings['counter_icon'] ); ?>" aria-hidden="true"></span>
                    <?php } ?>
                </span>
            </span>
        <?php } elseif ( $settings['eael_icon_type'] == 'image' ) {
            $image = $settings['icon_image'];
            if ( $image['url'] ) {
            ?>
                <span class="eael-counter-icon-wrap">
                    <span class="eael-counter-icon eael-counter-icon-img">
                        <img src="<?php echo esc_url( $image['url'] ); ?>" alt="<?php echo esc_attr(get_post_meta($image['id'], '_wp_attachment_image_alt', true)); ?>">
                    </span>
                </span>
            <?php }
        }

        if ( $settings['icon_divider'] == 'yes' ) {
            if ( $settings['counter_layout'] == 'layout-1' || $settings['counter_layout'] == 'layout-2' ) { ?>
                <div class="eael-counter-icon-divider-wrap">
                    <span class="eael-counter-icon-divider"></span>
                </div>
                <?php
            }
        }
    }



    /**
	 * Render counter widget output in the editor.
	 */
    protected function content_template() { }
}
