<?php

namespace ElementPack\Modules\ThumbGallery\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack\Utils;
use Elementor\Icons_Manager;

use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;
use ElementPack\Modules\ThumbGallery\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Thumb_Gallery extends Module_Base {
	use Group_Control_Query;
	public $_query = null;

	public function get_name() {
		return 'bdt-thumb-gallery';
	}

	public function get_title() {
		return BDTEP . esc_html__('Thumb Gallery', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-thumb-gallery';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['thumb', 'gallery', 'image', 'photo'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-font', 'ep-thumb-gallery'];
		}
	}

	public function get_script_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['imagesloaded', 'ep-scripts'];
		} else {
			return ['imagesloaded'];
		}
	}

	public function get_query() {
		return $this->_query;
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/NJ5ZR-9ODus';
	}

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Custom($this));
	}

	public function register_controls() {
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
				'options' => element_pack_position(),
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_title',
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
						'min' => 50,
						'max' => 1500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_title',
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
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_title',
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
				'default'   => 'h3',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'title_link_option',
			[
				'label'   => esc_html__('Title Link Option?', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
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
			'excerpt_length',
			[
				'label'     => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 25,
				'condition' => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'   => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_button',
			[
				'label'   => esc_html__('Button', 'bdthemes-element-pack'),
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
				'label' => esc_html__('Fullscreen', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_button',
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
			'thumb_gallery_icon',
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
					'thumb_gallery_icon[value]!' => '',
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
					'thumb_gallery_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_navigation',
			[
				'label' => esc_html__('Navigation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'navigation',
			[
				'label'   => esc_html__('Navigation', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'thumbnavs',
				'options' => [
					'arrows'           => esc_html__('Arrows', 'bdthemes-element-pack'),
					'thumbnavs'        => esc_html__('Thumbnavs', 'bdthemes-element-pack'),
					'arrows-thumbnavs' => esc_html__('Arrows and Thumbnavs', 'bdthemes-element-pack'),
					'none'             => esc_html__('None', 'bdthemes-element-pack'),
				],
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
					'navigation' => ['arrows-thumbnavs', 'arrows'],
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
					'navigation!' => ['thumbnavs', 'none'],
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
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_outside',
			[
				'label'      => esc_html__('Thumbnavs Outside', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SWITCHER,
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
				'label' => esc_html__('Thumbnavs Width', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav a' => 'width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_height',
			[
				'label' => esc_html__('Thumbnavs Height', 'bdthemes-element-pack'),
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
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav a' => 'height: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->end_controls_section();

		//New Query Builder Settings
		$this->start_controls_section(
			'section_post_query_builder',
			[
				'label' => __('Query', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->register_query_builder_controls();

		$this->update_control(
			'posts_per_page',
			[
				'default' => 5,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
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
				'label' => esc_html__('Pause on Hover', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'speed',
			[
				'label'   => esc_html__('Animation Speed', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::NUMBER,
				'default' => 500,
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

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_content',
			[
				'label' => esc_html__('Content', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'     => 'show_title',
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

		$this->add_control(
			'content_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-content' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'content_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-content',
			]
		);

		$this->add_control(
			'content_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-content' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'content_transition',
			[
				'label'   => esc_html__('Content Transition', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'fade',
				'options' => element_pack_transition_options(),
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_title',
			[
				'label'     => esc_html__('Title', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_title' => 'yes',
				]
			]
		);

		$this->add_control(
			'title_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-title' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-title' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-title' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-title',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_text',
			[
				'label'     => esc_html__('Text', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_text' => 'yes',
				]
			]
		);

		$this->add_control(
			'text_background',
			[
				'label'     => esc_html__('Background', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-text' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-text' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-text' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'text_space',
			[
				'label' => esc_html__('Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-text',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_button',
			[
				'label'     => esc_html__('Button', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_button' => 'yes'
				]
			]
		);

		$this->start_controls_tabs('tabs_button_style');

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'button_shadow',
				'selector' => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button',
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_space',
			[
				'label' => esc_html__('Space', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'button_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button',
			]
		);

		$this->add_control(
			'border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				//'scheme'   => Schemes\Typography::TYPOGRAPHY_4,
				'selector' => '{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumb-gallery-button:hover' => 'border-color: {{VALUE}};',
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
			'section_style_navigation',
			[
				'label'     => __('Navigation', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'navigation!' => 'none',
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
					'navigation!' => ['thumbnavs', 'none'],
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
						'min' => 10,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next i' => 'font-size: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_background',
			[
				'label'     => __('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_background',
			[
				'label'     => __('Hover Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next:hover i' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'     => __('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev:hover i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next:hover i' => 'color: {{VALUE}}',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next i' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'arrows_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev i, {{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next i' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'navigation!' => ['thumbnavs', 'none'],
				],
			]
		);

		$this->add_responsive_control(
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev' => 'margin-right: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next' => 'margin-left: {{SIZE}}px;',
				],
				'conditions' => [
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
			'heading_thumbnavs',
			[
				'label'     => esc_html__('Thumbnavs', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::HEADING,
				'separator' => 'after',
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->start_controls_tabs('tabs_thumbnavs_style');

		$this->start_controls_tab(
			'tab_thumbnavs_normal',
			[
				'label'     => esc_html__('Normal', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_background',
			[
				'label'     => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav a:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'thumbnavs_shadow',
				'selector'  => '{{WRAPPER}} .bdt-thumb-gallery-thumbnav a',
				'condition' => [
					'navigation!' => ['arrows', 'none'],
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
				'selector'    => '{{WRAPPER}} .bdt-thumb-gallery-thumbnav a',
				'condition' => [
					'navigation!' => ['arrows', 'none'],
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
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_spacing',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'selectors'  => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumbnav' => 'grid-gap: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .bdt-thumbnav:not(.bdt-thumbnav-vertical) > *' => 'padding-left: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .bdt-thumbnav:not(.bdt-thumbnav-vertical)'     => 'margin-left: -{{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .bdt-thumbnav-vertical > *'                    => 'padding-top: {{SIZE}}{{UNIT}};',
					// '{{WRAPPER}} .bdt-thumbnav-vertical'                        => 'margin-top: -{{SIZE}}{{UNIT}};',

				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_thumbnavs_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
				'condition' => [
					'navigation!' => ['arrows', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_overlay_active',
			[
				'label'     => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav.bdt-active a:after, {{WRAPPER}} .bdt-thumb-gallery-thumbnav a:hover:after' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'      => 'thumbnavs_hover_shadow',
				'selector'  => '{{WRAPPER}} .bdt-thumb-gallery-thumbnav a:hover',
				'condition' => [
					'navigation!' => ['arrows', 'both', 'none'],
				],
			]
		);

		$this->add_control(
			'thumbnavs_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery-thumbnav a:hover' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'thumbnavs_border_border!' => '',
					'navigation!' => ['arrows', 'both', 'none'],
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
				'separator' => 'after',
				'condition' => [
					'navigation!' => 'none',
				],
			]
		);

		$this->add_responsive_control(
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

		$this->add_responsive_control(
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-arrows-container' => 'transform: translate({{arrows_ncx_position.size}}px, {{SIZE}}px);',
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

		$this->add_responsive_control(
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
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-prev' => 'left: {{SIZE}}px;',
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-navigation-next' => 'right: {{SIZE}}px;',
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

		$this->add_responsive_control(
			'thumbnavs_x_position',
			[
				'label'   => __('Thumbnavs Horizontal Offset', 'bdthemes-element-pack'),
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
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->add_responsive_control(
			'thumbnavs_y_position',
			[
				'label'   => __('Thumbnavs Vertical Offset', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => -30,
				],
				'range' => [
					'px' => [
						'min' => -200,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-thumb-gallery .bdt-thumbnav-wrapper .bdt-thumbnav' => 'transform: translate({{thumbnavs_x_position.size}}px, {{SIZE}}px);',
				],
				'condition' => [
					'navigation!' => ['arrows', 'none'],
				],
			]
		);

		$this->end_controls_section();
	}

	public function get_taxonomies() {
		$taxonomies = get_taxonomies(['show_in_nav_menus' => true], 'objects');

		$options = ['' => ''];

		foreach ($taxonomies as $taxonomy) {
			$options[$taxonomy->name] = $taxonomy->label;
		}

		return $options;
	}

	public function query_posts($posts_per_page) {
		$settings = $this->get_settings_for_display();

		$args = [];
		if ($posts_per_page) {
			$args['posts_per_page'] = $posts_per_page;
			$args['paged']  = max(1, get_query_var('paged'), get_query_var('page'));
		}

		$default = $this->getGroupControlQueryArgs();
		$args = array_merge($default, $args);

		$this->_query = new \WP_Query($args);
	}

	public function render() {
		$settings = $this->get_settings_for_display();

		$posts_per_page = $settings['posts_per_page'];

		$this->query_posts($posts_per_page);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		add_filter('excerpt_more', [$this, 'filter_excerpt_more'], 15);
		add_filter('excerpt_length', [$this, 'filter_excerpt_length'], 15);

		$this->render_header();

		$this->render_post();

		$this->render_footer();

		remove_filter('excerpt_length', [$this, 'filter_excerpt_length'], 15);
		remove_filter('excerpt_more', [$this, 'filter_excerpt_more'], 15);

		wp_reset_postdata();
	}

	public function render_title() {
		$settings = $this->get_settings_for_display();
		if (!$this->get_settings('show_title')) {
			return;
		}

		$tag = $this->get_settings('title_tag');
		$classes = ['bdt-thumb-gallery-title'];
?>

		<?php if ('yes' == $settings['title_link_option']) { ?>
			<a href="<?php echo esc_url(get_permalink()); ?>">
			<?php } ?>

			<<?php echo Utils::get_valid_html_tag($tag) ?> class="<?php echo implode(" ", $classes); ?>">
				<?php the_title(); ?>
			</<?php echo Utils::get_valid_html_tag($tag); ?>>

			<?php if ('yes' == $settings['title_link_option']) { ?>
			</a>
		<?php } ?>

	<?php
	}

	public function render_excerpt() {
		if (!$this->get_settings('show_text')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

	?>
		<div class="bdt-thumb-gallery-text bdt-text-small">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo element_pack_custom_excerpt($this->get_settings_for_display('excerpt_length'), $strip_shortcode);
			}
			?>
		</div>
	<?php

	}

	public function render_button() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_button')) {
			return;
		}

		$animation = ($settings['button_hover_animation']) ? ' elementor-animation-' . $settings['button_hover_animation'] : '';

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['thumb_gallery_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

	?>
		<div class="bdt-thumb-gallery-button-wrapper">
			<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-thumb-gallery-button bdt-display-inline-block<?php echo esc_attr($animation); ?>">
				<?php echo esc_attr($settings['button_text']); ?>

				<?php if ($settings['thumb_gallery_icon']['value']) : ?>
					<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

						<?php if ($is_new || $migrated) :
							Icons_Manager::render_icon($settings['thumb_gallery_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
						else : ?>
							<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
						<?php endif; ?>

					</span>
				<?php endif; ?>
			</a>
		</div>
	<?php
	}

	public function render_header() {
		$id       = $this->get_id();
		$settings = $this->get_settings_for_display();

		$ratio = ($settings['slider_size_ratio']['width'] && $settings['slider_size_ratio']['height']) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '';

		$this->add_render_attribute(
			[
				'slider-settings' => [
					'class'         => [
						'bdt-position-relative',
						'bdt-visible-toggle'
					],
					'data-bdt-slideshow' => [
						wp_json_encode(array_filter([
							"animation"         => $settings["slider_animations"],
							"ratio"             => $ratio,
							"min-height"        => $settings["slider_min_height"]["size"],
							"autoplay"          => $settings["autoplay"],
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => $settings["pause_on_hover"]
						]))
					]
				]
			]
		);

	?>
		<div id="bdt-thumb-gallery-<?php echo esc_attr($id); ?>" class="bdt-thumb-gallery">
			<div <?php echo ($this->get_render_attribute_string('slider-settings')) ?>>
			<?php
		}

		public function render_footer() {
			?>
			</div>
		</div>
	<?php
		}

		public function render_loop_items() {
			$settings         = $this->get_settings_for_display();
			$kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

			$posts_per_page = $settings['posts_per_page'];

			$this->query_posts($posts_per_page);

			$content_transition = $settings['content_transition'] ? ' bdt-transition-' . $settings['content_transition'] : '';

			$wp_query = $this->get_query();

			if (!$wp_query->found_posts) {
				return;
			}

			$fullscreen = $settings['slideshow_fullscreen'] ? ' bdt-height-viewport="offset-top: true;"' : '';

	?>
		<ul class="bdt-slideshow-items" <?php echo esc_attr($fullscreen); ?>>

			<?php

			while ($wp_query->have_posts()) {
				$wp_query->the_post();

				$placeholder_image_src = Utils::get_placeholder_image_src();

				$gallery_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'full');

			?>
				<li class="bdt-slideshow-item">
					<?php if ($settings['kenburns_animation']) : ?>
						<div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center-left">
						<?php
					endif;

					if (!$gallery_thumbnail) {
						printf('<img src="%1$s" alt="%2$s">', $placeholder_image_src, esc_html(get_the_title()));
					} else {
						print(wp_get_attachment_image(
							get_post_thumbnail_id(),
							'full',
							false,
							[
								'alt' => esc_html(get_the_title())
							]
						));
					}
						?>

						<?php if ($settings['kenburns_animation']) : ?>
						</div>
					<?php endif; ?>
					<div class="bdt-position-z-index bdt-position-<?php echo esc_attr($settings['content_position']); ?> bdt-position-large">
						<?php if ($settings['show_title'] || $settings['show_text'] || $settings['show_button']) : ?>
							<div class="bdt-text-<?php echo esc_attr($settings['content_align']); ?>">
								<div class="bdt-thumb-gallery-content<?php echo esc_attr($content_transition); ?>">
									<?php $this->render_title(); ?>
									<?php $this->render_excerpt(); ?>
									<?php $this->render_button(); ?>
								</div>
							</div>
						<?php endif; ?>
					</div>
				</li>
			<?php
			}

			wp_reset_postdata();

			?>
		</ul>
	<?php
		}

		public function render_navigation() {
			$settings = $this->get_settings_for_display();

			if ('thumbnavs' == $settings['navigation'] || 'none' == $settings['navigation']) {
				return;
			}

	?>
		<div class="bdt-thumb-gallery-navigation-wrapper bdt-position-z-index bdt-visible@m bdt-position-<?php echo esc_attr($settings['arrows_position']); ?>">
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

		public function render_thumbnavs() {
			$settings = $this->get_settings_for_display();

			if ('arrows' == $settings['navigation'] or 'none' == $settings['navigation']) {
				return;
			}

			$thumbnavs_outside = '';
			$vertical_thumbnav = '';

			$posts_per_page = $settings['posts_per_page'];

			$this->query_posts($posts_per_page);

			$wp_query = $this->get_query();

			if (!$wp_query->found_posts) {
				return;
			}
			if ('center-left' == $settings['thumbnavs_position'] || 'center-right' == $settings['thumbnavs_position']) {
				if ($settings['thumbnavs_outside']) {
					$thumbnavs_outside = '-out';
				}
				$vertical_thumbnav = ' bdt-thumbnav-vertical';
			}

	?>
		<div class="bdt-thumbnav-wrapper bdt-position-<?php echo esc_attr($settings['thumbnavs_position'] . $thumbnavs_outside); ?> bdt-position-small">
			<ul class="bdt-thumbnav<?php echo esc_attr($vertical_thumbnav); ?>">

				<?php
				$bdt_counter = 0;

				while ($wp_query->have_posts()) {
					$wp_query->the_post();

					$placeholder_image_src = Utils::get_placeholder_image_src();

					$gallery_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');

					if (!$gallery_thumbnail) {
						$gallery_thumbnail = $placeholder_image_src;
					} else {
						$gallery_thumbnail = $gallery_thumbnail[0];
					}

					echo '<li class="bdt-thumb-gallery-thumbnav" data-bdt-slideshow-item="' . $bdt_counter . '"><a class="bdt-overflow-hidden bdt-background-cover" href="#" style="background-image: url(' . esc_url($gallery_thumbnail) . ')"></a></li>';
					$bdt_counter++;
				}

				wp_reset_postdata();

				?>
			</ul>
		</div>
<?php
		}

		public function render_post() {
			$this->render_loop_items();
			$this->render_navigation();
			$this->render_thumbnavs();
		}
	}
