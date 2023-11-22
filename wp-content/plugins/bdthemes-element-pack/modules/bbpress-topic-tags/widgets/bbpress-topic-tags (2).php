<?php

namespace ElementPack\Modules\BbpressTopicTags\Widgets;

use ElementPack\Base\Module_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Bbpress_Topic_Tags extends Module_Base {

	public function get_name() {
		return 'bdt-bbpress-topic-tags';
	}

	public function get_title() {
		return BDTEP . esc_html__('bbPress Topic Tags', 'bdthemes-element-pack');
	}

	public function get_icon() {
		return 'bdt-wi-bbpress-topic-tags';
	}

	public function get_categories() {
		return ['element-pack-bbpress'];
	}

	public function get_keywords() {
		return ['bbpress', 'forum', 'community', 'discussion', 'support'];
	}

	// public function get_custom_help_url() {
	// 	return 'https://youtu.be/7vkAHZ778c4';
	// }

	protected function register_controls() {
		$this->start_controls_section(
			'section_style_topic_tags',
			[
				'label' => esc_html__('Topic Tags', 'bdthemes-element-pack'),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'tags_alignment',
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
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}}',
				],
			]
		);

		$this->start_controls_tabs('tabs_topic_tags_style');

		$this->start_controls_tab(
			'tab_topic_tags_normal',
			[
				'label' => esc_html__('Normal', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'topic_tags_text_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'topic_tags_background_color',
				'selector' => '{{WRAPPER}} a.tag-cloud-link',
			]
		);

		$this->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name' => 'topic_tags_border',
				'label' => esc_html__('Border', 'bdthemes-element-pack'),
				'placeholder' => '1px',
				'default' => '1px',
				'selector' => '{{WRAPPER}} a.tag-cloud-link',
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'topic_tags_border_radius',
			[
				'label' => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%'],
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		$this->add_responsive_control(
			'topic_tags_padding',
			[
				'label' => esc_html__('Padding', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}}; display: inline-block;',
				],
			]
		);

		$this->add_responsive_control(
			'topic_tags_margin',
			[
				'label' => esc_html__('Margin', 'bdthemes-element-pack'),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', 'em', '%'],
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);

		// $this->add_group_control(
		// 	Group_Control_Typography::get_type(),
		// 	[
		// 		'name' => 'topic_tags_typography',
		// 		'selector' => '{{WRAPPER}} a.tag-cloud-link',
		// 	]
		// );
		$this->add_control(
			'topic_tags_font_family',
			[
				'label' => esc_html__( 'Font Family', 'textdomain' ),
				'type' => Controls_Manager::FONT,
				'default' => "'Open Sans', sans-serif",
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'font-family: {{VALUE}}',
				],
			]
		);

		$this->add_responsive_control(
			'topic_tags_typography',
			[
				'label' => esc_html__( 'Font Size', 'bdthemes-element-pack' ),
				'type' => Controls_Manager::SLIDER,
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link' => 'font-size: {{SIZE}}{{UNIT}} !important;',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name' => 'topic_tags_box_shadow',
				'selector' => '{{WRAPPER}} a.tag-cloud-link',
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_topic_tags_hover',
			[
				'label' => esc_html__('Hover', 'bdthemes-element-pack'),
			]
		);

		$this->add_control(
			'topic_tags_hover_color',
			[
				'label' => esc_html__('Text Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link:hover' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'topic_tags_background_color_hover',
				'selector' => '{{WRAPPER}} a.tag-cloud-link:hover',
			]
		);

		$this->add_control(
			'topic_tags_hover_border_color',
			[
				'label' => esc_html__('Border Color', 'bdthemes-element-pack'),
				'type' => Controls_Manager::COLOR,
				'condition' => [
					'topic_tags_border_border!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} a.tag-cloud-link:hover' => 'border-color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->end_controls_section();
	}


	public function render() {
		bbp_set_query_name('bbp_topic_tags');
		// Output the topic tags
		wp_tag_cloud(array(
			// 'smallest' => 9,
			'largest'  => 38,
			'number'   => 80,
			'taxonomy' => bbp_get_topic_tag_tax_id()
		));

		wp_reset_postdata();
	}
}
