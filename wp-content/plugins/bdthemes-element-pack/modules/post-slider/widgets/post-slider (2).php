<?php

namespace ElementPack\Modules\PostSlider\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Icons_Manager;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Background;
use ElementPack\Utils;

use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

use ElementPack\Modules\PostSlider\Skins;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

/**
 * Class Post Slider
 */
class Post_Slider extends Module_Base {
	use Group_Control_Query;

	public $_query = null;

	public function get_name() {
		return 'bdt-post-slider';
	}

	public function get_title() {
		return BDTEP . __('Post Slider', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-slider';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'slider', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-post-slider'];
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

	public function register_skins() {
		$this->add_skin(new Skins\Skin_Vast($this));
		$this->add_skin(new Skins\Skin_Hazel($this));
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/oPYzWVLPF7A';
	}

	public function register_controls() {

		$this->start_controls_section(
			'section_content_layout',
			[
				'label' => __('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'show_tag',
			[
				'label'   => __('Show Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_title',
			[
				'label'   => __('Show Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tag',
			[
				'label'     => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h1',
				'condition' => [
					'show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumb_title_tag',
			[
				'label'     => __('Thumb Title HTML Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => element_pack_title_tags(),
				'default'   => 'h6',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'show_text',
			[
				'label'   => __('Show Text', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'separator' => 'before'
			]
		);

		$this->add_control(
			'excerpt_length',
			[
				'label'     => __('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 35,
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
			'hide_on_tablet',
			[
				'label'   => esc_html__('Text Hide On Tablet', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'   => [
					'show_text' => 'yes',
				],
			]
		);

		$this->add_control(
			'hide_on_mobile',
			[
				'label'   => esc_html__('Text Hide On Mobile', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition'   => [
					'show_text' => 'yes',
					'hide_on_tablet' => '',
				],
			]
		);

		$this->add_control(
			'show_button',
			[
				'label' => __('Read More Button', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'   => __('Meta', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'show_meta' => 'yes'
				]
			]
		);

		$this->add_control(
			'human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'human_diff_time' => 'yes',
					'show_meta' => 'yes'
				]
			]
		);

		$this->add_control(
			'show_pagination_thumb',
			[
				'label'     => __('Pagination Thumb', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'slider_size_ratio',
			[
				'label'       => esc_html__('Size Ratio', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::IMAGE_DIMENSIONS,
				'description' => 'Slider ratio to widht and height, such as 16:9',
				'condition'   => [
					'_skin!' => 'bdt-vast',
				],
			]
		);

		$this->add_control(
			'slider_min_height',
			[
				'label'     => esc_html__('Slider Minimum Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'_skin!' => 'bdt-vast',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1024,
					],
				],
			]
		);

		$this->add_control(
			'slider_max_height',
			[
				'label'     => esc_html__('Slider Max Height', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'_skin!' => 'bdt-vast',
				],
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1024,
					],
				],
			]
		);

		$this->add_responsive_control(
			'slider_container_width',
			[
				'label' => esc_html__('Container Width', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 50,
						'max' => 1500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-content-wrap' => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-slider-content'      => 'max-width: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-slider-pagination'   => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => '',
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
						'min' => 300,
						'max' => 1500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-content' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'content_align',
			[
				'label'   => esc_html__('Content Alignment', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::CHOOSE,
				'options' => [
					'left'    => [
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
				],
				'description'  => 'Use align to match position',
				'default'      => 'left',
				'prefix_class' => 'elementor-align-',
			]
		);

		$this->add_control(
			'hazel_prev_text',
			[
				'label'       => esc_html__('Prev Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('PREV', 'bdthemes-element-pack'),
				'separator'	  => 'before',
				'condition'   => [
					'_skin' => 'bdt-hazel',
				],
			]
		);

		$this->add_control(
			'hazel_next_text',
			[
				'label'       => esc_html__('Next Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('NEXT', 'bdthemes-element-pack'),
				'condition'   => [
					'_skin' => 'bdt-hazel',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'      => 'thumbnail',
				'default'   => 'full',
				'fields_options' => [
					'size' => [
						'label' => esc_html__('Image Size', 'bdthemes-element-pack') . BDTEP_NC,
					],
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
			'post_slider_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
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
					'post_slider_icon[value]!' => '',
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
					'post_slider_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-slider .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
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
				'default' => 4,
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_post_slider_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'autoplay',
			[
				'label' => esc_html__('Autoplay', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
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
				'default'   => 'fade',
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
				'condition' => [
					'_skin' => ''
				]
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

		$this->start_controls_section(
			'section_style_slider',
			[
				'label'     => esc_html__('Slider', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => ''
				]
			]
		);

		$this->add_control(
			'overlay',
			[
				'label'   => esc_html__('Overlay', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'background',
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
				'default'   => '#333333',
				'condition' => [
					'overlay' => ['background', 'blend']
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-overlay-default' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'overlay_opacity',
			[
				'label'   => esc_html__('Overlay Opacity', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'default' => [
					'size' => 0.4,
				],
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.1,
						'step' => 0.01,
					],
				],
				'condition' => [
					'overlay' => ['background', 'blend']
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-overlay-default' => 'opacity: {{SIZE}};'
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
			'section_style_bottom_part',
			[
				'label'     => esc_html__('Bottom Part', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-vast'
				]
			]
		);

		$this->add_control(
			'bottom_part_bg',
			[
				'label'       => esc_html__('Background', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-content' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_responsive_control(
			'content_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_right_part',
			[
				'label'     => esc_html__('Right Part', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-hazel'
				]
			]
		);

		$this->add_control(
			'right_part_bg',
			[
				'label'       => esc_html__('Description Background', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-thumbnail ~ div' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_color',
			[
				'label'       => esc_html__('Navigation Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_hover_color',
			[
				'label'       => esc_html__('Navigation Hover Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_bg',
			[
				'label'       => esc_html__('Navigation Background', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_hover_bg',
			[
				'label'       => esc_html__('Navigation Hover Background', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a:hover' => 'background-color: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_arrows_color',
			[
				'label'       => esc_html__('Arrows Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a svg polyline' => 'stroke: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_nav_hover_arrows_color',
			[
				'label'       => esc_html__('Arrows Hover Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a:hover svg polyline' => 'stroke: {{VALUE}};'
				]
			]
		);

		$this->add_control(
			'right_part_line_color',
			[
				'label'       => esc_html__('Line Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'separator'   => 'before',
				'selectors'   => [
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner a:first-child:after' => 'background-color: {{VALUE}};',
					'{{WRAPPER}} .skin-hazel .bdt-post-slider-navigation-inner'                     => 'border-top-color: {{VALUE}};'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_tag',
			[
				'label'     => esc_html__('Tag', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_tag' => ['yes'],
				],
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-tag-wrap span a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tag_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-tag-wrap span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'     => 'tag_border',
				'label'    => __('Border', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider-tag-wrap span',
			]
		);

		$this->add_responsive_control(
			'tag_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-slider-tag-wrap span' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'tag_border_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-tag-wrap span' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'tag_space_between',
			[
				'label' => esc_html__('Space Between', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-tag-wrap span+span' => 'margin-left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'tag_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider-tag-wrap span',
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
			'title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-title' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider-title',
			]
		);

		$this->add_responsive_control(
			'title_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-title' => 'margin-top: {{SIZE}}{{UNIT}};',
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
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-text' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'text_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider .bdt-post-slider-text',
			]
		);

		$this->add_responsive_control(
			'text_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-text' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_meta',
			[
				'label' => esc_html__('Meta', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_meta' => 'yes',
				]
			]
		);

		$this->add_control(
			'meta_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta a, {{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta span, {{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta .bdt-author' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'meta_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'meta_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta',
			]
		);

		$this->add_responsive_control(
			'meta_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-meta' => 'margin-top: {{SIZE}}{{UNIT}};',
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
			'tab_button_normal',
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
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label' => esc_html__('Spacing', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button-wrap' => 'margin-top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'button_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button',
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
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button:hover' => 'color: {{VALUE}};',
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button:hover svg' => 'fill: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'button_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button:hover' => 'background-color: {{VALUE}};',
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
					'{{WRAPPER}} .bdt-post-slider .bdt-post-slider-button:hover' => 'border-color: {{VALUE}};',
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
			'section_style_pagination',
			[
				'label'     => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => '',
				],
			]
		);

		$this->add_control(
			'pagination_text_color',
			[
				'label'     => esc_html__('Pagination Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-pagination .thumb-title-default-skin'   => 'color: {{VALUE}}',
					'{{WRAPPER}} .bdt-post-slider-pagination span' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'thumb_background_color',
			[
				'label'     => esc_html__('Thumb Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-thumb-wrap' => 'background-color: {{VALUE}}',
				],
				'condition' => [
					'show_pagination_thumb' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumb_opacity',
			[
				'label' => esc_html__('Thumb Opacity', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 1,
						'min'  => 0.1,
						'step' => 0.01,
					],
				],
				'condition' => [
					'show_pagination_thumb' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-thumb-wrap img' => 'opacity: {{SIZE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'thumb_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-post-slider-thumb-wrap',
				'condition' => [
					'show_pagination_thumb' => 'yes',
				],
			]
		);

		$this->add_control(
			'thumb_border_radius',
			[
				'label' => __('Thumb Border Radius', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'condition' => [
					'show_pagination_thumb' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-thumb-wrap' => 'border-radius: {{SIZE}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_responsive_control(
			'thumb_size',
			[
				'label' => esc_html__('Thumb Size', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max'  => 300,
						'min'  => 10,
					],
				],
				'condition' => [
					'show_pagination_thumb' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-pagination .bdt-post-slider-thumb-wrap img' => 'height: {{SIZE}}{{UNIT}}; width: {{SIZE}}{{UNIT}};'
				]
			]
		);

		$this->add_control(
			'pagination_border_color',
			[
				'label'     => esc_html__('Upper Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-pagination .bdt-thumbnav' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[
				'label'     => esc_html__('Active Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-slider-pagination .bdt-active .bdt-post-slider-pagination-item' => 'border-color: {{VALUE}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_title_typography',
				'label'    => esc_html__('Title Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider-pagination .bdt-thumbnav .bdt-post-slider-pagination-item .thumb-title-default-skin',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'pagination_date_typography',
				'label'    => esc_html__('Date Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-slider-pagination .bdt-thumbnav .bdt-post-slider-pagination-item .bdt-post-slider-date',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_arrows',
			[
				'label'     => esc_html__('Arrows', 'bdthemes-element-pack') . BDTEP_NC,
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin' => 'bdt-vast'
				]
			]
		);

		$this->start_controls_tabs('tabs_arrows_style');

		$this->start_controls_tab(
			'tab_arrows_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'arrows_color',
			[
				'label'       => esc_html__('Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'arrows_background',
				'selector' => '{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'arrows_border',
				'label'       => __('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a',
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'arrows_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_margin',
			[
				'label'      => __('Marign', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'arrows_size',
			[
				'label'      => __('Size', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a' => 'font-size: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_arrows_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'arrows_hover_color',
			[
				'label'       => esc_html__('Color', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::COLOR,
				'selectors'   => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a:hover' => 'color: {{VALUE}};'
				]
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'arrows_hover_background',
				'selector' => '{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a:hover',
			]
		);

		$this->add_control(
			'arrows_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'arrows_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .skin-vast .bdt-post-slider-navigation a:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

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

	public function get_posts_tags() {
		$taxonomy = $this->get_settings('taxonomy');

		foreach ($this->_query->posts as $post) {
			if (!$taxonomy) {
				$post->tags = [];

				continue;
			}

			$tags = wp_get_post_terms($post->ID, $taxonomy);

			$tags_slugs = [];

			foreach ($tags as $tag) {
				$tags_slugs[$tag->term_id] = $tag;
			}

			$post->tags = $tags_slugs;
		}
	}

	/**
	 * Get post query builder arguments
	 */
	public function query_posts($posts_per_page) {
		$settings = $this->get_settings();

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

		//$post_limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;

		// TODO need to delete after v6.5
		if (isset($settings['item_limit']['size']) and $settings['posts_per_page'] == 6) {
			$limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;
		} else {
			$limit = $settings['posts_per_page'];
		}

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		$this->render_header();

		while ($wp_query->have_posts()) {
			$wp_query->the_post();

			$this->render_post();
		}

		$this->render_footer();

		wp_reset_postdata();
	}

	public function render_excerpt() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_text')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

		$this->add_render_attribute('slider-text', 'class', 'bdt-post-slider-text', true);

		if ($settings['hide_on_mobile'] == 'yes') {
			$this->add_render_attribute('slider-text', 'class', 'bdt-post-slider-text bdt-visible@s', true);
		} elseif ($settings['hide_on_tablet'] == 'yes') {
			$this->add_render_attribute('slider-text', 'class', 'bdt-post-slider-text bdt-visible@m', true);
		} else {
			$this->add_render_attribute('slider-text', 'class', 'bdt-post-slider-text', true);
		}

?>
		<div <?php echo $this->get_render_attribute_string('slider-text'); ?> data-bdt-slideshow-parallax="x: 500,-500">
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

	public function render_title() {
		if (!$this->get_settings('show_title')) {
			return;
		}

		$tag = $this->get_settings('title_tag');

	?>
		<div class="bdt-post-slider-title-wrap">
			<a href="<?php echo esc_url(get_permalink()); ?>">
				<<?php echo Utils::get_valid_html_tag($tag) ?> class="bdt-post-slider-title bdt-margin-remove-bottom" data-bdt-slideshow-parallax="x: 200,-200">
					<?php the_title() ?>
				</<?php echo Utils::get_valid_html_tag($tag) ?>>
			</a>
		</div>
	<?php
	}

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_meta')) {
			return;
		}



		if ($settings['human_diff_time'] == 'yes') {
			echo element_pack_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}
	}

	public function render_read_more_button() {
		$settings        = $this->get_settings_for_display();

		if (!$this->get_settings('show_button')) {
			return;
		}
		$settings  = $this->get_settings_for_display();
		$animation = ($settings['button_hover_animation']) ? ' elementor-animation-' . $settings['button_hover_animation'] : '';

		if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
			// add old default
			$settings['icon'] = 'fas fa-arrow-right';
		}

		$migrated  = isset($settings['__fa4_migrated']['post_slider_icon']);
		$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

	?>
		<div class="bdt-post-slider-button-wrap" data-bdt-slideshow-parallax="y: 200,-200">
			<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-slider-button bdt-display-inline-block<?php echo esc_attr($animation); ?>">
				<?php echo esc_attr($this->get_settings('button_text')); ?>

				<?php if ($settings['post_slider_icon']['value']) : ?>
					<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

						<?php if ($is_new || $migrated) :
							Icons_Manager::render_icon($settings['post_slider_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
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
		$settings = $this->get_settings_for_display();
		$id       = 'bdt-post-slider-' . $this->get_id();

		$ratio = ($settings['slider_size_ratio']['width'] && $settings['slider_size_ratio']['height']) ? $settings['slider_size_ratio']['width'] . ":" . $settings['slider_size_ratio']['height'] : '';

		$this->add_render_attribute(
			[
				'slider-settings' => [
					'id'    => esc_attr($id),
					'class' => [
						'bdt-post-slider',
						'skin-default'
					],
					'data-bdt-slideshow' => [
						wp_json_encode(array_filter([
							"animation"         => $settings["slider_animations"],
							"min-height"        => $settings["slider_min_height"]["size"],
							"max-height"        => $settings["slider_max_height"]["size"],
							"ratio"             => $ratio,
							"autoplay"          => $settings["autoplay"],
							"autoplay-interval" => $settings["autoplay_interval"],
							"pause-on-hover"    => $settings["pause_on_hover"] == 'yes' ? 'true' : 'false',
							"velocity"          => ($settings["velocity"]["size"]) ? $settings["velocity"]["size"] : 1,
						]))
					]
				]
			]
		);

	?>
		<div <?php echo $this->get_render_attribute_string('slider-settings'); ?>>
			<div class="bdt-slideshow-items">
			<?php
		}

		public function render_footer() {
			?>
			</div>
			<?php $this->render_loop_pagination(); ?>
		</div>

	<?php
		}

		public function render_loop_item() {
			$settings         = $this->get_settings_for_display();

			$thumbnail_size = $settings['thumbnail_size'];
			$placeholder_image_src = Utils::get_placeholder_image_src();

			$slider_thumbnail = wp_get_attachment_image_src(get_post_thumbnail_id(), $thumbnail_size);

			$this->add_render_attribute(
				[
					'post-slider-item' => [
						'class' => [
							'bdt-slideshow-item',
							'bdt-post-slider-item'
						]
					]
				],
				'',
				'',
				true
			);

			$kenburns_reverse = $settings['kenburns_reverse'] ? ' bdt-animation-reverse' : '';

	?>
		<div <?php echo $this->get_render_attribute_string('post-slider-item'); ?>>
			<?php if ($settings['kenburns_animation']) : ?>
				<div class="bdt-position-cover bdt-animation-kenburns<?php echo esc_attr($kenburns_reverse); ?> bdt-transform-origin-center">
				<?php endif; ?>
				<?php
				if (!$slider_thumbnail) {
					printf('<img src="%1$s" alt="%2$s">', $placeholder_image_src, esc_html(get_the_title()));
				} else {
					print(wp_get_attachment_image(
						get_post_thumbnail_id(),
						$thumbnail_size,
						false,
						[
							'alt' => esc_html(get_the_title()),
							'data-bdt-cover' => true
						]
					));
				}
				?>
				<?php if ($settings['kenburns_animation']) : ?>
				</div>
			<?php endif; ?>
			<div class="bdt-post-slider-content-wrap bdt-position-center bdt-position-z-index">
				<div class="bdt-post-slider-content">

					<?php if ($settings['show_tag']) : ?>
						<div class="bdt-post-slider-tag-wrap" data-bdt-slideshow-parallax="y: -200,200">
							<?php
							$tags_list = get_the_tag_list('<span class="bdt-background-primary">', '</span> <span class="bdt-background-primary">', '</span>');
							if ($tags_list) :
								echo  wp_kses_post($tags_list);
							endif; ?>
						</div>
					<?php endif;

					$this->render_title();
					$this->render_excerpt();

					if ($settings['show_meta']) : ?>
						<div class="bdt-post-slider-meta bdt-flex-inline bdt-flex-middle" data-bdt-slideshow-parallax="x: 250,-250">
							<a class="bdt-flex bdt-flex-middle" href="<?php echo get_author_posts_url(get_the_author_meta('ID')); ?>">
								<div class="bdt-author bdt-margin-small-right bdt-border-circle bdt-overflow-hidden bdt-visible@m">
									<?php echo get_avatar(get_the_author_meta('ID'), 28); ?>
								</div>
								<span class="bdt-author bdt-text-capitalize"><?php echo esc_attr(get_the_author()); ?> </span>
							</a>
							<span><?php esc_html_e('On', 'bdthemes-element-pack'); ?> <?php $this->render_date(); ?></span>
						</div>
					<?php endif; ?>

					<?php $this->render_read_more_button(); ?>

				</div>
			</div>

			<?php if ('none' !== $settings['overlay']) :
				$blend_type = ('blend' == $settings['overlay']) ? ' bdt-blend-' . $settings['blend_type'] : ''; ?>
				<div class="bdt-overlay-default bdt-position-cover<?php echo esc_attr($blend_type); ?>"></div>
			<?php endif; ?>
		</div>
	<?php

		}

		public function render_loop_pagination() {
			$settings = $this->get_settings_for_display();

			//$post_limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;

			// TODO need to delete after v6.5
			if (isset($settings['item_limit']['size']) and $settings['posts_per_page'] == 6) {
				$limit = ('bdt-hazel' == $settings['_skin'] or 'bdt-vast' == $settings['_skin']) ? $settings['item_limit']['size'] : 4;
			} else {
				$limit = $settings['posts_per_page'];
			}

			$this->query_posts($limit);

			$wp_query = $this->get_query();

			if (!$wp_query->found_posts) {
				return;
			}

			$settings = $this->get_settings_for_display();
			$id       = $this->get_id();
			$ps_count = 0;

	?>
		<div id="<?php echo esc_attr($id); ?>_nav" class="bdt-post-slider-pagination bdt-position-bottom-center">
			<ul class="bdt-thumbnav bdt-grid bdt-grid-small bdt-child-width-auto bdt-child-width-1-4@m bdt-flex-center" data-bdt-grid>

				<?php
				while ($wp_query->have_posts()) {
					$wp_query->the_post();

				?>
					<li data-bdt-slideshow-item="<?php echo esc_attr($ps_count); ?>">
						<div class="bdt-post-slider-pagination-item">
							<a href="#">
								<div class="bdt-flex bdt-flex-middle bdt-text-left">
									<?php if ($settings['show_pagination_thumb']) :

										$placeholder_image_src = Utils::get_placeholder_image_src();
										$slider_thumbnail      = wp_get_attachment_image_src(get_post_thumbnail_id(), 'thumbnail');

									?>
										<div class="bdt-width-auto bdt-post-slider-thumb-wrap">
											<?php
											if (!$slider_thumbnail) {
												printf('<img src="%1$s" alt="%2$s">', $placeholder_image_src, esc_html(get_the_title()));
											} else {
												print(wp_get_attachment_image(
													get_post_thumbnail_id(),
													'thumbnail',
													false,
													[
														'alt' => esc_html(get_the_title())
													]
												));
											}
											?>
										</div>
									<?php endif; ?>
									<div class="bdt-margin-small-left bdt-visible@m">
										<<?php echo Utils::get_valid_html_tag($settings['thumb_title_tag']); ?> class="bdt-margin-remove-bottom thumb-title-default-skin">
											<?php echo esc_attr(get_the_title()); ?>
										</<?php echo Utils::get_valid_html_tag($settings['thumb_title_tag']); ?>>

										<?php if ($settings['show_meta']) : ?>
											<span class="bdt-post-slider-date"><?php $this->render_date(); ?></span>
										<?php endif; ?>
									</div>
								</div>
							</a>
						</div>
					</li>

				<?php
					$ps_count++;
				} ?>

			</ul>
		</div>
<?php
		}

		public function render_post() {
			$this->render_loop_item();
		}
	}
