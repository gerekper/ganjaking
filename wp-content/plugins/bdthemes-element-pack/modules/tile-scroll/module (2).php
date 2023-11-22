<?php

namespace ElementPack\Modules\TileScroll;

use Elementor\Controls_Manager;
use Elementor\Repeater;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) {
	exit;
} // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-tile-scroll';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'element_pack_tile_scroll_section',
			[
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => BDTEP_CP . esc_html__('Tile Scroll', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);
		$element->end_controls_section();
	}

	public function register_controls($section, $args) {

		$section->add_control(
			'element_pack_tile_scroll_show',
			[
				'label'              => esc_html__('Use Tile Scroll?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'default'            => '',
				'return_value'       => 'yes',
				'prefix_class'       => 'bdt-tile-scroll-',
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);
		$section->start_controls_tabs(
			'tabs_element_pack_tile_scroll'
		);
		$section->start_controls_tab(
			'tabs_element_pack_tile_content',
			[
				'label'     => esc_html__('Content', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_tile_scroll_show' => 'yes'
				]
			]
		);
		$repeater = new Repeater();
		$repeater->add_control(
			'element_pack_tile_scroll_title',
			[
				'label'       => __('Title', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::TEXT,
				'default'     => __('Item #1', 'bdthemes-element-pack'),
				'label_block' => true,
				'render_type' => 'ui',
			]
		);
		$repeater->add_control(
			'element_pack_tile_scroll_images',
			[
				'label' => esc_html__('Images', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::GALLERY,
			]
		);


		$repeater->add_control(
			'element_pack_tile_scroll_x_start',
			[
				'label'   => esc_html__('Start', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SLIDER,
				'range'   => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => 150,
				],
			]
		);

		$repeater->add_control(
			'element_pack_tile_scroll_x_end',
			[
				'label' => esc_html__('End', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min'  => -500,
						'max'  => 500,
						'step' => 1,
					],
				],
				'default' => [
					'unit' => 'px',
					'size' => -150,
				],
			]
		);

		$section->add_control(
			'element_pack_tile_scroll_elements',
			[
				'label'              => __('Tile Scroll Items', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::REPEATER,
				'fields'             => $repeater->get_controls(),
				'prevent_empty'      => false,
				'title_field'        => '{{{ element_pack_tile_scroll_title }}}',
				'frontend_available' => true,
				'render_type'        => 'none',
				'condition'          => [
					'element_pack_tile_scroll_show' => 'yes'
				],
			]
		);
		$section->end_controls_tab();
		$section->start_controls_tab(
			'tabs_element_pack_tile_style',
			[
				'label'     => esc_html__('Style', 'bdthemes-element-pack'),
				'condition' => [
					'element_pack_tile_scroll_show' => 'yes'
				]
			]
		);
		$section->add_control(
			'element_pack_tile_scroll_display',
			[
				'label'              => esc_html__('Scroll Style', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SELECT,
				'default'            => 'horizontal',
				'options'            => [
					'horizontal' => esc_html__('Horizontal', 'bdthemes-element-pack'),
					'vertical'   => esc_html__('Vertical', 'bdthemes-element-pack'),
				],
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition'          => [
					'element_pack_tile_scroll_show' => 'yes'
				]
			]
		);
		$section->add_control(
			'element_pack_tile_scroll_rotate',
			[
				'label'     => esc_html__('Rotate', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'range'     => [
					'px' => [
						'min'  => 0,
						'max'  => 360,
						'step' => 1,
					],
				],
				'selectors' => [
					'{{WRAPPER}}' => '--bdt-tile-scroll-rotate: {{SIZE}}deg;',
				],
				'condition' => [
					'element_pack_tile_scroll_show'    => 'yes',
					'element_pack_tile_scroll_display' => 'horizontal'
				]
			]
		);
		$section->add_responsive_control(
			'element_pack_tile_scroll_item_width',
			[
				'label'      => esc_html__('Width Adjustment', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 0.5,
					]
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--bdt-tile-scroll-item-width: {{SIZE}}%;',
				],
				'condition'  => [
					'element_pack_tile_scroll_show'    => 'yes',
					'element_pack_tile_scroll_display' => 'horizontal'
				]
			]
		);
		$section->add_responsive_control(
			'element_pack_tile_scroll_height',
			[
				'label'      => esc_html__('Height Adjustment', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['vw'],
				'default'    => [
					'unit' => 'vw',
					'size' => 52,
				],
				'selectors'  => [
					'{{WRAPPER}}' => '--bdt-tile-scroll-height: {{SIZE}}vw;',
				],
				'condition'  => [
					'element_pack_tile_scroll_show' => 'yes',
				]
			]
		);
		$section->add_responsive_control(
			'element_pack_tile_scroll_gap',
			[
				'label'      => esc_html__('Grid Gap', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'default'    => [
					'unit' => 'px',
					'size' => 10,
				],
				'selectors'  => [
					'{{WRAPPER}}'     => '--bdt-tile-scroll-margin: {{SIZE}}{{UNIT}};',
				],
				'condition'  => [
					'element_pack_tile_scroll_show' => 'yes'
				]
			]
		);
		$section->add_group_control(
			Group_Control_Border::get_type(),
			[
				'name'      => 'element_pack_tile_scroll_gap',
				'label'     => esc_html__('Border', 'bdthemes-element-pack'),
				'selector'  => '{{WRAPPER}} .bdt-tile-scroll__line-img',
				'separator' => 'before'
			]
		);
		$section->add_responsive_control(
			'element_pack_title_radius',
			[
				'label'      => esc_html__('Border Radius', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::DIMENSIONS,
				'size_units' => ['px', '%', 'em'],
				'selectors'  => [
					'{{WRAPPER}} .bdt-tile-scroll__line-img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		);
		$section->add_group_control(
			Group_Control_Box_Shadow::get_type(),
			[
				'name'     => 'element_pack_tile_scroll_shadow',
				'label'    => esc_html__('Shadow', 'bdthemes-element-pack'),
				'selector' => '{{WRAPPER}} .bdt-tile-scroll__line-img',
			]
		);
		$section->end_controls_tab();
		$section->end_controls_tabs();
		$section->add_control(
			'element_pack_tile_scroll_notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => sprintf(__('Please use proper size (for example: 640px X 560px) and optimize image for this gallery because tile gallery will show full size image so if you use large image that can slow down your scroll animation and page loading time', 'bdthemes-element-pack')),
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => [
					'element_pack_tile_scroll_show' => 'yes',
				],
				'separator'       => 'before'
			]
		);
	}


	public function section_tile_scroll_before_render($section) {
		$settings = $section->get_settings_for_display();
		if ('yes' === $settings['element_pack_tile_scroll_show']) {
			wp_enqueue_style('ep-tile-scroll');
			wp_enqueue_script('ep-tile-scroll');
		}
	}

	protected function add_actions() {
		// Add section for settings
		add_action('elementor/element/section/section_advanced/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/section/element_pack_tile_scroll_section/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/frontend/section/before_render', [$this, 'section_tile_scroll_before_render'], 10, 1);

		add_action('elementor/element/container/section_layout/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/container/element_pack_tile_scroll_section/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/frontend/container/before_render', [$this, 'section_tile_scroll_before_render'], 10, 1);
	}
}
