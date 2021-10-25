<?php

namespace MasterAddons\Addons;

// Elementor Classes
use \Elementor\Widget_Base;
use \Elementor\Utils;
use \Elementor\Icons_Manager;
use \Elementor\Controls_Manager;
use \Elementor\Repeater;
use \Elementor\Scheme_Color;
use \Elementor\Control_Media;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Typography;
use \Elementor\Scheme_Typography;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Group_Control_Text_Shadow;

// Master Addons Classes
use MasterAddons\Inc\Helper\Master_Addons_Helper;
use MasterAddons\Inc\Controls\MA_Group_Control_Transition;

/**
 * Author Name: Liton Arefin
 * Author URL: https://jeweltheme.com
 * Date: 02/05/2020
 */

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Twitter_Slider extends Widget_Base
{

    private $_query = null;

    public function get_name()
    {
        return 'jltma-twitter-slider';
    }

    public function get_title()
    {
        return esc_html__('Twitter Slider', MELA_TD);
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-twitter-feed';
    }

    public function get_categories()
    {
        return ['master-addons'];
    }

    public function get_style_depends()
    {
        return [
            'font-awesome-5-all',
            'font-awesome-4-shim'
        ];
    }

    public function get_script_depends()
    {
        return ['swiper', 'master-addons-scripts'];
    }

    public function get_help_url()
    {
        return 'https://master-addons.com/demos/twitter-slider/';
    }

    public function on_import($element)
    {
        if (!get_post_type_object($element['settings']['posts_post_type'])) {
            $element['settings']['posts_post_type'] = 'post';
        }
        return $element;
    }

    public function get_query()
    {
        return $this->_query;
    }

    public function on_export($element)
    {
        $element = Group_Control_Posts::on_export_remove_setting_from_element($element, 'posts');
        return $element;
    }

    protected function _register_controls()
    {

        /*
        * Content: Layout
        */
        $this->start_controls_section(
            'jltma_ts_section_layout',
            [
                'label' => esc_html__('Layout', MELA_TD),
            ]
        );

        $this->add_control(
            'jltma_ts_tweet_num',
            [
                'label'   => esc_html__('Limit', MELA_TD),
                'type'    => Controls_Manager::NUMBER,
                'default' => 6,
            ]
        );

        $this->add_control(
            'jltma_ts_cache_time',
            [
                'label'   => esc_html__('Cache Time(m)', MELA_TD),
                'type'    => Controls_Manager::NUMBER,
                'default' => 60,
            ]
        );

        $this->add_control(
            'jltma_ts_show_avatar',
            [
                'label'   => esc_html__('Show Avatar', MELA_TD),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'jltma_ts_avatar_link',
            [
                'label'     => esc_html__('Avatar Link', MELA_TD),
                'type'      => Controls_Manager::SWITCHER,
                'condition' => [
                    'jltma_ts_show_avatar' => 'yes'
                ]
            ]
        );

        $this->add_control(
            'jltma_ts_show_time',
            [
                'label'   => esc_html__('Show Time', MELA_TD),
                'type'    => Controls_Manager::SWITCHER,
                'default' => 'yes',
            ]
        );

        $this->add_control(
            'jltma_ts_long_time_format',
            [
                'label'     => esc_html__('Long Time Format', MELA_TD),
                'type'      => Controls_Manager::SWITCHER,
                'default'   => 'yes',
                'condition' => [
                    'jltma_ts_show_time' => 'yes',
                ]
            ]
        );


        $this->add_control(
            'jltma_ts_show_meta_button',
            [
                'label' => esc_html__('Execute Buttons', MELA_TD),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->add_control(
            'jltma_ts_exclude_replies',
            [
                'label' => esc_html__('Exclude Replies', MELA_TD),
                'type'  => Controls_Manager::SWITCHER,
            ]
        );

        $this->end_controls_section();




		/* Carousel Settings */
		$this->start_controls_section(
			'section_carousel_settings',
			[
				'label' => esc_html__('Carousel Settings', MELA_TD),
			]
		);

		$this->add_control(
			'autoheight',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Auto Height', MELA_TD),
				'default' 		=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->add_control(
			'carousel_height',
			[
				'label' 		=> __('Custom Height', MELA_TD),
				'description'	=> __('The carousel needs to have a fixed defined height to work in vertical mode.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'size_units' 	=> [
					'px', '%', 'vh'
				],
				'default' => [
					'size' => 500,
					'unit' => 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 200,
						'max' => 2000,
					],
					'%' 		=> [
						'min' => 0,
						'max' => 100,
					],
					'vh' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__container' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition'		=> [
					'autoheight!' => 'yes'
				],
			]
		);

		$this->add_control(
			'slide_effect',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Effect', MELA_TD),
				'default' 		=> 'slide',
				'options' 		=> [
					'slide' 	=> __('Slide', MELA_TD),
					'fade' 		=> __('Fade', MELA_TD),
				],
				'frontend_available' => true
			]
		);

		$this->add_control(
			'slide_effect_fade_warning',
			[
				'type' 				=> Controls_Manager::RAW_HTML,
				'raw' 				=> __('The Fade effect ignores the Slides per View and Slides per Column settings', MELA_TD),
				'content_classes' 	=> 'elementor-panel-alert elementor-panel-alert-info',
				'condition' 		=> [
					'slide_effect' => 'fade'
				],
			]
		);


		$this->add_control(
			'duration_speed',
			[
				'label' 	=> __('Duration (ms)', MELA_TD),
				'description' => __('Duration of the effect transition.', MELA_TD),
				'type' 		=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 300,
					'unit' 	=> 'px',
				],
				'range' 	=> [
					'px' 	=> [
						'min' 	=> 0,
						'max' 	=> 2000,
						'step'	=> 100,
					],
				],
				'frontend_available' => true
			]
		);



		$this->add_control(
			'resistance_ratio',
			[
				'label' 		=> __('Resistance', MELA_TD),
				'description'	=> __('Set the value for resistant bounds.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 		=> [
					'size' 		=> 0.25,
					'unit' 		=> 'px',
				],
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.05,
					],
				],
				'frontend_available' => true
			]
		);


		$this->add_control(
			'carousel_layout_heading',
			[
				'label' 			=> __('Layout', MELA_TD),
				'type' 				=> Controls_Manager::HEADING,
				'separator'			=> 'before'
			]
		);

		$this->add_responsive_control(
			'carousel_direction',
			[
				'type' 				=> Controls_Manager::SELECT,
				'label' 			=> __('Orientation', MELA_TD),
				'default'			=> 'horizontal',
				'tablet_default'	=> 'horizontal',
				'mobile_default'	=> 'horizontal',
				'options' 			=> [
					'horizontal' 	=> __('Horizontal', MELA_TD),
					'vertical' 		=> __('Vertical', MELA_TD),
				],
				'frontend_available' 	=> true
			]
		);




		$slides_per_view = range(1, 6);
		$slides_per_view = array_combine($slides_per_view, $slides_per_view);

		$this->add_responsive_control(
			'slides_per_view',
			[
				'type'           		=> Controls_Manager::SELECT,
				'label'          		=> esc_html__('Slides Per View', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'        		=> '1',
				'tablet_default' 		=> '1',
				'mobile_default' 		=> '1',
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides Per Column', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'        		=> '1',
				'tablet_default' 		=> '1',
				'mobile_default' 		=> '1',
				'frontend_available' 	=> true,
				'condition' 		=> [
					'carousel_direction' => 'horizontal',
				],
			]
		);


		$this->add_responsive_control(
			'slides_to_scroll',
			[
				'type'      => Controls_Manager::SELECT,
				'label'     => esc_html__('Slides to Scroll', MELA_TD),
				'options' 	=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'default'   => '1',
				'frontend_available' 	=> true,
			]
		);


		$this->add_responsive_control(
			'columns_spacing',
			[
				'label' 			=> __('Columns Spacing', MELA_TD),
				'type' 				=> Controls_Manager::SLIDER,
				'default'			=> [
					'size' => 24,
					'unit' => 'px',
				],
				'tablet_default'	=> [
					'size' => 12,
					'unit' => 'px',
				],
				'mobile_default'	=> [
					'size' => 0,
					'unit' => 'px',
				],
				'size_units' 		=> ['px'],
				'range' 			=> [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'frontend_available' => true,
				'condition'				=> [
					'carousel_direction' => 'horizontal',
				],
			]
		);


		$this->add_control(
			'autoplay',
			[
				'label'     	=> esc_html__('Autoplay', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'   	=> 'yes',
				'separator'   	=> 'before',
				'return_value' 	=> 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->start_popover();

		$this->add_control(
			'autoplay_speed',
			[
				'label'     => esc_html__('Autoplay Speed', MELA_TD),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 5000,
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'pause_on_interaction',
			[
				'label' 		=> __('Disable on Interaction', MELA_TD),
				'description' 	=> __('Removes autoplay completely on the first interaction with the carousel.', MELA_TD),
				'type' 			=> Controls_Manager::SWITCHER,
				'default' 		=> '',
				'condition' 	=> [
					'autoplay'           => 'yes'
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'pause',
			[
				'label'     => esc_html__('Pause on Hover', MELA_TD),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'autoplay' => 'yes',
				],
				'frontend_available' 	=> true,
			]
		);

		$this->end_popover();




		$this->add_control(
			'free_mode',
			[
				'type' 					=> Controls_Manager::POPOVER_TOGGLE,
				'label' 				=> __('Free Mode', MELA_TD),
				'description'			=> __('Disable fixed positions for slides.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true
			]
		);

		$this->start_popover();

		$this->add_control(
			'free_mode_sticky',
			[
				'type' 					=> Controls_Manager::SWITCHER,
				'label' 				=> __('Snap to position', MELA_TD),
				'description'			=> __('Enable to snap slides to positions in free mode.', MELA_TD),
				'default' 				=> '',
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true,
				'condition' 			=> [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Momentum', MELA_TD),
				'description'	=> __('Enable to keep slide moving for a while after you release it.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'separator'		=> 'before',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' => '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_ratio',
			[
				'label' 		=> __('Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum distance after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_velocity',
			[
				'label' 		=> __('Velocity', MELA_TD),
				'description'	=> __('Higher value produces larger momentum velocity after you release slider.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Bounce', MELA_TD),
				'description'	=> __('Set to No if you want to disable momentum bounce in free mode.', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true,
				'condition' => [
					'free_mode!' 			=> '',
					'free_mode_momentum!' 	=> '',
				],
			]
		);

		$this->add_control(
			'free_mode_momentum_bounce_ratio',
			[
				'label' 		=> __('Bounce Ratio', MELA_TD),
				'description'	=> __('Higher value produces larger momentum bounce effect.', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 5,
						'step'	=> 0.1,
					],
				],
				'condition' => [
					'free_mode!' => '',
					'free_mode_momentum!' => '',
					'free_mode_momentum_bounce!' => '',
				],
				'frontend_available' => true,
			]
		);

		$this->end_popover();



		$this->add_control(
			'carousel_arrows',
			[
				'label'         => __('Arrows', MELA_TD),
				'type'          => Controls_Manager::POPOVER_TOGGLE,
				'default'       => 'yes',
				'return_value' 	=> 'yes',
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'arrows_placement',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Placement', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 	=> __('Inside', MELA_TD),
					'outside' 	=> __('Outside', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' => 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->add_control(
			'arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> __('Left', MELA_TD),
					'center' 	=> __('Center', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows' 	=> 'yes',
				]
			]
		);

		$this->end_popover();



		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Infinite Loop', MELA_TD),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'frontend_available' 	=> true,
			]
		);

		$this->add_control(
			'slide_change_resize',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Trigger Resize on Slide', MELA_TD),
				'description'	=> __('Some widgets inside post skins templates might require triggering a window resize event when changing slides to display correctly.', MELA_TD),
				'default' 		=> '',
				'frontend_available' => true,
			]
		);


		$this->add_control(
			'carousel_pagination',
			[
				'label' 		=> __('Pagination', MELA_TD),
				'type' 			=> Controls_Manager::POPOVER_TOGGLE,
				'frontend_available' => true
			]
		);

		$this->start_popover();

		$this->add_control(
			'pagination_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'inside',
				'options' 		=> [
					'inside' 		=> __('Inside', MELA_TD),
					'outside' 		=> __('Outside', MELA_TD),
				],
				'frontend_available' 	=> true,
				'condition'		=> [
					'carousel_pagination'         => 'yes',
				]
			]
		);

		$this->add_control(
			'pagination_type',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Type', MELA_TD),
				'default'		=> 'bullets',
				'options' 		=> [
					'bullets' 		=> __('Bullets', MELA_TD),
					'fraction' 		=> __('Fraction', MELA_TD),
				],
				'condition'		=> [
					'carousel_pagination'         => 'yes',
				],
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'carousel_pagination_clickable',
			[
				'type' 			=> Controls_Manager::SWITCHER,
				'label' 		=> __('Clickable', MELA_TD),
				'default' 		=> 'yes',
				'return_value' 	=> 'yes',
				'condition' => [
					'carousel_pagination'         => 'yes',
					'pagination_type'       		=> 'bullets'
				],
				'frontend_available' 	=> true,
			]
		);
		$this->end_popover();


		$this->end_controls_section();

        /*
        * STYLE: Items
        */

        $this->start_controls_section(
            'jltma_ts_section_style_layout',
            [
                'label' => esc_html__('Items', MELA_TD),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_control(
            'jltma_ts_item_color',
            [
                'label'     => esc_html__('Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .jltma-twitter-slider-item .bdt-twitter-text,
                    {{WRAPPER}} .jltma-twitter-slider .jltma-twitter-slider-item .bdt-twitter-text *' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_alignment',
            [
                'label'   => esc_html__('Alignment', MELA_TD),
                'type'    => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left'    => [
                        'title' => esc_html__('Left', MELA_TD),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', MELA_TD),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', MELA_TD),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .jltma-twitter-slider-item .bdt-card-body' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*
        * STYLE: Avatar
        */


        $this->start_controls_section(
            'jltma_ts_section_style_avatar',
            [
                'label'     => esc_html__('Avatar', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'jltma_ts_show_avatar' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_avatar_width',
            [
                'label' => esc_html__('Width', MELA_TD),
                'type'  => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 48,
                        'min' => 15,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper img' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );


        $this->add_control(
            'jltma_ts_avatar_align',
            [
                'label'   => esc_html__('Alignment', MELA_TD),
                'type'    => Controls_Manager::CHOOSE,
                'options' => [
                    'left'    => [
                        'title' => esc_html__('Left', MELA_TD),
                        'icon'  => 'fa fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', MELA_TD),
                        'icon'  => 'fa fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', MELA_TD),
                        'icon'  => 'fa fa-align-right',
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_avatar_background',
            [
                'label'     => esc_html__('Background', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper' => 'background-color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_ts_avatar_padding',
            [
                'label'      => esc_html__('Padding', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_ts_avatar_margin',
            [
                'label'      => esc_html__('Margin', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_ts_avatar_radius',
            [
                'label'      => esc_html__('Border Radius', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper,
                    {{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_avatar_opacity',
            [
                'label'   => esc_html__('Opacity (%)', MELA_TD),
                'type'    => Controls_Manager::SLIDER,
                'default' => [
                    'size' => 1,
                ],
                'range' => [
                    'px' => [
                        'max'  => 1,
                        'min'  => 0.10,
                        'step' => 0.01,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-thumb-wrapper img' => 'opacity: {{SIZE}};',
                ],
            ]
        );

        $this->end_controls_section();



        /*
        * STYLE: Execute Button
        */

        $this->start_controls_section(
            'jltma_ts_section_style_meta',
            [
                'label'     => esc_html__('Execute Buttons', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'jltma_ts_show_meta_button' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_meta_color',
            [
                'label'     => esc_html__('Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-meta-button > a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_meta_hover_color',
            [
                'label'     => esc_html__('Hover Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-meta-button > a:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();


        /*
        * STYLE: Time
        */

        $this->start_controls_section(
            'jltma_ts_section_style_time',
            [
                'label'     => esc_html__('Time', MELA_TD),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'jltma_ts_show_time' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_time_color',
            [
                'label'     => esc_html__('Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-meta-wrapper a.bdt-twitter-time-link' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'jltma_ts_time_hover_color',
            [
                'label'     => esc_html__('Hover Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-twitter-slider .bdt-twitter-meta-wrapper a.bdt-twitter-time-link:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->end_controls_section();



		/*
		Style Tab: Carousel Settings
		*/

		$this->start_controls_section(
			'carousel_style_section',
			[
				'label'         => __('Carousel', MELA_TD),
				'tab'           => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'carousel_arrows_style_heading',
			[
				'label' 	=> __('Arrows', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'     => [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_control(
			'carousel_arrows_position',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'middle',
				'options' 		=> [
					'top' 		=> __('Top', MELA_TD),
					'middle' 	=> __('Middle', MELA_TD),
					'bottom' 	=> __('Bottom', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_control(
			'carousel_arrows_position_vertical',
			[
				'type' 			=> Controls_Manager::SELECT,
				'label' 		=> __('Position', MELA_TD),
				'default'		=> 'center',
				'options' 		=> [
					'left' 		=> __('Left', MELA_TD),
					'center' 	=> __('Center', MELA_TD),
					'right' 	=> __('Right', MELA_TD),
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'carousel_arrows_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 12,
						'max' => 48,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'font-size: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'carousel_arrows_padding',
			[
				'label' 		=> __('Padding', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' 	=> 0,
						'max' 	=> 1,
						'step'	=> 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'padding: {{SIZE}}em;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);


		$this->add_responsive_control(
			'arrows_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--middle.jltma-arrows--horizontal .jltma-swiper__button' => 'margin-left: {{SIZE}}px; margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--middle).jltma-arrows--horizontal .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--prev' => 'left: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--horizontal .jltma-swiper__button--next' => 'right: -{{SIZE}}px;',

					'{{WRAPPER}} .jltma-swiper__navigation--inside.jltma-swiper__navigation--center.jltma-arrows--vertical .jltma-swiper__button' => 'margin-top: {{SIZE}}px; margin-bottom: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--inside:not(.jltma-swiper__navigation--center).jltma-arrows--vertical .jltma-swiper__button' => 'margin: {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--prev' => 'top: -{{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__navigation--outside.jltma-arrows--vertical .jltma-swiper__button--next' => 'bottom: -{{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_responsive_control(
			'arrows_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'default' 	=> [
					'size' 	=> 100,
				],
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__button' => 'border-radius: {{SIZE}}%;',
				],
				'separator'		=> 'after',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'arrows',
				'selector' 		=> '{{WRAPPER}} .jltma-swiper__button',
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]
			]
		);


		$this->start_controls_tabs('carousel_arrow_style_tabs');

		// Normal Tab
		$this->start_controls_tab(
			'carousel_arrow_style_tab',
			[
				'label'         => __('Normal', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'arrow_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();



		// Hover Tab
		$this->start_controls_tab(
			'carousel_arrow_hover_style_tab',
			[
				'label'         => __('Hover', MELA_TD),
				'condition'		=> [
					'carousel_arrows'         => 'yes'
				]

			]
		);
		$this->add_control(
			'arrow_hover_color',
			[
				'label'         => __('Arrow Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover i:before' => 'color: {{VALUE}};',
				]
			]
		);
		$this->add_control(
			'arrow_hover_bg_color',
			[
				'label'         => __('Background Color', MELA_TD),
				'type'          => Controls_Manager::COLOR,
				'selectors'     => [
					'{{WRAPPER}} .jltma-swiper__button:not(.jltma-swiper__button--disabled):hover' => 'background: {{VALUE}};',
				]
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();


		$this->add_control(
			'carousel_pagination_style_heading',
			[
				'separator'	=> 'before',
				'label' 	=> __('Pagination', MELA_TD),
				'type' 		=> Controls_Manager::HEADING,
				'condition'		=> [
					'carousel_pagination' => 'yes',
				]
			]
		);


		$this->add_responsive_control(
			'carousel_pagination_align',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'center',
				'options' 		=> [
					'left'    		=> [
						'title' 	=> __('Left', MELA_TD),
						'icon' 		=> 'fa fa-align-left',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'fa fa-align-center',
					],
					'right' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'fa fa-align-right',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--horizontal' => 'text-align: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'horizontal',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_align_vertical',
			[
				'label' 		=> __('Align', MELA_TD),
				'type' 			=> Controls_Manager::CHOOSE,
				'default' 		=> 'middle',
				'options' 		=> [
					'flex-start'    => [
						'title' 	=> __('Top', MELA_TD),
						'icon' 		=> 'eicon-v-align-top',
					],
					'center' 		=> [
						'title' 	=> __('Center', MELA_TD),
						'icon' 		=> 'eicon-v-align-middle',
					],
					'flex-end' 		=> [
						'title' 	=> __('Right', MELA_TD),
						'icon' 		=> 'eicon-v-align-bottom',
					],
				],
				'selectors'		=> [
					'{{WRAPPER}} .jltma-swiper__pagination.jltma-swiper__pagination--vertical' => 'justify-content: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'carousel_direction' => 'vertical'
				]
			]
		);


		$this->add_responsive_control(
			'carousel_pagination_distance',
			[
				'label' 		=> __('Distance', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--horizontal' => 'padding: 0 {{SIZE}}px {{SIZE}}px {{SIZE}}px;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--horizontal' => 'padding: {{SIZE}}px 0 0 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--inside.jltma-swiper__pagination--vertical' => 'padding: {{SIZE}}px {{SIZE}}px {{SIZE}}px 0;',
					'{{WRAPPER}} .jltma-swiper__pagination--outside.jltma-swiper__pagination--vertical' => 'padding: 0 0 0 {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_spacing',
			[
				'label' 		=> __('Spacing', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 20,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .jltma-swiper__pagination--horizontal .swiper-pagination-bullet' => 'margin: 0 {{SIZE}}px',
					'{{WRAPPER}} .jltma-swiper__pagination--vertical .swiper-pagination-bullet' => 'margin: {{SIZE}}px 0',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'pagination_bullets_border_radius',
			[
				'label' 		=> __('Border Radius', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'border-radius: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' => 'yes',
					'pagination_type' => 'bullets',
				],
				'separator'		=> 'after',
			]
		);

		$this->add_group_control(
			MA_Group_Control_Transition::get_type(),
			[
				'name' 			=> 'carousel_pagination_bullet',
				'selector' 		=> '{{WRAPPER}} .swiper-pagination-bullet',
				'condition'		=> [
					'carousel_pagination' => 'yes'
				]
			]
		);


		$this->start_controls_tabs('carousel_pagination_bullets_tabs_hover');

		$this->start_controls_tab('carousel_pagination_bullets_tab_default', [
			'label' 		=> __('Default', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 12,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'width: {{SIZE}}px; height: {{SIZE}}px;',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'on',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('carousel_pagination_bullets_tab_hover', [
			'label' 		=> __('Hover', MELA_TD),
			'condition'		=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size_hover',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color_hover',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity_hover',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet:hover' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab('carousel_pagination_bullets_tab_active', [
			'label' => __('Active', MELA_TD),
			'condition'	=> [
				'carousel_pagination' 		=> 'yes',
				'pagination_type' 	=> 'bullets',
			]
		]);

		$this->add_responsive_control(
			'carousel_pagination_bullets_size_active',
			[
				'label' 		=> __('Size', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 1,
						'max' => 1.5,
						'step' => 0.1,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'transform: scale({{SIZE}});',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_control(
			'carousel_pagination_bullets_color_active',
			[
				'label' 	=> __('Color', MELA_TD),
				'type' 		=> Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'background-color: {{VALUE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->add_responsive_control(
			'carousel_pagination_bullets_opacity_active',
			[
				'label' 		=> __('Opacity', MELA_TD),
				'type' 			=> Controls_Manager::SLIDER,
				'range' 		=> [
					'px' 		=> [
						'min' => 0,
						'max' => 1,
						'step' => 0.05,
					],
				],
				'selectors' 	=> [
					'{{WRAPPER}} .swiper-pagination-bullet-active' => 'opacity: {{SIZE}};',
				],
				'condition'		=> [
					'carousel_pagination' 		=> 'yes',
					'pagination_type' 	=> 'bullets',
				]
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();


		$this->end_controls_section();



		/**
		 * Content Tab: Docs Links
		 */
		$this->start_controls_section(
			'jltma_section_help_docs',
			[
				'label' => esc_html__('Help Docs', MELA_TD),
			]
		);


		$this->add_control(
			'help_doc_1',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/image-carousel/" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_2',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/image-carousel/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);

		$this->add_control(
			'help_doc_3',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=wXPEl93_UBw" target="_blank" rel="noopener">', '</a>'),
				'content_classes' => 'jltma-editor-doc-links',
			]
		);
		$this->end_controls_section();




		


    }

    // Twitter Slider: Loop
    public function jltma_ts_loop_twitter($twitter_consumer_key, $consumer_secret, $access_token, $access_token_secret, $twitter_username)
    {

        $settings          = $this->get_settings();

        $name              = $twitter_username;
        $exclude_replies   = ('yes' === $settings['jltma_ts_exclude_replies']) ? true : false;
        $transName         = 'bdt-tweets-' . $name; // Name of value in database. [added $name for multiple account use]
        $backupName        = $transName . '-backup'; // Name of backup value in database.


        if (false === ($tweets = get_transient($name))) :

            $connection = new \TwitterOAuth($twitter_consumer_key, $consumer_secret, $access_token, $access_token_secret);

            $totalToFetch = ($exclude_replies) ? max(50, $settings['jltma_ts_tweet_num'] * 3) : $settings['jltma_ts_tweet_num'];

            $fetchedTweets = $connection->get(
                'statuses/user_timeline',
                array(
                    'screen_name'     => $name,
                    'count'           => $totalToFetch,
                    'exclude_replies' => $exclude_replies
                )
            );

            // Did the fetch fail?
            if ($connection->http_code != 200) :
                $tweets = get_option($backupName); // False if there has never been data saved.
            else :
                // Fetch succeeded.
                // Now update the array to store just what we need.
                // (Done here instead of PHP doing this for every page load)
                $limitToDisplay = min($settings['jltma_ts_tweet_num'], count($fetchedTweets));

                for ($i = 0; $i < $limitToDisplay; $i++) :
                    $tweet = $fetchedTweets[$i];

                    // Core info.
                    $name = $tweet->user->name;
                    $screen_name = $tweet->user->screen_name;
                    $permalink = 'https://twitter.com/' . $screen_name . '/status/' . $tweet->id_str;
                    $tweet_id = $tweet->id_str;

                    /* Alternative image sizes method: http://dev.twitter.com/doc/get/users/profile_image/:screen_name */
                    //  Check for SSL via protocol https then display relevant image - thanks SO - this should do
                    if (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
                        // $protocol = 'https://';
                        $image = $tweet->user->profile_image_url_https;
                    } else {
                        // $protocol = 'http://';
                        $image = $tweet->user->profile_image_url;
                    }

                    // Process Tweets - Use Twitter entities for correct URL, hash and mentions
                    $text  = $this->process_links($tweet);
                    // lets strip 4-byte emojis
                    $text  = $this->twitter_api_strip_emoji($text);

                    // Need to get time in Unix format.
                    $time  = $tweet->created_at;
                    $time  = date_parse($time);
                    $uTime = mktime($time['hour'], $time['minute'], $time['second'], $time['month'], $time['day'], $time['year']);

                    // Now make the new array.
                    $tweets[] = array(
                        'text'      => $text,
                        'name'      => $name,
                        'permalink' => $permalink,
                        'image'     => $image,
                        'time'      => $uTime,
                        'tweet_id'  => $tweet_id
                    );
                endfor;

                set_transient($transName, $tweets, 60 * $settings['jltma_ts_cache_time']);
                update_option($backupName, $tweets);
            endif;
        endif;



        // Now display the tweets, if we can.
        if ($tweets) {

            $this->add_render_attribute([
                'swiper-item' => [
                    'class' => [
                        'jltma-twitter_slider__item',
                        'jltma-swiper__slide',
                        'swiper-slide',
                    ],
                ],
            ]);


            foreach ((array) $tweets as $t) {  ?>
                <div <?php echo $this->get_render_attribute_string('swiper-item'); ?>>
                    <div class="card text-center">
                            <div class="card-body">
                    <?php if ('yes' === $settings['jltma_ts_show_avatar']) : ?>

                        <?php if ('yes' === $settings['jltma_ts_avatar_link']) : ?>
                            <a href="https://twitter.com/<?php echo esc_attr($name); ?>">
                            <?php endif; ?>

                            <div class="jltma-twitter-thumb">
                                <img src="<?php echo esc_url($t['image']); ?>" alt="<?php echo esc_html($t['name']); ?>" />
                            </div>

                            <?php if ('yes' === $settings['jltma_ts_avatar_link']) : ?>
                            </a>
                        <?php endif; ?>

                    <?php endif; ?>

                    <div class="jltma-twitter-text jltma-clearfix">
                        <?php echo wp_kses_post($t['text']); ?>
                    </div>

                    <div class="jltma-twitter-meta-wrapper">

                        <?php if ('yes' === $settings['jltma_ts_show_time']) { ?>
                            <a href="<?php echo $t['permalink']; ?>" target="_blank" class="jltma-twitter-time-link">
                                <?php
                                // Original - long time ref: hours...
                                if ('yes' === $settings['jltma_ts_long_time_format']) {
                                    // Short Twitter style time ref: h...
                                    $timeDisplay = human_time_diff($t['time'], current_time('timestamp'));
                                } else {
                                    $timeDisplay = $this->twitter_time_diff($t['time'], current_time('timestamp'));
                                }
                                $displayAgo = _x('ago', 'leading space is required', MELA_TD);
                                // Use to make il8n compliant
                                printf(__('%1$s %2$s', MELA_TD), $timeDisplay, $displayAgo);
                                ?>
                            </a>
                        <?php } ?>


                        <?php if ('yes' === $settings['jltma_ts_show_meta_button']) { ?>
                            <div class="jltma-twitter-meta-button">
                                <a href="https://twitter.com/intent/tweet?in_reply_to=<?php echo $t['tweet_id']; ?>" data-lang="en" class="jltma-tmb-reply" title="<?php _e('Reply', MELA_TD); ?>" target="_blank">
                                    <i class="fas fa-reply"></i>
                                </a>
                                <a href="https://twitter.com/intent/retweet?tweet_id=<?php echo $t['tweet_id']; ?>" data-lang="en" class="jltma-tmb-retweet" title="<?php _e('Retweet', MELA_TD); ?>" target="_blank">
                                    <i class="fas fa-sync"></i>
                                </a>
                                <a href="https://twitter.com/intent/favorite?tweet_id=<?php echo $t['tweet_id']; ?>" data-lang="en" class="jltma-tmb-favorite" title="<?php _e('Favourite', MELA_TD); ?>" target="_blank">
                                    <i class="far fa-star"></i>
                                </a>
                            </div>
                        <?php } ?>

                    </div>
                    </div>
                        </div>
                </div>

            <?php } // endforeach
        }
    }


    // Render
    protected function render()
    {

        if (!class_exists('TwitterOAuth')) {
            include_once MELA_PLUGIN_PATH . '/inc/classes/twitteroauth/twitteroauth.php';
        }

        $settings               = $this->get_settings();
        $jltma_api_settings     = get_option('jltma_api_save_settings');

        $twitter_username       = (!empty($jltma_api_settings['twitter_username'])) ? $jltma_api_settings['twitter_username'] : '';

        $twitter_consumer_key   = (!empty($jltma_api_settings['twitter_consumer_key'])) ? $jltma_api_settings['twitter_consumer_key'] : '';
        $consumer_secret        = (!empty($jltma_api_settings['twitter_consumer_secret'])) ? $jltma_api_settings['twitter_consumer_secret'] : '';
        $access_token           = (!empty($jltma_api_settings['twitter_access_token'])) ? $jltma_api_settings['twitter_access_token'] : '';
        $access_token_secret    = (!empty($jltma_api_settings['twitter_access_token_secret'])) ? $jltma_api_settings['twitter_access_token_secret'] : '';


        $this->jltma_ts_loop_header($settings);


        if ($twitter_consumer_key and $consumer_secret and $access_token and $access_token_secret) {
            $this->jltma_ts_loop_twitter($twitter_consumer_key, $consumer_secret, $access_token, $access_token_secret, $twitter_username);
        } else { ?>

            <div class="ma-el-alert elementor-alert elementor-alert-warning" role="alert">
                <a class="elementor-alert-dismiss"></a>
                <?php $jltma_admin_api_url = esc_url(admin_url('admin.php?page=master-addons-settings#ma_api_keys')); ?>
                <p><?php printf(__('Please set Twitter API settings from here <a href="%s" target="_blank">Master Addons Settings</a> to show Tweet data correctly.', MELA_TD), $jltma_admin_api_url); ?></p>
            </div>
        <?php
        }

        $this->jltma_ts_loop_footer($settings);
    }


    // Twitter Slider: Header
    protected function jltma_ts_loop_header($settings)
    {

        $settings = $this->get_settings();

		$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

		$this->add_render_attribute([
			'jltma_twitter_slider' => [
				'class' => [
					'jltma-twitter-slider',
					'jltma-swiper',
					'jltma-swiper__container',
					'swiper-container',
                    // 'jltma-arrows-dots-align-' . $settings['jltma_ts_both_position'],
                    // 'jltma-arrows-align-' . $settings['jltma_ts_arrows_position'],
					'elementor-twitter-slider-element-' . $unique_id
				],
                // 'id'   => 'jltma-twitter-slider-' . $this->get_id(),
				'data-image-carousel-template-widget-id' => $unique_id
			],
			'swiper-wrapper' => [
				'class' => [
					'jltma-image-carousel',
					'jltma-swiper__wrapper',
					'swiper-wrapper',
				],
			]
		]);
        ?>

        <div <?php echo $this->get_render_attribute_string('jltma_twitter_slider'); ?>>
            <div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>
            <?php
    }

        // Twitter Slider: Footer
        protected function jltma_ts_loop_footer($settings)
            {
                $settings = $this->get_settings();
                ?>

                </div> <!-- swiper-wrapper -->

				<?php
				$this->render_swiper_navigation();
				$this->render_swiper_pagination();
				?>
        </div>
    <?php
            }



	protected function render_swiper_navigation()
	{
		$settings = $this->get_settings_for_display();
		$this->add_render_attribute([
			'navigation' => [
				'class' => [
					'jltma-arrows',
					'jltma-swiper__navigation',
					'jltma-swiper__navigation--' . $settings['arrows_placement'],
					'jltma-swiper__navigation--' . $settings['arrows_position'],
					'jltma-swiper__navigation--' . $settings['arrows_position_vertical']
				],
			],
		]);
	?>
		<div <?php echo $this->get_render_attribute_string('navigation'); ?>>
			<?php
			$this->render_swiper_arrows();
			?>
		</div>
	<?php
	}



	public function render_swiper_pagination()
	{
		$settings = $this->get_settings_for_display();
		if ('yes' !== $settings['carousel_pagination'])
			return;

		$this->add_render_attribute('pagination', 'class', [
			'jltma-swiper__pagination',
			'jltma-swiper__pagination--' . $settings['carousel_direction'],
			'jltma-swiper__pagination--' . $settings['pagination_position'],
			'jltma-swiper__pagination-' . $this->get_id(),
			'swiper-pagination',
		]);

	?>
		<div <?php echo $this->get_render_attribute_string('pagination'); ?>>
		</div>
	<?php
	}

	protected function render_swiper_arrows()
	{
		$settings = $this->get_settings_for_display();
		if ('yes' !== $settings['carousel_arrows'])
			return;

		$prev = is_rtl() ? 'right' : 'left';
		$next = is_rtl() ? 'left' : 'right';

		$this->add_render_attribute([
			'button-prev' => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--prev',
					'jltma-arrow',
					'jltma-arrow--prev',
					'jltma-swiper__button--prev-' . $this->get_id(),
				],
			],
			'button-prev-icon' => [
				'class' => 'eicon-chevron-' . $prev,
			],
			'button-next' => [
				'class' => [
					'jltma-swiper__button',
					'jltma-swiper__button--next',
					'jltma-arrow',
					'jltma-arrow--next',
					'jltma-swiper__button--next-' . $this->get_id(),
				],
			],
			'button-next-icon' => [
				'class' => 'eicon-chevron-' . $next,
			],
		]);

	?><div <?php echo $this->get_render_attribute_string('button-prev'); ?>>
			<i <?php echo $this->get_render_attribute_string('button-prev-icon'); ?>></i>
		</div>
		<div <?php echo $this->get_render_attribute_string('button-next'); ?>>
			<i <?php echo $this->get_render_attribute_string('button-next-icon'); ?>></i>
		</div><?php
}



            private function twitter_api_strip_emoji($text)
            {
                // four byte utf8: 11110www 10xxxxxx 10yyyyyy 10zzzzzz
                return preg_replace('/[\xF0-\xF7][\x80-\xBF]{3}/', '', $text);
            }


            private function process_links($tweet)
            {

                // Is the Tweet a ReTweet - then grab the full text of the original Tweet
                if (isset($tweet->retweeted_status)) {
                    // Split it so indices count correctly for @mentions etc.
                    $rt_section = current(explode(":", $tweet->text));
                    $text = $rt_section . ": ";
                    // Get Text
                    $text .= $tweet->retweeted_status->text;
                } else {
                    // Not a retweet - get Tweet
                    $text = $tweet->text;
                }

                // NEW Link Creation from clickable items in the text
                $text = preg_replace('/((http)+(s)?:\/\/[^<>\s]+)/i', '<a href="$0" target="_blank" rel="nofollow">$0</a>', $text);
                // Clickable Twitter names
                $text = preg_replace('/[@]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/$1" target="_blank" rel="nofollow">@$1</a>', $text);
                // Clickable Twitter hash tags
                $text = preg_replace('/[#]+([A-Za-z0-9-_]+)/', '<a href="http://twitter.com/search?q=%23$1" target="_blank" rel="nofollow">$0</a>', $text);
                // END TWEET CONTENT REGEX
                return $text;
            }

            private function twitter_time_diff($from, $to = '')
            {
                $diff = human_time_diff($from, $to);
                $replace = array(
                    ' hour'    => 'h',
                    ' hours'   => 'h',
                    ' day'     => 'd',
                    ' days'    => 'd',
                    ' minute'  => 'm',
                    ' minutes' => 'm',
                    ' second'  => 's',
                    ' seconds' => 's',
                );
                return strtr($diff, $replace);
            }
        }
