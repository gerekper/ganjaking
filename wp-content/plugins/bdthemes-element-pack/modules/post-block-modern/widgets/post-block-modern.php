<?php

namespace ElementPack\Modules\PostBlockModern\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Image_Size;
use Elementor\Group_Control_Typography;
use ElementPack\Utils;
use Elementor\Icons_Manager;

use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class Post_Block_Modern extends Module_Base {
	use Group_Control_Query;
	private $_query = null;

	public function get_name() {
		return 'bdt-post-block-modern';
	}

	public function get_title() {
		return BDTEP . esc_html__('Post Block Modern', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-block-modern';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'block', 'modern', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-post-block-modern'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/bFEyizMaPmw';
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_layout_post_block_modern',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_group_control(
			Group_Control_Image_Size::get_type(),
			[
				'name'    => 'thumbnail',
				'label'   => esc_html__('Image Size', 'bdthemes-element-pack'),
				'exclude' => ['custom'],
				'default' => 'large',
			]
		);

		$this->add_control(
			'title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'show_meta',
			[
				'label'   => esc_html__('Meta Data', 'bdthemes-element-pack'),
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
			'show_excerpt',
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
				'default'   => 15,
				'condition' => [
					'show_excerpt'   => 'yes',
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
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'show_read_more',
			[
				'label'   => esc_html__('Read More', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'title_tags',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'title' => 'yes'
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_content_read_more',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'condition' => [
					'show_read_more' => 'yes',
				],
			]
		);

		$this->add_control(
			'read_more_text',
			[
				'label'       => esc_html__('Read More Text', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'placeholder' => esc_html__('Read More', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'post_block_modern_icon',
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
					'post_block_modern_icon[value]!' => '',
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
					'post_block_modern_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-block-modern .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
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

		//Style
		$this->start_controls_section(
			'section_design_left_part',
			[
				'label' => esc_html__('Left Part', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_left_part_style');

		$this->start_controls_tab(
			'tab_left_part_image',
			[
				'label' => esc_html__('Image', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label' => esc_html__('Blur Effect', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
			]
		);

		$this->add_control(
			'overlay_blur_level',
			[
				'label'       => __('Blur Level', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SLIDER,
				'range'       => [
					'px' => [
						'min'  => 0,
						'step' => 1,
						'max'  => 50,
					]
				],
				'default'     => [
					'size' => 5
				],
				'selectors'   => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part .bdt-overlay-gradient' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes'
				]
			]
		);

		$this->add_control(
			'left_part_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack') . BDTEP_NC,
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part .bdt-overlay-gradient' => 'background: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'left_part_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'    => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part',
			]
		);

		$this->add_responsive_control(
			'left_part_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'left_part_shadow',
				'label'      => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_left_part_date',
			[
				'label' => esc_html__('Date', 'bdthemes-element-pack'),
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_date_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'left_part_date_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta span',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_left_part_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_category_bg_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'left_part_category_border',
				'label'       => __('Border', 'bdthemes-element-pack') . BDTEP_NC,
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'left_part_category_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'left_part_category_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'left_part_category_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta a',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-meta.bdt-subnav span:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_left_part_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'left_part_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-title',
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'left_part_title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part.bdt-post-block-modern-item .bdt-post-block-modern-meta' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_left_part_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'left_part_excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'left_part_excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .left-part .bdt-post-block-modern-excerpt',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'left_part_excerpt_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .left-part.bdt-post-block-modern-item .bdt-post-block-modern-excerpt' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_design_right_part',
			[
				'label' => esc_html__('Right Part', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'conditions'   => [
					'relation' => 'or',
					'terms' => [
						[
							'name'  => 'show_meta',
							'value' => 'yes',
						],
						[
							'name'     => 'show_excerpt',
							'value'    => 'yes',
						],
						[
							'name'     => 'title',
							'value'    => 'yes',
						],
					],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'      => 'right_part_background_color',
				'selector'  => '{{WRAPPER}} .bdt-post-block-modern .right-part-wrapper',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'right_part_border',
				'label'       => __('Border', 'bdthemes-element-pack') . BDTEP_NC,
				'selector'    => '{{WRAPPER}} .bdt-post-block-modern .right-part-wrapper',
			]
		);

		$this->add_responsive_control(
			'right_part_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part-wrapper' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; overflow: hidden;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'right_part_shadow',
				'label'      => __('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .right-part-wrapper',
			]
		);

		$this->start_controls_tabs('tabs_right_part_style');

		$this->start_controls_tab(
			'tab_right_part_date',
			[
				'label' => esc_html__('Date', 'bdthemes-element-pack'),
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_date_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'right_part_date_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta span',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_right_part_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_category_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_category_bg_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'right_part_category_border',
				'label'       => __('Border', 'bdthemes-element-pack') . BDTEP_NC,
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'category_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'right_part_category_padding',
			[
				'label'      => __('Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'right_part_category_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta a',
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-meta.bdt-subnav span:after' => 'background: {{VALUE}};',
				],
				'condition' => [
					'show_meta' => 'yes',
				],
			]
		);


		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_right_part_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-title' => 'color: {{VALUE}};',
				],
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'right_part_title_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-title',
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'right_part_title_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part.bdt-post-block-modern-item .bdt-post-block-modern-title' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'title' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_right_part_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_control(
			'right_part_excerpt_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'right_part_excerpt_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .right-part .bdt-post-block-modern-excerpt',
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);

		$this->add_responsive_control(
			'right_part_excerpt_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part.bdt-post-block-modern-item .bdt-post-block-modern-excerpt' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
				'condition' => [
					'show_excerpt' => 'yes',
				],
			]
		);
		$this->end_controls_tab();
		$this->end_controls_tabs();
		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_read_more',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_read_more' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_read_more_style');

		$this->start_controls_tab(
			'tab_read_more_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'read_more_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '',
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'read_more_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'        => 'read_more_border',
				'label'       => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default'     => '1px',
				'selector'    => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more',
			]
		);

		$this->add_responsive_control(
			'read_more_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'read_more_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'read_more_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'read_more_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more',
			]
		);

		$this->add_responsive_control(
			'button_spacing',
			[
				'label' => __('Spacing', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item .bdt-post-block-modern-read-more' => 'margin-top: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_read_more_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'read_more_hover_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more:hover svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'read_more_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'read_more_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'read_more_hover_box_shadow',
				'label'     => esc_html__('Box Shadow', 'bdthemes-element-pack') . BDTEP_NC,
				'selector' => '{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-read-more:hover',
			]
		);

		$this->add_control(
			'read_more_hover_animation',
			[
				'label' => esc_html__('Animation', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_additional_options',
			[
				'label' => esc_html__('Additional Options', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'item_left_padding',
			[
				'label'      => __('Left Part Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.left-part .bdt-post-block-modern-desc' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'wrap_padding',
			[
				'label'      => __('Right Part Wrap Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .right-part-wrapper' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_responsive_control(
			'item_padding',
			[
				'label'      => __('Right Part Padding', 'bdthemes-element-pack') . BDTEP_NC,
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-item.right-part' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'space_between',
			[
				'label'      => esc_html__('Space Between', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-meta' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-title' => 'margin-bottom: {{SIZE}}{{UNIT}}',
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-excerpt' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_control(
			'item_spacing',
			[
				'label'      => esc_html__('Item Spacing', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'range'      => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-modern .bdt-post-block-modern-desc' => 'margin-bottom: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->end_controls_section();
	}

	public function render_excerpt() {
		if (!$this->get_settings('show_excerpt')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

?>
		<div class="bdt-post-block-modern-excerpt">
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

	public function render_date() {
		$settings = $this->get_settings_for_display();

		if (!$this->get_settings('show_meta')) {
			return;
		}

		if ($settings['human_diff_time'] == 'yes') {
			return element_pack_post_time_diff(($settings['human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			return get_the_date();
		}
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

		$id      = uniqid('bdtpbm_');

		$animation = ($settings['read_more_hover_animation']) ? ' elementor-animation-' . $settings['read_more_hover_animation'] : '';

		// TODO need to delete after v6.5
		if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['posts_limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}
		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if ($wp_query->have_posts()) :

			$this->add_render_attribute(
				[
					'post-block-modern' => [
						'id'    => esc_attr($id),
						'class' => [
							'bdt-post-block-modern',
							'bdt-grid',
							'bdt-grid-match',
							'bdt-grid-collapse'
						],
						'data-bdt-grid' => ''
					]
				]
			);

			if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
				// add old default
				$settings['icon'] = 'fas fa-arrow-right';
			}

			$migrated  = isset($settings['__fa4_migrated']['post_block_modern_icon']);
			$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

			$this->add_render_attribute('bdt-post-block-modern-title', 'class', 'bdt-post-block-modern-title');

		?>
			<div <?php echo $this->get_render_attribute_string('post-block-modern'); ?>>

				<?php $count = 0;

				while ($wp_query->have_posts()) : $wp_query->the_post();

					$count++;

					$placeholder_image_src = Utils::get_placeholder_image_src();
					$image_src             = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), $settings['thumbnail_size']);

					if (!$image_src) {
						$image_src = $placeholder_image_src;
					} else {
						$image_src = $image_src[0];
					}

					if ($count == 1) : ?>

						<div class="bdt-width-3-5@m">
							<div class="bdt-post-block-modern-item left-part bdt-position-relative" style="background-image: url(<?php echo esc_url($image_src); ?>)">

								<div class="bdt-post-block-modern-desc bdt-position-bottom-center bdt-position-z-index bdt-width-2-3@m ">

									<?php if ('yes' == $settings['show_meta']) : ?>

										<?php $meta_list = '<span>' . $this->render_date() . '</span><span>' . get_the_category_list(', ') . '</span>'; ?>

										<div class="bdt-post-block-modern-meta bdt-subnav bdt-flex-middle bdt-flex-center"><?php echo $meta_list; ?></div>

									<?php endif ?>

									<?php if ('yes' == $settings['title']) : ?>
										<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-post-block-modern-title'); ?>>
											<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-link-reset" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></a>
										</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
									<?php endif ?>

									<?php $this->render_excerpt(); ?>

								</div>
								<div class="bdt-position-cover bdt-overlay-gradient"></div>

							</div>
						</div>

						<div class="bdt-width-2-5@m right-part-wrapper">
						<?php else : ?>
							<div class="bdt-post-block-modern-item right-part">
								<div class="bdt-post-block-modern-desc">

									<?php if ('yes' == $settings['show_meta']) : ?>
										<?php $meta_list = '<span>' . $this->render_date() . '</span><span>' . get_the_category_list(', ') . '</span>'; ?>

										<div class="bdt-post-block-modern-meta bdt-subnav"><?php echo wp_kses_post($meta_list); ?></div>
									<?php endif ?>

									<?php if ('yes' == $settings['title']) : ?>
										<<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?> <?php echo $this->get_render_attribute_string('bdt-post-block-modern-title'); ?>>
											<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-link-reset" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></a>
										</<?php echo Utils::get_valid_html_tag($settings['title_tags']); ?>>
									<?php endif ?>

									<?php $this->render_excerpt(); ?>

									<?php if ('yes' == $settings['show_read_more']) : ?>
										<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-modern-read-more bdt-link-reset<?php echo esc_attr($animation); ?>"><?php echo esc_html($settings['read_more_text']); ?>

											<?php if ($settings['post_block_modern_icon']['value']) : ?>
												<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

													<?php if ($is_new || $migrated) :
														Icons_Manager::render_icon($settings['post_block_modern_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
													else : ?>
														<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
													<?php endif; ?>

												</span>
											<?php endif; ?>

										</a>
									<?php endif ?>

								</div>

							</div>
						<?php endif; ?>

					<?php endwhile; ?>

						</div>

			</div>

			<?php wp_reset_postdata(); 	?>

<?php endif;
	}
}
