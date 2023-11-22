<?php

namespace ElementPack\Modules\Slideshow\Widgets;

use Elementor\Repeater;
use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Utils;
use Elementor\Icons_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Slideshow extends Module_Base {
	private $_query = null;

	public function get_name() {
		return 'bdt-slideshow';
	}

	public function get_title() {
		return BDTEP . esc_html__('Slideshow', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-slideshow';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['slider', 'slideshow', 'hero'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-slideshow', 'mThumbnailScroller'];
		}
	}

	public function get_script_depends() {
        if ($this->ep_is_edit_mode()) {
            return ['thumbnail-scroller', 'imagesloaded', 'ep-scripts'];
        } else {
			return ['thumbnail-scroller', 'imagesloaded', 'ep-slideshow'];
        }
	}

	public function on_import($element) {
		if (!get_post_type_object($element['settings']['posts_post_type'])) {
			$element['settings']['posts_post_type'] = 'services';
		}

		return $element;
	}

	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/BrrKmDfJ5ZI';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_content_sliders',
			[
				'label' => esc_html__('Sliders', 'bdthemes-element-pack'),
			]
		);

		$repeater = new Repeater();

		$repeater->add_control(
			'pre_title',
			[
				'label'       => esc_html__('Pre Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Slide Pre Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'title',
			[
				'label'       => esc_html__('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'default'     => esc_html__('Slide Title', 'bdthemes-element-pack'),
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'post_title',
			[
				'label'       => esc_html__('Post Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'dynamic'     => ['active' => true],
				'label_block' => true,
			]
		);

		$repeater->add_control(
			'background',
			[
				'label'   => esc_html__('Background', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'color',
				'options' => [
					'color' => [
						'title' => esc_html__('Color', 'bdthemes-element-pack'),
						'icon'  => 'eicon-square',
					],
					'image' => [
						'title' => esc_html__('Image', 'bdthemes-element-pack'),
						'icon'  => 'eicon-image',
					],
					'video' => [
						'title' => esc_html__('Video', 'bdthemes-element-pack'),
						'icon'  => 'eicon-play',
					],
					'youtube' => [
						'title' => esc_html__('Youtube', 'bdthemes-element-pack'),
						'icon'  => 'eicon-youtube',
					],
				],
			]
		);

		$repeater->add_control(
			'color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#14ABF4',
				'condition' => [
					'background' => 'color'
				],
				'selectors' => [
					'{{WRAPPER}} {{CURRENT_ITEM}}' => 'background-color: {{VALUE}}',
				],
			]
		);

		$repeater->add_control(
			'image',
			[
				'label'   => esc_html__('Image', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::MEDIA,
				'dynamic' => ['active' => true],
				'default' => [
					'url' => Utils::get_placeholder_image_src(),
				],
				'condition' => [
					'background' => 'image'
				],
			]
		);

		$repeater->add_control(
			'video_link',
			[
				'label'     => esc_html__('Video Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'video'
				],
				'default' => '//clips.vorwaerts-gmbh.de/big_buck_bunny.mp4',
			]
		);

		$repeater->add_control(
			'youtube_link',
			[
				'label'     => esc_html__('Youtube Link', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::TEXT,
				'condition' => [
					'background' => 'youtube'
				],
				'default' => 'https://youtu.be/YE7VzlLtp-4',
			]
		);

		$repeater->add_control(
			'button_link',
			[
				'label'       => esc_html__('Button Link', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::URL,
				'dynamic'     => ['active' => true],
				'separator'   => 'before',
				'placeholder' => 'https://bdthemes.com',
			]
		);

		$repeater->add_control(
			'text',
			[
				'label'      => esc_html__('Text', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::TEXTAREA,
				'dynamic'    => ['active' => true],
				'default'    => esc_html__('I am slideshow description text, you can edit this text from slider items of slideshow content.', 'bdthemes-element-pack'),
				'show_label' => false,
			]
		);

		$repeater->add_responsive_control(
			'marker_invisible_height',
			[
				'label'   => __('Height', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'tablet_default' => [
					'size' => 20,
				],
				'mobile_default' => [
					'size' => 20,
				],
				'size_units' => ['px', '%'],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-marker-wrapper {{CURRENT_ITEM}}.bdt-marker-item' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'select_type'	=> 'none'
				]
			]
		);

		$repeater->add_responsive_control(
			'item_alignment',
			[
				'label'       => __('Alignment', 'bdthemes-element-pack') . BDTEP_NC,
				'type'        => Controls_Manager::CHOOSE,
				'options'     => [
					'left'  => [
						'title' => __('Start', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-left',
					],
					'center' => [
						'title' => __('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-center',
					],
					'right' => [
						'title' => __('End', 'bdthemes-element-pack'),
						'icon'  => 'eicon-h-align-right',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-items {{CURRENT_ITEM}}.bdt-slideshow-item .bdt-slideshow-content-wrapper' => 'text-align: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'slides',
			[
				'label' => esc_html__('Slider Items', 'bdthemes-element-pack'),
				'type' => Controls_Manager::REPEATER,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'title'       => esc_html__('Slide Item 1', 'bdthemes-element-pack'),
						'button_link' => ['url' => '#'],
					],
					[
						'title'       => esc_html__('Slide Item 2', 'bdthemes-element-pack'),
						'button_link' => ['url' => '#'],
					],
					[
						'title'       => esc_html__('Slide Item 3', 'bdthemes-element-pack'),
						'button_link' => ['url' => '#'],
					],
					[
						'title'       => esc_html__('Slide Item 4', 'bdthemes-element-pack'),
						'button_link' => ['url' => '#'],
					],
				],
				'title_field' => '{{{ title }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'content_position',
			[
				'label'   => esc_html__('Content Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'center',
				'options' => [
					'top-left'      => esc_html__('Top Left', 'bdthemes-element-pack'),
					'top-center'    => esc_html__('Top Center', 'bdthemes-element-pack'),
					'top-right'     => esc_html__('Top Right', 'bdthemes-element-pack'),
					'center'        => esc_html__('Center', 'bdthemes-element-pack'),
					'center-left'   => esc_html__('Center Left', 'bdthemes-element-pack'),
					'center-right'  => esc_html__('Center Right', 'bdthemes-element-pack'),
					'bottom-left'   => esc_html__('Bottom Left', 'bdthemes-element-pack'),
					'bottom-center' => esc_html__('Bottom Center', 'bdthemes-element-pack'),
					'bottom-right'  => esc_html__('Bottom Right', 'bdthemes-element-pack'),
				]
			]
		);

		$this->add_responsive_control(
			'content_align',
			[
				'label'   => esc_html__('Content Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'default' => 'center',
				'options' => [
					'left' => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify' => [
						'title' => esc_html__('Justified', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
			]
		);

		$this->add_control(
			'show_pre_title',
			[
				'label'   => esc_html__('Show Pre Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => esc_html__('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => esc_html__('Title HTML Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h1',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_post_title',
			[
				'label' => esc_html__('Show Post Title', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'   => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__('Show Button', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'slider_size_ratio',
			[
				'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => 'Slider ratio to widht and height, such as 16:9',
			]
		);

		$this->add_control(
			'slider_min_height',
			[
				'label' => esc_html__('Minimum Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1024,
					],
				],
			]
		);

		$this->add_control(
			'slideshow_fullscreen',
			[
				'label' => esc_html__('Slideshow Fullscreen', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'scroll_to_section',
			[
				'label' => esc_html__('Scroll to Section', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'section_id',
			[
				'label'       => esc_html__('Section ID', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'placeholder' => 'Section ID Here',
				'description' => 'Enter section ID of this page, ex: #my-section',
				'label_block' => true,
				'condition'   => [
					'scroll_to_section' => 'yes',
				],
			]
		);

		$this->add_control(
			'slideshow_scroll_to_section_icon',
			[
				'label'       => esc_html__('Scroll to Section Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'scroll_to_section_icon',
				'default' => [
					'value' => 'fas fa-angle-double-down',
					'library' => 'fa-solid',
				],
				'condition'   => [
					'scroll_to_section' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_text',
			[
				'label'       => esc_html__('Button Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'slideshow_icon',
			[
				'label' => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'label_block' => false,
				'skin' => 'inline'
			]
		);

		$this->add_control(
			'icon_align',
			[
				'label'   => esc_html__('Icon Position', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'right',
				'options' => [
					'left'  => esc_html__('Left', 'bdthemes-element-pack'),
					'right' => esc_html__('Right', 'bdthemes-element-pack'),
				],
				'condition' => [
					'slideshow_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'icon_indent',
			[
				'label'   => esc_html__('Icon Spacing', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 8,
				],
				'range' => [
					'px' => [
						'max' => 50,
					],
				],
				'condition' => [
					'slideshow_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-button-icon-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-button-icon-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_navigation',
			[
				'label' => esc_html__('Navigation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'arrows',
				'options' => [
					'both'             => esc_html__('Arrows and Dots', 'bdthemes-element-pack'),
					'arrows-thumbnavs' => esc_html__('Arrows and Thumbnavs', 'bdthemes-element-pack'),
					'arrows'           => esc_html__('Arrows', 'bdthemes-element-pack'),
					'dots'             => esc_html__('Dots', 'bdthemes-element-pack'),
					'thumbnavs'        => esc_html__('Thumbnavs', 'bdthemes-element-pack'),
					'none'             => esc_html__('None', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'nav_arrows_icon',
			[
				'label'   => esc_html__('Arrows Icon', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SELECT,
				'default' => '0',
				'options' => [
					'0' => esc_html__('Default', 'bdthemes-element-pack'),
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
					'navigation' => ['both', 'arrows-thumbnavs', 'arrows'],
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
					'navigation' => ['arrows', 'arrows-thumbnavs'],
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

		$this->add_control(
			'thumbnavs_position',
			[
				'label'     => esc_html__('Thumbnavs Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'bottom-center',
				'options'   => element_pack_thumbnavs_position(),
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_outside',
			[
				'label'     => esc_html__('Thumbnavs Outside', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['thumbnavs', 'arrows-thumbnavs'],
						],
						[
							'name'     => 'thumbnavs_position',
							'operator' => 'in',
							'value'    => ['center-left', 'center-right'],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_max_width',
			[
				'label' => esc_html__('Thumbnav Max Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav-wrapper' => 'width: calc({{SIZE}}% - (15px * 2));',
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['thumbnavs', 'arrows-thumbnavs'],
						],
						[
							'name'     => 'thumbnavs_position',
							'operator' => 'in',
							'value'    => ['top-left', 'top-right', 'top-center', 'bottom-left', 'bottom-right', 'bottom-center'],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_max_height',
			[
				'label' => esc_html__('Thumbnav Max Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'size_units' => ['%'],
				'range' => [
					'%' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav-wrapper' => 'height: calc({{SIZE}}% - (15px * 2));',
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['thumbnavs', 'arrows-thumbnavs'],
						],
						[
							'name'     => 'thumbnavs_position',
							'operator' => 'in',
							'value'    => ['center-left', 'center-right'],
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_width',
			[
				'label' => esc_html__('Thumbnavs Image Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 110,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['thumbnavs', 'arrows-thumbnavs']
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_height',
			[
				'label' => esc_html__('Thumbnavs Image Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 20,
						'max' => 200,
					],
				],
				'default' => [
					'size' => 80,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation' => ['thumbnavs', 'arrows-thumbnavs']
				],
			]
		);

		$this->add_control(
			'hide_arrow_on_mobile',
			[
				'label'     => __('Hide Arrow on Mobile ?', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'navigation' => ['arrows', 'both'],
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label'   => esc_html__('Autoplay', 'bdthemes-element-pack'),
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
				'label'     => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'velocity',
			[
				'label'   => __('Animation Speed', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0.1,
						'max' => 1,
						'step' => 0.1,
					],
				],
			]
		);

		$this->add_control(
			'slider_animations',
			[
				'label'     => esc_html__('Slider Animations', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'separator' => 'before',
				'default'   => 'slide',
				'options'   => [
					'slide' => esc_html__('Slide', 'bdthemes-element-pack'),
					'fade'  => esc_html__('Fade', 'bdthemes-element-pack'),
					'scale' => esc_html__('Scale', 'bdthemes-element-pack'),
					'push'  => esc_html__('Push', 'bdthemes-element-pack'),
					'pull'  => esc_html__('Pull', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'kenburns_animation',
			[
				'label'     => esc_html__('Kenburns Animation', 'bdthemes-element-pack'),
				'separator' => 'before',
				'type'      => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'kenburns_reverse',
			[
				'label'     => esc_html__('Kenburn Reverse', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'condition' => [
					'kenburns_animation' => 'yes'
				]
			]
		);

		$this->add_control(
			'parallax_pre_title',
			[
				'label'     => esc_html__('Parallax Pre Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_pre_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_pre_title_x_start',
			[
				'label' => esc_html__('X Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_pre_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_pre_title_x_end',
			[
				'label' => esc_html__('X End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -200,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_pre_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_pre_title_y_start',
			[
				'label' => esc_html__('Y Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_pre_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_pre_title_y_end',
			[
				'label' => esc_html__('Y End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_pre_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_title',
			[
				'label'     => esc_html__('Parallax Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_title_x_start',
			[
				'label' => esc_html__('X Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 300,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_title_x_end',
			[
				'label' => esc_html__('X End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -300,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_title_y_start',
			[
				'label' => esc_html__('Y Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_title_y_end',
			[
				'label' => esc_html__('Y End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_post_title',
			[
				'label'     => esc_html__('Parallax Post Title', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_post_title_x_start',
			[
				'label' => esc_html__('X Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 350,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_post_title_x_end',
			[
				'label' => esc_html__('X End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -350,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_post_title_y_start',
			[
				'label' => esc_html__('Y Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_post_title_y_end',
			[
				'label' => esc_html__('Y End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_text',
			[
				'label'     => esc_html__('Parallax Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_text_x_start',
			[
				'label' => esc_html__('X Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_text_x_end',
			[
				'label' => esc_html__('X End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -500,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_text_y_start',
			[
				'label' => esc_html__('Y Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_text_y_end',
			[
				'label' => esc_html__('Y End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_button',
			[
				'label'     => esc_html__('Parallax Button', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'separator' => 'before',
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_button_x_start',
			[
				'label' => esc_html__('X Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => -150,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_button_x_end',
			[
				'label' => esc_html__('X End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 150,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_button_y_start',
			[
				'label' => esc_html__('Y Start Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_button' => 'yes',
				],
			]
		);

		$this->add_control(
			'parallax_button_y_end',
			[
				'label' => esc_html__('Y End Value', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0,
				],
				'range' => [
					'px' => [
						'min' => -500,
						'max' => 500,
					],
				],
				'condition' => [
					'parallax_button' => 'yes',
				],
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_slider',
			[
				'label' => esc_html__('Slider', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'overlay',
			[
				'label'   => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'none',
				'options' => [
					'none'       => esc_html__('None', 'bdthemes-element-pack'),
					'background' => esc_html__('Background', 'bdthemes-element-pack'),
					'blend'      => esc_html__('Blend', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'overlay' => ['background', 'blend']
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-overlay-default' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'blend_type',
			[
				'label'     => esc_html__('Blend Type', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'default'   => 'multiply',
				'options'   => element_pack_blend_options(),
				'condition' => [
					'overlay' => 'blend',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pre_title',
			[
				'label'     => esc_html__('Pre Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_pre_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'pre_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-pre-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'pre_title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-pre-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-pre-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pre_title_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-pre-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pre_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-pre-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => ['yes'],
				],
			]
		);

		$this->add_control(
			'show_text_stroke',
			[
				'label'   => esc_html__('Text Stroke', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'prefix_class' => 'bdt-text-stroke--',
			]
		);

		$this->add_responsive_control(
			'text_stroke_width',
			[
				'label' => esc_html__('Text Stroke Width', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => '-webkit-text-stroke-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'show_text_stroke' => 'yes'
				]
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => 'color: {{VALUE}}; -webkit-text-stroke-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'title_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title',
			]
		);

		$this->add_responsive_control(
			'title_space',
			[
				'label' => esc_html__('Top Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_post_title',
			[
				'label'     => esc_html__('Post Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_post_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'post_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'post_title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_title_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'post_title_radius',
			[
				'label'      => esc_html__('Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'post_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title',
			]
		);

		$this->add_responsive_control(
			'post_title_space',
			[
				'label' => esc_html__('Top Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-post-title' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Text Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-text',
			]
		);

		$this->add_responsive_control(
			'text_space',
			[
				'label' => esc_html__('Top Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button',
			]
		);

		$this->add_responsive_control(
			'button_top_space',
			[
				'label' => esc_html__('Top Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'button_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background_hover_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_content',
			[
				'label'     => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_pre_title',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_title',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_post_title',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_text',
							'value'    => 'yes',
						],
						[
							'name'     => 'show_button',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__('Content Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 200,
						'max' => 1500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-content-wrapper' => 'max-width: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_width',
			[
				'label' => esc_html__('Text Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 150,
						'max' => 1500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-content-wrapper .bdt-slideshow-text' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition'   => [
					'show_text' => 'yes',
				],

			]
		);

		$this->add_control(
			'content_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-content-wrapper' => 'background-color: {{VALUE}};',
				],				
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'content_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-content-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-content-wrapper',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'content_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-content-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-slideshow-items .bdt-slideshow-content-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation' => ['arrows', 'dots', 'both', 'arrows-thumbnavs', 'thumbnavs'],
				],
			]
		);

		$this->add_control(
			'heading_arrows',
			[
				'label'     => esc_html__('Arrows', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_size',
			[
				'label' => __('Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'default' => [
					'size' => 28,
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __('Hover Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next:hover i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-next i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev i, {{WRAPPER}} .bdt-slideshow .bdt-navigation-next i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['none', 'thumbnavs', 'dots'],
				],
			]
		);

		$this->add_control(
			'arrows_space',
			[
				'label' => __('Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'     => 'navigation',
							'value'    => 'both',
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

		$this->add_control(
			'heading_dots',
			[
				'label'     => esc_html__('Dots', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'navigation!' => ['arrows', 'arrows-thumbnavs', 'thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
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
					'{{WRAPPER}} .bdt-slideshow .bdt-dotnav li a' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-thumbnavs', 'thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'dots_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-dotnav li a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-thumbnavs', 'thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'active_dot_color',
			[
				'label'     => __('Active Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-dotnav li.bdt-active a' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['arrows', 'arrows-thumbnavs', 'thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'heading_thumbnavs',
			[
				'label'     => esc_html__('Thumbnavs', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_thumbnavs_style');

		$this->start_controls_tab(
			'tab_thumbnavs_normal',
			[
				'label'     => esc_html__('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav li a::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumbnavs_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow-thumbnav a',
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'thumbnavs_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slideshow-thumbnav a',
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_spacing',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumbnav:not(.bdt-thumbnav-vertical) > *' => 'padding-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-thumbnav:not(.bdt-thumbnav-vertical)'     => 'margin-left: -{{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-thumbnav-vertical > *'                    => 'padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-thumbnav-vertical'                        => 'margin-top: -{{SIZE}}{{UNIT}};',

				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbnavs_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_background_hover',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a:hover' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_overlay_color_hover',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav li a:hover:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumbnavs_hover_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow-thumbnav a:hover',
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
					'thumbnavs_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbnavs_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_background_active',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav.bdt-active a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_overlay_color_active',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav li.bdt-active a::after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'thumbnavs_active_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow-thumbnav.bdt-active a',
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_active_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow-thumbnav.bdt-active a' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
					'thumbnavs_border_border!' => '',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'heading_position',
			[
				'label'     => esc_html__('Position', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'before',
				'condition' => [
					'navigation!' => 'none',
				],
			]
		);

		$this->add_control(
			'arrows_ncx_position',
			[
				'label'   => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
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
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['arrows', 'arrows-thumbnavs'],
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

		$this->add_control(
			'arrows_ncy_position',
			[
				'label'   => __('Arrows Vertical Offset', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-slideshow .bdt-arrows-container' => 'transform: translate({{arrows_ncx_position.size}}px, {{SIZE}}px);',
				],
				'conditions'   => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['arrows', 'arrows-thumbnavs'],
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

		$this->add_control(
			'arrows_acx_position',
			[
				'label'   => __('Arrows Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-next' => 'right: {{SIZE}}px;',
				],
				'conditions' => [
					'terms' => [
						[
							'name'     => 'navigation',
							'operator' => 'in',
							'value'    => ['arrows', 'arrows-thumbnavs'],
						],
						[
							'name'  => 'arrows_position',
							'value' => 'center',
						],
					],
				],
			]
		);

		$this->add_control(
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

		$this->add_control(
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
					'{{WRAPPER}} .bdt-slideshow .bdt-dots-container' => 'transform: translate({{dots_nnx_position.size}}px, {{SIZE}}px);',
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

		$this->add_control(
			'thumbnavs_x_position',
			[
				'label'   => __('Thumbnavs Horizontal Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_y_position',
			[
				'label'   => __('Thumbnavs Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 15,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-thumbnav-wrapper .bdt-thumbnav-scroller' => 'transform: translate({{thumbnavs_x_position.size}}px, {{SIZE}}px);',
				],
				'condition' => [
					'navigation!' => ['arrows', 'dots', 'both', 'none'],
				],
			]
		);

		$this->add_control(
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

		$this->add_control(
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
					'{{WRAPPER}} .bdt-slideshow .bdt-arrows-dots-container' => 'transform: translate({{both_ncx_position.size}}px, {{SIZE}}px);',
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

		$this->add_control(
			'both_cx_position',
			[
				'label'   => __('Arrows Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 20,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-slideshow .bdt-navigation-next' => 'right: {{SIZE}}px;',
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

		$this->add_control(
			'both_cy_position',
			[
				'label'   => __('Dots Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -40,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-dots-container' => 'transform: translateY({{SIZE}}px);',
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

		$this->start_controls_section(
			'section_style_scroll_to_top',
			[
				'label'     => esc_html__('Scroll to Top', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'terms' => [
						[
							'name'     => 'scroll_to_section',
							'value'    => 'yes',
						],
						[
							'name'     => 'section_id',
							'operator' => '!=',
							'value'    => '',
						],
					],
				],
			]
		);

		$this->start_controls_tabs('tabs_scroll_to_top_style');

		$this->start_controls_tab(
			'scroll_to_top_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'scroll_to_top_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'scroll_to_top_shadow',
				'selector' => '{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'scroll_to_top_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_icon_size',
			[
				'label' => esc_html__('Icon Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a' => 'font-size: {{SIZE}}px;',
				],
			]
		);

		$this->add_responsive_control(
			'scroll_to_top_bottom_space',
			[
				'label' => esc_html__('Bottom Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 0,
						'max'  => 300,
						'step' => 5,
					],
				],
				'selectors'  => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section' => 'margin-bottom: {{SIZE}}px;',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'scroll_to_top_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'scroll_to_top_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_hover_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'scroll_to_top_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'scroll_to_top_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-slideshow .bdt-ep-scroll-to-section a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}

	protected function render_header() {
		$settings        = $this->get_settings_for_display();
		$slides_settings = [];

		$ratio = ($settings['slider_size_ratio']['width'] && $settings['slider_size_ratio']['height']) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '16:9';


		$this->add_render_attribute(
			[
				'slideshow' => [
					'data-bdt-slideshow' => [
						wp_json_encode([
							"animation"         => $settings["slider_animations"],
							"ratio"             => $ratio,
							"min-height"        => ($settings["slider_min_height"]["size"]) ? $settings["slider_min_height"]["size"] : false,
							"autoplay"          => ($settings["autoplay"]) ? true : false,
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => ("yes" === $settings["pause_on_hover"]) ? true : false,
							"velocity"          => ($settings["velocity"]["size"]) ? $settings["velocity"]["size"] : 1,
						])
					]
				]
			]
		);

		if ('both' == $settings['navigation']) {
			$this->add_render_attribute('slideshow', 'class', 'bdt-arrows-dots-align-' . $settings['both_position']);
		} elseif ('arrows' == $settings['navigation'] or 'arrows-thumbnavs' == $settings['navigation']) {
			$arrows_position = isset($settings['arrows_position']) ? $settings['arrows_position'] : '';
			$this->add_render_attribute('slideshow', 'class', 'bdt-arrows-align-' . $arrows_position);
		} elseif ('dots' == $settings['navigation']) {
			$this->add_render_attribute('slideshow', 'class', 'bdt-dots-align-' . $settings['dots_position']);
		}

		$slideshow_fullscreen = ($settings['slideshow_fullscreen']) ? ' bdt-height-viewport="offset-top: true"' : '';

		if (!isset($settings['scroll_to_section_icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['scroll_to_section_icon'] = 'fas fa-arrow-down';
		}

		$migrated  = isset($settings['__fa4_migrated']['slideshow_scroll_to_section_icon']);
		$is_new    = empty($settings['scroll_to_section_icon']) && Icons_Manager::is_migration_allowed();

?>
		<div <?php echo $this->get_render_attribute_string('slideshow'); ?>>
			<div class="bdt-position-relative bdt-visible-toggle">

				<?php if ($settings['scroll_to_section'] && $settings['section_id']) : ?>
					<div class="bdt-ep-scroll-to-section bdt-position-bottom-center">
						<a href="<?php echo esc_url($settings['section_id']); ?>" bdt-scroll>
							<span class="bdt-ep-scroll-to-section-icon">

								<?php if ($is_new || $migrated) :
									Icons_Manager::render_icon($settings['slideshow_scroll_to_section_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
								else : ?>
									<i class="<?php echo esc_attr($settings['scroll_to_section_icon']); ?>" aria-hidden="true"></i>
								<?php endif; ?>

							</span>
						</a>
					</div>
				<?php endif; ?>

				<ul class="bdt-slideshow-items" <?php echo esc_attr($slideshow_fullscreen); ?>>
				<?php
			}

			protected function render_footer() {
				$settings = $this->get_settings_for_display();

				?>
				</ul>
				<?php if ('both' == $settings['navigation']) : ?>
					<?php $this->render_both_navigation(); ?>

					<?php if ('center' === $settings['both_position']) : ?>
						<?php $this->render_dotnavs(); ?>
					<?php endif; ?>
				<?php elseif ('arrows-thumbnavs' == $settings['navigation']) : ?>
					<?php $this->render_navigation(); ?>
					<?php $this->render_thumbnavs(); ?>
				<?php elseif ('arrows' == $settings['navigation']) : ?>
					<?php $this->render_navigation(); ?>
				<?php elseif ('dots' == $settings['navigation']) : ?>
					<?php $this->render_dotnavs(); ?>
				<?php elseif ('thumbnavs' == $settings['navigation']) : ?>
					<?php $this->render_thumbnavs(); ?>
				<?php endif; ?>
			</div>
		</div>
	<?php
			}

			protected function render_navigation() {
				$settings = $this->get_settings_for_display();

				$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';

				$arrow_position = isset($settings['arrows_position']) ? $settings['arrows_position'] : '';

	?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($arrow_position . $hide_arrow_on_mobile); ?>">
			<div class="bdt-arrows-container bdt-slidenav-container">
				<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" data-bdt-slideshow-item="previous">
					<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
				<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slideshow-item="next">
					<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
				</a>
			</div>
		</div>
	<?php
			}

			protected function render_dotnavs() {
				$settings = $this->get_settings_for_display();

	?>
		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['dots_position']); ?>">
			<div class="bdt-dotnav-wrapper bdt-dots-container">
				<ul class="bdt-dotnav bdt-flex-center">

					<?php
					$bdt_counter = 0;

					foreach ($settings['slides'] as $slide) :
						echo '<li class="bdt-slideshow-dotnav bdt-active" bdt-slideshow-item="' . $bdt_counter . '"><a href="#"></a></li>';
						$bdt_counter++;
					endforeach; ?>

				</ul>
			</div>
		</div>
	<?php
			}

			protected function render_thumbnavs() {
				$settings = $this->get_settings_for_display();

				$thumbnavs_outside  = '';
				$vertical_thumbnav = '';

				if ('center-left' == $settings['thumbnavs_position'] || 'center-right' == $settings['thumbnavs_position']) {
					if ($settings['thumbnavs_outside']) {
						$thumbnavs_outside = '-out';
					}
					$vertical_thumbnav = ' bdt-thumbnav-vertical';
				}

	?>
		<div class="bdt-thumbnav-wrapper bdt-position-<?php echo esc_attr($settings['thumbnavs_position'] . $thumbnavs_outside); ?> bdt-position-small">
			<div class="bdt-thumbnav-scroller">
				<ul class="bdt-thumbnav<?php echo esc_attr($vertical_thumbnav); ?>">

					<?php
					$bdt_counter = 0;

					foreach ($settings['slides'] as $slide) :

						$slideshow_thumbnav = $this->render_thumbnavs_thumb($slide, 'thumbnail');
						echo '<li class="bdt-slideshow-thumbnav bdt-active" bdt-slideshow-item="' . $bdt_counter . '"><a class="bdt-overflow-hidden bdt-background-cover" href="#" style="background-image: url(' . esc_url($slideshow_thumbnav) . ')"></a></li>';
						$bdt_counter++;

					endforeach; ?>
				</ul>
			</div>
		</div>

	<?php
			}

			protected function render_both_navigation() {
				$settings = $this->get_settings_for_display();

				$hide_arrow_on_mobile = $settings['hide_arrow_on_mobile'] ? ' bdt-visible@m' : '';
	?>

		<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['both_position']); ?>">
			<div class="bdt-arrows-dots-container bdt-slidenav-container ">

				<div class="bdt-flex bdt-flex-middle">
					<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
						<a href="" class="bdt-navigation-prev bdt-slidenav-previous bdt-icon bdt-slidenav" bdt-slideshow-item="previous">
							<i class="ep-icon-arrow-left-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>

					</div>

					<?php if ('center' !== $settings['both_position']) : ?>
						<div class="bdt-dotnav-wrapper bdt-dots-container">
							<ul class="bdt-dotnav">
								<?php
								$bdt_counter = 0;

								foreach ($settings['slides'] as $slide) :
									echo '<li class="bdt-slideshow-dotnav bdt-active" data-bdt-slideshow-item="' . $bdt_counter . '"><a href="#"></a></li>';
									$bdt_counter++;
								endforeach; ?>
							</ul>
						</div>
					<?php endif; ?>

					<div class="<?php echo esc_attr($hide_arrow_on_mobile); ?>">
						<a href="" class="bdt-navigation-next bdt-slidenav-next bdt-icon bdt-slidenav" data-bdt-slideshow-item="next">
							<i class="ep-icon-arrow-right-<?php echo esc_attr($settings['nav_arrows_icon']); ?>" aria-hidden="true"></i>
						</a>

					</div>

				</div>
			</div>
		</div>

	<?php
			}

			protected function render_thumbnavs_thumb($image, $size) {
				$image_url = wp_get_attachment_image_src($image['image']['id'], $size);

				$image_url = ('' != $image_url) ? $image_url[0] : $image['image']['url'];

				return $image_url;
			}

			protected function render_item_image($image) {
				$image_src = wp_get_attachment_image_src($image['image']['id'], 'full');

				if ($image_src) :
					echo '<img src="' . esc_url($image_src[0]) . '" alt="' . get_the_title() . '" class="bdt-cover" data-bdt-cover="">';
				endif;

				return 0;
			}

			protected function render_item_video($link) {
				$video_src = $link['video_link'];

	?>
		<video autoplay loop muted playslinline bdt-cover class="bdt-cover">
			<source src="<?php echo  $video_src; ?>" type="video/mp4">
		</video>
	<?php
			}

			protected function render_item_youtube($link) {
				$match = [];
				$id = (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $link['youtube_link'], $match)) ? $match[1] : false;
				$url = '//www.youtube.com/embed/' . $id . '?autoplay=1&amp;controls=0&amp;showinfo=0&amp;rel=0&amp;loop=1&amp;modestbranding=1&amp;wmode=transparent&amp;playsinline=1';

	?>
		<iframe src="<?php echo  esc_url($url); ?>" frameborder="0" allowfullscreen bdt-cover class="bdt-cover"></iframe>
	<?php
			}

			protected function render_item_content($content) {
				$settings            = $this->get_settings_for_display();
				$animation           = ($settings['button_hover_animation']) ? ' elementor-animation-' . $settings['button_hover_animation'] : '';

				$this->add_render_attribute('parallax_pre_title', 'class', 'bdt-slideshow-pre-title bdt-display-inline-block', true);
				$this->add_render_attribute('parallax_title', 'class', 'bdt-slideshow-title bdt-display-inline-block', true);
				$this->add_render_attribute('parallax_post_title', 'class', 'bdt-slideshow-post-title bdt-display-inline-block', true);
				$this->add_render_attribute('parallax_text', 'class', 'bdt-slideshow-text', true);
				$this->add_render_attribute('parallax_button', 'class', 'bdt-slideshow-button bdt-display-inline-block' . $animation, true);

				if ($settings['parallax_pre_title']) {
					$this->add_render_attribute(
						[
							'parallax_pre_title' => [
								'bdt-slideshow-parallax' => [
									wp_json_encode(array_filter([
										"x" => $settings["parallax_pre_title_x_start"]["size"] . ", " . $settings["parallax_pre_title_x_end"]["size"],
										"y" => $settings["parallax_pre_title_y_start"]["size"] . ", " . $settings["parallax_pre_title_y_end"]["size"],
									])),
								],
							],
						],
						'',
						'',
						true
					);
				}

				if ($settings['parallax_title']) {
					$this->add_render_attribute(
						[
							'parallax_title' => [
								'bdt-slideshow-parallax' => [
									wp_json_encode(array_filter([
										"x" => $settings["parallax_title_x_start"]["size"] . ", " . $settings["parallax_title_x_end"]["size"],
										"y" => $settings["parallax_title_y_start"]["size"] . ", " . $settings["parallax_title_y_end"]["size"],
									])),
								],
							],
						],
						'',
						'',
						true
					);
				}

				if ($settings['parallax_post_title']) {
					$this->add_render_attribute(
						[
							'parallax_post_title' => [
								'bdt-slideshow-parallax' => [
									wp_json_encode(array_filter([
										"x" => $settings["parallax_post_title_x_start"]["size"] . ", " . $settings["parallax_post_title_x_end"]["size"],
										"y" => $settings["parallax_post_title_y_start"]["size"] . ", " . $settings["parallax_post_title_y_end"]["size"],
									])),
								],
							],
						],
						'',
						'',
						true
					);
				}

				if ($settings['parallax_text']) {
					$this->add_render_attribute(
						[
							'parallax_text' => [
								'bdt-slideshow-parallax' => [
									wp_json_encode(array_filter([
										"x" => $settings["parallax_text_x_start"]["size"] . ", " . $settings["parallax_text_x_end"]["size"],
										"y" => $settings["parallax_text_y_start"]["size"] . ", " . $settings["parallax_text_y_end"]["size"],
									])),
								],
							],
						],
						'',
						'',
						true
					);
				}

				if ($settings['parallax_button']) {
					$this->add_render_attribute(
						[
							'parallax_button' => [
								'bdt-slideshow-parallax' => [
									wp_json_encode(array_filter([
										"x" => $settings["parallax_button_x_start"]["size"] . ", " . $settings["parallax_button_x_end"]["size"],
										"y" => $settings["parallax_button_y_start"]["size"] . ", " . $settings["parallax_button_y_end"]["size"],
									])),
								],
							],
						],
						'',
						'',
						true
					);
				}

				if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
					// add old default
					$settings['icon'] = 'fas fa-arrow-right';
				}

				$migrated  = isset($settings['__fa4_migrated']['slideshow_icon']);
				$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

	?>
		<div class="bdt-slideshow-content-wrapper bdt-position-z-index bdt-position-<?php echo esc_attr($settings['content_position']); ?> bdt-position-large bdt-text-<?php echo esc_attr($settings['content_align']); ?>">
			<?php if ($content['pre_title'] && ($settings['show_pre_title'])) : ?>
				<div>
					<h4 <?php echo $this->get_render_attribute_string('parallax_pre_title'); ?>><?php echo esc_attr($content['pre_title']); ?></h4>
				</div>
			<?php endif; ?>

			<?php if ($content['title'] && ($settings['show_title'])) : ?>
				<div>
					<<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?> <?php echo $this->get_render_attribute_string('parallax_title'); ?>><?php echo wp_kses_post($content['title']); ?></<?php echo Utils::get_valid_html_tag($settings['title_tag']); ?>>
				</div>
			<?php endif; ?>

			<?php if ($content['post_title'] && ($settings['show_post_title'])) : ?>
				<div>
					<h4 <?php echo $this->get_render_attribute_string('parallax_post_title'); ?>><?php echo esc_attr($content['post_title']); ?></h4>
				</div>
			<?php endif; ?>

			<?php if ($content['text'] && ($settings['show_text'])) : ?>
				<div <?php echo $this->get_render_attribute_string('parallax_text'); ?>><?php echo wp_kses_post($content['text']); ?></div>
			<?php endif; ?>

			<?php if ((!empty($content['button_link']['url'])) && ($settings['show_button']) && ($settings['button_text'])) : ?>
				<div><a href="<?php echo esc_url($content['button_link']['url']); ?>" target="<?php echo ($content['button_link']['is_external']) ? '_blank' : '_self'; ?>" rel="<?php echo esc_url($content['button_link']['nofollow']) ? 'nofollow' : 'noreferrer'; ?>" <?php echo $this->get_render_attribute_string('parallax_button'); ?>><?php echo esc_attr($settings['button_text']); ?>

						<?php if ($settings['slideshow_icon']['value']) : ?>
							<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?> bdt-slideshow-button-icon-<?php echo esc_attr($settings['icon_align']); ?>">

								<?php if ($is_new || $migrated) :
									Icons_Manager::render_icon($settings['slideshow_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
								else : ?>
									<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
								<?php endif; ?>

							</span>
						<?php endif; ?>

					</a></div>
			<?php endif; ?>
		</div>

		<?php
			}

			public function render() {
				$settings         = $this->get_settings_for_display();
				$kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

				$this->render_header();

				foreach ($settings['slides'] as $slide) : ?>

			<li class="bdt-slideshow-item elementor-repeater-item-<?php echo esc_attr($slide['_id']); ?>">
				<?php if ($settings['kenburns_animation']) : ?>
					<div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
					<?php endif; ?>

					<?php if (($slide['background'] == 'image') && $slide['image']) : ?>
						<?php $this->render_item_image($slide); ?>
					<?php elseif (($slide['background'] == 'video') && $slide['video_link']) : ?>
						<?php $this->render_item_video($slide); ?>
					<?php elseif (($slide['background'] == 'youtube') && $slide['youtube_link']) : ?>
						<?php $this->render_item_youtube($slide); ?>
					<?php endif; ?>

					<?php if ($settings['kenburns_animation']) : ?>
					</div>
				<?php endif; ?>

				<?php if ('none' !== $settings['overlay']) :
						$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
					<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
				<?php endif; ?>

				<?php $this->render_item_content($slide); ?>
			</li>

<?php endforeach;

				$this->render_footer();
			}
		}
