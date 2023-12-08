<?php

namespace ElementPack\Modules\BbpressSingleForum\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Single_Forum extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-single-forum';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Single Forum', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-single-forum';
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
			'section_bbpress_content',
			[
				'label' => esc_html__('Layout', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'bbpress_single_id',
			[
				'label'       => __('Single Forums', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select Single Forum', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'bbpress_single_forum',
				],
			]
		);

		$this->add_control(
			'show_breadcrumb',
			[
				'label'     => __('Show Breadcrumb', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SWITCHER,
				'default'   => 'yes',
				'separator' => 'before'
			]
		);

		$this->end_controls_section();

		//Style
		$this->start_controls_section(
			'section_style_bbpress_breadcrumb',
			[
				'label' => esc_html__('Breadcrumb', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
				'condition' => [
					'show_breadcrumb' => 'yes'
				]
			]
		);

		$this->add_control(
			'breadcrumb_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb *, {{WRAPPER}} #bbpress-forums a.subscription-toggle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb a:hover, {{WRAPPER}} #bbpress-forums a.subscription-toggle:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bbp-breadcrumb, {{WRAPPER}} #bbpress-forums a.subscription-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_typography',
				'selector' => '{{WRAPPER}} .bbp-breadcrumb, {{WRAPPER}} #bbpress-forums a.subscription-toggle',
			]
		);

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
				'selector' => '{{WRAPPER}} #bbpress-forums ul.bbp-forums, {{WRAPPER}} #bbpress-forums ul.bbp-topics',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums, {{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums, {{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums, {{WRAPPER}} #bbpress-forums ul.bbp-topics' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-forum-title, {{WRAPPER}} #bbpress-forums .bbp-topic-permalink' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_title_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-forum-title:hover, {{WRAPPER}} #bbpress-forums .bbp-topic-permalink:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-forum-title, {{WRAPPER}} #bbpress-forums .bbp-topic-permalink' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_title_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-forum-title, {{WRAPPER}} #bbpress-forums .bbp-topic-permalink',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_text',
			[
				'label' => esc_html__('Forum Text', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_text_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-forum-content' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_text_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-forum-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_text_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-forum-content',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_title_list',
			[
				'label' => esc_html__('Forums List', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_title_list_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-forums-list a' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_title_list_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-forums-list a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_title_list_divider_color',
			[
				'label'     => esc_html__( 'Divider Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-forums-list' => 'border-left-color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'forum_title_list_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} .bbp-forums-list a' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_title_list_typography',
				'selector' => '{{WRAPPER}} .bbp-forums-list a',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_forum_count',
			[
				'label' => esc_html__('Topics/Posts Count', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'forum_count_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-forum-topic-count, {{WRAPPER}} .bbp-forum-reply-count, {{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bbp-forum-topic-count, {{WRAPPER}} .bbp-forum-reply-count, {{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_count_typography',
				'selector' => '{{WRAPPER}} .bbp-forum-topic-count, {{WRAPPER}} .bbp-forum-reply-count, {{WRAPPER}} .bbp-topic-voice-count, {{WRAPPER}} .bbp-topic-reply-count',
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
					'{{WRAPPER}} #bbpress-forums .bbp-forum-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_meta_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-forum-freshness a:hover, {{WRAPPER}} #bbpress-forums .bbp-topic-freshness a:hover, {{WRAPPER}} #bbpress-forums .bbp-topic-meta a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-forum-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_meta_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-forum-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-freshness a, {{WRAPPER}} #bbpress-forums .bbp-topic-meta *',
			]
		);

		$this->end_controls_section();

		//form
		$this->start_controls_section(
			'section_style_bbpress_forum_form',
			[
				'label' => esc_html__('Forum Form', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'main_title_color',
			[
				'label'     => esc_html__('Title Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form legend' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'form_border',
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
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-topic-form .bbp-form',
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form .bbp-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form .bbp-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'form_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form .bbp-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'label' => esc_html__('Title Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-topic-form legend',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_label',
			[
				'label' => esc_html__('Label', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'label_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form label' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'label_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-topic-form label',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_input',
			[
				'label' => esc_html__('Input', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_placeholder_color',
			[
				'label'     => esc_html__('Placeholder Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form textarea::placeholder' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'input_text_color',
			[
				'label'     => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'input_text_background',
				'selector' => '{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea',
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
				'selector' => '{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea',
			]
		);

		$this->add_responsive_control(
			'input_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'input_typography',
				'label' => esc_html__('Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea',
			]
		);

		$this->add_responsive_control(
			'input_height',
			[
				'label' => esc_html__( 'Input Height', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select' => 'height: {{SIZE}}{{UNIT}}; min-height: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'textarea_height',
			[
				'label' => esc_html__('Textarea Height', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 200,
				],
				'range' => [
					'px' => [
						'min' => 30,
						'max' => 500,
					],
				],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-topic-form textarea#bbp_forum_content' => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
				],
			]
		);

		$this->add_responsive_control(
			'input_textarea_width',
			[
				'label' => esc_html__( 'Input/Textarea Width(%)', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums input[type="text"], {{WRAPPER}} #bbpress-forums input[type="date"], {{WRAPPER}} #bbpress-forums input[type="email"], {{WRAPPER}} #bbpress-forums input[type="number"], {{WRAPPER}} #bbpress-forums input[type="password"], {{WRAPPER}} #bbpress-forums input[type="search"], {{WRAPPER}} #bbpress-forums input[type="tel"], {{WRAPPER}} #bbpress-forums input[type="url"], {{WRAPPER}} #bbpress-forums select, {{WRAPPER}} #bbpress-forums textarea' => 'width: {{SIZE}}%; max-width: {{SIZE}}%;',
				],
			]
		);

		$this->end_controls_section();


		$this->start_controls_section(
			'section_style_submit_button',
			[
				'label' => esc_html__('Submit Button', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'button_alignment',
			[
				'label'     => esc_html__('Alignment', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::CHOOSE,
				'options'   => [
					'left'   => [
						'title' => esc_html__('Left', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-center',
					],
					'right'  => [
						'title' => esc_html__('Right', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-right',
					],
					'justify'  => [
						'title' => esc_html__('Justify', 'bdthemes-element-pack'),
						'icon'  => 'eicon-text-align-justify',
					],
				],
				'selectors_dictionary' => [
					'left' => 'text-align: left; float: inherit;',
					'right' => 'text-align: right; float: inherit;',
					'center' => 'text-align: center; float: inherit;',
					'justify' => 'text-align: justify; float: inherit; width: 100%;',
				],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper' => '{{VALUE}};',
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button' => '{{VALUE}};',
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
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'button_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button',
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
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button',
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'button_box_shadow',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button',
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
			'button_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'button_background_color_hover',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button:hover',
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
					'{{WRAPPER}} #bbpress-forums .bbp-submit-wrapper button:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_pagination',
			[
				'label' => esc_html__('Pagination', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'pagination_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-pagination-count' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'pagination_text_typography',
				'label' => esc_html__('Text Typography', 'bdthemes-element-pack'),
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

		$this->start_controls_section(
			'section_style_notice',
			[
				'label' => esc_html__( 'Notice', 'bdthemes-element-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'notice_text_color',
			[
				'label'     => esc_html__( 'Color', 'bdthemes-element-pack' ),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'color: {{VALUE}};',
				],
			]
		);


		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'notice_background_color',
				'selector' => '{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint',
			]
		);


		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'notice_border',
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
				'selector' => '{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint',
			]
		);

		$this->add_responsive_control(
			'notice_border_radius',
			[
				'label'      => __( 'Border Radius', 'bdthemes-element-pack' ),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'notice_padding',
			[
				'label' => esc_html__( 'Padding', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'notice_margin',
			[
				'label' => esc_html__( 'Margin', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', 'em', '%' ],
				'selectors' => [
					'{{WRAPPER}} div.bbp-template-notice, {{WRAPPER}} div.indicator-hint' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'notice_text_typography',
				'label' => esc_html__( 'Title Typography', 'bdthemes-element-pack' ),
				'selector' => '{{WRAPPER}} div.bbp-template-notice p, {{WRAPPER}} div.bbp-template-notice li',
			]
		);


		$this->end_controls_section();
	}


	protected function render_loop_single_forum() { ?>
		<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(); ?>>
			<li class="bbp-forum-info">

				<?php if (bbp_is_user_home() && bbp_is_subscriptions()) : ?>

					<span class="bbp-row-actions">

						<?php do_action('bbp_theme_before_forum_subscription_action'); ?>

						<?php bbp_forum_subscription_link(array('before' => '', 'subscribe' => '+', 'unsubscribe' => '&times;')); ?>

						<?php do_action('bbp_theme_after_forum_subscription_action'); ?>

					</span>

				<?php endif; ?>

				<?php do_action('bbp_theme_before_forum_title'); ?>

				<a class="bbp-forum-title bdt-inline" href="<?php bbp_forum_permalink(); ?>"><?php bbp_forum_title(); ?></a>

				<?php do_action('bbp_theme_after_forum_title'); ?>

				<?php do_action('bbp_theme_before_forum_description'); ?>

				<div class="bbp-forum-content"><?php bbp_forum_content(); ?></div>

				<?php do_action('bbp_theme_after_forum_description'); ?>

				<?php do_action('bbp_theme_before_forum_sub_forums'); ?>

				<?php bbp_list_forums(); ?>

				<?php do_action('bbp_theme_after_forum_sub_forums'); ?>

				<?php bbp_forum_row_actions(); ?>

			</li>

			<li class="bbp-forum-topic-count"><?php bbp_forum_topic_count(); ?></li>

			<li class="bbp-forum-reply-count"><?php bbp_show_lead_topic() ? bbp_forum_reply_count() : bbp_forum_post_count(); ?></li>

			<li class="bbp-forum-freshness">

				<?php do_action('bbp_theme_before_forum_freshness_link'); ?>

				<?php bbp_forum_freshness_link(); ?>

				<?php do_action('bbp_theme_after_forum_freshness_link'); ?>

				<p class="bbp-topic-meta">

					<?php do_action('bbp_theme_before_topic_author'); ?>

					<span class="bbp-topic-freshness-author"><?php bbp_author_link(array('post_id' => bbp_get_forum_last_active_id(), 'size' => 14)); ?></span>

					<?php do_action('bbp_theme_after_topic_author'); ?>

				</p>
			</li>
		</ul>
	<?php
	}
	protected function render_loop_forum() {

		do_action('bbp_template_before_forums_loop'); ?>

		<ul id="forums-list-<?php bbp_forum_id(); ?>" class="bbp-forums">

			<li class="bbp-header">

				<ul class="forum-titles">
					<li class="bbp-forum-info"><?php esc_html_e('Forum', 'bbpress'); ?></li>
					<li class="bbp-forum-topic-count"><?php esc_html_e('Topics', 'bbpress'); ?></li>
					<li class="bbp-forum-reply-count"><?php bbp_show_lead_topic() ? esc_html_e('Replies', 'bbpress') : esc_html_e('Posts',   'bbpress'); ?></li>
					<li class="bbp-forum-freshness"><?php esc_html_e('Last Post', 'bbpress'); ?></li>
				</ul>

			</li><!-- .bbp-header -->

			<li class="bbp-body">

				<?php while (bbp_forums()) : bbp_the_forum(); ?>
					<?php $this->render_loop_single_forum(); ?>
				<?php endwhile; ?>

			</li><!-- .bbp-body -->

			<li class="bbp-footer">

				<div class="tr">
					<p class="td colspan4">&nbsp;</p>
				</div><!-- .tr -->

			</li><!-- .bbp-footer -->

		</ul><!-- .forums-directory -->

	<?php do_action('bbp_template_after_forums_loop');
	}
	protected function render_protected_form() { ?>
		<div id="bbpress-forums" class="bbpress-wrapper">
			<fieldset class="bbp-form" id="bbp-protected">
				<Legend><?php esc_html_e('Protected', 'bbpress'); ?></legend>
				<?php echo get_the_password_form(); ?>
			</fieldset>
		</div>
	<?php
	}


	protected function render_user_login_form() { ?>
		<form method="post" action="<?php bbp_wp_login_action(array('context' => 'login_post')); ?>" class="bbp-login-form">
			<fieldset class="bbp-form">
				<legend><?php esc_html_e('Log In', 'bbpress'); ?></legend>

				<div class="bbp-username">
					<label for="user_login"><?php esc_html_e('Username', 'bbpress'); ?>: </label>
					<input type="text" name="log" value="<?php bbp_sanitize_val('user_login', 'text'); ?>" size="20" maxlength="100" id="user_login" autocomplete="off" />
				</div>

				<div class="bbp-password">
					<label for="user_pass"><?php esc_html_e('Password', 'bbpress'); ?>: </label>
					<input type="password" name="pwd" value="<?php bbp_sanitize_val('user_pass', 'password'); ?>" size="20" id="user_pass" autocomplete="off" />
				</div>

				<div class="bbp-remember-me">
					<input type="checkbox" name="rememberme" value="forever" <?php checked(bbp_get_sanitize_val('rememberme', 'checkbox')); ?> id="rememberme" />
					<label for="rememberme"><?php esc_html_e('Keep me signed in', 'bbpress'); ?></label>
				</div>

				<?php do_action('login_form'); ?>

				<div class="bbp-submit-wrapper">

					<button type="submit" name="user-submit" id="user-submit" class="button submit user-submit"><?php esc_html_e('Log In', 'bbpress'); ?></button>

					<?php bbp_user_login_fields(); ?>

				</div>
			</fieldset>
		</form>
	<?php
	}

	protected function render_alert_topic_lock() {
		do_action('bbp_theme_before_alert_topic_lock'); ?>

		<?php if (bbp_show_topic_lock_alert()) : ?>

			<div class="bbp-alert-outer">
				<div class="bbp-alert-inner">
					<p class="bbp-alert-description"><?php bbp_topic_lock_description(); ?></p>
					<p class="bbp-alert-actions">
						<a class="bbp-alert-back" href="<?php bbp_forum_permalink(bbp_get_topic_forum_id()); ?>"><?php esc_html_e('Leave', 'bbpress'); ?></a>
						<a class="bbp-alert-close" href="#"><?php esc_html_e('Stay', 'bbpress'); ?></a>
					</p>
				</div>
			</div>

		<?php endif;

		do_action('bbp_theme_after_alert_topic_lock');
	}

	protected function render_form_anonymous() {
		if (bbp_current_user_can_access_anonymous_user_form()) : ?>

			<?php do_action('bbp_theme_before_anonymous_form'); ?>

			<fieldset class="bbp-form">
				<legend><?php (bbp_is_topic_edit() || bbp_is_reply_edit()) ? esc_html_e('Author Information', 'bbpress') : esc_html_e('Your information:', 'bbpress'); ?></legend>

				<?php do_action('bbp_theme_anonymous_form_extras_top'); ?>

				<p>
					<label for="bbp_anonymous_author"><?php esc_html_e('Name (required):', 'bbpress'); ?></label><br />
					<input type="text" id="bbp_anonymous_author" value="<?php bbp_author_display_name(); ?>" size="40" maxlength="100" name="bbp_anonymous_name" autocomplete="off" />
				</p>

				<p>
					<label for="bbp_anonymous_email"><?php esc_html_e('Mail (will not be published) (required):', 'bbpress'); ?></label><br />
					<input type="text" id="bbp_anonymous_email" value="<?php bbp_author_email(); ?>" size="40" maxlength="100" name="bbp_anonymous_email" />
				</p>

				<p>
					<label for="bbp_anonymous_website"><?php esc_html_e('Website:', 'bbpress'); ?></label><br />
					<input type="text" id="bbp_anonymous_website" value="<?php bbp_author_url(); ?>" size="40" maxlength="200" name="bbp_anonymous_website" />
				</p>

				<?php do_action('bbp_theme_anonymous_form_extras_bottom'); ?>

			</fieldset>

			<?php do_action('bbp_theme_after_anonymous_form'); ?>

		<?php endif;
	}
	protected function render_form_topic() {
		$settings = $this->get_settings_for_display();

		if (!bbp_is_single_forum()) : ?>

			<div id="bbpress-forums" class="bbpress-wrapper">
				<?php if ($settings['show_breadcrumb']) : ?>
					<?php bbp_breadcrumb(); ?>
				<?php endif; ?>
			<?php endif; ?>

			<?php if (bbp_is_topic_edit()) : ?>
				<?php bbp_topic_tag_list(bbp_get_topic_id()); ?>
				<?php bbp_single_topic_description(array('topic_id' => bbp_get_topic_id())); ?>
				<?php $this->render_alert_topic_lock(); ?>
			<?php endif; ?>

			<?php if (bbp_current_user_can_access_create_topic_form()) : ?>
				<div id="new-topic-<?php bbp_topic_id(); ?>" class="bbp-topic-form">
					<form id="new-post" name="new-post" method="post">
						<?php do_action('bbp_theme_before_topic_form'); ?>
						<fieldset class="bbp-form">
							<legend>
								<?php
								if (bbp_is_topic_edit()) :
									printf(esc_html__('Now Editing &ldquo;%s&rdquo;', 'bbpress'), bbp_get_topic_title());
								else : (bbp_is_single_forum() && bbp_get_forum_title())
										? printf(esc_html__('Create New Topic in &ldquo;%s&rdquo;', 'bbpress'), bbp_get_forum_title())
										: esc_html_e('Create New Topic', 'bbpress');
								endif; ?>
							</legend>

							<?php do_action('bbp_theme_before_topic_form_notices'); ?>

							<?php if (!bbp_is_topic_edit() && bbp_is_forum_closed()) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('This forum is marked as closed to new topics, however your posting capabilities still allow you to create a topic.', 'bbpress'); ?></li>
									</ul>
								</div>

							<?php endif; ?>

							<?php if (current_user_can('unfiltered_html')) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('Your account has the ability to post unrestricted HTML content.', 'bbpress'); ?></li>
									</ul>
								</div>

							<?php endif; ?>

							<?php do_action('bbp_template_notices'); ?>

							<div>

								<?php $this->render_form_anonymous(); ?>

								<?php do_action('bbp_theme_before_topic_form_title'); ?>

								<p>
									<label for="bbp_topic_title"><?php printf(esc_html__('Topic Title (Maximum Length: %d):', 'bbpress'), bbp_get_title_max_length()); ?></label><br />
									<input type="text" id="bbp_topic_title" value="<?php bbp_form_topic_title(); ?>" size="40" name="bbp_topic_title" maxlength="<?php bbp_title_max_length(); ?>" />
								</p>

								<?php do_action('bbp_theme_after_topic_form_title'); ?>

								<?php do_action('bbp_theme_before_topic_form_content'); ?>

								<?php bbp_the_content(array('context' => 'topic')); ?>

								<?php do_action('bbp_theme_after_topic_form_content'); ?>

								<?php if (!(bbp_use_wp_editor() || current_user_can('unfiltered_html'))) : ?>

									<p class="form-allowed-tags">
										<label><?php printf(esc_html__('You may use these %s tags and attributes:', 'bbpress'), '<abbr title="HyperText Markup Language">HTML</abbr>'); ?></label><br />
										<code><?php bbp_allowed_tags(); ?></code>
									</p>

								<?php endif; ?>

								<?php if (bbp_allow_topic_tags() && current_user_can('assign_topic_tags', bbp_get_topic_id())) : ?>

									<?php do_action('bbp_theme_before_topic_form_tags'); ?>

									<p>
										<label for="bbp_topic_tags"><?php esc_html_e('Topic Tags:', 'bbpress'); ?></label><br />
										<input type="text" value="<?php bbp_form_topic_tags(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" <?php disabled(bbp_is_topic_spam()); ?> />
									</p>

									<?php do_action('bbp_theme_after_topic_form_tags'); ?>

								<?php endif; ?>

								<?php if (!bbp_is_single_forum()) : ?>

									<?php do_action('bbp_theme_before_topic_form_forum'); ?>

									<p>
										<label for="bbp_forum_id"><?php esc_html_e('Forum:', 'bbpress'); ?></label><br />
										<?php
										bbp_dropdown(array(
											'show_none' => esc_html__('&mdash; No forum &mdash;', 'bbpress'),
											'selected'  => bbp_get_form_topic_forum()
										));
										?>
									</p>

									<?php do_action('bbp_theme_after_topic_form_forum'); ?>

								<?php endif; ?>

								<?php if (current_user_can('moderate', bbp_get_topic_id())) : ?>

									<?php do_action('bbp_theme_before_topic_form_type'); ?>

									<p>

										<label for="bbp_stick_topic"><?php esc_html_e('Topic Type:', 'bbpress'); ?></label><br />

										<?php bbp_form_topic_type_dropdown(); ?>

									</p>

									<?php do_action('bbp_theme_after_topic_form_type'); ?>

									<?php do_action('bbp_theme_before_topic_form_status'); ?>

									<p>

										<label for="bbp_topic_status"><?php esc_html_e('Topic Status:', 'bbpress'); ?></label><br />

										<?php bbp_form_topic_status_dropdown(); ?>

									</p>

									<?php do_action('bbp_theme_after_topic_form_status'); ?>

								<?php endif; ?>

								<?php if (bbp_is_subscriptions_active() && !bbp_is_anonymous() && (!bbp_is_topic_edit() || (bbp_is_topic_edit() && !bbp_is_topic_anonymous()))) : ?>

									<?php do_action('bbp_theme_before_topic_form_subscriptions'); ?>

									<p>
										<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> />

										<?php if (bbp_is_topic_edit() && (bbp_get_topic_author_id() !== bbp_get_current_user_id())) : ?>

											<label for="bbp_topic_subscription"><?php esc_html_e('Notify the author of follow-up replies via email', 'bbpress'); ?></label>

										<?php else : ?>

											<label for="bbp_topic_subscription"><?php esc_html_e('Notify me of follow-up replies via email', 'bbpress'); ?></label>

										<?php endif; ?>
									</p>

									<?php do_action('bbp_theme_after_topic_form_subscriptions'); ?>

								<?php endif; ?>

								<?php if (bbp_allow_revisions() && bbp_is_topic_edit()) : ?>

									<?php do_action('bbp_theme_before_topic_form_revisions'); ?>

									<fieldset class="bbp-form">
										<legend>
											<input name="bbp_log_topic_edit" id="bbp_log_topic_edit" type="checkbox" value="1" <?php bbp_form_topic_log_edit(); ?> />
											<label for="bbp_log_topic_edit"><?php esc_html_e('Keep a log of this edit:', 'bbpress'); ?></label><br />
										</legend>

										<div>
											<label for="bbp_topic_edit_reason"><?php printf(esc_html__('Optional reason for editing:', 'bbpress'), bbp_get_current_user_name()); ?></label><br />
											<input type="text" value="<?php bbp_form_topic_edit_reason(); ?>" size="40" name="bbp_topic_edit_reason" id="bbp_topic_edit_reason" />
										</div>
									</fieldset>

									<?php do_action('bbp_theme_after_topic_form_revisions'); ?>

								<?php endif; ?>

								<?php do_action('bbp_theme_before_topic_form_submit_wrapper'); ?>

								<div class="bbp-submit-wrapper">

									<?php do_action('bbp_theme_before_topic_form_submit_button'); ?>

									<button type="submit" id="bbp_topic_submit" name="bbp_topic_submit" class="button submit"><?php esc_html_e('Submit', 'bbpress'); ?></button>

									<?php do_action('bbp_theme_after_topic_form_submit_button'); ?>

								</div>

								<?php do_action('bbp_theme_after_topic_form_submit_wrapper'); ?>

							</div>

							<?php bbp_topic_form_fields(); ?>

						</fieldset>

						<?php do_action('bbp_theme_after_topic_form'); ?>

					</form>
				</div>

			<?php elseif (bbp_is_forum_closed()) : ?>

				<div id="forum-closed-<?php bbp_forum_id(); ?>" class="bbp-forum-closed">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress'), bbp_get_forum_title()); ?></li>
						</ul>
					</div>
				</div>

			<?php else : ?>

				<div id="no-topic-<?php bbp_forum_id(); ?>" class="bbp-no-topic">
					<div class="bbp-template-notice">
						<ul>
							<li><?php is_user_logged_in()
									? esc_html_e('You cannot create new topics.',               'bbpress')
									: esc_html_e('You must be logged in to create new topics.', 'bbpress');
								?></li>
						</ul>
					</div>

					<?php if (!is_user_logged_in()) : ?>

						<?php $this->render_user_login_form(); ?>

					<?php endif; ?>

				</div>

			<?php endif; ?>

			<?php if (!bbp_is_single_forum()) : ?>

			</div>

		<?php endif;
		}

		protected function render_loop_single_topic() { ?>
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

				<a class="bbp-topic-permalink bdt-inline" href="<?php bbp_topic_permalink(); ?>"><?php bbp_topic_title(); ?></a>

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
		</ul>
	<?php
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
		protected function render_pagination_topics() {
			do_action('bbp_template_before_pagination_loop'); ?>
		<div class="bbp-pagination">
			<div class="bbp-pagination-count"><?php bbp_forum_pagination_count(); ?></div>
			<div class="bbp-pagination-links"><?php bbp_forum_pagination_links(); ?></div>
		</div>
	<?php do_action('bbp_template_after_pagination_loop');
		}

		protected function render_feedback_no_topics() { ?>
		<div class="bbp-template-notice">
			<ul>
				<li><?php esc_html_e('Oh, bother! No topics were found here.', 'bbpress'); ?></li>
			</ul>
		</div>
	<?php
		}
		protected function render_feedback_no_access() { ?>
		<div id="forum-private" class="bbp-forum-content">
			<h1 class="entry-title"><?php esc_html_e('Private', 'bbpress'); ?></h1>
			<div class="entry-content">
				<div class="bbp-template-notice info">
					<ul>
						<li><?php esc_html_e('You do not have permission to view this forum.', 'bbpress'); ?></li>
					</ul>
				</div>
			</div>
		</div><!-- #forum-private -->
	<?php
		}


		protected function content_single_form() {
	?>
		<div id="bbpress-forums" class="bbpress-wrapper">

			<?php bbp_breadcrumb(); ?>

			<?php bbp_forum_subscription_link(); ?>

			<?php do_action('bbp_template_before_single_forum'); ?>

			<?php if (post_password_required()) : ?>

				<?php $this->render_protected_form(); ?>

			<?php else : ?>

				<?php bbp_single_forum_description(); ?>

				<?php if (bbp_has_forums()) : ?>
					<?php $this->render_loop_forum(); ?>
				<?php endif; ?>

				<?php if (!bbp_is_forum_category() && bbp_has_topics()) :
					$this->render_pagination_topics();
					$this->render_loop_topics();
					$this->render_pagination_topics();
					$this->render_form_topic(); ?>

				<?php elseif (!bbp_is_forum_category()) : ?>
					<?php $this->render_feedback_no_topics(); ?>
					<?php $this->render_form_topic(); ?>
				<?php endif; ?>

			<?php endif; ?>

			<?php do_action('bbp_template_after_single_forum'); ?>

		</div>
<?php
		}
		public function render() {
			$forum_id = bbpress()->current_forum_id = $this->get_settings_for_display('bbpress_single_id');
			if (!bbp_is_forum($forum_id)) {
				return false;
			}
			bbp_set_query_name('bbp_single_forum');
			// Start output buffer
			if (bbp_user_can_view_forum(array('forum_id' => $forum_id))) {
				$this->content_single_form();
				// Forum is private and user does not have caps
			} elseif (bbp_is_forum_private($forum_id, false)) {
				$this->render_feedback_no_access();
			}
			wp_reset_postdata();
		}
	}
