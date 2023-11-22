<?php

namespace ElementPack\Modules\ThreedText;

use Elementor\Controls_Manager;
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
		return 'bdt-threed-text';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'section_element_pack_threed_text_controls',
			[
				'tab'   => Controls_Manager::TAB_CONTENT,
				'label' => BDTEP_CP . esc_html__('3D Text', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);
		$element->end_controls_section();
	}


	public function register_controls($widget, $args) {

		$widget->add_control(
			'ep_threed_text_active',
			[
				'label'              => esc_html__('3D Text', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_threed_text_depth',
			[
				'label'     => __('Depth', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'size_units' => ['px', 'em', 'rem', '%'],
				'default' => [
					'size' => 30,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 100,
					],
					'em' => [
						'min' => 0,
						'max' => 10,
					],
					'rem' => [
						'min' => 0,
						'max' => 10,
					],
					'%' => [
						'min' => 0,
						'max' => 100,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
				],
				'render_type' => 'template'
			]
		);

		$widget->add_control(
			'ep_threed_text_layers',
			[
				'label' => esc_html__('Layers', 'plugin-name'),
				'type' => Controls_Manager::NUMBER,
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 8,
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_depth_color',
			[
				'label'     => esc_html__('Depth Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_perspective',
			[
				'label'     => __('Perspective', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'size_units' => ['px'],
				'default' => [
					'size' => 500,
				],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_fade',
			[
				'label'       => esc_html__('Fade', 'bdthemes-element-pack'),
				'type'        => Controls_Manager::SWITCHER,
				'default'     => 'yes',
				'condition'   => [
					'ep_threed_text_active' => 'yes'
				],
				'frontend_available' => true,
			]
		);

		// $widget->add_control(
		// 	'ep_threed_text_direction',
		// 	[
		// 		'label'   => esc_html__('Direction', 'bdthemes-element-pack'),
		// 		'type'    => Controls_Manager::SELECT,
		// 		'options' => [
		// 			'both'      => esc_html__('Both', 'bdthemes-element-pack'),
		// 			'backwards' => esc_html__('Backwards', 'bdthemes-element-pack'),
		// 			'forwards'  => esc_html__('Forwards', 'bdthemes-element-pack'),
		// 		],
		// 		'default' => 'both',
		// 		'frontend_available' => true,
		// 		'condition' => [
		// 			'ep_threed_text_active' => 'yes'
		// 		],
		// 	]
		// );

		// $widget->add_control(
		// 	'ep_threed_text_bg_color',
		// 	[
		// 		'label'     => esc_html__( 'Direction Background', 'bdthemes-element-pack' ),
		// 		'type'      => Controls_Manager::COLOR,
		// 		'frontend_available' => true,
		// 		'condition' => [
		// 			'ep_threed_text_active' => 'yes'
		// 		],
		// 	]
		// );

		$widget->add_control(
			'ep_threed_text_event',
			[
				'label'   => esc_html__('Event', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'none'    => esc_html__('None', 'bdthemes-element-pack'),
					'pointer' => esc_html__('Pointer', 'bdthemes-element-pack'),
					'scroll'  => esc_html__('Scroll', 'bdthemes-element-pack'),
					'scrollX' => esc_html__('ScrollX', 'bdthemes-element-pack'),
					'scrollY' => esc_html__('ScrollY', 'bdthemes-element-pack'),
				],
				'default' => 'none',
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes'
				],
			]
		);

		$widget->add_control(
			'ep_threed_text_event_rotation',
			[
				'label'     => __('Event Rotation', 'bdthemes-element-pack'),
				'type' => Controls_Manager::SLIDER,
				'default' => [
					'size' => 35,
				],
				'range' => [
					'px' => [
						'max' => 360,
						'min' => -360,
					],
				],
				'condition' => [
					'ep_threed_text_active' => 'yes',
					'ep_threed_text_event!' => 'none',
				],
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'ep_threed_text_event_direction',
			[
				'label'   => esc_html__('Event Direction', 'bdthemes-element-pack'),
				'type'    => Controls_Manager::SELECT,
				'options' => [
					'default'  => esc_html__('Default', 'bdthemes-element-pack'),
					'reverse'  => esc_html__('Reverse', 'bdthemes-element-pack'),
				],
				'default' => 'default',
				'frontend_available' => true,
				'condition' => [
					'ep_threed_text_active' => 'yes',
					'ep_threed_text_event!' => 'none',
				],
			]
		);
	}

	public function enqueue_scripts() {
		wp_enqueue_script('ztext-js', BDTEP_ASSETS_URL . 'vendor/js/ztext.min.js', ['jquery'], '0.0.2', true);
	}
	public function should_script_enqueue($widget) {
		if ('yes' === $widget->get_settings_for_display('ep_threed_text_active')) {
			$this->enqueue_scripts();
			wp_enqueue_script('ep-threed-text');
		}
	}
	protected function add_actions() {

		add_action('elementor/element/heading/section_title_style/before_section_start', [$this, 'register_section']);
		add_action('elementor/element/bdt-advanced-heading/section_style_sub_heading/before_section_start', [$this, 'register_section']);
		add_action('elementor/element/heading/section_element_pack_threed_text_controls/before_section_end', [$this, 'register_controls'], 10, 2);
		add_action('elementor/element/bdt-advanced-heading/section_element_pack_threed_text_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		// render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
		add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
	}
}
