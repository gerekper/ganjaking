<?php

namespace MasterAddons\Addons;


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


// Exit if accessed directly
if (!defined('ABSPATH')) {
	exit;
}


/**
 * Modulify Site Title
 */
class Slider extends Widget_Base
{

	public function get_name()
	{
		return 'modulify-all-widgets';
	}

	public function get_title()
	{
		return __('Modulify Widgets', MELA_TD);
	}

	public function get_icon()
	{
		return 'eicon-image-rollover';
	}

	public function get_categories()
	{
		return ['modulify-elements'];
	}

	protected function _register_controls()
	{

		$this->start_controls_section(
			'all_filter',
			[
				'label' => __('Filter', MELA_TD),
			]
		);


		$this->add_control(
			'fn_widget_layout',
			[
				'label' => esc_html__('Layout', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'carousel_circle' 	=> esc_html__('Carousel Circle', MELA_TD),
					'carousel_full_a' 	=> esc_html__('Carousel Full Alpha', MELA_TD),
					'carousel_full_b' 	=> esc_html__('Carousel Full Beta', MELA_TD),
					'carousel_full_i' 	=> esc_html__('Carousel Full Interactive', MELA_TD),
					'carousel_square' 	=> esc_html__('Carousel Square', MELA_TD),
					'carousel_with_c' 	=> esc_html__('Carousel With Content', MELA_TD),
					'list_just' 		=> esc_html__('List Justified', MELA_TD),
					'list_masonry' 		=> esc_html__('List Masonry', MELA_TD),
					'slider_a' 			=> esc_html__('Slider Alpha', MELA_TD),
					'slider_b' 			=> esc_html__('Slider Beta', MELA_TD),
					'slider_d' 			=> esc_html__('Slider Delta', MELA_TD),
					'slider_e' 			=> esc_html__('Slider Epsilon', MELA_TD),
					'slider_g' 			=> esc_html__('Slider Gamma', MELA_TD),
					'slider_z' 			=> esc_html__('Slider Zeta', MELA_TD),
				],
				'default' => 'carousel_circle',

			]
		);

		$repeater = new \Elementor\Repeater();

		$repeater->add_control(
			'module_title',
			[
				'label'       	=> __('Module Title', 'frenify-core'),
				'type'        	=> Controls_Manager::TEXT,
				'placeholder' 	=> __('Module Title Here...', 'frenify-core'),
				'default' 	    => __('Module Title', 'frenify-core'),
				'label_block' 	=> true,
			]
		);

		$repeater->add_control(
			'module_url',
			[
				'label'       	=> __('Module URL', 'frenify-core'),
				'type'        	=> Controls_Manager::TEXT,
				'placeholder' 	=> __('Module URL Here...', 'frenify-core'),
				'default' 	    => '#',
				'label_block' 	=> true,
			]
		);
		$repeater->add_control(
			'module_categories',
			[
				'label'       	=> __('Module Category', 'frenify-core'),
				'type'        	=> Controls_Manager::TEXTAREA,
				'placeholder' 	=> __('Module Category Here...', 'frenify-core'),
				'default' 	    => __('Category', 'frenify-core'),
				'label_block' 	=> true,
			]
		);

		$repeater->add_control(
			'module_image',
			[
				'label' 		=> __('Module Image', 'frenify-core'),
				'type' 		=> Controls_Manager::MEDIA,
				'default' 		=> [
					'url' 		=> MODULIFY_PLACEHOLDERS_URL . '1.jpg'
				],
			]
		);
		$this->add_control(
			'module_items',
			[
				'label' => __('Module Items', 'frenify-core'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'module_title' 			=> __('Alpha Title', 'frenify-core'),
						'module_categories' 	=> __('Modern', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '1.jpg',
						]
					],
					[
						'module_title' 			=> __('Beta Title', 'frenify-core'),
						'module_categories' 	=> __('Colorful', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '2.jpg',
						]
					],
					[
						'module_title' 			=> __('Gamma Title', 'frenify-core'),
						'module_categories' 	=> __('Beautiful', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '3.jpg',
						]
					],
					[
						'module_title' 			=> __('Delta Title', 'frenify-core'),
						'module_categories' 	=> __('Amazing', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '4.jpg',
						]
					],
					[
						'module_title' 			=> __('Epsilon Title', 'frenify-core'),
						'module_categories' 	=> __('Wonderful', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '5.jpg',
						]
					],
					[
						'module_title' 			=> __('Eta Title', 'frenify-core'),
						'module_categories' 	=> __('Easy', 'frenify-core'),
						'module_url' 			=> '#',
						'module_image' 	=> [
							'url'		=> MODULIFY_PLACEHOLDERS_URL . '6.jpg',
						]
					],
				],
				'title_field' => '{{{ module_title }}}',
			]
		);
		$this->end_controls_section();


		/************************************************************************/
		/************************* CAROUSEL CIRCLE *****************************/
		/**********************************************************************/

