<?php
namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use Elementor\Core\Files\Assets\Svg\Svg_Handler;
use \Elementor\Group_Control_Background;
use Elementor\Icons_Manager;
use Elementor\Repeater;
use \Elementor\Core\Schemes\Typography;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Group_Control_Image_Size;
use \Elementor\Widget_Base;
use Essential_Addons_Elementor\Classes\Helper as HelperClass;

if ( ! defined( 'ABSPATH' ) ) exit; // If this file is called directly, abort.

class Flip_Carousel extends Widget_Base {

	public function get_name() {
		return 'eael-flip-carousel';
	}

	public function get_title() {
		return esc_html__( 'Flip Carousel', 'essential-addons-elementor' );
	}

	public function get_icon() {
		return 'eaicon-flip-carousel';
	}

   	public function get_categories() {
		return [ 'essential-addons-elementor' ];
	}

	public function get_keywords()
	{
        return [
			'media slider',
			'ea slider',
			'ea flip slider',
			'ea flip carousel',
			'flip carousel',
			'flip effect',
			'flip slider',
			'image slider',
			'ea',
			'essential addons'
        ];
    }

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/flip-carousel/';
	}

	protected function register_controls() {

		/**
  		 * Flip Carousel Settings
  		 */
  		$this->start_controls_section(
  			'eael_section_flip_carousel_settings',
  			[
  				'label' => esc_html__( 'Filp Carousel Settings', 'essential-addons-elementor' )
  			]
  		);

  		$this->add_control(
		  'eael_flip_carousel_type',
		  	[
		   	'label'       	=> esc_html__( 'Carousel Type', 'essential-addons-elementor' ),
		     	'type' 			=> Controls_Manager::SELECT,
		     	'default' 		=> 'coverflow',
		     	'label_block' 	=> false,
		     	'options' 		=> [
		     		'coverflow' => esc_html__( 'Cover-Flow', 'essential-addons-elementor' ),
		     		'carousel'  => esc_html__( 'Carousel', 'essential-addons-elementor' ),
		     		'flat'  	=> esc_html__( 'Flat', 'essential-addons-elementor' ),
		     		'wheel'  	=> esc_html__( 'Wheel', 'essential-addons-elementor' ),
		     	],
		  	]
		);

		$this->add_control(
			'eael_flip_carousel_fade_in',
			[
				'label' => esc_html__( 'Fade In (ms)', 'essential-addons-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => 400,
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
		  'eael_flip_carousel_start_from',
		  	[
				'label' => __( 'Item Starts From Center?', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		/**
		 * Condition: 'eael_flip_carousel_start_from' => 'true'
		 */
		$this->add_control(
			'eael_flip_carousel_starting_number',
			[
				'label' => esc_html__( 'Enter Starts Number', 'essential-addons-elementor' ),
				'type' => Controls_Manager::TEXT,
                'dynamic' => [ 'active' => true ],
				'label_block' => false,
				'default' => 1,
				'condition' => [
					'eael_flip_carousel_start_from!' => 'true'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
		  'eael_flip_carousel_loop',
		  	[
				'label' => __( 'Loop', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
		  'eael_flip_carousel_autoplay',
		  	[
				'label' => __( 'Autoplay', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		/**
		 * Condition: 'eael_flip_carousel_autoplay' => 'true'
		 */
		$this->add_control(
			'eael_flip_carousel_autoplay_time',
			[
				'label' => esc_html__( 'Autoplay Timeout (ms)', 'essential-addons-elementor' ),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => 2000,
				'condition' => [
					'eael_flip_carousel_autoplay' => 'true'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
		  'eael_flip_carousel_pause_on_hover',
		  	[
				'label' => __( 'Pause On Hover', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
		  'eael_flip_carousel_click',
		  	[
				'label' => __( 'On Click Play?', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
		  'eael_flip_carousel_scrollwheel',
		  	[
				'label' => __( 'On Scroll Wheel Play?', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
		  'eael_flip_carousel_touch',
		 	[
				'label' => __( 'On Touch Play?', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
		  'eael_flip_carousel_button',
		  	[
				'label' => __( 'Carousel Navigator', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'true',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		$this->add_control(
			'eael_flip_carousel_spacing',
			[
				'label' => esc_html__( 'Slide Spacing', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -0.6
				],
				'range' => [
					'px' => [
						'min' => -1,
						'max' => 1,
						'step' => 0.1
					],
				],
			]
		);

		$this->end_controls_section();

		/**
		 * Filp Carousel Slides
		 */
		$this->start_controls_section(
			'eael_flip_carousel_slides_label',
			[
				'label' => esc_html__( 'Flip Carousel Slides', 'essential-addons-elementor' ),
			]
		);

		$this->add_control(
			'eael_flip_carousel_content_view',
			[
				'label' => esc_html__( 'Content Appearance', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none' => esc_html__( 'No Content', 'essential-addons-elementor' ),
					'hover'  => esc_html__( 'On Hover', 'essential-addons-elementor' ),
					'always' => esc_html__( 'Always Show', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_content_overlay',
			[
				'label' => __( 'Enable Overlay', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Enable', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'Disable', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition' => [
					'eael_flip_carousel_content_view!' => 'none'
				]
			]
		);

		$this->add_control(
			'eael_flip_carousel_content_active_only',
			[
				'label' => __( 'Content on active only', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'label_on' => esc_html__( 'Enable', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'Disable', 'essential-addons-elementor' ),
				'return_value' => 'yes',
				'condition' => [
					'eael_flip_carousel_content_view' => 'always'
				]
			]
		);

        $repeater = new Repeater();

        $repeater->add_control(
            'eael_flip_carousel_slide',
            [
                'label' => esc_html__( 'Slide', 'essential-addons-elementor' ),
                'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png',
                ],
				'ai' => [
					'active' => false,
				],
            ]
        );

        $repeater->add_control(
            'eael_flip_carousel_slide_text',
            [
                'label' => esc_html__( 'Slide Text', 'essential-addons-elementor' ),
                'type' => Controls_Manager::TEXT,
                'dynamic' => [
                    'active' => true,
                ],
                'label_block' => true,
                'default' => esc_html__( '', 'essential-addons-elementor' ),
				'ai' => [
					'active' => false,
				],
            ]
        );

		$repeater->add_control(
			'eael_flip_carousel_content',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::WYSIWYG,
				'default' => esc_html__( 'Lorem ipsum dolor sit amet, consectetur adipisicing elit. Optio, neque qui velit. Magni dolorum quidem ipsam eligendi, totam, facilis laudantium cum accusamus ullam voluptatibus commodi numquam, error, est. Ea, consequatur.', 'essential-addons-elementor' ),
				'placeholder' => esc_html__( 'Type your description here', 'essential-addons-elementor' ),
			]
		);

        $repeater->add_control(
            'eael_flip_carousel_enable_slide_link',
            [
                'label' => __( 'Enable Slide Link', 'essential-addons-elementor' ),
                'type' => Controls_Manager::SWITCHER,
                'default' => 'false',
                'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
                'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
                'return_value' => 'true',
            ]
        );

        $repeater->add_control(
            'eael_flip_carousel_slide_link',
            [
                'label' => esc_html__( 'Slide Link', 'essential-addons-elementor' ),
                'type' => Controls_Manager::URL,
                'dynamic'   => ['active' => true],
                'label_block' => true,
                'default' => [
                    'url' => '#',
                    'is_external' => '',
                ],
                'show_external' => true,
                'condition' => [
                    'eael_flip_carousel_enable_slide_link' => 'true'
                ]
            ]
        );

		$this->add_control(
			'eael_flip_carousel_slides',
			[
				'type' => Controls_Manager::REPEATER,
				'seperator' => 'before',
				'default' => [
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
					[ 'eael_flip_carousel_slide' => EAEL_PRO_PLUGIN_URL . 'assets/front-end/img/slide.png' ],
				],
				'fields' => $repeater->get_controls(),
				'title_field' => '{{eael_flip_carousel_slide_text}}',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Flip Carousel Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_flip_carousel_style_settings',
			[
				'label' => esc_html__( 'Flip Carousel Style', 'essential-addons-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
			'eael_flip_carousel_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_flip_carousel_container_padding',
			[
				'label' => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .eael-flip-carousel' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
			]
		);

		$this->add_responsive_control(
			'eael_flip_carousel_container_margin',
			[
				'label' => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .eael-flip-carousel' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_flip_carousel_border',
				'label' => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector' => '{{WRAPPER}} .eael-flip-carousel',
			]
		);

		$this->add_control(
			'eael_flip_carousel_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_flip_carousel_shadow',
				'selector' => '{{WRAPPER}} .eael-flip-carousel',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Flip Carousel Navigator Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_filp_carousel_custom_nav_settings',
			[
				'label' => esc_html__( 'Navigator Style', 'essential-addons-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

		$this->add_control(
		  'eael_flip_carousel_custom_nav',
		  	[
				'label' => __( 'Navigator', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'false',
				'label_on' => esc_html__( 'Yes', 'essential-addons-elementor' ),
				'label_off' => esc_html__( 'No', 'essential-addons-elementor' ),
				'return_value' => 'true',
		  	]
		);

		/**
		 * Condition: 'eael_flip_carousel_custom_nav' => 'true'
		 */
		$this->add_control(
			'eael_flip_carousel_custom_nav_prev_new',
			[
				'label' => esc_html__( 'Previous Icon', 'essential-addons-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'eael_flip_carousel_custom_nav_prev',
				'default' => [
					'value' => 'fas fa-arrow-left',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_flip_carousel_custom_nav' => 'true'
				]
			]
		);

		/**
		 * Condition: 'eael_flip_carousel_custom_nav' => 'true'
		 */
		$this->add_control(
			'eael_flip_carousel_custom_nav_next_new',
			[
				'label' => esc_html__( 'Next Icon', 'essential-addons-elementor' ),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'eael_flip_carousel_custom_nav_next',
				'default' => [
					'value' => 'fas fa-arrow-right',
					'library' => 'fa-solid',
				],
				'condition' => [
					'eael_flip_carousel_custom_nav' => 'true'
				]
			]
		);

		$this->add_responsive_control(
			'eael_flip_carousel_custom_nav_margin',
			[
				'label' => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
	 					'{{WRAPPER}} .flip-custom-nav' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
	 			],
			]
		);

		$this->add_control(
			'eael_flip_carousel_custom_nav_size',
			[
				'label' => esc_html__( 'Icon Size', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => '30'
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .flip-custom-nav' => 'font-size: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .eael-flip-carousel-svg-icon'	=> 'width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .flip-custom-nav svg'	=> 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_custom_nav_bg_size',
			[
				'label' => esc_html__( 'Background Size', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'max' => 80,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .flip-custom-nav' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}}; line-height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_custom_nav_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'essential-addons-elementor' ),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 50,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .flip-custom-nav' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_custom_nav_color',
			[
				'label' => esc_html__( 'Icon Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#42418e',
				'selectors' => [
					'{{WRAPPER}} .flip-custom-nav' => 'color: {{VALUE}};',
					'{{WRAPPER}} .flip-custom-nav svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_custom_nav_bg_color',
			[
				'label' => esc_html__( 'Background Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .flip-custom-nav' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_flip_carousel_custom_nav_border',
				'label' => esc_html__( 'Border', 'essential-addons-elementor' ),
				'selector' => '{{WRAPPER}} .flip-custom-nav',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_flip_carousel_custom_navl_shadow',
				'selector' => '{{WRAPPER}} .flip-custom-nav',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Flip Carousel Content Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_filp_carousel_main_content_style_settings',
			[
				'label' => esc_html__( 'Content', 'essential-addons-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_flip_carousel_content_view!' => 'none'
				]
			]
		);

		$this->add_responsive_control(
			'eael__filp_carousel_main_content_alignment',
			[
				'label'       => esc_html__('Alignment', 'essential-addons-elementor'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel-content' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_filp_carousel_main_content_color',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4d4d4d',
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel-content' => 'color: {{VALUE}};',
					'{{WRAPPER}} .eael-flip-carousel-content *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_flip_carousel_main_content_typography',
				'selector' => '{{WRAPPER}} .eael-flip-carousel-content',
			]
		);

		$this->add_control(
			'eael_flip_carousel_main_content_padding',
			[
				'label' => esc_html__( 'Padding', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_main_content_margin',
			[
				'label' => esc_html__( 'Margin', 'essential-addons-elementor' ),
				'type' => \Elementor\Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .eael-flip-carousel-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_flip_carousel_main_content_heading',
			[
				'label' => esc_html__( 'Overlay Style', 'essential-addons-elementor' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);
		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'eael_flip_carousel_main_content_overlay_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => ['image'],
				'selector' => '{{WRAPPER}} .eael-flip-carousel-content',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Flip Carousel footer Content Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_section_filp_carousel_content_style_settings',
			[
				'label' => esc_html__( 'Footer Content', 'essential-addons-elementor' ),
				'tab' => Controls_Manager::TAB_STYLE
			]
		);

        $this->add_responsive_control(
			'eael__filp_carousel_content_alignment',
			[
				'label'       => esc_html__('Alignment', 'essential-addons-elementor'),
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left' => [
						'title' => esc_html__('Left', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'essential-addons-elementor'),
						'icon'  => 'eicon-text-align-right',
					],
				],
				'default'   => 'center',
				'selectors' => [
					'{{WRAPPER}} .flip-carousel-text' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'eael_filp_carousel_content_color',
			[
				'label' => esc_html__( 'Color', 'essential-addons-elementor' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#4d4d4d',
				'selectors' => [
					'{{WRAPPER}} .flip-carousel-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
             'name' => 'eael_flip_carousel_content_typography',
				'selector' => '{{WRAPPER}} .flip-carousel-text',
			]
		);

		$this->end_controls_section();



	}


	protected function render()
	{
		$settings = $this->get_settings_for_display();
		
		$nav_prev = ((isset($settings['__fa4_migrated']['eael_flip_carousel_custom_nav_prev_new']) || empty($settings['eael_flip_carousel_custom_nav_prev'])) ? $this->get_settings('eael_flip_carousel_custom_nav_prev_new')['value'] : $this->get_settings('eael_flip_carousel_custom_nav_prev'));
		$nav_next = ((isset($settings['__fa4_migrated']['eael_flip_carousel_custom_nav_next_new']) || empty($settings['eael_flip_carousel_custom_nav_next'])) ? $this->get_settings('eael_flip_carousel_custom_nav_next_new')['value'] : $this->get_settings('eael_flip_carousel_custom_nav_next'));

		// Loop Value
		if( 'true' == $settings['eael_flip_carousel_loop'] ) : $eael_loop = 'true'; else: $eael_loop = 'false'; endif;
		// Autoplay Value
		if( 'true' == $settings['eael_flip_carousel_autoplay'] ) : $eael_autoplay = $settings['eael_flip_carousel_autoplay_time']; else: $eael_autoplay = 'false'; endif;
		// Pause On Hover Value
		if( 'true' == $settings['eael_flip_carousel_pause_on_hover'] ) : $eael_pause_hover = 'true'; else: $eael_pause_hover = 'false'; endif;
		// Click Value
		if( 'true' == $settings['eael_flip_carousel_click'] ) : $eael_click = 'true'; else: $eael_click = 'false'; endif;
		// Scroll Wheel Value
		if( 'true' == $settings['eael_flip_carousel_scrollwheel'] ) : $eael_wheel = 'true'; else: $eael_wheel = 'false'; endif;
		// Touch Play Value
		if( 'true' == $settings['eael_flip_carousel_touch'] ) : $eael_touch = 'true'; else: $eael_touch = 'false'; endif;
		// Navigator Value
		if( 'true' == $settings['eael_flip_carousel_button'] ) : $eael_buttons = 'true'; else: $eael_buttons = 'false'; endif;
		if( 'true' == $settings['eael_flip_carousel_custom_nav'] ) : $eael_custom_buttons = 'custom';else: $eael_custom_buttons = ''; endif;
		// Start Value
		if( 'true' == $settings['eael_flip_carousel_start_from'] ) : $eael_start = 'center'; else: $eael_start = (int) $settings['eael_flip_carousel_starting_number']; endif;

		$this->add_render_attribute(
			'eael-flip-carousel-wrap',
			[
				'class'	=> [
					'eael-flip-carousel',
					'flip-carousel-'.esc_attr( $this->get_id())
				],
				'data-style'	=> esc_attr( $settings['eael_flip_carousel_type'] ),
				'data-start'	=> $eael_start,
				'data-fadein'	=> esc_attr( (int) $settings['eael_flip_carousel_fade_in'] ),
				'data-loop'		=> $eael_loop,
				'data-autoplay'	=> $eael_autoplay,
				'data-pauseonhover'	=> $eael_pause_hover,
				'data-spacing'	=> esc_attr( $settings['eael_flip_carousel_spacing']['size'] ),
				'data-click'	=> $eael_click,
				'data-scrollwheel'	=> $eael_wheel,
				'data-touch'	=> $eael_touch,
				'data-buttons'	=> $eael_custom_buttons
			]
		);

		if( isset($nav_prev['url']) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['eael_flip_carousel_custom_nav_prev_new'], [ 'aria-hidden' => 'true' ] );
			$nav_prev_icon = ob_get_clean();

			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-buttonprev', $nav_prev_icon );
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-icon', 'svg' );
		} else {
			$this->add_render_attribute('eael-flip-carousel-wrap', 'data-buttonprev', $nav_prev );
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-icon', 'icon' );
		}

		if( isset($nav_next['url']) ) {
			ob_start();
			Icons_Manager::render_icon( $settings['eael_flip_carousel_custom_nav_next_new'], [ 'aria-hidden' => 'true' ] );
			$nav_next_icon = ob_get_clean();

			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-buttonnext', $nav_next_icon );
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-nexticon', 'svg' );
		}else {
			$this->add_render_attribute('eael-flip-carousel-wrap', 'data-buttonnext', $nav_next );
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'data-nexticon', 'icon' );
		}

		if ( $settings['eael_flip_carousel_content_active_only'] === 'yes' ){
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'class', 'show-active-only' );
		}
		else{
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'class', 'show-all' );
		}

		if ( $settings['eael_flip_carousel_content_view'] !== 'none' ){
			$this->add_render_attribute( 'eael-flip-carousel-wrap', 'class', esc_attr( $settings['eael_flip_carousel_content_view'] ) );
		}

		?>
		<div <?php echo $this->get_render_attribute_string('eael-flip-carousel-wrap'); ?>>
			<ul class="flip-items">
				<?php
					foreach( $settings['eael_flip_carousel_slides'] as $slides ) :
						$image_alt_text = get_post_meta( $slides['eael_flip_carousel_slide']['id'], '_wp_attachment_image_alt', true );
						$content_type = $settings['eael_flip_carousel_content_view'];
						$overlay = $settings['eael_flip_carousel_content_overlay'];
				?>
					<li>
						<?php if( 'true' == $slides['eael_flip_carousel_enable_slide_link'] ) :
							$eael_slide_link = $slides['eael_flip_carousel_slide_link']['url'];
							$target          = $slides['eael_flip_carousel_slide_link']['is_external'] ? 'target="_blank"' : '';
							$nofollow        = $slides['eael_flip_carousel_slide_link']['nofollow'] ? 'rel="nofollow"' : '';
							?>
							<a href="<?php echo esc_url($eael_slide_link); ?>" <?php echo $target; ?> <?php echo $nofollow; ?>>
								<img src="<?php echo $slides['eael_flip_carousel_slide']['url'] ?>" alt="<?php echo esc_attr($image_alt_text); ?>">
							</a>
							<?php if( $slides['eael_flip_carousel_slide_text'] !='' ) : ?>
								<p class="flip-carousel-text"><?php echo HelperClass::eael_wp_kses($slides['eael_flip_carousel_slide_text'] ); ?></p>
							<?php endif; ?>
						<?php else: ?>
							<img src="<?php echo $slides['eael_flip_carousel_slide']['url'] ?>" alt="<?php echo esc_attr($image_alt_text); ?>">
							<?php if ( $content_type != 'none' ){
								echo $overlay === 'yes' ? "<div class='eael-flip-carousel-content-overlay'></div>" : '';
								echo "<div class='eael-flip-carousel-content '>". HelperClass::eael_wp_kses( $slides['eael_flip_carousel_content'] ) ."</div>";
							}?>
							<?php if( $slides['eael_flip_carousel_slide_text'] !='' ) : ?>
								<p class="flip-carousel-text"><?php echo HelperClass::eael_wp_kses($slides['eael_flip_carousel_slide_text'] ); ?></p>
							<?php endif; ?>
						<?php endif; ?>

					</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php
	}
}
