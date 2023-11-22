<?php

namespace ElementPack\Modules\RealisticImageShadow;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Typography;
use ElementPack;
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
		return 'bdt-realistic-image-shadow';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'section_element_pack_ris_controls',
			[
				'tab'   => Controls_Manager::TAB_ADVANCED,
				'label' => BDTEP_CP . esc_html__('Realistic Image Shadow', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);
		$element->end_controls_section();
	}


	public function register_controls($widget, $args) {

		$widget->add_control(
			'element_pack_ris_enable',
			[
				'label'              => esc_html__('Use Realistic Shadow?', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'label_on'           => esc_html__('Yes', 'bdthemes-element-pack'),
				'label_off'          => esc_html__('No', 'bdthemes-element-pack'),
				'render_type'        => 'template',
				'frontend_available' => true,
			]
		);

		$widget->add_control(
			'element_pack_ris_on_hover',
			[
				'label'              => esc_html__('Shadow On Hover', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SWITCHER,
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_selector',
			[
				'label'              => esc_html__('Selector', 'bdthemes-element-pack'),
				'placeholder'        => esc_html__('.my-class', 'bdthemes-element-pack'),
				'description'        => esc_html__('Enter your class or id selector of your img parent tag. e.g: .my-class, #my-id', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::TEXT,
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_x',
			[
				'label'              => esc_html__('Position X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
				'selectors'          => [
					'{{WRAPPER}} .element-pack-ris-image' => 'left: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_y',
			[
				'label'              => esc_html__('Position Y', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => -100,
						'max'  => 100,
						'step' => 1,
					],
				],
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
				'selectors'          => [
					'{{WRAPPER}} .element-pack-ris-image' => 'top: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_blur',
			[
				'label'              => esc_html__('Blur', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 100,
						'step' => 1,
					],
				],
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
				'selectors'          => [
					'{{WRAPPER}} .element-pack-ris-image' => 'filter: blur({{SIZE}}{{UNIT}});',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_opacity',
			[
				'label'      => esc_html__('Opacity', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px'],
				'range'      => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
				'selectors'          => [
					'{{WRAPPER}} .element-pack-ris-image' => 'opacity: {{SIZE}};',
				],
			]
		);

		$widget->add_control(
			'element_pack_ris_scale_x',
			[
				'label'              => esc_html__('Scale X', 'bdthemes-element-pack'),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 1,
						'step' => 0.01,
					],
				],
				'frontend_available' => true,
				'condition'          => [
					'element_pack_ris_enable' => 'yes',
				],
				'selectors'          => [
					'{{WRAPPER}} .element-pack-ris-image' => 'transform: scaleX({{SIZE}});',
				],
			]
		);
	}

	public function should_script_enqueue($widget) {
		if ('yes' === $widget->get_settings_for_display('element_pack_ris_enable')) {
			wp_enqueue_script('ep-realistic-image-shadow');
		}
	}

	protected function add_actions() {

		add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);

		add_action('elementor/element/common/section_element_pack_ris_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		//render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
	}
}
