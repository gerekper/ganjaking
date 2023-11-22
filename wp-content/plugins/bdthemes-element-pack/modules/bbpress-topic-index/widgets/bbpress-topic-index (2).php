<?php

namespace ElementPack\Modules\BbpressTopicIndex\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Topic_Index extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-topic-index';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Topic Index', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-topic-index';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support'];
	}

	public function get_custom_help_url() {
		return 'https://youtu.be/7vkAHZ778c4';
	}

	protected function register_controls() {

		$this->start_controls_section(
			'section_style_bbpress_layout',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'show_search_form',
			[
				'label'     => __('Show Search Form', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->add_control(
			'show_breadcrumb',
			[
				'label'     => __('Show Breadcrumb', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_bbpress_breadcrumb',
			[
				'label' => esc_html__('Breadcrumb', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'breadcrumb_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'breadcrumb_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_typography',
				'selector' => '{{WRAPPER}} .bbp-breadcrumb',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_search',
			[
				'label' => esc_html__('Search', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->start_controls_tabs('tabs_bbpress_search_style');

		$this->start_controls_tab(
			'tab_bbpress_search_input',
			[
				'label' => __('Input', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'input_text_background',
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'input_border',
				'label' => esc_html__('Border', 'elementor-addons'),
				'fields_options' => [
					'border' => [
						'default' => 'solid',
					],
					'width' => [
						'default' => [
							'top' => '1',
							'right' => '1',
							'bottom' => '1',
							'left' => '1',
							'unit' => 'px',
							'isLinked' => false,
						],
					],
					'color' => [
						'default' => '#c0c0c0',
					],
				],
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'input_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} input[type="text"], {{WRAPPER}} input[type="date"], {{WRAPPER}} input[type="email"], {{WRAPPER}} input[type="number"], {{WRAPPER}} input[type="password"], {{WRAPPER}} input[type="search"], {{WRAPPER}} input[type="tel"], {{WRAPPER}} input[type="url"], {{WRAPPER}} select, {{WRAPPER}} textarea',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_bbpress_search_submit',
			[
				'label' => __('Submit', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'button_normal_heading',
			[
				'label' => esc_html__('NORMAL', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form .button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color',
				'selector' => '{{WRAPPER}} .bbp-search-form .button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} .bbp-search-form .button',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'button_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form .button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form .button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'button_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} .bbp-search-form .button',
			]
		);

		$this->add_control(
			'button_hover_heading',
			[
				'label' => esc_html__('HOVER', 'bdthemes-element-pack'),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before'
			]
		);

		$this->add_control(
			'button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form .button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color_hover',
				'selector' => '{{WRAPPER}} .bbp-search-form .button:hover',
			]
		);

		$this->add_control(
			'button_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'button_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .bbp-search-form .button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_header',
			[
				'label' => esc_html__('Header', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'header_title_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums li.bbp-header li' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_background',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'header_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'header_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums li.bbp-header' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums li.bbp-header' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'header_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums li.bbp-header' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header li',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_body',
			[
				'label' => esc_html__('Body', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_body_odd_color',
			[
				'label'     => esc_html__('Odd Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_body_even_color',
			[
				'label'     => esc_html__('Even Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_body_list_border_color',
			[
				'label'     => esc_html__('Odd/Even Border Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums li.bbp-body ul.forum' => 'border-top-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'odd_even_forum_body_padding',
			[
				'label' => esc_html__( 'Odd/Even Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.even, {{WRAPPER}} #bbpress-forums ul.even, {{WRAPPER}} #bbpress-forums div.odd, {{WRAPPER}} #bbpress-forums ul.odd' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'forum_body_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums ul.bbp-topics',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'forum_body_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_body_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_body_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_title',
			[
				'label' => esc_html__('Forum Title', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_title_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-permalink' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_title_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-permalink:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_title_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-permalink' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_title_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-topic-permalink',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_count',
			[
				'label' => esc_html__('Voices/Posts Count', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_count_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_count_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_count_typography',
				'selector' => '{{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_meta',
			[
				'label' => esc_html__('Forum Meta', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_meta_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_meta_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-freshness a:hover, {{WRAPPER}} #bbpress-forums .bbp-topic-meta a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_meta_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_meta_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_count_color',
			[
				'label' => esc_html__('Count Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-pagination-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_count_typography',
				'selector' => '{{WRAPPER}} .bbp-pagination-count',
			]
		);

		$this->start_controls_tabs('tabs_pagination_style');

		$this->start_controls_tab(
			'tab_pagination_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pagination_background_color',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-pagination-links a',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'pagination_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'pagination_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'pagination_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'pagination_box_shadow',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-pagination-links a, {{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_hover_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pagination_background_color_hover',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-pagination-links a:hover:hover',
			]
		);

		$this->add_control(
			'pagination_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'pagination_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links a:hover:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_pagination_active',
			[
				'label' => esc_html__('Active', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'pagination_active_color',
			[
				'label' => esc_html__('Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-pagination-links span.current' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'pagination_background_color_active',
				'selector' => '#bbpress-forums .bbp-pagination-links span.current',
			]
		);

		$this->add_control(
			'pagination_active_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'pagination_border_border!' => '',
				],
				'selectors' => [
					'#bbpress-forums .bbp-pagination-links span.current' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	public function render_form_search() {
		$settings = $this->get_settings_for_display();

		if ('yes' !== $settings['show_search_form']) {
			return;
		}

?>
		<div class="bbp-search-form">
			<?php if (bbp_allow_search()) : ?>
				<div class="bbp-search-form">
					<form role="search" method="get" id="bbp-search-form">
						<div>
							<label class="screen-reader-text hidden" for="bbp_search"><?php esc_html_e('Search for:', 'bbpress'); ?></label>
							<input type="hidden" name="action" value="bbp-search-request" />
							<input type="text" value="<?php bbp_search_terms(); ?>" name="bbp_search" id="bbp_search" />
							<input class="button" type="submit" id="bbp_search_submit" value="<?php esc_attr_e('Search', 'bbpress'); ?>" />
						</div>
					</form>
				</div>
			<?php endif; ?>
		</div>
	<?php
	}
	public function render_loop_single_topic() { ?>
		<ul id="bbp-topic-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
			<li class="bbp-topic-title">
				<?php if (bbp_is_user_home()) : ?>
					<?php if (bbp_is_favorites()) : ?>
						<span class="bbp-row-actions">
							<?php do_action('bbp_theme_before_topic_favorites_action'); ?>
							<?php bbp_topic_favorite_link(array('before' => '', 'favorite' => '+', 'favorited' => '&times;')); ?>
							<?php do_action('bbp_theme_after_topic_favorites_action'); ?>
						</span>

					<?php elseif (bbp_is_subscriptions()) : ?>
						<span class="bbp-row-actions">
							<?php do_action('bbp_theme_before_topic_subscription_action'); ?>
							<?php bbp_topic_subscription_link(array('before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;')); ?>
							<?php do_action('bbp_theme_after_topic_subscription_action'); ?>
						</span>
					<?php endif; ?>

				<?php endif; ?>

				<?php do_action('bbp_theme_before_topic_title'); ?>
				<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>
				<?php do_action('bbp_theme_after_topic_title'); ?>
				<?php bbp_topic_pagination(); ?>
				<?php do_action('bbp_theme_before_topic_meta'); ?>

				<p class="bbp-topic-meta">
					<?php do_action('bbp_theme_before_topic_started_by'); ?>
					<span class="bbp-topic-started-by"><?php printf(esc_html__('Started by: %1$s', 'bbpress'), bbp_get_topic_author_link(array('size' => '14'))); ?></span>
					<?php do_action('bbp_theme_after_topic_started_by'); ?>
					<?php if (!bbp_is_single_forum() || (bbp_get_topic_forum_id() !== bbp_get_forum_id())) : ?>
						<?php do_action('bbp_theme_before_topic_started_in'); ?>
						<span class="bbp-topic-started-in"><?php printf(esc_html__('in: %1$s', 'bbpress'), '<a href="' . bbp_get_forum_permalink(bbp_get_topic_forum_id()) . '">' . bbp_get_forum_title(bbp_get_topic_forum_id()) . '</a>'); ?></span>
						<?php do_action('bbp_theme_after_topic_started_in'); ?>
					<?php endif; ?>
				</p>

				<?php do_action('bbp_theme_after_topic_meta'); ?>

				<?php bbp_topic_row_actions(); ?>

			</li>

			<li class="bbp-topic-voice-count"><?php bbp_topic_voice_count(); ?></li>

			<li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? bbp_topic_reply_count() : bbp_topic_post_count(); ?></li>

			<li class="bbp-topic-freshness">

				<?php do_action('bbp_theme_before_topic_freshness_link'); ?>

				<?php bbp_topic_freshness_link(); ?>

				<?php do_action('bbp_theme_after_topic_freshness_link'); ?>

				<p class="bbp-topic-meta">

					<?php do_action('bbp_theme_before_topic_freshness_author'); ?>

					<span class="bbp-topic-freshness-author"><?php bbp_author_link(array('post_id' => bbp_get_topic_last_active_id(), 'size' => 14)); ?></span>

					<?php do_action('bbp_theme_after_topic_freshness_author'); ?>

				</p>
			</li>
		</ul><!-- #bbp-topic-<?php bbp_topic_id(); ?> -->
	<?php
	}

	protected function render_pagination_topics() {
		do_action('bbp_template_before_pagination_loop'); ?>
		<div class="bbp-pagination">
			<div class="bbp-pagination-count"><?php bbp_forum_pagination_count(); ?></div>
			<div class="bbp-pagination-links"><?php bbp_forum_pagination_links(); ?></div>
		</div>
	<?php do_action('bbp_template_after_pagination_loop');
	}

	protected function render_loop_topics() {
		do_action('bbp_template_before_topics_loop'); ?>

		<ul id="bbp-forum-<?php bbp_forum_id(); ?>" class="bbp-topics">
			<li class="bbp-header">
				<ul class="forum-titles">
					<li class="bbp-topic-title"><?php esc_html_e('Topic', 'bbpress'); ?></li>
					<li class="bbp-topic-voice-count"><?php esc_html_e('Voices', 'bbpress'); ?></li>
					<li class="bbp-topic-reply-count"><?php bbp_show_lead_topic() ? esc_html_e('Replies', 'bbpress') : esc_html_e('Posts',   'bbpress'); ?></li>
					<li class="bbp-topic-freshness"><?php esc_html_e('Last Post', 'bbpress'); ?></li>
				</ul>
			</li>
			<li class="bbp-body">
				<?php while (bbp_topics()) : bbp_the_topic(); ?>
					<?php $this->render_loop_single_topic(); ?>
				<?php endwhile; ?>
			</li>
			<li class="bbp-footer">
				<div class="tr">
					<p>
						<span class="td colspan<?php echo (bbp_is_user_home() && (bbp_is_favorites() || bbp_is_subscriptions())) ? '5' : '4'; ?>">&nbsp;</span>
					</p>
				</div>
			</li>
		</ul>

	<?php do_action('bbp_template_after_topics_loop');
	}

	public function display_topic_index_query($args = array()) {
		$args['author']        = 0;
		$args['show_stickies'] = true;
		$args['order']         = 'DESC';
		return $args;
	}
	public function render() {
		$settings = $this->get_settings_for_display();
		if (!bbp_is_topic_archive()) {
			add_filter('bbp_before_has_topics_parse_args', [$this, 'display_topic_index_query']);
		}
		bbp_set_query_name('bbp_topic_archive'); ?>

		<div id="bbpress-forums" class="bbpress-wrapper">
			<?php if (bbp_allow_search()) : ?>
				<?php $this->render_form_search(); ?>
			<?php endif; ?>
			<?php if ($settings['show_breadcrumb']) : ?>
				<?php bbp_breadcrumb(); ?>
			<?php endif; ?>
			<?php do_action('bbp_template_before_topic_tag_description'); ?>
			<?php if (bbp_is_topic_tag()) : ?>
				<?php bbp_topic_tag_description(array('before' => '<div class="bbp-template-notice info"><ul><li>', 'after' => '</li></ul></div>')); ?>
			<?php endif; ?>

			<?php do_action('bbp_template_after_topic_tag_description'); ?>
			<?php do_action('bbp_template_before_topics_index'); ?>

			<?php if (bbp_has_topics()) : ?>
				<?php $this->render_loop_topics(); ?>
				<?php $this->render_pagination_topics(); ?>
			<?php else : ?>
				<div class="bbp-template-notice">
					<ul>
						<li><?php esc_html_e('Oh, bother! No topics were found here.', 'bbpress'); ?></li>
					</ul>
				</div>
			<?php endif; ?>

			<?php do_action('bbp_template_after_topics_index'); ?>

		</div>
<?php

		// Return contents of output buffer
		// return $this->end();
		wp_reset_postdata();
	}
}
