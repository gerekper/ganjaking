<?php

namespace Essential_Addons_Elementor\Pro\Elements;

use \Elementor\Controls_Manager;
use Elementor\Icons_Manager;
use \Elementor\Plugin;
use \Elementor\Group_Control_Border;
use \Elementor\Group_Control_Box_Shadow;
use \Elementor\Group_Control_Typography;
use \Elementor\Utils;
use \Elementor\Widget_Base;
use \Essential_Addons_Elementor\Classes\Helper;


if (!defined('ABSPATH')) exit; // If this file is called directly, abort.

class Interactive_Card extends Widget_Base
{

	public function get_name()
	{
		return 'eael-interactive-card';
	}

	public function get_title()
	{
		return esc_html__('Interactive Card', 'essential-addons-elementor');
	}

	public function get_icon()
	{
		return 'eaicon-interactive-cards';
	}

	public function get_categories()
	{
		return ['essential-addons-elementor'];
	}

	public function get_keywords()
	{
		return [
			'interactive',
			'reveal',
			'card',
			'ea interactive cards',
			'click reveal',
			'click effect',
			'animation',
			'animated card',
			'ea',
			'essential addons'
		];
	}

	public function get_custom_help_url()
	{
		return 'https://essential-addons.com/elementor/docs/interactive-cards/';
	}