		$this->start_controls_section(
			'carousel_circle_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_circle')
				]
			]
		);
		$this->add_control(
			'cc_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cc_main_layout',
			[
				'label' => esc_html__('Layout', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'alpha' 			=> esc_html__('Alpha', MELA_TD),
					'beta' 				=> esc_html__('Beta', MELA_TD),
					'gamma' 			=> esc_html__('Gamma', MELA_TD),
					'numbered' 			=> esc_html__('Numbered', MELA_TD),
					'numbered2' 		=> esc_html__('Numbered 2', MELA_TD),
				],
				'default' => 'alpha',

			]
		);

		$this->add_control(
			'cc_numbered_img_thumb',
			[
				'label' => __('Image Thumb', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cc_main_layout' => array('numbered', 'numbered2')
				]
			]
		);

		$this->add_control(
			'cc_numbered_hover_thumb',
			[
				'label' => __('Hover Thumb', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cc_main_layout' => array('numbered', 'numbered2')
				]
			]
		);

		$this->add_control(
			'cc_beta_bg_line',
			[
				'label' => esc_html__('Background Line', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'gradient' 			=> esc_html__('Gradient Overlay', MELA_TD),
					'color' 			=> esc_html__('Color Overlay', MELA_TD),
				],
				'default' => 'gradient',
				'condition' => [
					'cc_main_layout' => 'beta'
				]

			]
		);

		$this->add_control(
			'cc_box_shadow_gamma',
			[
				'label' => __('Box Shadow for Active Item', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cc_main_layout' => 'gamma'
				]
			]
		);

		$this->add_control(
			'cc_box_shadow_numbered',
			[
				'label' => __('Box Shadow Item on hover', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cc_main_layout' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cc_box_shadow_numbered2',
			[
				'label' => __('Box Shadow Item on hover', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cc_main_layout' => 'numbered2'
				]
			]
		);
		// alpha navigation
		$this->add_control(
			'cc_alpha_nav_color',
			[
				'label' => __('Navigation Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev.swiper-button-disabled span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev.swiper-button-disabled span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next.swiper-button-disabled span.a' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next.swiper-button-disabled span.b' => 'background-color: {{VALUE}} !important;',
				],
				'default' => '#999',
				'condition' => [
					'cc_main_layout' => 'alpha',
				]
			]
		);
		$this->add_control(
			'cc_alpha_nav_hover_color',
			[
				'label' => __('Navigation Icon Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev:hover span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev:hover span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next:hover span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next:hover span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#ccc',
				'condition' => [
					'cc_main_layout' => 'alpha',
				]
			]
		);
		$this->add_control(
			'cc_alpha_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_prev' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .content-slider .fn_next' => 'background-color: {{VALUE}};',
				],
				'default' => '#222',
				'condition' => [
					'cc_main_layout' => 'alpha',
				]
			]
		);
		$this->add_control(
			'cc_alpha_title_bg_color',
			[
				'label' => __('Title Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder' => 'background-color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cc_main_layout' => 'alpha',
				]
			]
		);
		// beta navigation
		$this->add_control(
			'cc_beta_nav_color',
			[
				'label' => __('Navigation Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller .swiper-button-disabled span.a' => 'background-color: {{VALUE}} !important;',
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller .swiper-button-disabled span.b' => 'background-color: {{VALUE}} !important;',
				],
				'default' => '#999',
				'condition' => [
					'cc_main_layout' => 'beta',
				]
			]
		);
		$this->add_control(
			'cc_beta_nav_hover_color',
			[
				'label' => __('Navigation Icon Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller div:hover span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller div:hover span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#ccc',
				'condition' => [
					'cc_main_layout' => 'beta',
				]
			]
		);
		$this->add_control(
			'cc_beta_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller div' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle .beta_controller:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#090909',
				'condition' => [
					'cc_main_layout' => 'beta',
				]
			]
		);
		// gamma navigation
		$this->add_control(
			'cc_nav_color',
			[
				'label' => __('Navigation Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle .gamma_controller span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle .gamma_controller span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'gamma',
				]
			]
		);
		$this->add_control(
			'cc_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle .gamma_controller div' => 'background-color: {{VALUE}};',
				],
				'default' => '#fb3183',
				'condition' => [
					'cc_main_layout' => 'gamma',
				]
			]
		);
		$this->add_control(
			'cc_gamma_line_color',
			[
				'label' => __('Line Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.gamma .content-slider:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#827b79',
				'condition' => [
					'cc_main_layout' => 'gamma',
				]
			]
		);
		// numbered navigation
		$this->add_control(
			'cc_numbered_nav_color',
			[
				'label' => __('Navigation Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .numbered_controller .fn_next span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered .numbered_controller .fn_next span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered .numbered_controller .fn_prev span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered .numbered_controller .fn_prev span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		$this->add_control(
			'cc_numbered_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .numbered_controller div' => 'background-color: {{VALUE}};',
				],
				'default' => '#777',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		$this->add_control(
			'cc_numbered_number_color',
			[
				'label' => __('Number Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered span.count' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		$this->add_control(
			'cc_numbered_number_bg_color',
			[
				'label' => __('Number Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered span.count' => 'background-color: {{VALUE}};',
				],
				'default' => '#333',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		$this->add_control(
			'cc_numbered_number_active_color',
			[
				'label' => __('Number Active Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .swiper-slide-active span.count' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		$this->add_control(
			'cc_numbered_number_active_bg_color',
			[
				'label' => __('Number Active Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .swiper-slide-active span.count' => 'background-color: {{VALUE}};',
				],
				'default' => '#fb3183',
				'condition' => [
					'cc_main_layout' => 'numbered',
				]
			]
		);
		// numbered2 navigation
		$this->add_control(
			'cc_numbered2_nav_color',
			[
				'label' => __('Navigation Icon Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .numbered_controller .fn_next span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .numbered_controller .fn_next span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .numbered_controller .fn_prev span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .numbered_controller .fn_prev span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_numbered2_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .numbered_controller div' => 'background-color: {{VALUE}};',
				],
				'default' => '#777',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_numbered2_number_color',
			[
				'label' => __('Number Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 span.count' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_numbered2_number_bg_color',
			[
				'label' => __('Number Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 span.count' => 'background-color: {{VALUE}};',
				],
				'default' => '#333',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_numbered2_number_active_color',
			[
				'label' => __('Number Active Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .swiper-slide-active span.count' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_numbered2_number_active_bg_color',
			[
				'label' => __('Number Active Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .swiper-slide-active span.count' => 'background-color: {{VALUE}};',
				],
				'default' => '#fb3183',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				]
			]
		);
		$this->add_control(
			'cc_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cc_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cc_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_circle .title_holder p,{{WRAPPER}} .modulify_carousel_circle .title_holder span',
				'condition' => [
					'cc_category_show' => 'yes'
				]
			]
		);
		// alpha color
		$this->add_control(
			'cc_alpha_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cc_category_show' => 'yes',
					'cc_main_layout'	=> 'alpha'
				],
				'default' => '#fff',
			]
		);
		// beta color
		$this->add_control(
			'cc_beta_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.beta .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cc_category_show' => 'yes',
					'cc_main_layout' => 'beta',
				],
				'default' => '#111',
			]
		);
		// gamma color
		$this->add_control(
			'cc_gamma_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.gamma .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cc_category_show' => 'yes',
					'cc_main_layout' => 'gamma',
				],
				'default' => '#111',
			]
		);
		// numbered color
		$this->add_control(
			'cc_numbered_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cc_category_show' => 'yes',
					'cc_main_layout' => 'numbered',
				],
				'default' => '#111',
			]
		);
		// numbered2 color
		$this->add_control(
			'cc_numbered2_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cc_category_show' => 'yes',
					'cc_main_layout' => 'numbered2',
				],
				'default' => '#111',
			]
		);
		$this->add_control(
			'cc_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cc_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_circle .title_holder h3',
			]
		);

		// alpha title color
		$this->add_control(
			'cc_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#eee',
				'condition' => [
					'cc_main_layout' => 'alpha',
				],
			]
		);

		$this->add_control(
			'cc_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.alpha .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cc_main_layout' => 'alpha',
				],
			]
		);

		// beta title color
		$this->add_control(
			'cc_beta_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.beta .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.beta .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cc_main_layout' => 'beta',
				],
			]
		);

		$this->add_control(
			'cc_beta_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.beta .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.beta .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
				'condition' => [
					'cc_main_layout' => 'beta',
				],
			]
		);

		// gamma title color
		$this->add_control(
			'cc_gamma_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.gamma .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.gamma .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cc_main_layout' => 'gamma',
				],
			]
		);

		$this->add_control(
			'cc_gamma_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.gamma .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.gamma .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
				'condition' => [
					'cc_main_layout' => 'gamma',
				],
			]
		);

		// numbered title color
		$this->add_control(
			'cc_numbered_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cc_main_layout' => 'numbered',
				],
			]
		);

		$this->add_control(
			'cc_numbered_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
				'condition' => [
					'cc_main_layout' => 'numbered',
				],
			]
		);

		// numbered2 title color
		$this->add_control(
			'cc_numbered2_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				],
			]
		);

		$this->add_control(
			'cc_numbered2_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_circle.numbered2 .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
				'condition' => [
					'cc_main_layout' => 'numbered2',
				],
			]
		);


		$this->end_controls_section();

		/************************************************************************/
		/*********************** CAROUSEL FULL ALPHA ***************************/
		/**********************************************************************/

		$this->start_controls_section(
			'carousel_full_alpha_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_full_a')
				]
			]
		);
		$this->add_control(
			'cfa_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cfa_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'cfa_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['cfa_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'cfa_columns_number',
			[
				'label' => __('Columns Number', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 5,
						'step' => 1,
					]
				],
				'size_units' => [''],
			]
		);
		$this->add_control(
			'cfa_title_holder_type',
			[
				'label' => esc_html__('Title Holder Type', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'static' 			=> esc_html__('Static', MELA_TD),
					'dynamic' 			=> esc_html__('Dynamic', MELA_TD),
					'smooth' 			=> esc_html__('Smooth', MELA_TD),
				],
				'default' => 'static',

			]
		);
		$this->add_control(
			'cfa_separator_color',
			[
				'label' => __('Separator Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl-carousel .item:after' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(255,255,255,0)',
			]
		);

		$this->add_control(
			'cfa_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);

		$this->add_control(
			'cfa_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_carousel_full_alpha[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_carousel_full_alpha[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_carousel_full_alpha[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cfa_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.1)',
			]
		);
		$this->add_control(
			'cfa_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cfa_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfa_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder p',
				'condition' => [
					'cfa_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'cfa_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder p a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cfa_category_show' => 'yes'
				],
				'default' => '#eee',
			]
		);
		$this->add_control(
			'cfa_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfa_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder h3 a',
			]
		);

		$this->add_control(
			'cfa_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#eee',
			]
		);

		$this->add_control(
			'cfa_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cfa_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cfa_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'extendable',

			]
		);
		$this->add_control(
			'cfa_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl-carousel .item .title_holder > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl-carousel .item .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl-carousel .item .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_alpha .owl-carousel .item .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cfa_read_more_types!' => 'none',
				],
				'default' => '#fff',
			]
		);


		$this->end_controls_section();

		/************************************************************************/
		/************************ CAROUSEL FULL BETA ***************************/
		/**********************************************************************/

		$this->start_controls_section(
			'carousel_full_beta_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_full_b')
				]
			]
		);
		$this->add_control(
			'cfb_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cfb_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'cfb_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['cfb_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'cfb_columns_number',
			[
				'label' => __('Columns Number', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 5,
						'step' => 1,
					]
				],
				'size_units' => [''],
			]
		);
		$this->add_control(
			'cfb_title_holder_type',
			[
				'label' => esc_html__('Title Holder Type', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'static' 			=> esc_html__('Static', MELA_TD),
					'dynamic' 			=> esc_html__('Dynamic', MELA_TD),
				],
				'default' => 'static',
				'condition' => [
					'cfb_title_holder_position!' => 'middle'
				]
			]
		);

		$this->add_control(
			'cfb_title_holder_position',
			[
				'label' => esc_html__('Title Holder Position', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'top' 				=> esc_html__('Top', MELA_TD),
					'middle' 			=> esc_html__('Middle', MELA_TD),
					'bottom' 			=> esc_html__('Bottom', MELA_TD),
				],
				'default' => 'bottom',

			]
		);
		$this->add_control(
			'cfb_title_holder_alignment',
			[
				'label' => esc_html__('Title Holder Alignment', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'left' 				=> esc_html__('Left Align', MELA_TD),
					'center' 			=> esc_html__('Center Align', MELA_TD),
					'right' 			=> esc_html__('Right Align', MELA_TD),
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder' => 'text-align: {{VALUE}};',
				],
				'default' => 'center',
			]
		);

		$this->add_control(
			'cfb_title_holder_bg',
			[
				'label' => __('Title Holder Background', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'cfb_separator_color',
			[
				'label' => __('Separator Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item:after' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,0.5)',
			]
		);

		$this->add_control(
			'cfb_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);

		$this->add_control(
			'cfb_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_carousel_full_beta[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_carousel_full_beta[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_carousel_full_beta[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'cfb_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.1)',
			]
		);
		$this->add_control(
			'cfb_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cfb_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfb_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_beta .item .title_holder p',
				'condition' => [
					'cfb_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'cfb_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder p a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cfb_category_show' => 'yes'
				],
				'default' => '#111',
			]
		);
		$this->add_control(
			'cfb_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfb_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder h3 a',
			]
		);

		$this->add_control(
			'cfb_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#111',
			]
		);

		$this->add_control(
			'cfb_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_beta .owl-carousel .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
			]
		);
		$this->end_controls_section();

		/************************************************************************/
		/********************* CAROUSEL FULL INTERACTIVE ***********************/
		/**********************************************************************/

		$this->start_controls_section(
			'carousel_full_interactive_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_full_i')
				]
			]
		);
		$this->add_control(
			'cfi_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'cfi_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'cfi_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['cfi_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'cfi_columns_number',
			[
				'label' => __('Columns Number', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 4,
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 5,
						'step' => 1,
					]
				],
				'size_units' => [''],
			]
		);
		$this->add_control(
			'cfi_title_holder_type',
			[
				'label' => esc_html__('Title Holder Type', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'static' 			=> esc_html__('Static', MELA_TD),
					'dynamic' 			=> esc_html__('Dynamic', MELA_TD),
					'smooth' 			=> esc_html__('Smooth', MELA_TD),
				],
				'default' => 'static',

			]
		);
		$this->add_control(
			'cfi_separator_color',
			[
				'label' => __('Separator Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item:after' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'cfi_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',
			]
		);

		$this->add_control(
			'cfi_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_carousel_full_interactive[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_carousel_full_interactive[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_carousel_full_interactive[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cfi_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.1)',
			]
		);
		$this->add_control(
			'cfi_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cfi_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfi_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder p',
				'condition' => [
					'cfi_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'cfi_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder p a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cfi_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cfi_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cfi_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder h3 a',
			]
		);

		$this->add_control(
			'cfi_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#eee',
			]
		);

		$this->add_control(
			'cfi_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cfi_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cfi_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'extendable',

			]
		);
		$this->add_control(
			'cfi_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item .title_holder > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_full_interactive .owl-carousel .item .title_holder > span a.read_more' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cfi_read_more_types!' => 'none',
				],
				'default' => '#fff',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/*************************** CAROUSEL SQUARE ***************************/
		/**********************************************************************/
		$this->start_controls_section(
			'carousel_square_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_square')
				]
			]
		);

		$this->add_control(
			'cs_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cs_main_layout',
			[
				'label' => esc_html__('Layout', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'alpha' 			=> esc_html__('Alpha', MELA_TD),
					'beta' 				=> esc_html__('Beta', MELA_TD),
					'gamma' 			=> esc_html__('Gamma', MELA_TD),
					'mini' 				=> esc_html__('Mini', MELA_TD),
					'numbered' 			=> esc_html__('Numbered', MELA_TD),
				],
				'default' => 'alpha',

			]
		);

		$this->add_control(
			'cs_item_ratio',
			[
				'label' => esc_html__('Item Ratio', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'landscape' 			=> esc_html__('Landscape', MELA_TD),
					'portrait' 				=> esc_html__('Portrait', MELA_TD),
					'square' 				=> esc_html__('Square', MELA_TD),
				],
				'default' => 'portrait',
			]
		);

		$this->add_control(
			'cs_box_shadow_alpha',
			[
				'label' => __('Box Shadow for Active Item', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cs_main_layout' => 'alpha'
				]
			]
		);

		$this->add_control(
			'cs_box_shadow_beta',
			[
				'label' => __('Box Shadow Item on hover', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
				'condition' => [
					'cs_main_layout' => 'beta'
				]
			]
		);
		$this->add_control(
			'cs_title_bg_color_gamma',
			[
				'label' => __('Title Holder Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.gamma .title_holder' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(10,44,174,.5)',
				'condition' => [
					'cs_main_layout' => 'gamma'
				]
			]
		);

		$this->add_control(
			'cs_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',
				'condition' => [
					'cs_main_layout!' => 'numbered'
				]

			]
		);
		// all navigation color without NUMBERED
		$this->add_control(
			'cs_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_carousel_square[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_carousel_square[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_carousel_square[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_carousel_square.numbered .numbered_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .numbered_control span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cs_main_layout!' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cs_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square .owl_control > div' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .n_prev:after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .n_prev > span:after, {{WRAPPER}} .modulify_carousel_square.numbered .n_next > span:after' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.1)',
				'condition' => [
					'cs_main_layout!' => 'numbered'
				]
			]
		);
		// navigation color ONLY for NUMBERED
		$this->add_control(
			'cs_numbered_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.numbered .numbered_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .numbered_control span.b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cs_main_layout' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cs_numbered_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.numbered .n_prev:after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .n_prev > span:after, {{WRAPPER}} .modulify_carousel_square.numbered .n_next > span:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#222',
				'condition' => [
					'cs_main_layout' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cs_numbered_number_color',
			[
				'label' => __('Numbers Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.numbered .number_holder span' => 'color: {{VALUE}};',
				],
				'default' => '#333',
				'condition' => [
					'cs_main_layout' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cs_numbered_line_color',
			[
				'label' => __('Line Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.numbered .item:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#baafaf',
				'condition' => [
					'cs_main_layout' => 'numbered'
				]
			]
		);
		$this->add_control(
			'cs_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cs_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_square .item .title_holder p',
				'condition' => [
					'cs_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'cs_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.alpha .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.beta .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.gamma .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cs_category_show' => 'yes',
					'cs_main_layout' => array('alpha', 'beta', 'gamma'),
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cs_mininum_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.mini .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .title_holder p' => 'color: {{VALUE}};',
				],
				'condition' => [
					'cs_category_show' => 'yes',
					'cs_main_layout' => array('mini', 'numbered'),
				],
				'default' => '#111',
			]
		);
		$this->add_control(
			'cs_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cs_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_square .item .title_holder h3 a',
			]
		);

		$this->add_control(
			'cs_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.alpha .item .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.beta .item .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.gamma .item .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#eee',
				'condition' => [
					'cs_main_layout' => array('alpha', 'beta', 'gamma'),
				],
			]
		);

		$this->add_control(
			'cs_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.alpha .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.beta .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.gamma .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
				'condition' => [
					'cs_main_layout' => array('alpha', 'beta', 'gamma'),
				],
			]

		);

		// for mini & numbered
		$this->add_control(
			'cs_mininum_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.mini .item .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .item .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#111',
				'condition' => [
					'cs_main_layout' => array('mini', 'numbered'),
				],
			]
		);

		$this->add_control(
			'cs_mininum_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_square.mini .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_square.numbered .item .title_holder h3 a:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
				'condition' => [
					'cs_main_layout' => array('mini', 'numbered', 'gamma'),
				],
			]

		);

		$this->end_controls_section();

		/************************************************************************/
		/************************ CAROUSEL WITH CONTENT ************************/
		/**********************************************************************/
		$this->start_controls_section(
			'carousel_with_content_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('carousel_with_c')
				]
			]
		);

		$this->add_control(
			'cwc_content_part',
			[
				'label' => __('Content Part', MELA_TD),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'cwc_fslide_title',
			[
				'label'       => __('Title', MELA_TD),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => __('Type your title text here', MELA_TD),
				'default' 	   => __('Modern Photo Carousel', MELA_TD),
				'label_block' => true
			]
		);

		$this->add_control(
			'cwc_fslide_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_with_content .description h3' => 'color: {{VALUE}};',
				],
				'default' => '#ccc',
			]
		);

		$this->add_control(
			'cwc_fslide_desc',
			[
				'label'   => __('Description', MELA_TD),
				'type'    => Controls_Manager::TEXTAREA,
				'placeholder' => __('Type your description text here', MELA_TD),
				'default' => __('Cras aliquam sagitadditis urna in vutsanili consectetur.Vivamus nuriaec lacus sed odio metus lobortis at.', MELA_TD),
			]
		);

		$this->add_control(
			'cwc_fslide_desc_color',
			[
				'label' => __('Description Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_with_content .description p' => 'color: {{VALUE}};',
				],
				'default' => '#999',
			]
		);

		$this->add_control(
			'cwc_fslide_sign',
			[
				'label' => __('Choose Your Sign', MELA_TD),
				'type' => Controls_Manager::MEDIA,
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
			]
		);

		$this->add_control(
			'cwc_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cwc_main_layout',
			[
				'label' => esc_html__('Layout', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'alpha' 			=> esc_html__('Alpha', MELA_TD),
					'beta' 				=> esc_html__('Beta', MELA_TD),
				],
				'default' => 'alpha',

			]
		);

		$this->add_control(
			'cwc_img_ratio',
			[
				'label' => esc_html__('Image Ratio', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'landscape' 		=> esc_html__('Landscape', MELA_TD),
					'portrait' 			=> esc_html__('Portrait', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'square',

			]
		);

		$this->add_control(
			'cwc_alpha_bg_type',
			[
				'label' => esc_html__('Title Background Overlay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'no_o' 				=> esc_html__('No Overlay', MELA_TD),
					'color_o' 			=> esc_html__('Color Overlay', MELA_TD),
					'gradient_o'		=> esc_html__('Gradient Overlay', MELA_TD),
				],
				'default' => 'color_o',
				'condition' => [
					'cwc_main_layout' => 'alpha'
				],
			]
		);

		$this->add_control(
			'cwc_alpha_bg_color',
			[
				'label' => __('Title Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .alpha .fn-sample-slides .title_holder' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'cwc_alpha_bg_type' => 'color_o'
				],
				'default' => 'rgba(0,0,0,0.4)',
			]
		);

		$this->add_control(
			'cwc_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'cwc_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cwc_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_with_content .title_holder p',
				'condition' => [
					'cwc_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'cwc_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder p a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder p a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'cwc_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'cwc_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'cwc_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_carousel_with_content .title_holder h3 a',
			]
		);

		$this->add_control(
			'cwc_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder h3:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'cwc_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .title_holder h3:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_carousel_with_content .beta_title_holder h3:hover:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#ccc',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/*************************** LIST JUSTIFIED ****************************/
		/**********************************************************************/
		$this->start_controls_section(
			'list_justified_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('list_just')
				]
			]
		);

		$this->add_control(
			'lj_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lj_img_height',
			[
				'label' => __('Image Height (px)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300,
				],
				'range' => [
					'px' => [
						'min' => 100,
						'max' => 700,
						'step' => 5,
					]
				],
				'size_units' => [''],
			]
		);

		$this->add_control(
			'lj_img_gutter',
			[
				'label' => __('Image Gutter (px)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 40,
						'step' => 1,
					]
				],
				'size_units' => [''],
			]
		);
		$this->add_control(
			'lj_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lj_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lj_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_justified_images .caption p',
				'condition' => [
					'lj_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'lj_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_justified_images .caption p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_justified_images .caption p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'lj_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'lj_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lj_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_justified_images .caption h3 a',
			]
		);

		$this->add_control(
			'lj_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_justified_images .caption h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_justified_images .caption h3' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'lj_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_justified_images .caption h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_justified_images .caption h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#eee',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/**************************** LIST MASONRY *****************************/
		/**********************************************************************/

		$this->start_controls_section(
			'list_masonry_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('list_masonry')
				]
			]
		);

		$this->add_control(
			'lm_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lm_main_layout',
			[
				'label' => esc_html__('Layout', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'masonry' 			=> esc_html__('Masonry', MELA_TD),
					'grid' 				=> esc_html__('Grid', MELA_TD),
				],
				'default' => 'masonry',

			]
		);

		$this->add_control(
			'lm_grid_ratio',
			[
				'label' => esc_html__('Grid Ratio', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'landscape' 			=> esc_html__('Landscape', MELA_TD),
					'portrait' 				=> esc_html__('Portrait', MELA_TD),
					'square' 				=> esc_html__('Square', MELA_TD),
				],
				'default' => 'square',
				'condition' => [
					'lm_main_layout' => 'grid'
				]
			]
		);

		$this->add_control(
			'lm_title_holder_pos',
			[
				'label' => esc_html__('Title Holder Position', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'inside' 			=> esc_html__('Inside', MELA_TD),
					'outside' 			=> esc_html__('Outside', MELA_TD),
					'hover' 			=> esc_html__('On Hover', MELA_TD),
				],
				'default' => 'outside',
			]
		);

		$this->add_control(
			'lm_title_holder_bg',
			[
				'label' => esc_html__('Title Holder Background', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'no_o' 					=> esc_html__('No Overlay', MELA_TD),
					'color_o' 				=> esc_html__('Color Overlay', MELA_TD),
					'gradient_o' 			=> esc_html__('Gradient Overlay', MELA_TD),
				],
				'default' => 'color_o',
				'condition' => [
					'lm_title_holder_pos' => 'inside'
				]
			]
		);

		$this->add_control(
			'lm_title_holder_bgcolor',
			[
				'label' => __('Title Holder Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap[data-title-holder-pos="inside"][data-title-holder-bg="color_o"] .title_holder' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'lm_title_holder_bg' => 'color_o',
				],
				'default' => '#333',
			]
		);
		$this->add_control(
			'lm_title_holder_bghovercolor',
			[
				'label' => __('Title Holder Background Color on Hover', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap[data-title-holder-pos="hover"] .title_holder' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'lm_title_holder_pos' => 'hover',
				],
				'default' => 'rgba(0,0,0,0.9)',
			]
		);

		$this->add_control(
			'lm_title_holder_animation',
			[
				'label' => esc_html__('Title Holder Animation', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'full' 					=> esc_html__('Full Overlay', MELA_TD),
					'boxed' 				=> esc_html__('Boxed Overlay', MELA_TD),
				],
				'default' => 'full',
				'condition' => [
					'lm_title_holder_pos' => 'hover'
				]
			]
		);

		$this->add_control(
			'lm_cols_number',
			[
				'label' => __('Columns Number', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 3,
				],
				'range' => [
					'px' => [
						'min' => 2,
						'max' => 6,
						'step' => 1,
					]
				],
				'size_units' => [''],
				'selectors' => [
					'{{WRAPPER}} .box' => 'width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'lm_cols_gutter',
			[
				'label' => __('Columns Gutter (px)', MELA_TD),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 10,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 40,
						'step' => 1,
					]
				],
				'size_units' => [''],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap ul.modulify_list li' => 'padding-left: {{SIZE}}{{UNIT}};margin-bottom: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .modulify_all_list_wrap ul.modulify_list' => 'margin-left: -{{SIZE}}{{UNIT}};',
				],
			]
		);


		$this->add_control(
			'lm_term_filter',
			[
				'label' => __('Filter By Category', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lm_typography_4',
				'label' => __('Filter Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_all_list_wrap ul.modulify_filter li a',
				'condition' => [
					'lm_term_filter' => 'yes'
				]
			]
		);
		$this->add_control(
			'lm_filter_color',
			[
				'label' => __('Filter Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap ul.modulify_filter li a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'lm_term_filter' => 'yes'
				],
				'default' => '#777',
			]
		);
		$this->add_control(
			'lm_filter_hover_color',
			[
				'label' => __('On Hover and Selected Filter Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap ul.modulify_filter li a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_all_list_wrap ul.modulify_filter li a.current' => 'color: {{VALUE}};',
				],
				'condition' => [
					'lm_term_filter' => 'yes'
				],
				'default' => '#000',
			]
		);
		$this->add_control(
			'lm_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'lm_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lm_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_all_list_wrap .title_holder p',
				'condition' => [
					'lm_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'lm_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'lm_category_show' => 'yes'
				],
				'default' => '#333',
			]
		);
		$this->add_control(
			'lm_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'lm_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_all_list_wrap .title_holder h3 a',
			]
		);

		$this->add_control(
			'lm_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder h3 a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder h3' => 'color: {{VALUE}};',
				],
				'default' => '#333',
			]
		);

		$this->add_control(
			'lm_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder h3 a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_all_list_wrap .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/*************************** SLIDER ALPHA ******************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_alpha_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_a')
				]
			]
		);


		$this->add_control(
			'sa_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sa_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'sa_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['sa_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'sa_title_bgcolor',
			[
				'label' => __('Title Holder Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .title_holder' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(14,14,14,0.9)',
			]
		);
		$this->add_control(
			'sa_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'square',

			]
		);

		$this->add_control(
			'sa_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_alpha[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_alpha[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_alpha[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sa_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => '#d91a46',
			]
		);
		$this->add_control(
			'sa_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sa_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sa_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_alpha .item .title_holder p',
				'condition' => [
					'sa_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'sa_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sa_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sa_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sa_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_alpha .title_holder h3 a',
			]
		);

		$this->add_control(
			'sa_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sa_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder h3:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sa_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sa_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'extendable',

			]
		);
		$this->add_control(
			'sa_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_alpha .title_holder .in > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_alpha .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sa_read_more_types!' => 'none',
				],
				'default' => '#fff',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/**************************** SLIDER BETA ******************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_beta_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_b')
				]
			]
		);
		$this->add_control(
			'sb_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sb_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'sb_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['sb_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'sb_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);
		$this->add_control(
			'sb_numbered_nav_color',
			[
				'label' => __('Numbered Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .swiper-slide.fn-numbered-pagination span.number' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .swiper-slide.fn-numbered-pagination span.line:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sb_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_beta[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_beta[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_beta[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sb_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.6)',
			]
		);
		$this->add_control(
			'sb_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sb_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sb_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_beta .item .title_holder p',
				'condition' => [
					'sb_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'sb_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sb_category_show' => 'yes'
				],
				'default' => '#d18750',
			]
		);
		$this->add_control(
			'sb_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sb_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_beta .title_holder h3 a',
			]
		);

		$this->add_control(
			'sb_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sb_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder h3:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sb_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sb_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'transform',

			]
		);
		$this->add_control(
			'sb_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_beta .title_holder .in > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_beta .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sb_read_more_types!' => 'none',
				],
				'default' => '#d18750',
			]
		);
		$this->end_controls_section();

		/************************************************************************/
		/**************************** SLIDER DELTA *****************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_delta_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_d')
				]
			]
		);
		$this->add_control(
			'sd_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sd_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'sd_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['sd_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'sd_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);
		$this->add_control(
			'sd_numbered_nav_color',
			[
				'label' => __('Numbered Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .swiper-slide.fn-numbered-pagination span.number' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .swiper-slide.fn-numbered-pagination span.line:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sd_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_delta[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_delta[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_delta[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sd_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.6)',
			]
		);
		$this->add_control(
			'sd_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sd_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sd_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_delta .item .title_holder p',
				'condition' => [
					'sd_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'sd_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sd_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sd_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sd_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_delta .title_holder h3 a',
			]
		);

		$this->add_control(
			'sd_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sd_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .title_holder h3:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sd_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);


		$this->add_control(
			'sd_read_more_bgcolor',
			[
				'label' => __('Read More Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .title_holder a.open_post' => 'background-color: {{VALUE}};',
				],
				'default' => '#df3838',
			]
		);

		$this->add_control(
			'sd_read_more_color',
			[
				'label' => __('Read More Arrow Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_delta .title_holder a.open_post .a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_delta .title_holder a.open_post .b' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/**************************** SLIDER EPSILON ***************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_epsilon_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_e')
				]
			]
		);
		$this->add_control(
			'se_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'se_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'se_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['se_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'se_title_holder_gradient',
			[
				'label' => esc_html__('Title Holder Skin', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'black' 			=> esc_html__('Black', MELA_TD),
					'white' 			=> esc_html__('White', MELA_TD),
				],
				'default' => 'white',

			]
		);
		$this->add_control(
			'se_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);
		$this->add_control(
			'se_numbered_nav_color',
			[
				'label' => __('Numbered Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .swiper-slide.fn-numbered-pagination span.number' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .swiper-slide.fn-numbered-pagination span.line:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'se_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_epsilon[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_epsilon[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_epsilon[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'se_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.9)',
			]
		);
		$this->add_control(
			'se_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'se_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'se_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_epsilon .item .title_holder p',
				'condition' => [
					'se_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'se_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'se_category_show' => 'yes'
				],
				'default' => '#0b0464',
			]
		);
		$this->add_control(
			'se_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'se_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_epsilon .title_holder h3 a',
			]
		);

		$this->add_control(
			'se_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#333',
			]
		);

		$this->add_control(
			'se_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#000',
			]
		);
		$this->add_control(
			'se_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'se_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'transform',

			]
		);
		$this->add_control(
			'se_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder .in > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_epsilon .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'se_read_more_types!' => 'none',
				],
				'default' => '#0b0464',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/**************************** SLIDER GAMMA *****************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_gamma_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_g')
				]
			]
		);
		$this->add_control(
			'sg_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sg_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'sg_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['sg_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'sg_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);
		$this->add_control(
			'sg_numbered_nav_color',
			[
				'label' => __('Numbered Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .swiper-slide.fn-numbered-pagination span.number' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .swiper-slide.fn-numbered-pagination span.line:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sg_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_gamma[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_gamma[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_gamma[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sg_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.6)',
			]
		);
		$this->add_control(
			'sg_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sg_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sg_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_gamma .item .title_holder p',
				'condition' => [
					'sg_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'sg_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sg_category_show' => 'yes'
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sg_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sg_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_gamma .title_holder h3 a',
			]
		);

		$this->add_control(
			'sg_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sg_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder h3:hover' => 'color: {{VALUE}};',
				],
			]
		);
		$this->add_control(
			'sg_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sg_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'extendable',

			]
		);
		$this->add_control(
			'sg_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_gamma .title_holder .in > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_gamma .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sg_read_more_types!' => 'none',
				],
				'default' => '#870724',
			]
		);

		$this->end_controls_section();

		/************************************************************************/
		/**************************** SLIDER ZETA *****************************/
		/**********************************************************************/
		$this->start_controls_section(
			'slider_zeta_design',
			[
				'label' => __('Design', MELA_TD),
				'condition' => [
					'fn_widget_layout' => array('slider_z')
				]
			]
		);
		$this->add_control(
			'sz_main_style',
			[
				'label' => __('Main Styles', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);
		$this->add_control(
			'sz_autoplay_switch',
			[
				'label' => esc_html__('Autoplay', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enabled' 				=> esc_html__('Enabled', MELA_TD),
					'disabled' 				=> esc_html__('Disabled', MELA_TD),
				],
				'default' => 'disabled',

			]
		);
		$this->add_control(
			'sz_autoplay_time',
			[
				'label'   => __('Autoplay Time in Milliseconds', MELA_TD),
				'type'    => Controls_Manager::NUMBER,
				'default' => 5000,
				'min'     => 500,
				'max'     => 20000,
				'step'    => 50,
				'condition' => ['sz_autoplay_switch' => 'enabled']
			]
		);
		$this->add_control(
			'sz_nav_types',
			[
				'label' => esc_html__('Navigation Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'round' 			=> esc_html__('Round', MELA_TD),
					'square' 			=> esc_html__('Square', MELA_TD),
				],
				'default' => 'round',

			]
		);
		$this->add_control(
			'sz_numbered_nav_color',
			[
				'label' => __('Numbered Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .swiper-slide.fn-numbered-pagination span.number' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .swiper-slide.fn-numbered-pagination span.line:after' => 'background-color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);

		$this->add_control(
			'sz_nav_color',
			[
				'label' => __('Navigation Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .owl_control span.a' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .owl_control span.b' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .owl_control > div:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .owl_control > div > span:hover:after' => 'border-top-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .owl_control > div > span:hover:before' => 'border-top-color: {{VALUE}};border-right-color: {{VALUE}};border-bottom-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta[data-nav-types="square"] .owl_control > div > span:before, {{WRAPPER}} .modulify_slider_zeta[data-nav-types="square"] .owl_control > div > span:after' => 'background: {{VALUE}}',
					'{{WRAPPER}} .modulify_slider_zeta[data-nav-types="square"] .owl_control > div > span .c:before, {{WRAPPER}} .modulify_slider_zeta[data-nav-types="square"] .owl_control > div > span .c:after' => 'background: {{VALUE}}',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sz_nav_bg_color',
			[
				'label' => __('Navigation Background Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .owl_control > div' => 'background-color: {{VALUE}};',
				],
				'default' => 'rgba(0,0,0,.5)',
			]
		);
		$this->add_control(
			'sz_category_style',
			[
				'label' => __('Category Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sz_category_show',
			[
				'label' => __('Category Show', MELA_TD),
				'type' => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'label_on' 	=> __('Show', MELA_TD),
				'label_off' => __('Hide', MELA_TD),
				'return_value' => 'yes',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sz_typography_2',
				'label' => __('Category Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_zeta .item .title_holder p',
				'condition' => [
					'sz_category_show' => 'yes'
				]
			]
		);
		$this->add_control(
			'sz_category_color',
			[
				'label' => __('Category Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .title_holder p' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder p a:hover' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sz_category_show' => 'yes'
				],
				'default' => '#33703d',
			]
		);
		$this->add_control(
			'sz_title_style',
			[
				'label' => __('Title Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'sz_typography_3',
				'label' => __('Title Typography', MELA_TD),
				'scheme' => Scheme_Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .modulify_slider_zeta .title_holder h3 a',
			]
		);

		$this->add_control(
			'sz_title_color',
			[
				'label' => __('Title Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .title_holder h3' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder h3 a' => 'color: {{VALUE}};',
				],
				'default' => '#ccc',
			]
		);

		$this->add_control(
			'sz_title_hover_color',
			[
				'label' => __('Title Hover Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .title_holder h3:hover a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder h3:hover' => 'color: {{VALUE}};',
				],
				'default' => '#fff',
			]
		);
		$this->add_control(
			'sz_read_more_style',
			[
				'label' => __('Read More Style', MELA_TD),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'sz_read_more_types',
			[
				'label' => esc_html__('Link Types', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'none' 				=> esc_html__('None', MELA_TD),
					'extendable' 		=> esc_html__('Extendable', MELA_TD),
					'transform' 		=> esc_html__('Transform', MELA_TD),
					'static' 			=> esc_html__('Static', MELA_TD),
				],
				'default' => 'static',

			]
		);
		$this->add_control(
			'sz_read_more_color',
			[
				'label' => __('Read More Color', MELA_TD),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_1,
				],
				'selectors' => [
					'{{WRAPPER}} .modulify_slider_zeta .title_holder .in > span a' => 'color: {{VALUE}};border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder a.read_more:after' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder a.read_more .arrow:before' => 'color: {{VALUE}};',
					'{{WRAPPER}} .modulify_slider_zeta .title_holder a.read_more .arrow:after' => 'border-bottom-color: {{VALUE}};',
				],
				'condition' => [
					'sz_read_more_types!' => 'none',
				],
				'default' => '#ccc',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'mdlfy_lightbox_s',
			[
				'label' => __('Lightbox', MELA_TD),
				'condition' => [
					'fn_widget_layout!' => 'list_just'
				]
			]
		);

		$this->add_control(
			'mdlfy_lightbox',
			[
				'label' => esc_html__('Lightbox to Images', MELA_TD),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'enable' 			=> esc_html__('Enable', MELA_TD),
					'disable' 			=> esc_html__('Disable', MELA_TD),
				],
				'default' => 'disable',

			]
		);
		$this->end_controls_section();


		$this->start_controls_section(
			'mdlfy_read_more_section',
			[
				'label' => __('Read More', MELA_TD),
			]
		);
		$this->add_control(
			'mdlfy_read_more_text',
			[
				'label'       	=> __('Read More Text', 'frenify-core'),
				'type'        	=> Controls_Manager::TEXT,
				'placeholder' 	=> __('Read More Text Here...', 'frenify-core'),
				'default' 	    => __('Read More', 'frenify-core'),
				'label_block' 	=> true,
			]
		);
		$this->end_controls_section();
	}




	protected function render()
	{
		$title = get_bloginfo('name');

		if (empty($title))
			return;

		$settings = $this->get_settings();

		// FILTERS
		$fn_widget_layout 			= $settings['fn_widget_layout'];
		// LIGHTBOX
		$mdlfy_lightbox				= $settings['mdlfy_lightbox'];
		// REPEATER
		$module_items				= $settings['module_items'];
		// READ MORE TEXT
		$mdlfy_read_more_text		= $settings['mdlfy_read_more_text'];

		if ($fn_widget_layout === 'carousel_circle') {
			$cc_category_show 			= $settings['cc_category_show'];
			$cc_main_layout				= $settings['cc_main_layout'];
			$cc_box_shadow_gamma		= $settings['cc_box_shadow_gamma'];
			$cc_box_shadow_numbered		= $settings['cc_box_shadow_numbered'];
			$cc_box_shadow_numbered2	= $settings['cc_box_shadow_numbered2'];
			$cc_beta_bg_line			= $settings['cc_beta_bg_line'];
			$cc_numbered_img_thumb		= $settings['cc_numbered_img_thumb'];
			$cc_numbered_hover_thumb	= $settings['cc_numbered_hover_thumb'];

			// before repeater
			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="circle_carousel_version"><div class="modulify_carousel_circle ' . $cc_main_layout . '" data-category-show="' . $cc_category_show . '" data-box-shadow-gamma="' . $cc_box_shadow_gamma . '" data-box-shadow-numbered="' . $cc_box_shadow_numbered . '" data-box-shadow-numbered2="' . $cc_box_shadow_numbered2 . '" data-bg-line="' . $cc_beta_bg_line . '" data-numbered-img-thumb="' . $cc_numbered_img_thumb . '" data-numbered-hover-thumb="' . $cc_numbered_hover_thumb . '">';


			$img_slider = $content_controller = $content_slider = '';
			$arrow 		= '<span class="a"></span><span class="b"></span>';
			$fn_prev 	= '<div class="fn_prev">' . $arrow . '</div>';
			$fn_next 	= '<div class="fn_next">' . $arrow . '</div>';
			$real_img	= '<img src="' . MODULIFY_PLUGIN_URL . 'assets/img/thumb-square.jpg" alt="" />';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			if ($cc_main_layout === 'alpha') {

				// ALPHA VERSION
				$img_slider	.= '<div class="img-slider ' . $parentLight . '"><div class="swiper-wrapper">';
				$content_controller .= '<div class="alpha_controller">' . $fn_prev . $fn_next . '</div>';
				$content_slider .= '<div class="content-slider">' . $content_controller . '<div class="swiper-wrapper">';
			} else if ($cc_main_layout === 'beta') {

				// BETA VERSION
				$content_controller .= '<div class="beta_controller">' . $fn_prev . $fn_next . '</div>';
				$img_slider	.= '<div class="img_slider_wrap ' . $parentLight . '"><div class="inner"><div class="img-slider">' . $content_controller . '<div class="swiper-wrapper">';
				$content_slider .= '<div class="content-slider"><div class="swiper-wrapper">';
			} else if ($cc_main_layout === 'gamma') {

				// GAMMA VERSION
				$content_controller .= '<div class="gamma_controller">' . $fn_prev . $fn_next . '</div>';
				$img_slider	.= '<div class="img-slider ' . $parentLight . '">' . $content_controller . '<div class="swiper-wrapper">';
				$content_slider .= '<div class="content-slider"><div class="swiper-wrapper">';
			} else if ($cc_main_layout === 'numbered') {

				// NUMBERED VERSION
				$content_controller .= '<div class="numbered_controller">' . $fn_prev . $fn_next . '</div>';
				$img_slider	.= '<div class="img_slider_wrap ' . $parentLight . '"><div class="img-slider"><div class="img_slider_in">' . $content_controller . '<div class="swiper-wrapper">';
				$content_slider .= '<div class="content-slider"><div class="swiper-wrapper">';
			} else if ($cc_main_layout === 'numbered2') {

				// NUMBERED2 VERSION
				$content_controller .= '<div class="numbered_controller">' . $fn_prev . $fn_next . '</div>';
				$img_slider	.= '<div class="img_slider_wrap ' . $parentLight . '"><div class="img-slider"><div class="img_slider_in">' . $content_controller . '<div class="swiper-wrapper">';
				$content_slider .= '<div class="content-slider"><div class="swiper-wrapper">';
			}

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($post_permalink !== '') {
						$linkStart		= '<a href="' . $post_permalink . '">';
						$linkEnd		= '</a>';
					} else {
						$linkStart 		= $linkEnd = '';
					}

					$image_holder		= '<div class="img_holder" data-bg-img="' . $image . '"></div>';
					$title_holder		= '<div class="title_holder"><p><span>' . $post_cats . '</span></p><h3>' . $linkStart . $post_title . $linkEnd . '</h3></div>';

					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					$count = $key + 1;
					if ($count < 10) {
						$count2 = '0' . $count;
					} else {
						$count2 = $count;
					}
					$spanCount = '<span class="count">' . $count2 . '</span>';

					if ($cc_main_layout === 'alpha') {

						$content_slider .= '<div class="swiper-slide">' . $title_holder . '</div>';
						$img_slider .= '<div class="swiper-slide"><div class="item_holder ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="item">' . $image_holder . '</div></div></div>';
					} else if ($cc_main_layout === 'beta') {

						$content_slider .= '<div class="swiper-slide">' . $title_holder . '</div>';
						$img_slider .= '<div class="swiper-slide"><div class="item_holder ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="item">' . $real_img . $image_holder . '</div></div></div>';
					} else if ($cc_main_layout === 'gamma') {

						$content_slider .= '<div class="swiper-slide">' . $title_holder . '</div>';
						$img_slider .= '<div class="swiper-slide"><div class="item_holder ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="item">' . $image_holder . '</div></div></div>';
					} else if ($cc_main_layout === 'numbered') {

						$content_slider .= '<div class="swiper-slide">' . $title_holder . '</div>';
						$img_slider .= '<div class="swiper-slide">' . $real_img . '<div class="item_holder ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="item">' . $image_holder . $spanCount . '</div></div></div>';
					} else if ($cc_main_layout === 'numbered2') {

						$content_slider .= '<div class="swiper-slide">' . $title_holder . '</div>';
						$img_slider .= '<div class="swiper-slide">' . $real_img . '<div class="item_holder ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="item">' . $image_holder . $spanCount . '</div></div></div>';
					}
				}
			}

			// after repeater
			if ($cc_main_layout === 'alpha') {
				$img_slider .= '</div></div>';
				$content_slider .= '</div></div>';
			} else if ($cc_main_layout === 'beta') {
				$img_slider .= '</div></div></div></div>';
				$content_slider .= '</div></div>';
			} else if ($cc_main_layout === 'gamma') {
				$img_slider .= '</div></div>';
				$content_slider .= '</div></div>';
			} else if ($cc_main_layout === 'numbered') {
				$img_slider .= '</div></div></div></div>';
				$content_slider .= '</div></div>';
			} else if ($cc_main_layout === 'numbered2') {
				$img_slider .= '</div></div></div></div>';
				$content_slider .= '</div></div>';
			}

			$html .= $img_slider . $content_slider;
			$html .= '</div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();


			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'carousel_full_a') {
			$cfa_category_show 		= $settings['cfa_category_show'];
			$cfa_link_types 		= $settings['cfa_read_more_types'];
			$cfa_nav_types 			= $settings['cfa_nav_types'];
			$cfa_title_holder_type 	= $settings['cfa_title_holder_type'];
			$cfa_columns_number 	= $settings['cfa_columns_number']['size'];
			$cfa_autoplay_switch	= $settings['cfa_autoplay_switch'];
			$cfa_autoplay_time 		= $settings['cfa_autoplay_time'];

			// before repeater
			if ($cfa_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="full_carousel_version"><div class="modulify_carousel_full_alpha ' . $parentLight . '" data-columns-number="' . $cfa_columns_number . '" data-category-show="' . $cfa_category_show . '" data-link-types="' . $cfa_link_types . '" data-nav-types="' . $cfa_nav_types . '" data-title-holder-type="' . $cfa_title_holder_type . '" data-autoplay-switch="' . $cfa_autoplay_switch . '" data-autoplay-time="' . $cfa_autoplay_time . '">' . $owl_control . '<div class="owl-carousel">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];

					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($cfa_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="item ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="img_holder" data-bg-img="' . $image . '"></div><div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div>';
					$html .= '';
				}
			}

			// after repeater
			$html .= '</div></div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'carousel_full_b') {
			$cfb_category_show 			= $settings['cfb_category_show'];
			$cfb_nav_types 				= $settings['cfb_nav_types'];
			$cfb_title_holder_type 		= $settings['cfb_title_holder_type'];
			$cfb_title_holder_position 	= $settings['cfb_title_holder_position'];
			$cfb_columns_number 		= $settings['cfb_columns_number']['size'];
			$cfb_autoplay_switch		= $settings['cfb_autoplay_switch'];
			$cfb_autoplay_time 			= $settings['cfb_autoplay_time'];

			// before repeater
			if ($cfb_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();

			$html .= '<div class="full_carousel_version"><div class="modulify_carousel_full_beta modulify_fn_miniboxes ' . $parentLight . '" data-columns-number="' . $cfb_columns_number . '" data-category-show="' . $cfb_category_show . '" data-nav-types="' . $cfb_nav_types . '" data-title-holder-animation="' . $cfb_title_holder_type . '" data-title-holder-position="' . $cfb_title_holder_position . '" data-autoplay-switch="' . $cfb_autoplay_switch . '" data-autoplay-time="' . $cfb_autoplay_time . '">' . $owl_control . '<div class="owl-carousel">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					$html .= '<div class="item ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="img_holder" data-bg-img="' . $image . '"></div><div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3></div></div>';
				}
			}

			// after repeater
			$html .= '</div></div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'carousel_full_i') {
			$cfi_category_show 		= $settings['cfi_category_show'];
			$cfi_link_types 		= $settings['cfi_read_more_types'];
			$cfi_nav_types 			= $settings['cfi_nav_types'];
			$cfi_title_holder_type 	= $settings['cfi_title_holder_type'];
			$cfi_columns_number 	= $settings['cfi_columns_number']['size'];
			$cfi_autoplay_switch	= $settings['cfi_autoplay_switch'];
			$cfi_autoplay_time 		= $settings['cfi_autoplay_time'];

			// before repeater
			if ($cfi_nav_types == 'square') {
				$spanCount = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="full_carousel_version"><div class="modulify_carousel_full_interactive ' . $parentLight . '" data-columns-number="' . $cfi_columns_number . '" data-category-show="' . $cfi_category_show . '" data-link-types="' . $cfi_link_types . '" data-nav-types="' . $cfi_nav_types . '" data-autoplay-switch="' . $cfi_autoplay_switch . '" data-autoplay-time="' . $cfi_autoplay_time . '" data-title-holder-type="' . $cfi_title_holder_type . '">' . $owl_control . '<div class="owl-carousel">';

			$loop_image = '<div class="interactive_overlay">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];

					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($cfi_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					if ($key === 0) {
						$hovered = 'hovered';
					} else {
						$hovered = '';
					}

					$loop_image .= '<div class="fn_interactive' . $key . ' ' . $hovered . '" data-bg-img="' . $image . '"></div>';

					$html .= '<div class="item ' . $childLight . ' ' . $hovered . '" data-interactive="fn_interactive' . $key . '" data-src="' . $image . '">' . $lightboxImg . '<div class="img_holder" data-bg-img="' . $image . '"></div><div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div>';
				}
			}


			// after repeater
			$loop_image .= '</div>';
			$html .= '</div>' . $loop_image . '</div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'carousel_square') {
			$cs_category_show 		= $settings['cs_category_show'];
			$cs_nav_types 			= $settings['cs_nav_types'];
			$cs_main_layout			= $settings['cs_main_layout'];
			$cs_box_shadow_alpha	= $settings['cs_box_shadow_alpha'];
			$cs_box_shadow_beta		= $settings['cs_box_shadow_beta'];
			$cs_item_ratio			= $settings['cs_item_ratio'];

			// before repeater
			if ($cs_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			if ($cs_main_layout === 'numbered') {
				$owl_control = '<div class="numbered_control"><div class="n_prev">' . $arrow . '</div><div class="n_next">' . $arrow . '</div></div>';
			} else {
				$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';
			}




			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="square_carousel_version"><div class="modulify_carousel_square ' . $parentLight . ' ' . $cs_main_layout . '" data-category-show="' . $cs_category_show . '" data-nav-types="' . $cs_nav_types . '" data-box-shadow-alpha="' . $cs_box_shadow_alpha . '" data-box-shadow-beta="' . $cs_box_shadow_beta . '"><div class="inner">' . $owl_control . '<div class="owl-carousel">';

			$image_relative = '<img src="' . MODULIFY_PLUGIN_URL . 'assets/img/thumb-' . $cs_item_ratio . '.jpg' . '" alt="" />';

			if ($cs_main_layout === 'mini' || $cs_main_layout === 'numbered') {
				$mini_opener = '<div class="mini_img_holder">';
				$mini_closer = '</div>';
			} else {
				$mini_opener = $mini_closer = '';
			}

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					$image_holder		= '<div class="img_holder" data-bg-img="' . $image . '"></div>';
					$title_holder		= '<div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3></div>';

					$keyPro = $key + 1;
					if ($keyPro > 9) {
						$count = $keyPro;
					} else {
						$count = '0' . $keyPro;
					}
					if ($cs_main_layout === 'numbered') {
						$number_holder = '<div class="number_holder"><span>' . $count . '</span></div>';
					} else {
						$number_holder = '';
					}
					$html .= '<div><div class="item ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . $number_holder . $mini_opener . $image_relative . $image_holder . $mini_closer . $title_holder . '</div></div>';
				}
			}


			// after repeater
			$html .= '</div></div></div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'carousel_with_c') {
			$cwc_category_show 			= $settings['cwc_category_show'];
			$cwc_main_layout			= $settings['cwc_main_layout'];
			$cwc_img_ratio				= $settings['cwc_img_ratio'];
			$cwc_fslide_title			= $settings['cwc_fslide_title'];
			$cwc_fslide_desc			= $settings['cwc_fslide_desc'];
			$cwc_fslide_sign			= $settings['cwc_fslide_sign']['url'];
			$cwc_alpha_bg_type			= $settings['cwc_alpha_bg_type'];

			// before repeater
			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			// FOR ALL VERSIONS
			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="with_content_carousel_version"><div class="modulify_carousel_with_content ' . $parentLight . ' ' . $cwc_main_layout . '" data-category-show="' . $cwc_category_show . '" data-alpha-bg-type="' . $cwc_alpha_bg_type . '">';


			$img_slider = $content_controller = '';
			$arrow 		= '<span class="a"></span><span class="b"></span>';
			$fn_prev 	= '<div class="fn_prev">' . $arrow . '</div>';
			$fn_next 	= '<div class="fn_next">' . $arrow . '</div>';
			$real_img	= '<img src="' . MODULIFY_PLUGIN_URL . 'assets/img/thumb-' . $cwc_img_ratio . '.jpg" alt="" />';
			$real_img2	= '<img src="' . MODULIFY_PLUGIN_URL . 'assets/img/thumb-square.jpg" alt="" />';



			// ALPHA VERSION
			$content_controller .= '<div class="alpha_controller">' . $fn_prev . $fn_next . '</div>';
			$img_slider	.= '<div class="img-slider">' . $content_controller . '<div class="swiper-wrapper">';

			// FIRST SLIDE
			$fslideTitle		= '<h3>' . $cwc_fslide_title . '</h3>';
			$fslideDesc			= '<p>' . $cwc_fslide_desc . '</p>';
			$fslideSign			= '<img class="fn_sign" src="' . $cwc_fslide_sign . '" alt="" />';
			$content_holder		= '<div class="desc_wrap"><div class="desc_holder"><div class="description">' . $fslideTitle . $fslideDesc . $fslideSign . '</div></div></div>';
			$first_slide		= '<div class="swiper-slide fn-swiper-slides fn-width-auto fn-first-slide"><div class="item_wrap"><div class="item_holder"><div class="item"><div class="img_holder">' . $real_img2 . '</div>' . $content_holder . '</div></div></div></div>';

			$img_slider 		.= $first_slide;

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					$image_holder		= '<div class="abs_img" data-bg-img="' . $image . '"></div>';
					if ($cwc_main_layout === 'beta') {
						$title_holder		= '<div class="beta_title_holder"><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><p><span>' . $post_cats . '</span></p></div>';
					} else {
						$title_holder		= '<div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3></div>';
					}


					$img_slider .= '<div class="swiper-slide fn-width-auto fn-swiper-slides fn-sample-slides"><div class="item_wrap"><div class="item_holder"><div class="item ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . '<div class="img_holder">' . $real_img . $image_holder . '</div>' . $title_holder . '</div></div></div></div>';
				}
			}


			// after repeater
			$img_slider .= '<div class="swiper-slide fn-width-auto fn-swiper-slides fn-last-slide"></div>';
			$img_slider .= '</div></div>';


			$html .= $img_slider;
			$html .= '</div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'list_just') {
			$lj_category_show 			= $settings['lj_category_show'];
			$lj_img_height				= $settings['lj_img_height']['size'];
			$lj_img_gutter				= $settings['lj_img_gutter']['size'];

			// before repeater
			// FOR ALL VERSIONS
			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="list_version"><div class="modulify_justified_images" data-category-show="' . $lj_category_show . '" data-img-gutter="' . $lj_img_gutter . '" data-img-height="' . $lj_img_height . '">';



			$newItem = '';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					$title_holder		= '<div class="caption"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3></div>';
					$image_holder = '<img src="' . $image . '" alt="" />';
					$newItem .= '<div><a href="' . $post_permalink . '">' . $image_holder . $title_holder . '</a></div>';
				}
			}
			// after repeater
			$html .= $newItem;
			$html .= '</div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();
			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'list_masonry') {
			$lm_category_show 			= $settings['lm_category_show'];
			$lm_main_layout				= $settings['lm_main_layout'];
			$lm_cols_number				= $settings['lm_cols_number']['size'];
			$lm_term_filter				= $settings['lm_term_filter'];
			$lm_title_holder_pos		= $settings['lm_title_holder_pos'];
			$lm_title_holder_bg			= $settings['lm_title_holder_bg'];
			$lm_title_holder_animation	= $settings['lm_title_holder_animation'];
			$lm_grid_ratio				= $settings['lm_grid_ratio'];

			// before repeater
			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			// FOR ALL VERSIONS
			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="list_version"><div class="modulify_all_list_wrap ' . $parentLight . ' ' . $lm_main_layout . '" data-category-show="' . $lm_category_show . '" data-cols-number="' . $lm_cols_number . '" data-term-filter="' . $lm_term_filter . '" data-title-holder-bg="' . $lm_title_holder_bg . '" data-title-holder-pos="' . $lm_title_holder_pos . '" data-title-holder-animation="' . $lm_title_holder_animation . '" data-grid-ratio="' . $lm_grid_ratio . '">';


			$real_img	= '<img src="' . MODULIFY_PLUGIN_URL . 'assets/img/thumb-' . $lm_grid_ratio . '.jpg" alt="" />';

			$fn_filter 	= '<ul class="modulify_filter">';
			$fn_filter 	.= '<li><a href="#" class="current" data-filter="*">All</a></li>';

			$fn_list	= '<ul class="modulify_list">';

			$image_holder = '';
			$post_cats3 = '';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$post_cats2 		= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					$title_holder		= '<div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3></div></div></div>';

					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}


					if ($lm_main_layout == 'masonry') {
						$image_holder = '<img src="' . $image . '" alt="" />';
					} else if ($lm_main_layout == 'grid') {
						$image_holder = '<div class="img_holder">' . $real_img . '<div class="abs_img" data-bg-img="' . $image . '"></div></div>';
					}
					$item = '<div class="item ' . $childLight . '" data-src="' . $image . '">' . $lightboxImg . $image_holder . $title_holder . '</div>';
					$fn_list .= '<li class="' . $post_cats2 . '">' . $item . '</li>';
					$post_cats3 .= $post_cats2 . ' ';
				}
			}
			// after repeater
			$removedLastCharacter 	= rtrim($post_cats3, ", "); 				// remove last character from string
			$stringToArray 			= explode(" ", $removedLastCharacter);	// string to array
			$removeUniqueElements 	= array_unique($stringToArray);			// remove unique elements from array

			foreach ($removeUniqueElements as $cat) {
				$fn_filter 	.= '<li><a href="#" data-filter=".' . $cat . '">' . $cat . '</a></li>';
			}

			$fn_list 	.= '</ul>';
			$fn_filter 	.= '</ul>';
			$fn_filter 	.= '<div class="fn_clearfix"></div>';

			$html .= $fn_filter . $fn_list;
			$html .= '</div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();
			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_a') {
			$sa_category_show 		= $settings['sa_category_show'];
			$sa_link_types 			= $settings['sa_read_more_types'];
			$sa_nav_types 			= $settings['sa_nav_types'];
			$sa_autoplay_switch		= $settings['sa_autoplay_switch'];
			$sa_autoplay_time 		= $settings['sa_autoplay_time'];

			// before repeater
			if ($sa_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_alpha ' . $parentLight . '" data-category-show="' . $sa_category_show . '" data-link-types="' . $sa_link_types . '" data-nav-types="' . $sa_nav_types . '" data-autoplay-switch="' . $sa_autoplay_switch . '" data-autoplay-time="' . $sa_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($sa_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div></div></div></div>';
				}
			}
			// after repeater
			$html .= '</div></div></div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_b') {
			$sb_category_show 		= $settings['sb_category_show'];
			$sb_link_types 			= $settings['sb_read_more_types'];
			$sb_nav_types 			= $settings['sb_nav_types'];
			$sb_autoplay_switch		= $settings['sb_autoplay_switch'];
			$sb_autoplay_time 		= $settings['sb_autoplay_time'];

			// before repeater
			if ($sb_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_beta ' . $parentLight . '" data-category-show="' . $sb_category_show . '" data-link-types="' . $sb_link_types . '" data-nav-types="' . $sb_nav_types . '" data-autoplay-switch="' . $sb_autoplay_switch . '" data-autoplay-time="' . $sb_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';


			$paginationNumber = '<div class="beta_pagination"><div class="swiper-wrapper">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($sb_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div></div></div></div>';
					if ($key + 1 > 9) {
						$keyy = $key + 1;
					} else {
						$keyy = '0' . ($key + 1);
					}
					$paginationNumber .= '<div class="swiper-slide fn-numbered-pagination"><span class="line"></span><span class="number">' . $keyy . '</span></div>';
				}
			}
			// after repeater
			$paginationNumber .= '</div></div>';
			$html .= '</div></div>' . $paginationNumber . '</div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_d') {
			$sd_category_show 		= $settings['sd_category_show'];
			$sd_nav_types 			= $settings['sd_nav_types'];
			$sd_autoplay_switch	= $settings['sd_autoplay_switch'];
			$sd_autoplay_time 		= $settings['sd_autoplay_time'];

			// before repeater

			if ($sd_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow				= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 		= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_delta ' . $parentLight . '" data-category-show="' . $sd_category_show . '" data-nav-types="' . $sd_nav_types . '" data-autoplay-switch="' . $sd_autoplay_switch . '" data-autoplay-time="' . $sd_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';


			$paginationNumber = '<div class="delta_pagination"><div class="swiper-wrapper">';
			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><a class="open_post" href="' . $post_permalink . '"><span class="a"></span><span class="b"></span></a></div></div></div>';

					$paginationNumber .= '<div class="swiper-slide fn-numbered-pagination"><span class="line"></span></div>';
				}
			}
			// after repeater
			$paginationNumber .= '</div></div>';
			$html .= '</div></div>' . $paginationNumber . '</div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_e') {
			$se_category_show 			= $settings['se_category_show'];
			$se_link_types 				= $settings['se_read_more_types'];
			$se_nav_types 				= $settings['se_nav_types'];
			$se_title_holder_gradient 	= $settings['se_title_holder_gradient'];
			$se_autoplay_switch			= $settings['se_autoplay_switch'];
			$se_autoplay_time 			= $settings['se_autoplay_time'];

			// before repeater
			if ($se_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_epsilon ' . $parentLight . '" data-category-show="' . $se_category_show . '" data-link-types="' . $se_link_types . '" data-nav-types="' . $se_nav_types . '" data-title-gradient="' . $se_title_holder_gradient . '" data-autoplay-switch="' . $se_autoplay_switch . '" data-autoplay-time="' . $se_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';


			$paginationNumber = '<div class="epsilon_pagination"><div class="swiper-wrapper">';

			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($se_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div></div></div></div>';
					if ($key + 1 > 9) {
						$keyy = $key + 1;
					} else {
						$keyy = '0' . ($key + 1);
					}
					$paginationNumber .= '<div class="swiper-slide fn-numbered-pagination"><span class="line"></span><span class="number">' . $keyy . '</span></div>';
				}
			}
			// after repeater
			$paginationNumber .= '</div></div>';
			$html .= '</div></div>' . $paginationNumber . '</div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_g') {
			$sg_category_show 		= $settings['sg_category_show'];
			$sg_link_types 			= $settings['sg_read_more_types'];
			$sg_nav_types 			= $settings['sg_nav_types'];
			$sg_autoplay_switch		= $settings['sg_autoplay_switch'];
			$sg_autoplay_time 		= $settings['sg_autoplay_time'];

			// before repeater

			if ($sg_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_gamma ' . $parentLight . '" data-category-show="' . $sg_category_show . '" data-link-types="' . $sg_link_types . '" data-nav-types="' . $sg_nav_types . '" data-autoplay-switch="' . $sg_autoplay_switch . '" data-autoplay-time="' . $sg_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';


			$paginationNumber = '<div class="gamma_pagination"><div class="swiper-wrapper">';
			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($sg_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div></div></div></div>';
					if ($key + 1 > 9) {
						$keyy = $key + 1;
					} else {
						$keyy = '0' . ($key + 1);
					}
					$paginationNumber .= '<div class="swiper-slide fn-numbered-pagination"><span class="line"></span><span class="number">' . $keyy . '</span></div>';
				}
			}
			// after repeater
			$paginationNumber .= '</div></div>';
			$html .= '</div></div>' . $paginationNumber . '</div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		} else if ($fn_widget_layout === 'slider_z') {
			$sz_category_show 		= $settings['sz_category_show'];
			$sz_link_types 			= $settings['sz_read_more_types'];
			$sz_nav_types 			= $settings['sz_nav_types'];
			$sz_autoplay_switch		= $settings['sz_autoplay_switch'];
			$sz_autoplay_time 		= $settings['sz_autoplay_time'];

			// before repeater
			if ($sz_nav_types == 'square') {
				$spanc = '<span class="c"></span>';
			} else {
				$spanc = '';
			}
			$arrow			= '<span><span class="a"></span><span class="b"></span>' . $spanc . '</span>';
			$owl_control 	= '<div class="owl_control"><div class="fn_prev">' . $arrow . '</div><div class="fn_next">' . $arrow . '</div></div>';

			if ($mdlfy_lightbox === 'enable') {
				$parentLight = 'modulify_fn_lightbox';
				$childLight	 = 'lightbox';
			} else {
				$parentLight = '';
				$childLight	 = '';
			}

			$html = Modulify_Helper::modulify_open_wrap();
			$html .= '<div class="slider_version"><div class="modulify_slider_zeta ' . $parentLight . '" data-category-show="' . $sz_category_show . '" data-link-types="' . $sz_link_types . '" data-nav-types="' . $sz_nav_types . '" data-autoplay-switch="' . $sz_autoplay_switch . '" data-autoplay-time="' . $sz_autoplay_time . '">' . $owl_control . '<div class="swiper-wrapper">';


			$paginationNumber = '<div class="zeta_pagination"><div class="swiper-wrapper">';


			// repeater
			if ($module_items) {

				foreach ($module_items as $key => $item) {
					$post_permalink 	= $item['module_url'];
					$post_cats 			= $item['module_categories'];
					$image 				= $item['module_image']['url'];
					$post_title 		= $item['module_title'];
					if ($mdlfy_lightbox === 'enable') {
						$imageURLThumb 	= $image;
						$lightboxImg	= '<img class="mdlfy_light_img" src="' . $imageURLThumb . '" alt="" />';
					} else {
						$lightboxImg 	= '';
						$imageURLThumb 	= '';
					}

					if ($sz_link_types == 'transform') {
						$read_more = '<a class="read_more" href="' . $post_permalink . '">' . $mdlfy_read_more_text . '<span class="arrow"></span></a>';
					} else {
						$read_more = '<a class="simple_read_more" href="' . $post_permalink . '"><span>' . $mdlfy_read_more_text . '</span><i class="xcon-right-open"></i></a>';
					}
					$html .= '<div class="swiper-slide"><div class="item"><div class="img_holder ' . $childLight . '" data-bg-img="' . $image . '" data-src="' . $image . '">' . $lightboxImg . '</div><div class="title_holder"><div class="inner"><div class="in"><p><span>' . $post_cats . '</span></p><h3><a href="' . $post_permalink . '">' . $post_title . '</a></h3><span>' . $read_more . '</span></div></div></div></div></div>';

					$paginationNumber .= '<div class="swiper-slide fn-numbered-pagination"><span class="line"></span></div>';
				}
			}
			// after repeater
			$paginationNumber .= '</div></div>';
			$html .= '</div></div>' . $paginationNumber . '</div>';
			$html .= Modulify_Helper::modulify_close_wrap();

			// ECHO PROCESS
			echo $html;
		}
	}
}
