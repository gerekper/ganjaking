<?php

namespace MasterAddons\Addons;

if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

use \Elementor\Widget_Base;
use \Elementor\Repeater;
use \Elementor\Utils;
use \Elementor\Controls_Manager;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Group_Control_Background;
use \Elementor\Group_Control_Css_Filter;
use \Elementor\Group_Control_Typography;
use \Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use \Elementor\Core\Kits\Documents\Tabs\Global_Typography;

// Master Addons Classes
use MasterAddons\Inc\Controls\MA_Group_Control_Transition;
use MasterAddons\Inc\Helper\Master_Addons_Helper;


class Logo_Slider extends Widget_Base
{

    public function get_name()
    {
        return 'jltma-logo-slider';
    }

    public function get_title()
    {
        return esc_html__('Logo Slider', MELA_TD);
    }

    public function get_icon()
    {
        return 'ma-el-icon eicon-slider-push';
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
        return 'https://master-addons.com/demos/logo-slider/';
    }

    protected function _register_controls()
    {

        /*
        * Logo Images
        */
        $this->start_controls_section(
            'jltma_logo_slider_section_logos',
            [
                'label' => esc_html__('Logo Items', MELA_TD)
            ]
        );


        $repeater = new Repeater();

        $repeater->add_control(
            'jltma_logo_slider_image_normal',
            [
                'label' => esc_html__('Client Logo', MELA_TD),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'normal_img_thumb',
                'default'   => 'large',
                'separator' => 'before',
                'exclude' => [
                    'custom'
                ]
            ]
        );


        $repeater->add_control(
            'jltma_logo_slider_brand_name',
            [
                'label' => __('Brand Name', MELA_TD),
                'type' => Controls_Manager::TEXT,
                'default' => __('Brand Name', MELA_TD),
            ]
        );

        $repeater->add_control(
            'jltma_logo_slider_brand_description',
            [
                'label' => __('Description', MELA_TD),
                'type' => Controls_Manager::TEXTAREA,
                'default' => __('Brand Short Description Type Here.', MELA_TD),
            ]
        );

        $repeater->add_control(
            'jltma_logo_slider_website_link',
            [
                'label' => esc_html__('Link', MELA_TD),
                'type' => Controls_Manager::URL,
                'placeholder' => esc_html__('https://your-link.com', MELA_TD),
                'show_external' => true
            ]
        );


        $repeater->add_control(
            'jltma_logo_slider_enable_hover_logo',
            [
                'label' => esc_html__('Image Hover on Logo?', MELA_TD),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', MELA_TD),
                'label_off' => esc_html__('No', MELA_TD),
                'return_value' => 'yes',
                'default' => '',
            ]
        );


        $repeater->add_control(
            'jltma_logo_slider_image_hover',
            [
                'label' => esc_html__('Hover Logo Image', MELA_TD),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
                'condition' => [
                    'jltma_logo_slider_enable_hover_logo' => 'yes'
                ]
            ]
        );

        $repeater->add_group_control(
            Group_Control_Image_Size::get_type(),
            [
                'name'      => 'hover_img_thumb',
                'default'   => 'large',
                'separator' => 'before',
                'exclude' => [
                    'custom'
                ],
                'condition' => [
                    'jltma_logo_slider_enable_hover_logo' => 'yes'
                ]
            ]
        );


        $repeater->add_control(
            'jltma_logo_slider_item_logo_tooltip',
            [
                'label'         => esc_html__('Tooltip', MELA_TD),
                'type'          => Controls_Manager::SWITCHER,
                'return_value'  => 'yes',
            ]
        );

        $repeater->add_control(
            'jltma_logo_slider_item_logo_tooltip_placement',
            [
                'label'   => esc_html__('Placement', MELA_TD),
                'type'    => Controls_Manager::SELECT,
                'default' => 'tooltip-right',
                'options' => [
                    'tooltip-left'      => esc_html__('Left', MELA_TD),
                    'tooltip-right'     => esc_html__('Right', MELA_TD),
                    'tooltip-top'       => esc_html__('Top', MELA_TD),
                    'tooltip-bottom'    => esc_html__('Bottom', MELA_TD),
                ],
                'condition'   => [
                    'jltma_logo_slider_item_logo_tooltip' => 'yes',
                ],
            ]
        );

        $this->add_control(
            'jltma_logo_slider_items',
            [
                'label' => esc_html__('', MELA_TD),
                'type' => Controls_Manager::REPEATER,
                'fields' => $repeater->get_controls(),
                'default' => [
                    [
                        'jltma_logo_slider_brand_name' => esc_html__('Brand Name 1', MELA_TD),
                    ],
                    [
                        'jltma_logo_slider_brand_name' => esc_html__('Brand Name 2', MELA_TD),
                    ],
                    [
                        'jltma_logo_slider_brand_name' => esc_html__('Brand Name 3', MELA_TD),
                    ],
                    [
                        'jltma_logo_slider_brand_name' => esc_html__('Brand Name 4', MELA_TD),
                    ],
                    [
                        'jltma_logo_slider_brand_name' => esc_html__('Brand Name 5', MELA_TD),
                    ],
                ],
                'title_field' => '{{{ jltma_logo_slider_brand_name }}}',
            ]
        );


        $this->add_control(
            'title_html_tag',
            [
                'label'   => esc_html__('Title HTML Tag', MELA_TD),
                'type'    => Controls_Manager::SELECT,
                'options' => Master_Addons_Helper::ma_el_title_tags(),
                'default' => 'h3',
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
					'' 		=> __('Default', MELA_TD),
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
				'default'        		=> '4',
				'tablet_default' 		=> '3',
				'mobile_default' 		=> '2',
				'frontend_available' 	=> true,
			]
		);

		$this->add_responsive_control(
			'slides_per_column',
			[
				'type' 					=> Controls_Manager::SELECT,
				'label' 				=> __('Slides Per Column', MELA_TD),
				'options' 				=> ['' => __('Default', MELA_TD)] + $slides_per_view,
				'frontend_available' 	=> true,
				'default'        		=> '4',
				'tablet_default' 		=> '3',
				'mobile_default' 		=> '2',
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
        * Logo Style
        *
        */
        $this->start_controls_section(
            'jltma_logo_slider_sesction_style_carousel',
            [
                'label' => esc_html__('Logo Carousel', MELA_TD),
                'tab'   => Controls_Manager::TAB_STYLE
            ]
        );

        $this->start_controls_tabs('jltma_logo_slider_carousel_tabs');

        # Normal tab
        $this->start_controls_tab('normal', ['label' => esc_html__('Normal', MELA_TD)]);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'jltma_logo_slider_carousel_normal_background',
                'types'     => ['classic', 'gradient'],
                'separator' => 'before',
                'selector'  => '{{WRAPPER}} .exad-logo .exad-logo-item'
            ]
        );

        $this->add_control(
            'jltma_logo_slider_carousel_opacity',
            [
                'label' => __('Opacity', MELA_TD),
                'type'  => Controls_Manager::NUMBER,
                'range' => [
                    'min'   => 0,
                    'max'   => 1
                ],
                'selectors' => [
                    '{{WRAPPER}} .exad-logo .exad-logo-item img' => 'opacity: {{VALUE}};'
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'jltma_logo_slider_carousel_item_border',
                'label'       => esc_html__('Border', MELA_TD),
                'selector'    => '{{WRAPPER}} .jltma-logo-slider-figure',
            ]
        );


        $this->add_control(
            'jltma_logo_slider_carousel_item_border_radius',
            [
                'label'      => esc_html__('Border Radius', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .jltma-logo-slider-figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_logo_slider_carousel_padding',
            [
                'label' => __('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-logo-slider-figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'jltma_logo_slider_carousel_box_shadow',
                'selector' => '{{WRAPPER}} .exad-logo .exad-logo-item'
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'jltma_logo_slider_carousel_image_css_filters',
                'selector' => '{{WRAPPER}} .jltma-logo-slider-figure img',
            ]
        );
        $this->end_controls_tab();

        # Hover tab
        $this->start_controls_tab('jltma_logo_slider_carousel_hover_tab', ['label' => esc_html__('Hover', MELA_TD)]);

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'      => 'jltma_logo_slider_carousel_hover_background',
                'types'     => ['classic', 'gradient'],
                'separator' => 'before',
                'selector'  => '{{WRAPPER}} .exad-logo .exad-logo-item'
            ]
        );

        $this->add_control(
            'jltma_logo_slider_carousel_hover_opacity',
            [
                'label' => __('Opacity', MELA_TD),
                'type'  => Controls_Manager::NUMBER,
                'range' => [
                    'min'   => 0,
                    'max'   => 1
                ],
                'selectors' => [
                    '{{WRAPPER}} .exad-logo .exad-logo-item img' => 'opacity: {{VALUE}};'
                ]
            ]
        );


        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'jltma_logo_slider_carousel_hover_item_border',
                'label'       => esc_html__('Border', MELA_TD),
                'selector'    => '{{WRAPPER}} .jltma-logo-slider-figure',
            ]
        );


        $this->add_control(
            'jltma_logo_slider_carousel_hover_item_border_radius',
            [
                'label'      => esc_html__('Border Radius', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .jltma-logo-slider-figure' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_logo_slider_carousel_hover_padding',
            [
                'label' => __('Padding', MELA_TD),
                'type' => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors' => [
                    '{{WRAPPER}} .jltma-logo-slider-figure' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );


        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'jltma_logo_slider_carousel_hover_box_shadow',
                'selector' => '{{WRAPPER}} .exad-logo .exad-logo-item'
            ]
        );

        $this->add_group_control(
            Group_Control_Css_Filter::get_type(),
            [
                'name' => 'jltma_logo_slider_carousel_hover_image_css_filters',
                'selector' => '{{WRAPPER}} .jltma-logo-slider-figure img',
            ]
        );


        $this->add_control(
            'jltma_logo_slider_carousel_hover_image_hover_transition',
            [
                'label' => __('Transition Duration', MELA_TD),
                'type' => Controls_Manager::SLIDER,
                'range' => [
                    'px' => [
                        'max' => 3,
                        'step' => 0.1,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .bdt-logo-carousel-figure:hover img' => 'transition-duration: {{SIZE}}s;',
                ],
            ]
        );

        $this->add_control(
            'jltma_logo_slider_carousel_hover_animation',
            [
                'label' => __('Hover Animation', MELA_TD),
                'type' => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab();

        $this->end_controls_tabs();

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



        /*
        Tab Style: Tooltip
        */
        $this->start_controls_section(
            'jltma_logo_slider_section_style_tooltip',
            [
                'label' => esc_html__('Tooltip', MELA_TD),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'jltma_logo_slider_tooltip_width',
            [
                'label'      => esc_html__('Width', MELA_TD),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => [
                    'px', 'em',
                ],
                'range' => [
                    'px' => [
                        'min' => 50,
                        'max' => 500,
                    ],
                ],
                'selectors' => [
                    '{{WRAPPER}} .tippy-tooltip' => 'width: {{SIZE}}{{UNIT}};',
                ],
                'render_type' => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'jltma_logo_slider_tooltip_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_SECONDARY,
				],
                'selector' => '{{WRAPPER}} .tippy-tooltip .tippy-content',
            ]
        );

        $this->add_control(
            'jltma_logo_slider_tooltip_color',
            [
                'label'     => esc_html__('Text Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .tippy-tooltip' => 'color: {{VALUE}}',
                ],
            ]
        );

        $this->add_control(
            'jltma_logo_slider_tooltip_text_align',
            [
                'label'   => esc_html__('Text Alignment', MELA_TD),
                'type'    => Controls_Manager::CHOOSE,
                'default' => 'center',
                'options' => [
                    'left'    => [
                        'title' => esc_html__('Left', MELA_TD),
                        'icon'  => 'fas fa-align-left',
                    ],
                    'center' => [
                        'title' => esc_html__('Center', MELA_TD),
                        'icon'  => 'fas fa-align-center',
                    ],
                    'right' => [
                        'title' => esc_html__('Right', MELA_TD),
                        'icon'  => 'fas fa-align-right',
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .tippy-tooltip .tippy-content' => 'text-align: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'jltma_logo_slider_tooltip_background',
                'selector' => '{{WRAPPER}} .tippy-tooltip, {{WRAPPER}} .tippy-tooltip .tippy-backdrop',
            ]
        );

        $this->add_control(
            'jltma_logo_slider_tooltip_arrow_color',
            [
                'label'     => esc_html__('Arrow Color', MELA_TD),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .jltma-ls-prev svg'  => 'fill: {{VALUE}}',
                    '{{WRAPPER}} .jltma-ls-next svg'  => 'fill: {{VALUE}}'
                ],
            ]
        );

        $this->add_responsive_control(
            'jltma_logo_slider_tooltip_padding',
            [
                'label'      => __('Padding', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tippy-tooltip' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'render_type'  => 'template',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'        => 'jltma_logo_slider_tooltip_border',
                'label'       => esc_html__('Border', MELA_TD),
                'placeholder' => '1px',
                'default'     => '1px',
                'selector'    => '{{WRAPPER}} .tippy-tooltip',
            ]
        );

        $this->add_responsive_control(
            'jltma_logo_slider_tooltip_border_radius',
            [
                'label'      => __('Border Radius', MELA_TD),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .tippy-tooltip' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'jltma_logo_slider_tooltip_box_shadow',
                'selector' => '{{WRAPPER}} .tippy-tooltip',
            ]
        );

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
                'raw'             => sprintf(esc_html__('%1$s Live Demo %2$s', MELA_TD), '<a href="https://master-addons.com/demos/team-carousel/" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_2',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Documentation %2$s', MELA_TD), '<a href="https://master-addons.com/docs/addons/team-members-carousel/?utm_source=widget&utm_medium=panel&utm_campaign=dashboard" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );

        $this->add_control(
            'help_doc_3',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => sprintf(esc_html__('%1$s Watch Video Tutorial %2$s', MELA_TD), '<a href="https://www.youtube.com/watch?v=ubP_h86bP-c" target="_blank" rel="noopener">', '</a>'),
                'content_classes' => 'jltma-editor-doc-links',
            ]
        );
        $this->end_controls_section();




       
    }





    /*
    * Logo Slider: Render Header
    */
    public function jltma_render_logo_slider_header($settings)
    {
        $settings = $this->get_settings_for_display();

		$unique_id 	= implode('-', [$this->get_id(), get_the_ID()]);

		$this->add_render_attribute([
			'jltma-logo-carousel-wrapper' => [
				'class' => [
					'jltma-image-carousel-wrapper',
					'jltma-swiper',
					'jltma-swiper__container',
					'swiper-container',
					'elementor-jltma-element-' . $unique_id
				],
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

        <div <?php echo $this->get_render_attribute_string('jltma-logo-carousel-wrapper'); ?>>
			<div <?php echo $this->get_render_attribute_string('swiper-wrapper'); ?>>

                <?php
            }



            /*
    * Render Logo Loop
    */

            public function jltma_render_logo_slider_loop_item($settings)
            {
                $settings = $this->get_settings_for_display();

                $slider_items = $settings['jltma_logo_slider_items'];

                if (empty($slider_items)) {
                    return;
                }

                if (count($slider_items) > 1) {
                    $demo_images = [];

                    if (empty($slider_items[0]['jltma_logo_slider_image_normal']) && empty($slider_items[1]['jltma_logo_slider_image_normal']) && empty($slider_items[0]['jltma_logo_slider_image_normal'])) {
                        $demo_images[] = Master_Addons_Helper::jltma_placeholder_images();
                    }

                    foreach ($slider_items as $index => $item) {

                        $images = $item['jltma_logo_slider_image_normal'];
                        if (empty($images)) {
                            $images = $demo_images;
                        }

                        $repeater_key = 'carousel_item' . $index;
                        $tag = 'div';
                        $image_alt = esc_html($item['jltma_logo_slider_brand_name']) . ' : ' . esc_html($item['jltma_logo_slider_brand_description']);
                        $title_html_tag = ($settings['title_html_tag']) ? $settings['title_html_tag'] : 'h3';
                        $this->add_render_attribute($repeater_key, 'class', 'jltma-logo-slider-item');

                        // Website Links
                        if ($item['jltma_logo_slider_website_link']['url']) {
                            $tag = 'a';
                            $this->add_render_attribute($repeater_key, 'class', 'jltma-logo-slider-link');
                            $this->add_render_attribute($repeater_key, 'target', '_blank');
                            $this->add_render_attribute($repeater_key, 'rel', 'noopener');
                            $this->add_render_attribute($repeater_key, 'href', esc_url($item['jltma_logo_slider_website_link']['url']));
                            $this->add_render_attribute($repeater_key, 'title', $item['jltma_logo_slider_brand_name']);
                        }

                        // Slider Items
                        $this->add_render_attribute([
                            $repeater_key => [
                                'class' => [
                                    'jltma-slider__item',
                                    'jltma-swiper__slide',
                                    'swiper-slide',
                                ],
                            ]
                        ]);

                        // Tooltips
                        if ($item['jltma_logo_slider_brand_name'] and $item['jltma_logo_slider_brand_description'] and $item['jltma_logo_slider_item_logo_tooltip'] == "yes") {

                            if ($item['jltma_logo_slider_item_logo_tooltip'] == "yes") {
                                echo '<div class="ma-el-tooltip"><div class="ma-el-tooltip-item ' . esc_attr($item['jltma_logo_slider_item_logo_tooltip_placement']) . '">';
                                $this->add_render_attribute($repeater_key, 'class', 'ma-el-tooltip-content');
                            }
                        } ?>

                        <<?php echo $tag; ?> <?php $this->print_render_attribute_string($repeater_key); ?>>
                            <figure class="jltma-logo-slider-figure">

                                <?php
                                    if (!empty($images)) {
                                        if (isset($item['jltma_logo_slider_image_normal']['id']) && $item['jltma_logo_slider_image_normal']['id']) {
                                            echo wp_get_attachment_image(
                                                $item['jltma_logo_slider_image_normal']['id'],
                                                $item['normal_img_thumb_size'],
                                                false,
                                                [
                                                    'class' => 'jltma-logo-slider-img elementor-animation-' . esc_attr($settings['jltma_logo_slider_carousel_hover_animation']),
                                                    'alt' => esc_attr($image_alt),
                                                ]
                                            );
                                        } else {
                                            echo "<img src=" . $images['url'] . ">";
                                        }
                                    }
                                ?>

                            </figure>

                            <figcaption>
                                <?php
                                // Hover Logo Image
                                if ((isset($item['jltma_logo_slider_enable_hover_logo']) && $item['jltma_logo_slider_enable_hover_logo'] == "yes") && $item['jltma_logo_slider_image_hover']['url']) {

                                    $slider_hover_image = wp_get_attachment_image_url($item['jltma_logo_slider_image_hover']['id'], $item['hover_img_thumb_size']);
                                    $image_hover_alt = esc_html($item['jltma_logo_slider_brand_name']) . ' : ' . esc_html($item['jltma_logo_slider_brand_description']);

                                    if ($slider_hover_image) {
                                        echo wp_get_attachment_image(
                                            $item['jltma_logo_slider_image_hover']['id'],
                                            $item['hover_img_thumb_size'],
                                            false,
                                            [
                                                'class' => 'jltma-logo-slider-hover-img elementor-animation-' . esc_attr($settings['jltma_logo_slider_carousel_hover_animation']),
                                                'alt' => esc_attr($image_hover_alt),
                                            ]
                                        );
                                    }
                                }
                                ?>
                            </figcaption>

                        </<?php echo $tag; ?>>

                    <?php

                        if ($item['jltma_logo_slider_item_logo_tooltip'] == "yes") {
                            echo '<div class="ma-el-tooltip-text">' . esc_html($item['jltma_logo_slider_brand_description']) . '</div></div></div>';
                        }
                    }  // end of foreach
                } // end of slider items

            }



            /*
    * Render Footer
    */
	public function jltma_render_logo_slider_footer($settings)
	{
		$settings = $this->get_settings_for_display(); ?>

		</div>
			<?php
			$this->render_swiper_navigation();
			$this->render_swiper_pagination();
			?>
        </div>
        <!--/.jltma-logo-slider-->

    <?php }



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


	public function render()
	{

		$settings = $this->get_settings_for_display();

		$this->jltma_render_logo_slider_header($settings);
		$this->jltma_render_logo_slider_loop_item($settings);
		$this->jltma_render_logo_slider_footer($settings);
	}


	/**
		* Render logo box widget output in the editor.
		*
		* Written as a Backbone JavaScript template and used to generate the live preview.
		*
		* @since 1.0.0
		* @access protected
		*/
	protected function _content_template()
	{
	}
}