	protected function register_controls()
	{

		/**
		 * Interactive Cards Contents
		 */
		$this->start_controls_section(
			'eael_section_interactive_card_contents',
			[
				'label' => esc_html__('Interactive Card', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'eael_interactive_card_style',
			[
				'label'     	=> esc_html__('Front Panel Card Style', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'text-card',
				'label_block' 	=> false,
				'options' 		=> [
					'text-card' => esc_html__('Text Card', 'essential-addons-elementor'),
					'img-card' 	=> esc_html__('Image Card', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_type',
			[
				'label'     	=> esc_html__('Rear Panel Card Type', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'img-grid',
				'label_block' 	=> false,
				'options' 		=> [
					'img-grid' 		=> esc_html__('Image Grid', 'essential-addons-elementor'),
					'scrollable' 	=> esc_html__('Scrollable Content', 'essential-addons-elementor'),
					'video' 		=> esc_html__('Video', 'essential-addons-elementor'),
				],
			]
		);


		$this->start_controls_tabs('eael_interactive_card_Tabs');
		// Front Panel Tab
		$this->start_controls_tab('front-panel', ['label' => esc_html__('Front Panel', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_interactive_card_is_show_front_panel_cover',
			[
				'label' => __('Show Cover Image', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'essential-addons-elementor'),
				'label_off' => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_cover',
			[
				'label' => esc_html__('Cover Image', 'essential-addons-elementor'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
					'eael_interactive_card_is_show_front_panel_cover' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content .image-screen' => 'background: center / cover url({{URL}}) no-repeat !important;',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_front_panel_counter',
			[
				'label' => esc_html__('Counter', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('1', 'essential-addons-elementor'),
				'dynamic' => ['active' => true],
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'counter_html_tag',
			[
				'label'   => __( 'Counter HTML Tag', 'essential-addons-elementor' ),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h1',
				'options' => [
					'h1'   => __( 'H1', 'essential-addons-elementor' ),
					'h2'   => __( 'H2', 'essential-addons-elementor' ),
					'h3'   => __( 'H3', 'essential-addons-elementor' ),
					'h4'   => __( 'H4', 'essential-addons-elementor' ),
					'h5'   => __( 'H5', 'essential-addons-elementor' ),
					'h6'   => __( 'H6', 'essential-addons-elementor' ),
					'div'  => __( 'div', 'essential-addons-elementor' ),
					'span' => __( 'span', 'essential-addons-elementor' ),
					'p'    => __( 'p', 'essential-addons-elementor' ),
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_front_panel_title',
			[
				'label' => esc_html__('Title', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('Interactive Cards', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				],
				'dynamic' => ['active' => true],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_front_img',
			[
				'label' => esc_html__('Cover Image', 'essential-addons-elementor'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'eael_interactive_card_style' => 'img-card'
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content .image-screen' => 'background-image: url({{URL}});',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_text_type',
			[
				'label'                 => __('Content Type', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'content'       => __('Content', 'essential-addons-elementor'),
					'template'      => __('Saved Templates', 'essential-addons-elementor'),
				],
				'default'               => 'content',
			]
		);

		$this->add_control(
			'eael_primary_templates',
			[
				'label'                 => __('Choose Template', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Helper::get_elementor_templates(),
				'condition'             => [
					'eael_interactive_card_text_type'      => 'template',
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_front_panel_content',
			[
				'label' => esc_html__('Content', 'essential-addons-elementor'),
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'default' => esc_html__('A new concept of showing content in your web page with more interactive way.', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
					'eael_interactive_card_text_type' => 'content'
				],
				'dynamic' => ['active' => true]
			]
		);
		$this->add_control(
			'eael_interactive_card_front_button_heading',
			[
				'label' => esc_html__('Button', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_is_show_front_panel_btn_icon',
			[
				'label' => __('Show Button Icon', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'essential-addons-elementor'),
				'label_off' => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_front_panel_btn',
			[
				'label' => esc_html__('Button Text', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
                'dynamic'   => ['active' => true],
                'label_block' => false,
				'default' => esc_html__( 'More', 'essential-addons-elementor' ),
				'condition' => [
					'eael_interactive_card_style' => 'text-card',
				],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_front_panel_btn_icon_alignment',
			[
				'label' => __('Button Icon Alignment', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'condition'	=> [
					'eael_interactive_card_is_show_front_panel_btn_icon'	=> 'yes'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_front_panel_btn_icon_spacing',
			[
				'label' => __('Button Icon Spacing', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn .front-btn-icon.left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn .front-btn-icon.right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'eael_interactive_card_is_show_front_panel_btn_icon'	=> 'yes'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_front_panel_btn_icon',
			[
				'label' => __('Button Icon', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-circle-right',
					'library' => 'fa-solid',
				],
				'condition'	=> [
					'eael_interactive_card_is_show_front_panel_btn_icon'	=> 'yes'
				]
			]
		);
		$this->end_controls_tab();

		// Rear Panel Tab
		$this->start_controls_tab('rear-panel', ['label' => esc_html__('Rear Panel', 'essential-addons-elementor')]);
		$this->add_control(
			'eael_interactive_card_rear_image',
			[
				'label' => esc_html__('Cover Image', 'essential-addons-elementor'),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'selectors' => [
					'{{WRAPPER}} .content .image' => 'background-image: url({{URL}});',
				],
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_image_alignment',
			[
				'label'     	=> esc_html__('Image Alignment', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'top',
				'label_block' 	=> false,
				'options' 		=> [
					'left' 			=> esc_html__('Left', 'essential-addons-elementor'),
					'right' 		=> esc_html__('Right', 'essential-addons-elementor'),
					'top' 			=> esc_html__('Top', 'essential-addons-elementor'),
				],
				'prefix_class' => 'eael-interactive-card-rear-img-align-',
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_image_height',
			[
				'label' => esc_html__('Image Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 62,
					'unit' => '%',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}}.eael-interactive-card-rear-img-align-top .interactive-card .content .content-inner .image' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'eael_interactive_card_rear_image_alignment' => 'top'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_title',
			[
				'label' => esc_html__('Title', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Cool Headline', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				],
				'dynamic' => ['active' => true],
				'ai' => [
					'active' => false,
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_text_type',
			[
				'label'                 => __('Content Type', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'options'               => [
					'content'       => __('Content', 'essential-addons-elementor'),
					'template'      => __('Saved Templates', 'essential-addons-elementor'),
				],
				'default'               => 'content',
			]
		);

		$this->add_control(
			'eael_primary_rear_templates',
			[
				'label'                 => __('Choose Template', 'essential-addons-elementor'),
				'type'                  => Controls_Manager::SELECT,
				'options'               => Helper::get_elementor_templates(),
				'condition'             => [
					'eael_interactive_card_rear_text_type'      => 'template',
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_content',
			[
				'label' => esc_html__('Content', 'essential-addons-elementor'),
				'type' => Controls_Manager::WYSIWYG,
				'label_block' => true,
				'default' => esc_html__('A new concept of showing content in your web page with more interactive way.', 'essential-addons-elementor'),
				'dynamic' => ['active' =>  true],
				'condition' => [
					'eael_interactive_card_type' => 'img-grid',
					'eael_interactive_card_rear_text_type' => 'content'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_button_heading',
			[
				'label' => esc_html__('Button', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_is_show_rear_panel_btn_icon',
			[
				'label' => __('Show Button Icon', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::SWITCHER,
				'label_on' => __('Show', 'essential-addons-elementor'),
				'label_off' => __('Hide', 'essential-addons-elementor'),
				'return_value' => 'yes',
				'default' => '',
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_btn',
			[
				'label' => esc_html__('Button Text', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => true,
				'default' => esc_html__('Read More', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_btn_link',
			[
				'label' => esc_html__('Button Link', 'essential-addons-elementor'),
				'type' => Controls_Manager::URL,
                'dynamic'     => ['active' => true],
				'label_block' => true,
				'default' => [
					'url' => '#',
					'is_external' => '',
				],
				'show_external' => true,
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_panel_btn_icon_alignment',
			[
				'label' => __('Button Icon Alignment', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'left',
				'condition'	=> [
					'eael_interactive_card_is_show_rear_panel_btn_icon'	=> 'yes'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_panel_btn_icon_spacing',
			[
				'label' => __('Button Icon Spacing', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
						'step' => 5,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 5,
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card a.interactive-btn .rear-btn-icon.left' => 'margin-right: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .interactive-card a.interactive-btn .rear-btn-icon.right' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
				'condition'	=> [
					'eael_interactive_card_is_show_rear_panel_btn_icon'	=> 'yes'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_panel_btn_icon',
			[
				'label' => __('Button Icon', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::ICONS,
				'default' => [
					'value' => 'fas fa-arrow-circle-left',
					'library' => 'fa-solid',
				],
				'condition'	=> [
					'eael_interactive_card_is_show_rear_panel_btn_icon'	=> 'yes'
				]
			]
		);

		/**
		 * Scrollable Content
		 */
		$this->add_control(
			'eael_interactive_card_rear_custom_code',
			[
				'label'			=> esc_html__('Custom Content', 'essential-addons-elementor'),
				'type'			=> Controls_Manager::WYSIWYG,
				'label_block' 	=> true,
				'default' 		=> __('<h2>Custom Content</h2> <strong>A new concept of showing content in your web page with more interactive way</strong>. <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Voluptates assumenda recusandae a dolorum, nulla fugit reiciendis inventore explicabo cum autem placeat dignissimos doloremque quae magni sapiente eligendi hic ipsum quaerat mollitia, natus ullam. Repellat eligendi corporis cum suscipit totam molestiae ad, explicabo magnam libero, iusto sequi voluptatem nam culpa laboriosam officia consequatur eaque accusamus distinctio quas ipsa fuga consectetur iure asperiores! Ratione veniam magnam culpa temporibus nam quam cumque nesciunt debitis reprehenderit obcaecati eum tempore harum officiis autem facere, quos, ad officia sunt asperiores. Reprehenderit molestiae, vero omnis alias voluptatem recusandae dolores ab at. Nemo aliquam fuga vel necessitatibus voluptatum officiis ipsum, consequuntur id eum maiores debitis nostrum expedita libero saepe, doloribus mollitia minus quidem quo facere, consequatur! Veniam delectus doloribus blanditiis aliquid iure officiis modi sapiente unde. Ad, placeat suscipit. Perspiciatis dolores, expedita optio omnis reiciendis obcaecati quidem saepe praesentium autem unde suscipit nostrum natus vel tempore quas laudantium, excepturi! Ad, illo. Libero earum doloribus perspiciatis impedit, cum magni sint odio! Maxime sunt iste quibusdam nisi quia, voluptas, dolore tempora dolor neque error ducimus. Quas excepturi qui inventore quod at amet ipsa quasi blanditiis, voluptatem aliquam dolor beatae enim obcaecati alias voluptatibus vel molestias deleniti eius error nostrum, nesciunt adipisci quibusdam. Non mollitia rerum in commodi optio ipsam, neque quidem voluptatum velit quaerat suscipit consectetur nostrum odio, rem illo! Id placeat dignissimos tempora aliquam fugit veniam quam cum repudiandae fugiat nemo ad iure qui cupiditate natus aspernatur, dicta dolore ab corporis perferendis quaerat eaque assumenda libero explicabo beatae. Quas.</p>', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_type' => 'scrollable',
					'eael_interactive_card_rear_text_type' => 'content'
				]
			]
		);

		/**
		 * Video Content
		 */
		$this->add_control(
			'eael_interactive_card_youtube_video_url',
			[
				'label' => esc_html__('Youtube URL', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => esc_html__('https://www.youtube.com/watch?v=3rV9imkbV7k', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_type' => 'video'
				],
				'ai' => [
					'active' => false,
				],
			]

		);

		$this->add_control(
			'eael_interactive_card_youtube_video_fullscreen',
			[
				'label' => esc_html__('Allow Full Screen?', 'essential-addons-elementor'),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'default' => 'no',
				'condition' => [
					'eael_interactive_card_type' => 'video'
				]
			]
		);
		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		/**
		 * Interactive Cards Settings
		 */
		$this->start_controls_section(
			'eael_section_interactive_card_animation_settings',
			[
				'label' => esc_html__('Animation Settings', 'essential-addons-elementor')
			]
		);

		$this->add_control(
			'eael_interactive_card_content_animation',
			[
				'label'     	=> esc_html__('Content Animation', 'essential-addons-elementor'),
				'type' 			=> Controls_Manager::SELECT,
				'default' 		=> 'content-show',
				'label_block' 	=> false,
				'options' 		=> [
					'content-show'  	=> esc_html__('Appear', 'essential-addons-elementor'),
					'slide-in-left'  	=> esc_html__('SlideInLeft', 'essential-addons-elementor'),
					'slide-in-right'  	=> esc_html__('SlideInRight', 'essential-addons-elementor'),
					'slide-in-swing-left'  	=> esc_html__('SlideInSwingLeft', 'essential-addons-elementor'),
					'slide-in-swing-right'  => esc_html__('SlideInSwingRight', 'essential-addons-elementor'),
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_animation_reveal_time',
			[
				'label' => esc_html__('Timing (ms)', 'essential-addons-elementor'),
				'type' => Controls_Manager::TEXT,
				'label_block' => false,
				'default' => 400,
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (General Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_general_style',
			[
				'label' => esc_html__('General Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_general_width',
			[
				'label' => esc_html__('Max Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_general_height',
			[
				'label' => esc_html__('Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 600,
					'unit' => 'px',
				],
				'size_units' => ['px', 'vh', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'vh' => [
						'min' => 1,
						'max' => 100,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_general_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#262C37',
				'selectors' => [
					'{{WRAPPER}} .interactive-card' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_general_container_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_general_container_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .interactive-card .content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_small_overlay_circle_bg',
			[
				'label' => esc_html__('Small Overlay Circle', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content::after' => 'background-color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_large_overlay_circle_bg',
			[
				'label' => esc_html__('Large Overlay Circle', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content::before' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Interactive Card Front Panel)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_front_style',
			[
				'label' => esc_html__('Front Panel Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_interactive_card_front_panel_content_align',
			[
				'label' => __('Content Alignment', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .image-screen' => 'text-align: {{VALUE}};',
				],
				'condition'	=> [
					'eael_interactive_card_style'	=> 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_panel_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#262C37',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .image-screen' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_content_width',
			[
				'label' => esc_html__('Front Content Width', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'eael_interactive_card_style' => 'img-card',
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_content_height',
			[
				'label' => esc_html__('Front Content Height', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
						'step' => 5,
					],
					'%' => [
						'min' => 1,
						'max' => 100,
					],
				],
				'condition' => [
					'eael_interactive_card_style' => 'img-card',
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_cardfront_panel_container_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .image-screen' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_front_panel_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .front-content',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_front_content_shadow',
				'selector' => '{{WRAPPER}} .interactive-card .front-content .image-screen',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Interactive Card Front Panel thumbnail style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_front_thumbnail_style',
			[
				'label' => esc_html__('Front Panel Thumbnail Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition'	=> [
					'eael_interactive_card_style'	=> 'img-card'
				]
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_front_thumbnail_border',
				'label' => __('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .front-content .image-screen',
			]
		);

		$this->add_control(
			'eael_interactive_card_front_thumbnail_radius',
			[
				'label' => __('Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-content .image-screen' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_front_thumbnail_shadow',
				'label' => __('Shadow', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .front-content .image-screen',
			]
		);


		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Interactive Card Rear Panel)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_rear_style',
			[
				'label' => esc_html__('Rear Panel Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_panel_content_align',
			[
				'label' => __('Content Alignment', 'essential-addons-elementor'),
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'essential-addons-elementor'),
						'icon' => 'eicon-text-align-right',
					],
				],
				'toggle' => true,
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content .content-inner' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_panel_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_rear_panel_container_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_rear_panel_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .content',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_rear_content_shadow',
				'selector' => '{{WRAPPER}} .interactive-card .content .content-inner',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Interactive Card Rear Panel thumbnail style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_rear_thumbnail_style',
			[
				'label' => esc_html__('Rear Panel Thumbnail Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_rear_thumbnail_border',
				'label' => __('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .content .content-inner .image',
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_thumbnail_radius',
			[
				'label' => __('Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content .content-inner .image' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_rear_thumbnail_shadow',
				'label' => __('Shadow', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .content .content-inner .image',
			]
		);


		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Front Panel Typogrpahy)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_front_typography',
			[
				'label' => esc_html__('Front Panel Color &amp; Typography', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_title_counter_heading',
			[
				'label' => esc_html__('Counter Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'eael_interactive_card_front_counter_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#737373',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .header .card-number' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_front_counter_typography',
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .header .card-number',
			]
		);

		$this->add_control(
			'eael_interactive_card_front_title_heading',
			[
				'label' => esc_html__('Title Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_interactive_card_front_title_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .header .title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_front_title_typography',
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .header .title',
			]
		);

		$this->add_control(
			'eael_interactive_card_front_content_heading',
			[
				'label' => esc_html__('Content Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_interactive_card_front_content_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#cecece',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .front-text-body' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_front_content_typography',
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .front-text-body',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Rear Panel Typogrpahy)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_rear_typography',
			[
				'label' => esc_html__('Rear Panel Color &amp; Typography', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_title_heading',
			[
				'label' => esc_html__('Title Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_title_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content .text .title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_type!' => 'scrollable'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_rear_title_typography',
				'selector' => '{{WRAPPER}} .interactive-card .content .text .title',
				'condition' => [
					'eael_interactive_card_type!' => 'scrollable'
				]
			]
		);
		// heading - h1 to h6 tag style
		$this->add_control(
			'eael_interactive_card_rear_title_heading_tags',
			[
				'label' => esc_html__('All Heading Tags Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_interactive_card_type' => 'scrollable'
				]
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_title_tags_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#444',
				'selectors' => [
					'	{{WRAPPER}} .interactive-card .content h1, 
						{{WRAPPER}} .interactive-card .content h2,
						{{WRAPPER}} .interactive-card .content h3,
						{{WRAPPER}} .interactive-card .content h4,
						{{WRAPPER}} .interactive-card .content h5,
						{{WRAPPER}} .interactive-card .content h6
					' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_type' => 'scrollable'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_rear_title_tags_typography',
				'selector' => '{{WRAPPER}} .interactive-card .content h1, 
				{{WRAPPER}} .interactive-card .content h2,
				{{WRAPPER}} .interactive-card .content h3,
				{{WRAPPER}} .interactive-card .content h4,
				{{WRAPPER}} .interactive-card .content h5,
				{{WRAPPER}} .interactive-card .content h6',
				'condition' => [
					'eael_interactive_card_type' => 'scrollable'
				]
			]
		);

		// content
		$this->add_control(
			'eael_interactive_card_rear_content_heading',
			[
				'label' => esc_html__('Content Style', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_content_color',
			[
				'label' => esc_html__('Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#4d4d4d',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .content .text, {{WRAPPER}} .interactive-card .content p' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_rear_content_typography',
				'selector' => '{{WRAPPER}} .interactive-card .content .text, {{WRAPPER}} .interactive-card .content p',
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Button Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_front_button_style',
			[
				'label' => esc_html__('Front Panel Button Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		/**
		 * Front Panel Button
		 */
		$this->add_control(
			'eael_interactive_card_button_style_front_panel',
			[
				'label' => esc_html__('Button Style ( Front Panel )', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_btn_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_btn_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_front_btn_typography',
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn',
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_btn_icon_size',
			[
				'label' => __('Icon Size', 'essential-addons-for-elementor-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-btn .front-btn-icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .interactive-btn .front-btn-icon svg' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
					'{{WRAPPER}} .interactive-btn .front-btn-icon' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
				],
				'condition' => [
					'eael_interactive_card_is_show_front_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->start_controls_tabs('eael_interactive_card_front_button_tabs');

		// Normal State Tab
		$this->start_controls_tab(
			'eael_interactive_card_front_btn_normal',
			[
				'label' => esc_html__('Normal', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_normal_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_normal_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#49508c',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_normal_icon_color',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn .front-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .interactive-card .interactive-btn .front-btn-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_is_show_rear_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_front_btn_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn',
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_front_btn_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn' => 'border-radius: {{SIZE}}px;',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->end_controls_tab();

		// Hover State Tab
		$this->start_controls_tab(
			'eael_interactive_card_front_btn_hover',
			[
				'label' => esc_html__('Hover', 'essential-addons-elementor'),
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_hover_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn:hover' => 'color: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#7e5ae2',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn:hover' => 'background: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_normal_icon_color_hover',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn:hover .front-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .interactive-card .interactive-btn:hover .front-btn-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_is_show_front_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_front_btn_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				]
			]

		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_front_button_shadow',
				'selector' => '{{WRAPPER}} .interactive-card .front-text-content .footer a.interactive-btn',
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				],
				'separator' => 'none'
			]
		);



		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Rear Panel Button Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_rear_button_style',
			[
				'label' => esc_html__('Rear Panel Button Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
				'condition' => [
					'eael_interactive_card_type' => 'img-grid'
				]
			]
		);

		/**
		 * Rear Panel Button
		 */
		$this->add_control(
			'eael_interactive_card_button_style_rear_text_panel',
			[
				'label' => esc_html__('Button Style ( Rear Panel )', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'eael_interactive_card_style' => 'text-card'
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_rear_btn_padding',
			[
				'label' => esc_html__('Padding', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_rear_btn_margin',
			[
				'label' => esc_html__('Margin', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'eael_interactive_card_rear_btn_typography',
				'selector' => '{{WRAPPER}} .interactive-card .interactive-btn',
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_rear_btn_icon_size',
			[
				'label' => __('Icon Size', 'essential-addons-for-elementor-lite'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 14,
				],
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 100,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-btn .rear-btn-icon i' => 'font-size: {{SIZE}}px;',
					'{{WRAPPER}} .interactive-btn .rear-btn-icon svg' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
					'{{WRAPPER}} .interactive-btn .rear-btn-icon' => 'height: {{SIZE}}px; width: {{SIZE}}px;',
				],
				'condition' => [
					'eael_interactive_card_is_show_rear_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->start_controls_tabs('eael_interactive_card_rear_button_tabs');

		// Normal State Tab
		$this->start_controls_tab('eael_interactive_card_rear_btn_normal', ['label' => esc_html__('Normal', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_interactive_card_rear_btn_normal_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_btn_normal_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#49508c',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_btn_normal_icon_color_hover',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn .rear-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .interactive-card .interactive-btn .rear-btn-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_is_show_rear_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'eael_interactive_card_rear_btn_border',
				'label' => esc_html__('Border', 'essential-addons-elementor'),
				'selector' => '{{WRAPPER}} .interactive-card .interactive-btn',
			]
		);

		$this->add_responsive_control(
			'eael_interactive_card_rear_btn_border_radius',
			[
				'label' => esc_html__('Border Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn' => 'border-radius: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		// Hover State Tab
		$this->start_controls_tab('eael_interactive_card_rear_btn_hover', ['label' => esc_html__('Hover', 'essential-addons-elementor')]);

		$this->add_control(
			'eael_interactive_card_rear_btn_hover_text_color',
			[
				'label' => esc_html__('Text Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#f9f9f9',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_btn_hover_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#7e5ae2',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn:hover' => 'background: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_rear_btn_normal_icon_color',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn:hover .rear-btn-icon i' => 'color: {{VALUE}};',
					'{{WRAPPER}} .interactive-card .interactive-btn:hover .rear-btn-icon svg' => 'fill: {{VALUE}};',
				],
				'condition' => [
					'eael_interactive_card_is_show_rear_panel_btn_icon' => 'yes',
				]
			]
		);

		$this->add_control(
			'eael_interactive_card_rear_btn_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .interactive-btn:hover' => 'border-color: {{VALUE}};',
				],
			]

		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'eael_interactive_card_rear_button_shadow',
				'selector' => '{{WRAPPER}} .interactive-card .interactive-btn',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		/**
		 * -------------------------------------------
		 * Tab Style (Close Button Style)
		 * -------------------------------------------
		 */
		$this->start_controls_section(
			'eael_interactive_card_close_button_style',
			[
				'label' => esc_html__('Close Button Style', 'essential-addons-elementor'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'eael_interactive_card_close_button_bg_color',
			[
				'label' => esc_html__('Background Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#fff',
				'selectors' => [
					'{{WRAPPER}} .interactive-card .close-me' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_close_button_icon_color',
			[
				'label' => esc_html__('Icon Color', 'essential-addons-elementor'),
				'type' => Controls_Manager::COLOR,
				'default' => '#333',
				'selectors' => [
                    '{{WRAPPER}} .interactive-card .close-me' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .interactive-card .close-me svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_close_button_icon_new',
			[
				'label' => esc_html__('Icon', 'essential-addons-elementor'),
				'type' => Controls_Manager::ICONS,
				'fa4compatibility' => 'eael_interactive_card_close_button_icon',
				'default' => [
					'value' => 'fas fa-times',
					'library' => 'fa-solid',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_close_button_icon_size',
			[
				'label' => esc_html__('Icon Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .close-me' => 'width: {{SIZE}}px; height: {{SIZE}}px; line-height: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_close_button_icon_font_size',
			[
				'label' => esc_html__('Icon Font Size', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
                        'unit' => 'px',
				    	'size' => 13
				],
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors' => [
                    '{{WRAPPER}} .interactive-card .close-me' => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .interactive-card .close-me svg' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};line-height: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .interactive-card .close-me .eael-interactive-card-svg-icon' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$this->add_control(
			'eael_interactive_card_close_button_radius',
			[
				'label' => __('Icon Radius', 'essential-addons-elementor'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .close-me' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_colse_btn_position_heading',
			[
				'label' => esc_html__('Position', 'essential-addons-elementor'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'eael_interactive_card_close_btn_from_top',
			[
				'label' => esc_html__('Vertical', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15
				],
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .close-me' => 'top: {{SIZE}}px;',
				],
			]
		);

		$this->add_control(
			'eael_interactive_card_close_btn_from_right',
			[
				'label' => esc_html__('Horizontal', 'essential-addons-elementor'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15
				],
				'range' => [
					'px' => [
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .interactive-card .close-me' => 'right: {{SIZE}}px;',
					'.rtl {{WRAPPER}} .interactive-card .close-me' => 'left: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_section();
	}


	protected function render()
	{

		$settings = $this->get_settings_for_display();

		// Rear Button Link Target and NoFollow
		$target = $this->get_settings('eael_interactive_card_rear_btn_link')['is_external'] ? 'target="_blank"' : '';
		$nofollow = $this->get_settings('eael_interactive_card_rear_btn_link')['nofollow'] ? 'rel="nofollow"' : '';

		if (isset($settings['__fa4_migrated']['eael_interactive_card_close_button_icon_new']) || empty($settings['eael_interactive_card_close_button_icon'])) {
			if (isset($settings['eael_interactive_card_close_button_icon_new']['value']['url'])) {
				$icon = Helper::get_render_icon($settings['eael_interactive_card_close_button_icon_new']);
			} else {
				$icon = Helper::get_render_icon($settings['eael_interactive_card_close_button_icon_new']);
			}
		} else {
			$icon = '<i class="'.$settings['eael_interactive_card_close_button_icon'].'"></i>';
		}

		// Youtube FullScreen
		if ('yes' === $settings['eael_interactive_card_youtube_video_fullscreen']) : $full_screen = 'allowfullscreen';
		else : $full_screen = '';
		endif;

		$this->add_render_attribute('eael-interactive-card', [
			'class'	=> 'interactive-card',
			'data-interactive-card-id'	=> esc_attr($this->get_id()),
			'data-animation'			=> $settings['eael_interactive_card_content_animation'],
			'data-animation-time'		=> $settings['eael_interactive_card_animation_reveal_time']
		]);

?>

		<div id="interactive-card-<?php echo esc_attr($this->get_id()); ?>" <?php echo 	$this->get_render_attribute_string('eael-interactive-card'); ?>>
			<?php if ('text-card' === $settings['eael_interactive_card_style']) : ?>
				<div class="front-content front-text-content">
					<div class="image-screen">
						<div class="header">
							<?php if ( ! empty( $settings['eael_interactive_card_front_panel_counter'] ) ) {
								printf( '<%1$s class="card-number">', $settings['counter_html_tag'] );
								echo $settings['eael_interactive_card_front_panel_counter'];
								printf( '</%1$s>', $settings['counter_html_tag'] );
							} ?>
							<?php if (!empty($settings['eael_interactive_card_front_panel_title'])) : ?>
								<h2 class="title"><?php echo $settings['eael_interactive_card_front_panel_title']; ?></h2>
							<?php endif; ?>
						</div>
						<?php if ('content' == $settings['eael_interactive_card_text_type']) :  ?>
							<?php if (!empty($settings['eael_interactive_card_front_panel_content'])) : ?>
								<div class="front-text-body">
									<?php echo $settings['eael_interactive_card_front_panel_content']; ?>
								</div>
							<?php endif; ?>
						<?php elseif ('template' == $settings['eael_interactive_card_text_type']) : ?>
							<div class="front-text-body">
								<?php
								if ( ! empty( $settings['eael_primary_templates'] ) ) {
									// WPML Compatibility
									if ( ! is_array( $settings['eael_primary_templates'] ) ) {
										$settings['eael_primary_templates'] = apply_filters( 'wpml_object_id', $settings['eael_primary_templates'], 'wp_template', true );
									}
									echo Plugin::$instance->frontend->get_builder_content( $settings['eael_primary_templates'], true );
								}
								?>
							</div>
						<?php endif; ?>
						<?php if (!empty($settings['eael_interactive_card_front_panel_btn'])) : ?>
							<div class="footer">
								<a href="javascript:;" class="interactive-btn">
									<?php
									if ($settings['eael_interactive_card_is_show_front_panel_btn_icon'] == 'yes' && $settings['eael_interactive_card_front_panel_btn_icon_alignment'] == 'left') {
										echo '<span class="' . $settings['eael_interactive_card_front_panel_btn_icon_alignment'] . ' front-btn-icon"></i>';
									    Icons_Manager::render_icon( $settings['eael_interactive_card_front_panel_btn_icon'], [ 'aria-hidden' => 'true' ] );
										echo '</span>';
									}
									echo $settings['eael_interactive_card_front_panel_btn'];
									if ($settings['eael_interactive_card_is_show_front_panel_btn_icon'] == 'yes' && $settings['eael_interactive_card_front_panel_btn_icon_alignment'] == 'right') {
										echo '<span class="' . $settings['eael_interactive_card_front_panel_btn_icon_alignment'] . ' front-btn-icon"></i>';
										Icons_Manager::render_icon( $settings['eael_interactive_card_front_panel_btn_icon'], [ 'aria-hidden' => 'true' ] );
										echo '</span>';
									}
									?>
								</a>
							</div>
						<?php endif; ?>
					</div>
				</div>
			<?php elseif ('img-card' === $settings['eael_interactive_card_style']) : ?>
				<div class="front-content">
					<div class="image-screen">
						<div class="image-screen-overlay"></div>
					</div>
				</div>
			<?php endif; ?>

			<div class="content">
				<span class="close close-me">
					<?php if (is_array($icon) && isset($icon['url'])) : ?>
						<img src="<?php echo esc_url($icon['url']); ?>" class="eael-interactive-card-svg-icon" alt="<?php echo esc_attr(get_post_meta($icon['id'], '_wp_attachment_image_alt', true)); ?>" />
					<?php else :
                            echo $icon;
                        endif; ?>
				</span>
				<?php if ('img-grid' === $settings['eael_interactive_card_type']) : ?>
					<div class="content-inner">
						<div class="text">
							<div class="text-inner">
								<?php if (!empty($settings['eael_interactive_card_rear_title'])) : ?>
									<h2 class="title"><?php echo $settings['eael_interactive_card_rear_title']; ?></h2>
								<?php endif; ?>
								<?php if ('content' == $settings['eael_interactive_card_rear_text_type']) : ?>
									<?php echo wpautop($settings['eael_interactive_card_rear_content']); ?>
								<?php elseif ('template' == $settings['eael_interactive_card_rear_text_type']) : ?>
									<?php
									if ( ! empty( $settings['eael_primary_rear_templates'] ) ) {
										// WPML Compatibility
										if ( ! is_array( $settings['eael_primary_rear_templates'] ) ) {
											$settings['eael_primary_rear_templates'] = apply_filters( 'wpml_object_id', $settings['eael_primary_rear_templates'], 'wp_template', true );
										}
										echo Plugin::$instance->frontend->get_builder_content( $settings['eael_primary_rear_templates'], true );
									}
									?>
								<?php endif; ?>
								<?php if (!empty($settings['eael_interactive_card_rear_btn'])) : ?>
									<a href="<?php echo esc_url($settings['eael_interactive_card_rear_btn_link']['url']); ?>" <?php echo $target; ?> <?php echo $nofollow; ?> class="interactive-btn">
										<?php
										if ($settings['eael_interactive_card_is_show_rear_panel_btn_icon'] == 'yes' && $settings['eael_interactive_card_rear_panel_btn_icon_alignment'] == 'left') {
											echo '<span class="' . $settings['eael_interactive_card_rear_panel_btn_icon_alignment'] . ' rear-btn-icon"></i>';
											Icons_Manager::render_icon( $settings['eael_interactive_card_rear_panel_btn_icon'], [ 'aria-hidden' => 'true' ] );
											echo '</span>';
										}
										echo $settings['eael_interactive_card_rear_btn'];
										if ($settings['eael_interactive_card_is_show_rear_panel_btn_icon'] == 'yes' && $settings['eael_interactive_card_rear_panel_btn_icon_alignment'] == 'right') {
											echo '<span class="' . $settings['eael_interactive_card_rear_panel_btn_icon_alignment'] . ' rear-btn-icon"></i>';
											Icons_Manager::render_icon( $settings['eael_interactive_card_rear_panel_btn_icon'], [ 'aria-hidden' => 'true' ] );
											echo '</span>';
										}
										?>
									</a>
								<?php endif; ?>
							</div>
						</div>
						<?php if (!empty($settings['eael_interactive_card_rear_image'])) : ?>
							<div class="image"></div>
						<?php endif; ?>
					</div>
				<?php elseif ('scrollable' === $settings['eael_interactive_card_type']) : ?>
					<div class="content-overflow">
						<?php if ('content' == $settings['eael_interactive_card_rear_text_type']) : ?>
							<?php echo do_shortcode(wp_kses_post($settings['eael_interactive_card_rear_custom_code'])); ?>
						<?php elseif ('template' == $settings['eael_interactive_card_rear_text_type']) : ?>
							<?php
							if ( ! empty( $settings['eael_primary_rear_templates'] ) ) {
								// WPML Compatibility
								if ( ! is_array( $settings['eael_primary_rear_templates'] ) ) {
									$settings['eael_primary_rear_templates'] = apply_filters( 'wpml_object_id', $settings['eael_primary_rear_templates'], 'wp_template', true );
								}
								echo Plugin::$instance->frontend->get_builder_content( $settings['eael_primary_rear_templates'], true );
							}
							?>
						<?php endif; ?>
					</div>
				<?php
				elseif ('video' === $settings['eael_interactive_card_type']) :
				?>
					<iframe src="<?php echo esc_url(str_replace('watch?v=', 'embed/', $settings['eael_interactive_card_youtube_video_url'])); ?>" <?php echo $full_screen; ?>></iframe>
				<?php endif; ?>
			</div>
		</div>
<?php
	}

	protected function content_template()
	{
	}
}
