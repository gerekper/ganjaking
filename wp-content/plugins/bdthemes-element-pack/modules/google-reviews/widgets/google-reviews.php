<?php

namespace ElementPack\Modules\GoogleReviews\Widgets;

use Elementor\Plugin;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Google_Reviews extends Module_Base {

	protected $google_place_url = "https://maps.googleapis.com/maps/api/place/";

	public function get_name() {
		return 'bdt-google-reviews';
	}

	public function get_title() {
		return BDTEP . esc_html__('Google Reviews', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-google-reviews';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['Google', 'Reviews', 'Google Reviews'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-google-reviews'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/pp0mQpyKqfs';
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'google_place_id',
			[
				'label'       => esc_html__('Place ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => esc_html__('Google Place ID', 'bdthemes-element-pack'),
				'description' => sprintf(__('Click %1s HERE %2s to find place ID. It will show only recent 5 reviews.', 'bdthemes-element-pack'), '<a href="https://developers-dot-devsite-v2-prod.appspot.com/maps/documentation/javascript/examples/full/places-placeid-finder" target="_blank">', '</a>'),
				'label_block' => true,
			]
		);

		$this->add_control(
			'cache_reviews',
			[
				'label'   => esc_html__('Cache Reviews', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'refresh_reviews',
			array(
				'label'   => __('Reload Reviews after a', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'day',
				'options' => array(
					'hour'  => __('Hour', 'bdthemes-element-pack'),
					'day'   => __('Day', 'bdthemes-element-pack'),
					'week'  => __('Week', 'bdthemes-element-pack'),
					'month' => __('Month', 'bdthemes-element-pack'),
					'year'  => __('Year', 'bdthemes-element-pack'),
				),
				'condition' => [
					'cache_reviews' => 'yes'
				]
			)
		);

		$this->add_control(
			'review_message',
			[
				'type' => Controls_Manager::RAW_HTML,
				'raw' => __('Note: You can show only 5 most popular review right now.', 'bdthemes-element-pack'),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition' => [
					'cache_reviews' => 'yes'
				]

			]
		);

		$this->add_control(
			'show_image',
			[
				'label'   => esc_html__('Show Thumb', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_time',
			[
				'label'   => esc_html__('Show Time', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_name',
			[
				'label'   => esc_html__('Show Name', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_rating',
			[
				'label'   => esc_html__('Show Rating', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_google_icon',
			[
				'label'   => esc_html__('Show Google Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				// 'default' => 'yes',
			]
		);

		$this->add_control(
			'show_excerpt',
			[
				'label'   => esc_html__('Show Excerpt', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'excerpt_limit',
			[
				'label'     => esc_html__('Excerpt Limit', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 200,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'carousel_columns',
			[
				'label'          => esc_html__('Columns', 'bdthemes-element-pack') . BDTEP_NC,
				'type'           => Controls_Manager::SELECT,
				'default'        => '1',
				'tablet_default' => '1',
				'mobile_default' => '1',
				'options'        => [
					'1' => '1',
					'2' => '2',
					'3' => '3',
					'4' => '4',
					'5' => '5',
					'6' => '6',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'item_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-slider-items.bdt-grid'     => 'margin-left: -{{SIZE}}px',
					'{{WRAPPER}} .bdt-slider-items.bdt-grid > *' => 'padding-left: {{SIZE}}px',
				],
			]
		);

		$this->add_control(
			'match_height',
			[
				'label'   => esc_html__('Match Height', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => __('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'   => __('Arrows and Dots', 'bdthemes-element-pack'),
					'arrows' => __('Arrows', 'bdthemes-element-pack'),
					'dots'   => __('Dots', 'bdthemes-element-pack'),
					'none'   => __('None', 'bdthemes-element-pack'),
				],
				'prefix_class' => 'bdt-navigation-type-',
				'render_type'  => 'template',
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__('Arrows Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => '5',
				'options' => [
					'1' => esc_html__('Style 1', 'bdthemes-element-pack'),
					'2' => esc_html__('Style 2', 'bdthemes-element-pack'),
					'3' => esc_html__('Style 3', 'bdthemes-element-pack'),
					'4' => esc_html__('Style 4', 'bdthemes-element-pack'),
					'5' => esc_html__('Style 5', 'bdthemes-element-pack'),
					'6' => esc_html__('Style 6', 'bdthemes-element-pack'),
					'7' => esc_html__('Style 7', 'bdthemes-element-pack'),
					'8' => esc_html__('Style 8', 'bdthemes-element-pack'),
					'9' => esc_html__('Style 9', 'bdthemes-element-pack'),
					'10' => esc_html__('Style 10', 'bdthemes-element-pack'),
					'11' => esc_html__('Style 11', 'bdthemes-element-pack'),
					'12' => esc_html__('Style 12', 'bdthemes-element-pack'),
					'13' => esc_html__('Style 13', 'bdthemes-element-pack'),
					'14' => esc_html__('Style 14', 'bdthemes-element-pack'),
					'15' => esc_html__('Style 15', 'bdthemes-element-pack'),
					'16' => esc_html__('Style 16', 'bdthemes-element-pack'),
					'17' => esc_html__('Style 17', 'bdthemes-element-pack'),
					'18' => esc_html__('Style 18', 'bdthemes-element-pack'),
					'circle-1' => esc_html__('Style 19', 'bdthemes-element-pack'),
					'circle-2' => esc_html__('Style 20', 'bdthemes-element-pack'),
					'circle-3' => esc_html__('Style 21', 'bdthemes-element-pack'),
					'circle-4' => esc_html__('Style 22', 'bdthemes-element-pack'),
					'square-1' => esc_html__('Style 23', 'bdthemes-element-pack'),
				],
				'condition' => [
					'navigation' => ['both', 'arrows'],
				],
			]
		);

		$this->add_control(
			'both_position',
			[
				'label'     => __('Arrows and Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'both',
				],
			]
		);

		$this->add_control(
			'arrows_position',
			[
				'label'     => __('Arrows Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'center',
				'options'   => element_pack_navigation_position(),
				'condition' => [
					'navigation' => 'arrows',
				],
			]
		);

		$this->add_control(
			'dots_position',
			[
				'label'     => __('Dots Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_pagination_position(),
				'condition' => [
					'navigation' => 'dots',
				],
			]
		);

		//arrows_dots_hide_on_mobile controls
		$this->add_control(
			'arrows_dots_hide_on_mobile',
			[
				'label'     => __('Hide Arrows and Dots on Mobile', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navigation!' => 'none',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_slider_settins',
			[
				'label' => esc_html__('Slider Settings', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Auto Play', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'autoplay_interval',
			[
				'label'     => esc_html__('Autoplay Interval', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 7000,
				'condition' => [
					'autoplay' => 'yes',
				],
			]
		);

		$this->add_control(
			'pause_on_hover',
			[
				'label'   => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'loop',
			[
				'label'   => esc_html__('Loop', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_additonal',
			[
				'label' => esc_html__('Additional Settings', 'bdthemes-element-pack'),
			]
		);

		$languageArr = array(
			'' => 'Language disable',
			'ar' => 'Arabic',
			'bg' => 'Bulgarian',
			'bn' => 'Bengali',
			'ca' => 'Catalan',
			'cs' => 'Czech',
			'da' => 'Danish',
			'de' => 'German',
			'el' => 'Greek',
			'en' => 'English',
			'custom' => 'Custom',
		);

		$languageArr = apply_filters('ep_google_reviews_review_language', $languageArr);
		$this->add_control(
			'reviews_lang',
			[
				'label'   => esc_html__('Filter Reviews language', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => '',
				'options' => $languageArr,
			]
		);

		$this->add_control(
			'custom_lang',
			[
				'label'       => esc_html__('Custom Language', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'placeholder' => __('Your Language', 'bdthemes-element-pack'),
				'description' => sprintf(__('Please write your Language code here. It supports only language code. For the language code,  please look <a href="%s" target="_blank">here</a>
					 Please delete your transient if not works. You can simply delete transient from Layout ( Cache Reviews ) by on/off.'), 'http://www.lingoes.net/en/translator/langcode.htm'),
				'condition'	  => [
					'reviews_lang' => 'custom'
				]
			]
		);

		//Flex
		$this->add_responsive_control(
			'flex_direction',
			[
				'label'   => esc_html__('Info Position', 'bdthemes-prime-slider') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'column',
				'toggle' => false,
				'options' => [
					'column' => [
						'title' => esc_html__('Top', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-v-align-top',
					],
					'column-reverse' => [
						'title' => esc_html__('Bottom', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item' => 'flex-direction: {{VALUE}};',
				],
				'label_block' => false,
				'separator' => 'before'
			]
		);

		// layout style
		$this->add_control(
			'thumb_image_direction',
			[
				'label'   => esc_html__('Direction', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				'toggle' => false,
				'default' => 'left',
				'options' => [
					'top' => [
						'title' => esc_html__('Top', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-v-align-top',
					],

					'left' => [
						'title' => esc_html__('Left', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-h-align-right',
					],
					'bottom' => [
						'title' => esc_html__('Bottom', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-v-align-bottom',
					],
				],
			]
		);

		$this->add_responsive_control(
			'justify_content',
			[
				'label'   => esc_html__('Self Align', 'bdthemes-prime-slider') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				// 'default' => 'center',
				// 'toggle' => false,
				'options' => [
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-center-h',
					],
					'flex-start' => [
						'title' => esc_html__('Start', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-start-v',
					],
					'flex-end' => [
						'title' => esc_html__('End', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-end-v',
					],
					'space-between' => [
						'title' => esc_html__('Space Between', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-justify-space-between-v',
					],
					'space-around' => [
						'title' => esc_html__('Space Around', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-justify-space-around-v',
					],
					'space-evenly' => [
						'title' => esc_html__('Space Evenly', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-justify-space-evenly-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item' => 'justify-content: {{VALUE}};',
				],
				'label_block' => true,
				'condition' => [
					'flex_direction' => ['column', 'column-reverse']
				]
			]
		);

		$this->add_responsive_control(
			'align_items',
			[
				'label'   => esc_html__('Self Align', 'bdthemes-prime-slider') . BDTEP_NC,
				'type'    => Controls_Manager::CHOOSE,
				// 'default' => 'center',
				// 'toggle' => false,
				'options' => [
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-center-v',
					],
					'flex-start' => [
						'title' => esc_html__('Start', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-start-v',
					],
					'flex-end' => [
						'title' => esc_html__('End', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-end-v',
					],
					'stretch' => [
						'title' => esc_html__('stretch', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-align-stretch-v',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item' => 'align-items: {{VALUE}};',
				],
				'label_block' => true,
				'condition' => [
					'flex_direction' => ['row', 'row-reverse']
				]
			]
		);

		// $this->add_responsive_control(
		// 	'content_alignment',
		// 	[
		// 		'label'   => esc_html__('Text Alignment', 'bdthemes-prime-slider') . BDTEP_NC,
		// 		'type'    => Controls_Manager::CHOOSE,
		// 		'options' => [
		// 			'left' => [
		// 				'title' => esc_html__('Left', 'bdthemes-prime-slider'),
		// 				'icon'  => 'eicon-text-align-left',
		// 			],
		// 			'center' => [
		// 				'title' => esc_html__('Center', 'bdthemes-prime-slider'),
		// 				'icon'  => 'eicon-text-align-center',
		// 			],
		// 			'right' => [
		// 				'title' => esc_html__('Right', 'bdthemes-prime-slider'),
		// 				'icon'  => 'eicon-text-align-right',
		// 			],
		// 			'justify' => [
		// 				'title' => esc_html__('Justify', 'bdthemes-prime-slider'),
		// 				'icon'  => 'eicon-text-align-justify',
		// 			],
		// 		],
		// 		'selectors' => [
		// 			'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item *' => 'text-align: {{VALUE}};',
		// 		],
		// 	]
		// );

		$this->add_responsive_control(
			'content_width',
			[
				'label'   => esc_html__('Content Width', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 1200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews-desc' => 'width: {{SIZE}}{{UNIT}}; max-width: {{SIZE}}{{UNIT}};  margin: 0 auto;',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'thumb_info_width',
			[
				'label'   => esc_html__('Thumb/info Width', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 50,
						'max'  => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-info' => 'width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}}; margin: 0 auto;',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_google_reviews_style',
			[
				'label' => __('Google Reviews', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_google_reviews_item_style');

		$this->start_controls_tab(
			'google_reviews_item_normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'google_reviews_item_background',
				'selector'  => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item',
			]
		);

		$this->add_responsive_control(
			'google_reviews_item_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'google_reviews_item_border',
				'selector'    => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item',
			]
		);

		$this->add_control(
			'google_reviews_item_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'google_reviews_item_shadow',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item'
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'google_reviews_item_hover',
			[
				'label' => __('hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'google_reviews_item_hover_background',
				'selector'  => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item:hover',
			]
		);

		$this->add_control(
			'google_reviews_item_hover_border_color',
			[
				'label'     => __('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item:hover'  => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'google_reviews_item_border_border!' => '',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'google_reviews_item_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item:hover',
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_image',
			[
				'label'     => esc_html__('Image', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_image' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'image_size',
			[
				'label' => esc_html__('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 150,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-img img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}}; min-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'image_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-thumb-info' => 'gap: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_name',
			[
				'label'     => esc_html__('Name', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_name' => 'yes',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-name a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-name a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'name_typography',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-name a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_time',
			[
				'label'     => esc_html__('Time', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_time' => 'yes',
				],
			]
		);

		$this->add_control(
			'time_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-date' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'time_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-date' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'time_typography',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-date',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_rating',
			[
				'label'     => esc_html__('Rating', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_rating' => 'yes',
				],
			]
		);

		$this->add_control(
			'rating_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#e7e7e7',
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating .bdt-rating-item' => 'color: {{VALUE}};',
				]
			]
		);

		$this->add_control(
			'active_rating_color',
			[
				'label'     => esc_html__('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#FFCC00',
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating.bdt-rating-1 .bdt-rating-item:nth-child(1)'    => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating.bdt-rating-2 .bdt-rating-item:nth-child(-n+2)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating.bdt-rating-3 .bdt-rating-item:nth-child(-n+3)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating.bdt-rating-4 .bdt-rating-item:nth-child(-n+4)' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating.bdt-rating-5 .bdt-rating-item:nth-child(-n+5)' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-rating .bdt-rating-item' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'rating_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-rating' => 'padding-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_excerpt',
			[
				'label'     => esc_html__('Excerpt', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-desc' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_align',
			[
				'label'   => __('Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => __('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => __('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-desc' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'excerpt_spacing',
			[
				'label'     => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-desc' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'excerpt_typography',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-google-reviews-item .bdt-google-reviews-desc',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_google_icon',
			[
				'label'     => esc_html__('Google Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_google_icon' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'google_icon_position',
			[
				'label'   => esc_html__('Position', 'bdthemes-prime-slider'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'right',
				'toggle' => false,
				'options' => [
					'left' => [
						'title' => esc_html__('left', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-h-align-left',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-prime-slider'),
						'icon'  => 'eicon-h-align-right',
					]
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-icon' => '{{VALUE}}: 0;',
				],
				'label_block' => true,
			]
		);

		$this->add_responsive_control(
			'google_icon_size',
			[
				'label'     => esc_html__('Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-icon svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'google_icon_margin',
			[
				'label'      => esc_html__('Margin', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-google-icon' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'      => __('Navigation', 'bdthemes-element-pack'),
				'tab'        => Controls_Manager::TAB_STYLE,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => '!=',
							'value'    => 'none',
						],
					],
				],
			]
		);

		//arrows heading
		$this->add_control(
			'arrows_heading',
			[
				'label'     => __('Arrows', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev i,
					{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev:hover i,
					{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev,
					{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __('Hover Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev:hover,
					{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next:hover' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		//border type control
		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'arrows_border',
				'label'    => __('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev, {{WRAPPER}} .bdt-google-reviews .bdt-navigation-next',
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev, {{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);
		
		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev, {{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		//box shadow control
		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'arrows_shadow',
				'selector' => '{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev, {{WRAPPER}} .bdt-google-reviews .bdt-navigation-next',
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev i, {{WRAPPER}} .bdt-google-reviews .bdt-navigation-next i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_space',
			[
				'label' => __('Space Between', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		//dots heading
		$this->add_control(
			'dots_heading',
			[
				'label'     => __('Dots', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
				'separator' => 'before'
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-slider-nav li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-slider-nav li.bdt-active a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		$this->add_responsive_control(
			'dots_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 5,
						'max' => 20,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-slider-nav li a' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['dots', 'both'],
				],
			]
		);

		//offset heading
		$this->add_control(
			'offset_heading',
			[
				'label'     => __('Offset', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'arrows_ncx_position',
			[
				'label'   => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_ncy_position',
			[
				'label'   => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-arrows-container' => 'transform: translate({{arrows_ncx_position.size}}px, {{SIZE}}px);',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'     => 'arrows_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_acx_position',
			[
				'label'   => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'arrows',
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nnx_position',
			[
				'label'   => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'dots_nny_position',
			[
				'label'   => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-dots-container' => 'transform: translate({{dots_nnx_position.size}}px, {{SIZE}}px);',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'dots',
						],
						[
							'name'     => 'dots_position',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_ncx_position',
			[
				'label'   => __('Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_ncy_position',
			[
				'label'   => __('Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-arrows-dots-container' => 'transform: translate({{both_ncx_position.size}}px, {{SIZE}}px);',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'     => 'both_position',
							'operator' => '!=',
							'value'    => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cx_position',
			[
				'label'   => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -60,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-google-reviews .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'both_cy_position',
			[
				'label'   => __('Dots Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-google-reviews .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
				],
				'conditions' => [
					'terms' => [
						[
							'name'  => 'navigation',
							'value' => 'both',
						],
						[
							'name'  => 'both_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_transient_expire($settings) {

		$expire_value = $settings['refresh_reviews'];
		$expire_time  = 24 * HOUR_IN_SECONDS;

		if ('hour' === $expire_value) {
			$expire_time = 60 * MINUTE_IN_SECONDS;
		} elseif ('week' === $expire_value) {
			$expire_time = 7 * DAY_IN_SECONDS;
		} elseif ('month' === $expire_value) {
			$expire_time = 30 * DAY_IN_SECONDS;
		} elseif ('year' === $expire_value) {
			$expire_time = 365 * DAY_IN_SECONDS;
		}

		return $expire_time;
	}

	public function get_transient_key($placeId) {
		$placeId = strtolower($placeId);
		$transient = 'google_reviews_data_' . $placeId;
		return $transient;
	}

	public function get_api_url($api_key, $placeid, $language) {
		$url = $this->google_place_url . 'details/json?placeid=' . $placeid . '&key=' . $api_key;
		if (strlen($language) > 0) {
			$url = $url . '&language=' . $language;
		}
		return $url;
	}

	public function get_cache_data($placeId) {
		$settings   = $this->get_settings_for_display();

		$transient = $this->get_transient_key($placeId);
		$data      = get_transient($transient);

		if ($settings['cache_reviews'] != 'yes') {
			delete_transient($transient);
		}

		if (is_array($data) && count($data) > 0) {
			if ($placeId == $data['place_id']) {
				return $data;
			} else {
				delete_transient($transient);
			}
		}
		return false;
	}

	public function getReviews() {

		$settings   = $this->get_settings_for_display();
		$options    = get_option('element_pack_api_settings');
		$placeId    = isset($settings['google_place_id']) ? esc_html($settings['google_place_id']) : '';
		$ApiKey     = isset($options['google_map_key']) ? esc_html($options['google_map_key']) : '';
		// $language   = isset($options['reviews_lang']) ? esc_html($options['reviews_lang']):'';

		$language = '';

		if (isset($settings['reviews_lang'])) {
			if ($settings['reviews_lang'] == 'custom') {
				if (empty($settings['custom_lang'])) {
					$language = '';
				} else {
					$language = esc_html($settings['custom_lang']);
				}
			} else {
				$language = esc_html($settings['reviews_lang']);
			}
		} else {
			$language = '';
		}

		//$language   = isset($settings['reviews_lang']) ? esc_html($settings['reviews_lang']):'';

		if (!$placeId || !$ApiKey) {
			return false;
		}


		$reviewData = $this->get_cache_data($placeId);

		if ($reviewData) {
			return $reviewData;
		} else {
			$requestUrl = $this->get_api_url($ApiKey, $placeId, $language);

			$response = wp_remote_get($requestUrl);

			if (is_wp_error($response)) {
				return array('error_message' => $response->get_error_message());
			}
			$response   = json_decode($response['body'], true);
			$result     = (isset($response['result']) && is_array($response['result'])) ? $response['result'] : '';

			if (is_array($result)) {
				if (isset($result['error_message'])) {
					return $result;
				}

				$transient = $this->get_transient_key($placeId);
				$expireTime = $this->get_transient_expire($settings);

				set_transient($transient, $result, $expireTime); // One day
				return $result;
			}
			return $response;
		}
	}

	public function render_rating($rating) {
		$settings = $this->get_settings_for_display();

		if (!$settings['show_rating']) {
			return;
		}

?>
		<div class="bdt-google-reviews-rating">
			<ul class="bdt-rating bdt-grid bdt-grid-collapse bdt-rating-<?php echo esc_attr($rating); ?>" data-bdt-grid>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
				<li class="bdt-rating-item"><i class="ep-icon-star-full" aria-hidden="true"></i></li>
			</ul>
		</div>
	<?php
	}

	public function render_excerpt($excerpt, $limit = '', $trail = '') {
		$settings = $this->get_settings_for_display();

		$excerpt_limit = $settings['excerpt_limit'];
		$limit = $excerpt_limit;

		// Ellipsis with PHP
		$excerpt_limit = $limit;           // Max. number of characters
		if (strlen($excerpt) > $excerpt_limit)
		{
			$lastPos = $excerpt_limit - strlen($excerpt);
			$excerpt = substr($excerpt, 0, strrpos($excerpt, ' ', $lastPos)) . '...';
		}


		$output = $excerpt;
		//$output = strip_shortcodes(wp_trim_words($excerpt, $limit, $trail));

		return $output;
	}

	public function render_footer() {
		$settings = $this->get_settings_for_display();

	?>
		</ul>
		<?php if ('both' == $settings['navigation']) : ?>
			<?php $this->render_both_navigation(); ?>

			<?php if ('center' === $settings['both_position']) : ?>
				<?php $this->render_dotnavs(); ?>
			<?php endif; ?>

		<?php elseif ('arrows' == $settings['navigation']) : ?>
			<?php $this->render_navigation(); ?>
		<?php elseif ('dots' == $settings['navigation']) : ?>
			<?php $this->render_dotnavs(); ?>
		<?php endif; ?>
		</div>
		</div>
	<?php
	}

	public function render_navigation() {
		$settings = $this->get_settings_for_display();

		if (('both' == $settings['navigation']) and ('center' == $settings['both_position'])) {
			$arrows_position = 'center';
		} else {
			$arrows_position = $settings['arrows_position'];
		}
		if ($settings['arrows_dots_hide_on_mobile']) {
			$this->add_render_attribute('arrows-container', 'class', 'bdt-visible@m');
		}
		$this->add_render_attribute('arrows-container', 'class', 'bdt-position-z-index bdt-position-' . esc_attr($arrows_position));

		?>
		<div <?php echo $this->get_render_attribute_string('arrows-container'); ?>>
			<div class="bdt-arrows-container bdt-slidenav-container">
				<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slider-item="previous">
					<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
				<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slider-item="next">
					<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
			</div>
		</div>
		<?php
	}

	public function render_dotnavs() {
		$settings = $this->get_settings_for_display();

		if (('both' == $settings['navigation']) and ('center' == $settings['both_position'])) {
			$dots_position = 'bottom-center';
		} else {
			$dots_position = $settings['dots_position'];
		}

		if ($settings['arrows_dots_hide_on_mobile']) {
			$this->add_render_attribute('dots-container', 'class', 'bdt-visible@m');
		}
		$this->add_render_attribute('dots-container', 'class', 'bdt-position-z-index bdt-position-' . esc_attr($dots_position));

		?>
		<div <?php echo $this->get_render_attribute_string('dots-container'); ?>>
			<div class="bdt-dotnav-wrapper bdt-dots-container">

				<ul class="bdt-slider-nav bdt-dotnav bdt-flex-center">
				</ul>

			</div>
		</div>
		<?php
	}

	public function render_both_navigation() {
		$settings = $this->get_settings_for_display();

		$both_position = $settings['both_position'];

		if ($settings['arrows_dots_hide_on_mobile']) {
			$this->add_render_attribute('both-container', 'class', 'bdt-visible@m');
		}
		$this->add_render_attribute('both-container', 'class', 'bdt-position-z-index bdt-position-' . esc_attr($both_position));

		?>
		<div <?php echo $this->get_render_attribute_string('both-container'); ?>>
			<div class="bdt-arrows-dots-container bdt-slidenav-container ">

				<div class="bdt-flex bdt-flex-middle">
					<div>
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slider-item="previous">
							<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>

					<?php if ('center' !== $settings['both_position']) : ?>
						<div class="bdt-dotnav-wrapper bdt-dots-container">
							<ul class="bdt-dotnav">
							</ul>
						</div>
					<?php endif; ?>

					<div>
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slider-item="next">
							<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>
					</div>

				</div>
			</div>
		</div>
		<?php
	}

	protected function render_header() {
		$settings = $this->get_settings_for_display();
		$reviewData     = $this->getReviews();
		// $is_editor      = Plugin::instance()->editor->is_edit_mode();


		// $errorMessage   = "";
		// if ($is_editor) {
		// 	$errorMessage   = (isset($reviewData['error_message'])) ? $reviewData['error_message'] : '';
		// }

		$errorMessage   = (isset($reviewData['error_message'])) ? '<div class="bdt-alert-warning bdt-text-center">'.$reviewData["error_message"].'</div>' : '';
		
		$clientReview = isset($reviewData['reviews']) ? $reviewData['reviews'] : [];

		$this->add_render_attribute('google-reviews', 'class', 'bdt-google-reviews bdt-google-reviews-slider bdt-thumb-direction-' . esc_attr($settings['thumb_image_direction']));


		$carousel_columns_mobile = isset($settings['carousel_columns_mobile']) ? $settings['carousel_columns_mobile'] : '1';
		$carousel_columns_tablet = isset($settings['carousel_columns_tablet']) ? $settings['carousel_columns_tablet'] : '1';
		$carousel_columns 		 = isset($settings['carousel_columns']) ? $settings['carousel_columns'] : '1';


		$this->add_render_attribute('carousel', 'data-bdt-grid', '');
		$this->add_render_attribute('carousel', 'class', ['bdt-grid', 'bdt-grid-small']);
		if ($settings['match_height']) {
			$this->add_render_attribute('carousel', 'class', ['bdt-grid-match']);
		}
		$this->add_render_attribute('carousel', 'class', 'bdt-slider-items');
		$this->add_render_attribute('carousel', 'class', 'bdt-child-width-1-' . esc_attr($carousel_columns_mobile));
		$this->add_render_attribute('carousel', 'class', 'bdt-child-width-1-' . esc_attr($carousel_columns_tablet) . '@s');
		$this->add_render_attribute('carousel', 'class', 'bdt-child-width-1-' . esc_attr($carousel_columns) . '@m');

	?>

		<div <?php echo $this->get_render_attribute_string('google-reviews'); ?>>
			<?php
			echo $errorMessage;
			

			$this->add_render_attribute(
				[
					'slider-settings' => [
						'class' => [
							('both' == $settings['navigation']) ? 'bdt-arrows-dots-align-' . $settings['both_position'] : '',
							('arrows' == $settings['navigation'] or 'arrows-thumbnavs' == $settings['navigation']) ? 'bdt-arrows-align-' . $settings['arrows_position'] : '',
							('dots' == $settings['navigation']) ? 'bdt-dots-align-' . $settings['dots_position'] : '',
						],
						'data-bdt-slider' => [
							wp_json_encode(array_filter([
								"autoplay"          => $settings["autoplay"],
								"autoplay-interval" => $settings["autoplay_interval"],
								"finite"            => $settings["loop"] ? false : true,
								"pause-on-hover"    => $settings["pause_on_hover"] ? true : false,
								// "center"            => true,
							]))
						]
					]
				]
			);

			?>
			<div <?php echo ($this->get_render_attribute_string('slider-settings')); ?>>
				<ul <?php echo $this->get_render_attribute_string('carousel'); ?>>

					<?php
					foreach ($clientReview as $review) {
						$author_name    = $review['author_name'];
						$author_url     = $review['author_url'];
						//$language     = $review['language'];
						$profile_photo_url = $review['profile_photo_url'];
						$humanTime      = $review['relative_time_description'];
						$review_text    = $review['text'];
						//$timeStamp    = $review['time'];
						$rating      = $review['rating'];

					?>

						<li>
							<div class="bdt-google-reviews-item">

								<div class="bdt-thumb-info bdt-flex bdt-flex-middle">
									<?php if ('yes' == $settings['show_image']) : ?>
										<div class="bdt-google-reviews-img">
											<img src="<?php echo esc_url($profile_photo_url); ?>" alt="<?php echo esc_html($author_name); ?>">
										</div>
									<?php endif; ?>

									<div>

										<?php if ('yes' == $settings['show_name']) : ?>
											<div class="bdt-google-reviews-name">
												<a href="<?php echo esc_url($author_url) ?>" target="_blank"><?php echo esc_html($author_name); ?></a>
											</div>
										<?php endif; ?>

										<?php if ('yes' == $settings['show_time']) : ?>
											<div class="bdt-google-reviews-date">
												<?php echo esc_attr($humanTime); ?>
											</div>
										<?php endif; ?>

										<?php $this->render_rating($rating); ?>

									</div>
								</div>


								<?php if ('yes' == $settings['show_excerpt']) : ?>
									<div class="bdt-google-reviews-desc">
										<?php echo $this->render_excerpt($review_text); ?>
									</div>
								<?php endif; ?>

								<?php if ('yes' == $settings['show_google_icon']) : ?>
									<div class="bdt-google-icon">
										<svg width="24px" height="24px" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink">
											<path fill="#EA4335 " d="M5.26620003,9.76452941 C6.19878754,6.93863203 8.85444915,4.90909091 12,4.90909091 C13.6909091,4.90909091 15.2181818,5.50909091 16.4181818,6.49090909 L19.9090909,3 C17.7818182,1.14545455 15.0545455,0 12,0 C7.27006974,0 3.1977497,2.69829785 1.23999023,6.65002441 L5.26620003,9.76452941 Z" />
											<path fill="#34A853" d="M16.0407269,18.0125889 C14.9509167,18.7163016 13.5660892,19.0909091 12,19.0909091 C8.86648613,19.0909091 6.21911939,17.076871 5.27698177,14.2678769 L1.23746264,17.3349879 C3.19279051,21.2936293 7.26500293,24 12,24 C14.9328362,24 17.7353462,22.9573905 19.834192,20.9995801 L16.0407269,18.0125889 Z" />
											<path fill="#4A90E2" d="M19.834192,20.9995801 C22.0291676,18.9520994 23.4545455,15.903663 23.4545455,12 C23.4545455,11.2909091 23.3454545,10.5272727 23.1818182,9.81818182 L12,9.81818182 L12,14.4545455 L18.4363636,14.4545455 C18.1187732,16.013626 17.2662994,17.2212117 16.0407269,18.0125889 L19.834192,20.9995801 Z" />
											<path fill="#FBBC05" d="M5.27698177,14.2678769 C5.03832634,13.556323 4.90909091,12.7937589 4.90909091,12 C4.90909091,11.2182781 5.03443647,10.4668121 5.26620003,9.76452941 L1.23999023,6.65002441 C0.43658717,8.26043162 0,10.0753848 0,12 C0,13.9195484 0.444780743,15.7301709 1.23746264,17.3349879 L5.27698177,14.2678769 Z" />
										</svg>
									</div>
								<?php endif; ?>

							</div>
						</li>

					<?php

					}

					?>

			<?php
		}

		protected function render() {
			$settings = $this->get_settings_for_display();

			if (empty($settings['google_place_id'])) {
				echo '<div class="bdt-alert bdt-alert-warning">' . __('Please type google place id from Setting!', 'bdthemes-element-pack') . '</div>';
				return;
			}

			$this->render_header();
			$this->render_footer();
		}
	}
