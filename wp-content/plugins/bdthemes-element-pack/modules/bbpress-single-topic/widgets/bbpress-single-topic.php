<?php

namespace ElementPack\Modules\BbpressSingleTopic\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use ElementPack\Includes\Controls\SelectInput\Dynamic_Select;


if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Single_Topic extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-single-topic';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Single Topic', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-single-topic';
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
			'bbpress_topic_id',
			[
				'label'       => __('Single Topics', 'bdthemes-element-pack'),
				'type'        => Dynamic_Select::TYPE,
				'label_block' => true,
				'placeholder' => __('Type and select Single Topic', 'bdthemes-element-pack'),
				'query_args'  => [
					'query'        => 'bbpress_single_topic',
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
					'{{WRAPPER}} .bbp-breadcrumb *, {{WRAPPER}} #bbpress-forums a.subscription-toggle, {{WRAPPER}} #bbpress-forums a.favorite-toggle' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'breadcrumb_hover_color',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-breadcrumb a:hover, {{WRAPPER}} #bbpress-forums a.subscription-toggle:hover, {{WRAPPER}} #bbpress-forums a.favorite-toggle:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} .bbp-breadcrumb, {{WRAPPER}} #bbpress-forums a.subscription-toggle, {{WRAPPER}} #bbpress-forums a.favorite-toggle' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'breadcrumb_typography',
				'selector' => '{{WRAPPER}} .bbp-breadcrumb, {{WRAPPER}} #bbpress-forums a.subscription-toggle, {{WRAPPER}} #bbpress-forums a.favorite-toggle',
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
					'{{WRAPPER}} #bbpress-forums li.bbp-header>div, {{WRAPPER}} #bbpress-forums li.bbp-footer>div' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'header_background',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'header_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer',
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
					'{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'header_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums li.bbp-header, {{WRAPPER}} #bbpress-forums li.bbp-footer li',
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
				'selector' => '{{WRAPPER}} #bbpress-forums ul.bbp-forums',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums ul.bbp-forums' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_bbpress_author',
			[
				'label' => esc_html__('Author', 'bdthemes-element-pack'),
				'tab'   => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'author_color',
			[
				'label'     => esc_html__('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-author *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'author_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .bbp-reply-author a:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_responsive_control(
			'author_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-author' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'author_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-author *',
			]
		);

		$this->add_responsive_control(
			'author_avatar',
			[
				'label'     => esc_html__('Avatar Size', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-author img.avatar' => 'max-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before'
			]
		);

		$this->add_responsive_control(
			'author_avatar_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors'  => [
					'{{WRAPPER}} #bbpress-forums div.bbp-reply-author img.avatar' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-content' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-content' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_text_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-content',
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
					'{{WRAPPER}} #bbpress-forums .bbp-meta *' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'forum_meta_color_hover',
			[
				'label'     => esc_html__('Hover Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #bbpress-forums .bbp-meta a:hover' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'forum_meta_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-meta *',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form legend' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-form .bbp-form',
			]
		);

		$this->add_responsive_control(
			'form_border_radius',
			[
				'label'      => __('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors'  => [
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form .bbp-form' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form .bbp-form' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form .bbp-form' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'main_title_typography',
				'label' => esc_html__('Title Typography', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-form legend',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form label' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'label_typography',
				'selector' => '{{WRAPPER}} #bbpress-forums .bbp-reply-form label',
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
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form input::placeholder' => 'color: {{VALUE}};',
					'{{WRAPPER}} #bbpress-forums .bbp-reply-form textarea::placeholder' => 'color: {{VALUE}};',
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
					'{{WRAPPER}} #bbpress-forums div.bbp-the-content-wrapper textarea.bbp-the-content' => 'height: {{SIZE}}{{UNIT}}; width: 100%;',
				],
				'separator' => 'before',

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
					'{{WRAPPER}} div.bbp-template-notice *, {{WRAPPER}} div.indicator-hint' => 'color: {{VALUE}};',
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
				'selector' => '{{WRAPPER}} div.bbp-template-notice *, {{WRAPPER}} div.bbp-template-notice li',
			]
		);

		$this->end_controls_section();
	}


	protected function loop_single_reply() { ?>
		<div id="post-<?php bbp_reply_id(); ?>" class="bbp-reply-header">
			<div class="bbp-meta">
				<span class="bbp-reply-post-date"><?php bbp_reply_post_date(); ?></span>
				<?php if (bbp_is_single_user_replies()) : ?>
					<span class="bbp-header">
						<?php esc_html_e('in reply to: ', 'bbpress'); ?>
						<a class="bbp-topic-permalink" href="<?php bbp_topic_permalink(bbp_get_reply_topic_id()); ?>"><?php bbp_topic_title(bbp_get_reply_topic_id()); ?></a>
					</span>
				<?php endif; ?>

				<a href="<?php bbp_reply_url(); ?>" class="bbp-reply-permalink">#<?php bbp_reply_id(); ?></a>

				<?php do_action('bbp_theme_before_reply_admin_links'); ?>

				<?php bbp_reply_admin_links(); ?>

				<?php do_action('bbp_theme_after_reply_admin_links'); ?>

			</div><!-- .bbp-meta -->
		</div><!-- #post-<?php bbp_reply_id(); ?> -->

		<div <?php bbp_reply_class(); ?>>
			<div class="bbp-reply-author">

				<?php do_action('bbp_theme_before_reply_author_details'); ?>

				<?php bbp_reply_author_link(array('show_role' => true)); ?>

				<?php if (current_user_can('moderate', bbp_get_reply_id())) : ?>

					<?php do_action('bbp_theme_before_reply_author_admin_details'); ?>

					<div class="bbp-reply-ip"><?php bbp_author_ip(bbp_get_reply_id()); ?></div>

					<?php do_action('bbp_theme_after_reply_author_admin_details'); ?>

				<?php endif; ?>

				<?php do_action('bbp_theme_after_reply_author_details'); ?>

			</div><!-- .bbp-reply-author -->

			<div class="bbp-reply-content">

				<?php do_action('bbp_theme_before_reply_content'); ?>

				<?php bbp_reply_content(); ?>

				<?php do_action('bbp_theme_after_reply_content'); ?>

			</div><!-- .bbp-reply-content -->
		</div><!-- .reply -->
		<?php
	}


	protected function form_anonymous() {
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

	protected function form_user_login() {
		?>
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
	protected function render_form_reply() {
		$settings = $this->get_settings_for_display();
		if (bbp_is_reply_edit()) : ?>

			<div id="bbpress-forums" class="bbpress-wrapper">

				<?php if ($settings['show_breadcrumb']) : ?>
					<?php bbp_breadcrumb(); ?>
				<?php endif; ?>

			<?php endif; ?>

			<?php if (bbp_current_user_can_access_create_reply_form()) : ?>

				<div id="new-reply-<?php bbp_topic_id(); ?>" class="bbp-reply-form">

					<form id="new-post" name="new-post" method="post">

						<?php do_action('bbp_theme_before_reply_form'); ?>

						<fieldset class="bbp-form">
							<legend><?php printf(esc_html__('Reply To: %s', 'bbpress'), (bbp_get_form_reply_to()) ? sprintf(esc_html__('Reply #%1$s in %2$s', 'bbpress'), bbp_get_form_reply_to(), bbp_get_topic_title()) : bbp_get_topic_title()); ?></legend>

							<?php do_action('bbp_theme_before_reply_form_notices'); ?>

							<?php if (!bbp_is_topic_open() && !bbp_is_reply_edit()) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('This topic is marked as closed to new replies, however your posting capabilities still allow you to reply.', 'bbpress'); ?></li>
									</ul>
								</div>

							<?php endif; ?>

							<?php if (!bbp_is_reply_edit() && bbp_is_forum_closed()) : ?>

								<div class="bbp-template-notice">
									<ul>
										<li><?php esc_html_e('This forum is closed to new content, however your posting capabilities still allow you to post.', 'bbpress'); ?></li>
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

								<?php $this->form_anonymous(); ?>

								<?php do_action('bbp_theme_before_reply_form_content'); ?>

								<?php bbp_the_content(array('context' => 'reply')); ?>

								<?php do_action('bbp_theme_after_reply_form_content'); ?>

								<?php if (!(bbp_use_wp_editor() || current_user_can('unfiltered_html'))) : ?>

									<p class="form-allowed-tags">
										<label><?php esc_html_e('You may use these <abbr title="HyperText Markup Language">HTML</abbr> tags and attributes:', 'bbpress'); ?></label><br />
										<code><?php bbp_allowed_tags(); ?></code>
									</p>

								<?php endif; ?>

								<?php if (bbp_allow_topic_tags() && current_user_can('assign_topic_tags', bbp_get_topic_id())) : ?>

									<?php do_action('bbp_theme_before_reply_form_tags'); ?>

									<p>
										<label for="bbp_topic_tags"><?php esc_html_e('Tags:', 'bbpress'); ?></label><br />
										<input type="text" value="<?php bbp_form_topic_tags(); ?>" size="40" name="bbp_topic_tags" id="bbp_topic_tags" <?php disabled(bbp_is_topic_spam()); ?> />
									</p>

									<?php do_action('bbp_theme_after_reply_form_tags'); ?>

								<?php endif; ?>

								<?php if (bbp_is_subscriptions_active() && !bbp_is_anonymous() && (!bbp_is_reply_edit() || (bbp_is_reply_edit() && !bbp_is_reply_anonymous()))) : ?>

									<?php do_action('bbp_theme_before_reply_form_subscription'); ?>

									<p>

										<input name="bbp_topic_subscription" id="bbp_topic_subscription" type="checkbox" value="bbp_subscribe" <?php bbp_form_topic_subscribed(); ?> />

										<?php if (bbp_is_reply_edit() && (bbp_get_reply_author_id() !== bbp_get_current_user_id())) : ?>

											<label for="bbp_topic_subscription"><?php esc_html_e('Notify the author of follow-up replies via email', 'bbpress'); ?></label>

										<?php else : ?>

											<label for="bbp_topic_subscription"><?php esc_html_e('Notify me of follow-up replies via email', 'bbpress'); ?></label>

										<?php endif; ?>

									</p>

									<?php do_action('bbp_theme_after_reply_form_subscription'); ?>

								<?php endif; ?>

								<?php if (bbp_is_reply_edit()) : ?>

									<?php if (current_user_can('moderate', bbp_get_reply_id())) : ?>

										<?php do_action('bbp_theme_before_reply_form_reply_to'); ?>

										<p class="form-reply-to">
											<label for="bbp_reply_to"><?php esc_html_e('Reply To:', 'bbpress'); ?></label><br />
											<?php bbp_reply_to_dropdown(); ?>
										</p>

										<?php do_action('bbp_theme_after_reply_form_reply_to'); ?>

										<?php do_action('bbp_theme_before_reply_form_status'); ?>

										<p>
											<label for="bbp_reply_status"><?php esc_html_e('Reply Status:', 'bbpress'); ?></label><br />
											<?php bbp_form_reply_status_dropdown(); ?>
										</p>

										<?php do_action('bbp_theme_after_reply_form_status'); ?>

									<?php endif; ?>

									<?php if (bbp_allow_revisions()) : ?>

										<?php do_action('bbp_theme_before_reply_form_revisions'); ?>

										<fieldset class="bbp-form">
											<legend>
												<input name="bbp_log_reply_edit" id="bbp_log_reply_edit" type="checkbox" value="1" <?php bbp_form_reply_log_edit(); ?> />
												<label for="bbp_log_reply_edit"><?php esc_html_e('Keep a log of this edit:', 'bbpress'); ?></label><br />
											</legend>

											<div>
												<label for="bbp_reply_edit_reason"><?php printf(esc_html__('Optional reason for editing:', 'bbpress'), bbp_get_current_user_name()); ?></label><br />
												<input type="text" value="<?php bbp_form_reply_edit_reason(); ?>" size="40" name="bbp_reply_edit_reason" id="bbp_reply_edit_reason" />
											</div>
										</fieldset>

										<?php do_action('bbp_theme_after_reply_form_revisions'); ?>

									<?php endif; ?>

								<?php endif; ?>

								<?php do_action('bbp_theme_before_reply_form_submit_wrapper'); ?>

								<div class="bbp-submit-wrapper">

									<?php do_action('bbp_theme_before_reply_form_submit_button'); ?>

									<?php bbp_cancel_reply_to_link(); ?>

									<button type="submit" id="bbp_reply_submit" name="bbp_reply_submit" class="button submit"><?php esc_html_e('Submit', 'bbpress'); ?></button>

									<?php do_action('bbp_theme_after_reply_form_submit_button'); ?>

								</div>

								<?php do_action('bbp_theme_after_reply_form_submit_wrapper'); ?>

							</div>

							<?php bbp_reply_form_fields(); ?>

						</fieldset>

						<?php do_action('bbp_theme_after_reply_form'); ?>

					</form>
				</div>

			<?php elseif (bbp_is_topic_closed()) : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The topic &#8216;%s&#8217; is closed to new replies.', 'bbpress'), bbp_get_topic_title()); ?></li>
						</ul>
					</div>
				</div>

			<?php elseif (bbp_is_forum_closed(bbp_get_topic_forum_id())) : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php printf(esc_html__('The forum &#8216;%s&#8217; is closed to new topics and replies.', 'bbpress'), bbp_get_forum_title(bbp_get_topic_forum_id())); ?></li>
						</ul>
					</div>
				</div>

			<?php else : ?>

				<div id="no-reply-<?php bbp_topic_id(); ?>" class="bbp-no-reply">
					<div class="bbp-template-notice">
						<ul>
							<li><?php is_user_logged_in()
									? esc_html_e('You cannot reply to this topic.',               'bbpress')
									: esc_html_e('You must be logged in to reply to this topic.', 'bbpress');
								?></li>
						</ul>
					</div>

					<?php if (!is_user_logged_in()) : ?>

						<?php $this->form_user_login(); ?>

					<?php endif; ?>

				</div>

			<?php endif; ?>
			<?php if (bbp_is_reply_edit()) : ?>
			</div>
		<?php endif;
		}

		protected function render_loop_replies() {
			do_action('bbp_template_before_replies_loop'); ?>
		<ul id="topic-<?php bbp_topic_id(); ?>-replies" class="forums bbp-replies">
			<li class="bbp-header">
				<div class="bbp-reply-author"><?php esc_html_e('Author',  'bbpress'); ?></div>
				<div class="bbp-reply-content"><?php bbp_show_lead_topic() ? esc_html_e('Replies', 'bbpress') : esc_html_e('Posts',   'bbpress'); ?></div>
			</li>
			<li class="bbp-body">

				<?php if (bbp_thread_replies()) : ?>

					<?php bbp_list_replies(); ?>

				<?php else : ?>

					<?php while (bbp_replies()) : bbp_the_reply(); ?>
						<?php $this->loop_single_reply(); ?>
					<?php endwhile; ?>

				<?php endif; ?>

			</li>

			<li class="bbp-footer">
				<div class="bbp-reply-author"><?php esc_html_e('Author',  'bbpress'); ?></div>
				<div class="bbp-reply-content">
					<?php bbp_show_lead_topic() ? esc_html_e('Replies', 'bbpress') : esc_html_e('Posts',   'bbpress'); ?></div>
			</li>
		</ul>
	<?php do_action('bbp_template_after_replies_loop');
		}
		protected function render_pagination_replies() {
			do_action('bbp_template_before_pagination_loop'); ?>
		<div class="bbp-pagination">
			<div class="bbp-pagination-count"><?php bbp_topic_pagination_count(); ?></div>
			<div class="bbp-pagination-links"><?php bbp_topic_pagination_links(); ?></div>
		</div>
	<?php do_action('bbp_template_after_pagination_loop');
		}

		protected function render_single_topic_lead() {
			do_action('bbp_template_before_lead_topic'); ?>
		<ul id="bbp-topic-<?php bbp_topic_id(); ?>-lead" class="bbp-lead-topic">
			<li class="bbp-header">
				<div class="bbp-topic-author"><?php esc_html_e('Creator',  'bbpress'); ?></div><!-- .bbp-topic-author -->
				<div class="bbp-topic-content">
					<?php esc_html_e('Topic', 'bbpress'); ?>
				</div><!-- .bbp-topic-content -->
			</li><!-- .bbp-header -->

			<li class="bbp-body">
				<div class="bbp-topic-header">
					<div class="bbp-meta">
						<span class="bbp-topic-post-date"><?php bbp_topic_post_date(); ?></span>
						<a href="<?php bbp_topic_permalink(); ?>" class="bbp-topic-permalink">#<?php bbp_topic_id(); ?></a>
						<?php do_action('bbp_theme_before_topic_admin_links'); ?>
						<?php bbp_topic_admin_links(); ?>
						<?php do_action('bbp_theme_after_topic_admin_links'); ?>
					</div>
				</div>

				<div id="post-<?php bbp_topic_id(); ?>" <?php bbp_topic_class(); ?>>
					<div class="bbp-topic-author">
						<?php do_action('bbp_theme_before_topic_author_details'); ?>
						<?php bbp_topic_author_link(array('show_role' => true)); ?>
						<?php if (current_user_can('moderate', bbp_get_reply_id())) : ?>
							<?php do_action('bbp_theme_before_topic_author_admin_details'); ?>
							<div class="bbp-topic-ip"><?php bbp_author_ip(bbp_get_topic_id()); ?></div>
							<?php do_action('bbp_theme_after_topic_author_admin_details'); ?>
						<?php endif; ?>
						<?php do_action('bbp_theme_after_topic_author_details'); ?>
					</div>
					<div class="bbp-topic-content">
						<?php do_action('bbp_theme_before_topic_content'); ?>
						<?php bbp_topic_content(); ?>
						<?php do_action('bbp_theme_after_topic_content'); ?>
					</div>

				</div>

			</li>

			<li class="bbp-footer">

				<div class="bbp-topic-author"><?php esc_html_e('Creator',  'bbpress'); ?></div>

				<div class="bbp-topic-content">

					<?php esc_html_e('Topic', 'bbpress'); ?>

				</div><!-- .bbp-topic-content -->

			</li>

		</ul>

	<?php do_action('bbp_template_after_lead_topic');
		}


		public function render_feedback_no_access() {
	?>
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



		protected function render_topic_lock_alert() {
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
		protected function render_form_protected() {
		?>
		<div id="bbpress-forums" class="bbpress-wrapper">
			<fieldset class="bbp-form" id="bbp-protected">
				<Legend><?php esc_html_e('Protected', 'bbpress'); ?></legend>
				<?php echo get_the_password_form(); ?>
			</fieldset>
		</div>
	<?php
		}
		protected function render_content_single_topic() {
	?>
		<div id="bbpress-forums" class="bbpress-wrapper">

			<?php bbp_breadcrumb(); ?>

			<?php bbp_topic_subscription_link(); ?>

			<?php bbp_topic_favorite_link(); ?>

			<?php do_action('bbp_template_before_single_topic'); ?>

			<?php if (post_password_required()) : ?>
				<?php $this->render_form_protected(); ?>
			<?php else : ?>

				<?php bbp_topic_tag_list(); ?>

				<?php bbp_single_topic_description(); ?>

				<?php if (bbp_show_lead_topic()) : ?>
					<?php $this->render_single_topic_lead(); ?>
				<?php endif; ?>

				<?php if (bbp_has_replies()) : ?>

					<?php $this->render_pagination_replies(); ?>

					<?php $this->render_loop_replies(); ?>

					<?php $this->render_pagination_replies(); ?>

				<?php endif; ?>

				<?php $this->render_form_reply(); ?>

			<?php endif; ?>

			<?php $this->render_topic_lock_alert(); ?>

			<?php do_action('bbp_template_after_single_topic'); ?>

		</div>
<?php
		}
		public function render() {

			// Set passed attribute to $forum_id for clarity
			$topic_id = bbpress()->current_topic_id = $this->get_settings_for_display('bbpress_topic_id');
			$forum_id = bbp_get_topic_forum_id($topic_id);

			// Bail if ID passed is not a topic
			if (!bbp_is_topic($topic_id)) {
				element_pack_alert('Ops, Your single topic ID is Missing, Please enter your specific replies ID');
				return;
			}

			// Reset the queries if not in theme compat
			if (!bbp_is_theme_compat_active()) {

				$bbp = bbpress();

				// Reset necessary forum_query attributes for topics loop to function
				$bbp->forum_query->query_vars['post_type'] = bbp_get_forum_post_type();
				$bbp->forum_query->in_the_loop             = true;
				$bbp->forum_query->post                    = get_post($forum_id);

				// Reset necessary topic_query attributes for topics loop to function
				$bbp->topic_query->query_vars['post_type'] = bbp_get_topic_post_type();
				$bbp->topic_query->in_the_loop             = true;
				$bbp->topic_query->post                    = get_post($topic_id);
			}

			// Start output buffer
			bbp_set_query_name('bbp_single_topic');

			// Check forum caps
			if (bbp_user_can_view_forum(array('forum_id' => $forum_id))) {
				$this->render_content_single_topic();

				// Forum is private and user does not have caps
			} elseif (bbp_is_forum_private($forum_id, false)) {
				$this->render_feedback_no_access();
			}

			// reset query
			wp_reset_postdata();
		}
	}
