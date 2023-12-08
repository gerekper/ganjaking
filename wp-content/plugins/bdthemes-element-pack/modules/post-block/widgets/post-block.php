<?php

namespace ElementPack\Modules\PostBlock\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Css_Filter;
use ElementPack\Utils;
use Elementor\Icons_Manager;

use ElementPack\Base\Module_Base;
use ElementPack\Includes\Controls\GroupQuery\Group_Control_Query;


use ElementPack\Traits\Global_Widget_Controls;

use ElementPack\Modules\PostBlock\Skins;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Post_Block extends Module_Base {
	use Group_Control_Query;

	private $_query = null;

	use Global_Widget_Controls;

	public function get_name() {
		return 'bdt-post-block';
	}

	public function get_title() {
		return BDTEP . esc_html__('Post Block', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-post-block';
	}

	public function get_categories() {
		return ['element-pack'];
	}

	public function get_keywords() {
		return ['post', 'block', 'blog', 'recent', 'news'];
	}

	public function get_style_depends() {
		if ($this->ep_is_edit_mode()) {
			return ['ep-styles'];
		} else {
			return ['ep-post-block'];
		}
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/_KPAns0zjAo';
	}

	protected function register_skins() {
		$this->add_skin(new Skins\Skin_Genesis($this));
		$this->add_skin(new Skins\Skin_Trinity($this));
	}

	public function get_query() {
		return $this->_query;
	}

	protected function register_controls() {
		$this->start_controls_section(
			'section_featured_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'featured_item',
			[
				'label'       => esc_html__('Featured Item', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SELECT,
				'default'     => '1',
				'description' => 'For good looking set it 1 for default skin and 2 for another skin',
				'options'     => [
					'1' => esc_html__('One', 'bdthemes-element-pack'),
					'2' => esc_html__('Two', 'bdthemes-element-pack'),
					'3' => esc_html__('Three', 'bdthemes-element-pack'),
				],
			]
		);

		$this->add_control(
			'featured_show_tag',
			[
				'label'     => esc_html__('Tag', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin' => 'trinity',
				]
			]
		);

		$this->add_control(
			'featured_show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'featured_title_size',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'featured_show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'featured_show_date',
			[
				'label'   => esc_html__('Date', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'featured_human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'featured_show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'featured_human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'featured_human_diff_time' => 'yes',
					'featured_show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'featured_show_category',
			[
				'label'   => esc_html__('Category', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'featured_show_excerpt',
			[
				'label'     => esc_html__('Show Text', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin'   => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'featured_excerpt_length',
			[
				'label'     => esc_html__('Text Limit', 'bdthemes-element-pack'),
				'description' => esc_html__('It\'s just work for main content, but not working with excerpt. If you set 0 so you will get full main content.', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::NUMBER,
				'default'   => 15,
				'condition' => [
					'featured_show_excerpt' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'strip_shortcode',
			[
				'label'   => esc_html__('Strip Shortcode', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
				'condition' => [
					'featured_show_excerpt' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'featured_show_read_more',
			[
				'label'     => esc_html__('Read More', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'condition' => [
					'_skin'   => ['', 'genesis'],
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
				'condition'   => [
					'featured_show_read_more' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'post_block_icon',
			[
				'label'       => esc_html__('Icon', 'bdthemes-element-pack'),
				'type'             => Controls_Manager::ICONS,
				'fa4compatibility' => 'icon',
				'condition'   => [
					'featured_show_read_more' => 'yes',
					'_skin'                   => ['', 'genesis'],
				],
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
					'post_block_icon[value]!' => '',
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
					'post_block_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block .bdt-button-icon-align-right' => is_rtl() ? 'margin-right: {{SIZE}}{{UNIT}};' : 'margin-left: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .bdt-post-block .bdt-button-icon-align-left'  => is_rtl() ? 'margin-left: {{SIZE}}{{UNIT}};' : 'margin-right: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'trinity_column_gap',
			[
				'label'   => esc_html__('Column Gap', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'medium',
				'options' => [
					'small'    => esc_html__('Small', 'bdthemes-element-pack'),
					'medium'   => esc_html__('Medium', 'bdthemes-element-pack'),
					'large'    => esc_html__('Large', 'bdthemes-element-pack'),
					'collapse' => esc_html__('Collapse', 'bdthemes-element-pack'),
				],
				'condition' => [
					'_skin' => 'trinity',
				],
			]
		);

		$this->add_responsive_control(
			'featured_item_height',
			[
				'label' => esc_html__('Featured Item Height', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 100,
						'max'  => 800,
						'step' => 2,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img' => 'height: {{SIZE}}px',
					'{{WRAPPER}} .featured-part .bdt-post-block-thumbnail img' => 'height: {{SIZE}}px',
				]
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_layout',
			[
				'label'     => esc_html__('List Layout', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_CONTENT,
				'condition' => [
					'_skin'   => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'list_show_title',
			[
				'label'   => esc_html__('Title', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'list_title_size',
			[
				'label'   => __('Title HTML Tag', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'default' => 'h4',
				'options' => element_pack_title_tags(),
				'condition' => [
					'list_show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'list_show_date',
			[
				'label'   => esc_html__('Date', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'list_human_diff_time',
			[
				'label'   => esc_html__('Human Different Time', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'list_show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'list_human_diff_time_short',
			[
				'label'   => esc_html__('Time Short Format', 'bdthemes-element-pack') . BDTEP_NC,
				'type'    => Controls_Manager::SWITCHER,
				'condition' => [
					'list_human_diff_time' => 'yes',
					'list_show_date' => 'yes'
				]
			]
		);

		$this->add_control(
			'list_show_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);

		$this->add_control(
			'show_list_divider',
			[
				'label'   => esc_html__('Divider', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SWITCHER,
				'default' => 'yes',
			]
		);

		$this->add_control(
			'list_space_between',
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
				'separator' => 'before',
				'selectors' => [
					'{{WRAPPER}} .skin-base .bdt-list > li:nth-child(n+2)'           => 'margin-top: {{SIZE}}{{UNIT}}; padding-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .skin-genesis .list-part ul li'       => 'margin-top: {{SIZE}}{{UNIT}};',
					'{{WRAPPER}} .skin-genesis .list-part ul li > div' => 'padding-top: {{SIZE}}{{UNIT}};',
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
			'section_featured_image_style',
			[
				'label'     => esc_html__('Featured Image Style', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin!' => 'trinity',
				],
			]
		);

		$this->start_controls_tabs('featured_image_effects');

		$this->start_controls_tab(
			'normal',
			[
				'label' => __('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'featured_image_border',
				'selector' => '{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'featured_image_radius',
			[
				'label' => __('Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'featured_image_shadow',
				'selector' => '{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img',
			]
		);

		$this->add_control(
			'opacity',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters',
				'selector' => '{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'hover',
			[
				'label' => __('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'opacity_hover',
			[
				'label' => __('Opacity', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.10,
						'step' => 0.01,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper:hover img' => 'opacity: {{SIZE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Css_Filter::get_type(),
			[
				'name' => 'css_filters_hover',
				'selector' => '{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper:hover img',
			]
		);

		$this->add_control(
			'background_hover_transition',
			[
				'label' => __('Transition Duration', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'max' => 3,
						'step' => 0.1,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-img-wrapper img' => 'transition-duration: {{SIZE}}s',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_featured_style',
			[
				'label' => esc_html__('Featured Layout Style', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_featured_layout_style');

		$this->start_controls_tab(
			'tab_featured_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_title' => 'yes',
				],
			]
		);

		$this->add_control(
			'featured_title_color',
			[
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-title a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'featured_show_title' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'featured_title_typography',
				'selector'  => '{{WRAPPER}} .featured-part .bdt-post-block-title a',
				'condition' => [
					'featured_show_title' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_featured_tag',
			[
				'label' => esc_html__('Tag', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
			]
		);

		$this->add_control(
			'tag_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-tag-wrap span' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'tag_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => '#fff',
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-tag-wrap span a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'tag_border',
				'label'     => __('Border', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
				'selector' => '{{WRAPPER}} .bdt-post-block-tag-wrap span',
			]
		);

		$this->add_responsive_control(
			'tag_border_radius',
			[
				'label'     => __('Border Radius', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 200,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-tag-wrap span' => 'border-radius: {{SIZE}}{{UNIT}}',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'tag_typography',
				'condition' => [
					'featured_show_tag' => 'yes',
					'_skin'             => 'trinity',
				],
				'selector' => '{{WRAPPER}} .bdt-post-block-tag-wrap span',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_featured_date',
			[
				'label' => esc_html__('Date', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'featured_date_color',
			[
				'label'     => esc_html__('Date Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-meta span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'featured_show_date' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'featured_date_typography',
				'selector'  => '{{WRAPPER}} .featured-part .bdt-post-block-meta span',
				'condition' => [
					'featured_show_date' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_featured_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'featured_category_color',
			[
				'label'     => esc_html__('Category Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-meta a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'featured_show_category' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'featured_category_typography',
				'selector'  => '{{WRAPPER}} .featured-part .bdt-post-block-meta a',
				'condition' => [
					'featured_show_category' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_featured_text',
			[
				'label' => esc_html__('Text', 'bdthemes-element-pack'),
				'condition' => [
					'featured_show_excerpt' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'featured_excerpt_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-post-block-excerpt' => 'color: {{VALUE}};',
				],
				'condition' => [
					'featured_show_excerpt' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'featured_excerpt_typography',
				'selector'  => '{{WRAPPER}} .featured-part .bdt-post-block-excerpt',
				'condition' => [
					'featured_show_excerpt' => 'yes',
					'_skin'                 => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'overlay_blur_effect',
			[
				'label' => esc_html__('Blur Effect', 'bdthemes-element-pack') . BDTEP_NC,
				'type'  => Controls_Manager::SWITCHER,
				'description' => sprintf(__('This feature will not work in the Firefox browser untill you enable browser compatibility so please %1s look here %2s', 'bdthemes-element-pack'), '<a href="https://developer.mozilla.org/en-US/docs/Web/CSS/backdrop-filter#Browser_compatibility" target="_blank">', '</a>'),
				'separator' => 'before',
				'condition' => [
					'_skin' => 'trinity',
				],
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
					'{{WRAPPER}} .featured-part .bdt-overlay-primary' => 'backdrop-filter: blur({{SIZE}}px); -webkit-backdrop-filter: blur({{SIZE}}px);'
				],
				'condition' => [
					'overlay_blur_effect' => 'yes',
					'_skin' => 'trinity',
				]
			]
		);

		$this->add_control(
			'trinity_overlay_color',
			[
				'label'     => esc_html__('Overlay Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .featured-part .bdt-overlay-primary' => 'background-color: {{VALUE}};',
				],
				'condition' => [
					'_skin' => 'trinity',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_list_style',
			[
				'label'     => esc_html__('List Layout Style', 'bdthemes-element-pack'),
				'tab'       => Controls_Manager::TAB_STYLE,
				'condition' => [
					'_skin'   => ['', 'genesis'],
				],
			]
		);

		$this->add_control(
			'list_layout_image_size',
			[
				'label' => esc_html__('Image Size', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => 64,
						'max'  => 150,
						'step' => 10,
					]
				],
				'selectors' => [
					'{{WRAPPER}} .list-part .bdt-post-block-thumbnail img' => 'width: {{SIZE}}{{UNIT}}; height: auto;',
				],
			]
		);

		$this->add_control(
			'list_divider_color',
			[
				'label'     => esc_html__('Divider Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .skin-base .bdt-list > li:nth-child(n+2)' => 'border-color: {{VALUE}};',
					'{{WRAPPER}} .list-part .bdt-has-divider li > div' => 'border-color: {{VALUE}};',
				],
				'condition' => [
					'show_list_divider' => 'yes',
				],
			]
		);

		$this->start_controls_tabs('tabs_list_layout_style');

		$this->start_controls_tab(
			'tab_list_title',
			[
				'label' => esc_html__('Title', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'list_layout_title_color',
			[
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-part .bdt-post-block-title .bdt-post-block-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'list_layout_title_typography',
				'selector' => '{{WRAPPER}} .list-part .bdt-post-block-title .bdt-post-block-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_list_date',
			[
				'label' => esc_html__('Date', 'bdthemes-element-pack'),
				'condition' => [
					'list_show_date' => 'yes',
				],
			]
		);

		$this->add_control(
			'list_layout_date_color',
			[
				'label'     => esc_html__('Date Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-part .bdt-post-block-meta span' => 'color: {{VALUE}};',
				],
				'condition' => [
					'list_show_date' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'list_layout_date_typography',
				'selector'  => '{{WRAPPER}} .list-part .bdt-post-block-meta span',
				'condition' => [
					'list_show_date' => 'yes',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_list_category',
			[
				'label' => esc_html__('Category', 'bdthemes-element-pack'),
				'condition' => [
					'list_show_category' => 'yes',
				],
			]
		);

		$this->add_control(
			'list_layout_category_color',
			[
				'label'     => esc_html__('Category Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .list-part .bdt-post-block-meta a' => 'color: {{VALUE}};',
				],
				'condition' => [
					'list_show_category' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'      => 'list_layout_category_typography',
				'selector'  => '{{WRAPPER}} .list-part .bdt-post-block-meta a',
				'condition' => [
					'list_show_category' => 'yes',
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
					'featured_show_read_more' => 'yes',
					'_skin'                   => ['', 'genesis'],
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
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-read-more' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-post-block-read-more svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'read_more_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-read-more' => 'background-color: {{VALUE}};',
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
				'selector'    => '{{WRAPPER}} .bdt-post-block-read-more',
				'separator'   => 'before',
			]
		);

		$this->add_responsive_control(
			'read_more_border_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-read-more' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'read_more_box_shadow',
				'selector' => '{{WRAPPER}} .bdt-post-block-read-more',
			]
		);

		$this->add_responsive_control(
			'read_more_padding',
			[
				'label'      => esc_html__('Padding', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-post-block-read-more' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'read_more_typography',
				'label'    => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-post-block-read-more',
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
					'{{WRAPPER}} .bdt-post-block-read-more:hover' => 'color: {{VALUE}} !important;',
					'{{WRAPPER}} .bdt-post-block-read-more:hover svg' => 'fill: {{VALUE}} !important;',
				],
			]
		);

		$this->add_control(
			'read_more_hover_background_color',
			[
				'label'     => esc_html__('Background Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-read-more:hover' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'read_more_hover_border_color',
			[
				'label'     => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'condition' => [
					'border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bdt-post-block-read-more:hover' => 'border-color: {{VALUE}};',
				],
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

	public function render_excerpt() {
		if (!$this->get_settings('featured_show_excerpt')) {
			return;
		}

		$strip_shortcode = $this->get_settings_for_display('strip_shortcode');

?>
		<div class="bdt-post-block-excerpt">
			<?php
			if (has_excerpt()) {
				the_excerpt();
			} else {
				echo element_pack_custom_excerpt($this->get_settings_for_display('featured_excerpt_length'), $strip_shortcode);
			}
			?>
		</div>
		<?php

	}

	public function render_featured_date() {
		$settings = $this->get_settings_for_display();

		if (!$settings['featured_show_date']) {
			return;
		}

		echo '<span>';

		if ($settings['featured_human_diff_time'] == 'yes') {
			echo element_pack_post_time_diff(($settings['featured_human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}

		echo '</span>';
	}

	public function render_list_date() {
		$settings = $this->get_settings_for_display();

		if (!$settings['list_show_date']) {
			return;
		}

		echo '<span>';

		if ($settings['list_human_diff_time'] == 'yes') {
			echo element_pack_post_time_diff(($settings['list_human_diff_time_short'] == 'yes') ? 'short' : '');
		} else {
			echo get_the_date();
		}

		echo '</span>';
	}

	public function render() {
		$settings = $this->get_settings_for_display();
		$id       = uniqid('bdtpbm_');

		$animation        = ($settings['read_more_hover_animation']) ? ' elementor-animation-' . $settings['read_more_hover_animation'] : '';
		$bdt_list_divider = ($settings['show_list_divider']) ? ' bdt-list-divider' : '';

		// TODO need to delete after v6.5
		if (isset($settings['posts_limit']) and $settings['posts_per_page'] == 6) {
			$limit = $settings['posts_limit'];
		} else {
			$limit = $settings['posts_per_page'];
		}

		$this->query_posts($limit);

		$wp_query = $this->get_query();

		if (!$wp_query->found_posts) {
			return;
		}

		if ($wp_query->have_posts()) :

			$this->add_render_attribute(
				[
					'post-block' => [
						'id'    => esc_attr($id),
						'class' => [
							'bdt-post-block',
							'bdt-grid',
							'bdt-grid-match',
							'skin-base',
						],
						'data-bdt-grid' => ''
					]
				]
			);

			if (!isset($settings['icon']) && !Icons_Manager::is_migration_allowed()) {
				// add old default
				$settings['icon'] = 'fas fa-arrow-right';
			}

			$migrated  = isset($settings['__fa4_migrated']['post_block_icon']);
			$is_new    = empty($settings['icon']) && Icons_Manager::is_migration_allowed();

		?>
			<div <?php echo $this->get_render_attribute_string('post-block'); ?>>

				<?php $bdt_count = 0;

				while ($wp_query->have_posts()) : $wp_query->the_post();

					$placeholder_img_src = Utils::get_placeholder_image_src();
					$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'large');

					if ($bdt_count == 0) : ?>
						<div class="bdt-width-1-2@m">
						<?php endif; ?>

						<?php $bdt_count++; ?>
						<?php if ($bdt_count <= $settings['featured_item']) :

							$this->add_render_attribute('featured-title', 'class', 'bdt-post-block-title', true);

						?>

							<div class="bdt-post-block-item featured-part bdt-width-1-1@m bdt-margin">
								<div class="bdt-post-block-img-wrapper bdt-margin-bottom">
									<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
										<?php
										if (!$image_src) {
											printf('<img src="%1$s" alt="%2$s">', $placeholder_img_src, esc_html(get_the_title()));
										} else {
											print(wp_get_attachment_image(
												get_post_thumbnail_id(),
												'large',
												false,
												[
													'alt' => esc_html(get_the_title())
												]
											));
										}
										?>
									</a>
								</div>

								<div class="bdt-post-block-desc">

									<?php if ('yes' == $settings['featured_show_title']) : ?>
										<<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?> <?php echo $this->get_render_attribute_string('featured-title'); ?>>
											<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></a>
										</<?php echo Utils::get_valid_html_tag($settings['featured_title_size']); ?>>
									<?php endif ?>

									<?php if ($settings['featured_show_category'] or $settings['featured_show_date']) : ?>

										<div class="bdt-post-block-meta bdt-subnav bdt-flex-middle">
											<?php $this->render_featured_date(); ?>

											<?php if ($settings['featured_show_category']) : ?>
												<?php echo '<span>' . get_the_category_list(', ') . '</span>'; ?>
											<?php endif ?>

										</div>

									<?php endif ?>

									<?php $this->render_excerpt(); ?>

									<?php if ($settings['featured_show_read_more']) : ?>
										<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-read-more bdt-link-reset<?php echo esc_attr($animation); ?>"><?php echo esc_html($settings['read_more_text']); ?>

											<?php if ($settings['post_block_icon']['value']) : ?>
												<span class="bdt-button-icon-align-<?php echo esc_attr($settings['icon_align']); ?>">

													<?php if ($is_new || $migrated) :
														Icons_Manager::render_icon($settings['post_block_icon'], ['aria-hidden' => 'true', 'class' => 'fa-fw']);
													else : ?>
														<i class="<?php echo esc_attr($settings['icon']); ?>" aria-hidden="true"></i>
													<?php endif; ?>

												</span>
											<?php endif; ?>
										</a>
									<?php endif ?>
								</div>
							</div>

							<?php if ($bdt_count == $settings['featured_item']) : ?>

						</div>

						<div class="bdt-width-1-2@m" data-bdt-scrollspy="cls: bdt-animation-fade; target: > ul > .bdt-post-block-item; delay: 350;">
							<ul class="bdt-list bdt-list-large<?php echo esc_attr($bdt_list_divider); ?>">

							<?php endif; ?>

						<?php else :

							$image_src = wp_get_attachment_image_src(get_post_thumbnail_id(get_the_ID()), 'thumbnail');

						?>
							<li class="bdt-post-block-item list-part">
								<div class="bdt-grid bdt-grid-small" data-bdt-grid>
									<div class="bdt-post-block-thumbnail bdt-width-auto">
										<a href="<?php echo esc_url(get_permalink()); ?>" title="<?php echo esc_attr(get_the_title()); ?>">
											<?php
											if (!$image_src) {
												printf('<img src="%1$s" alt="%2$s">', $placeholder_img_src, esc_html(get_the_title()));
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
										</a>
									</div>
									<div class="bdt-post-block-desc bdt-width-expand">
										<?php if ('yes' == $settings['list_show_title']) : ?>
											<<?php echo esc_html($settings['list_title_size']); ?> <?php echo $this->get_render_attribute_string('featured-title'); ?>>
												<a href="<?php echo esc_url(get_permalink()); ?>" class="bdt-post-block-link" title="<?php echo esc_attr(get_the_title()); ?>"><?php echo esc_html(get_the_title()); ?></a>
											</<?php echo esc_html($settings['list_title_size']); ?>>
										<?php endif ?>

										<?php if ($settings['list_show_category'] or $settings['list_show_date']) : ?>

											<div class="bdt-post-block-meta bdt-subnav bdt-flex-middle">
												<?php $this->render_list_date(); ?>

												<?php if ($settings['list_show_category']) : ?>
													<?php echo '<span>' . get_the_category_list(', ') . '</span>'; ?>
												<?php endif ?>

											</div>
										<?php endif ?>
									</div>
								</div>
							</li>
					<?php endif;
					endwhile; ?>
							</ul>
						</div>
			</div>

<?php
			wp_reset_postdata();
		endif;
	}
}
