<?php

namespace ElementPack\Modules\ImageHoverEffects;

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
		return 'bdt-image-hover-effects';
	}

	public function register_section($element) {
		$element->start_controls_section(
			'ep_image_hover_effects_controls',
			[
				'tab'   => Controls_Manager::TAB_STYLE,
				'label' => BDTEP_CP . esc_html__('Image Hover Effects', 'bdthemes-element-pack') . BDTEP_NC,
			]
		);
		$element->end_controls_section();
	}

	public function register_controls($widget, $args) {

		$widget->add_control(
			'ep_image_hover_effects_on',
			[
				'label' => esc_html__('Hover Effects?', 'bdthemes-element-pack'),
				'type'  => Controls_Manager::SWITCHER,
			]
		);
		$widget->add_control(
			'hover_effects',
			[
				'label'     => esc_html__('Choose Effect', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'1'    => 'Effect 01',
					'2'    => 'Effect 02',
					'3'    => 'Effect 03',
					'4'    => 'Effect 04',
					'5'    => 'Effect 05',
					'6'    => 'Effect 06',
				],
				'default'   => '1',
				'prefix_class'       => 'bdt-image-hover-effect-wrap bdt-image-hover-effect-',
				'condition' => [
					'ep_image_hover_effects_on' => 'yes',
				],
			]
		);
		$widget->add_control(
			'effects_color',
			[
				'label'     => esc_html__('Effects Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'default'   => 'rgba(0, 0, 0, .1)',
				'condition' => [
					'ep_image_hover_effects_on' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.bdt-image-hover-effect-wrap::before, {{WRAPPER}}.bdt-image-hover-effect-wrap::after' => 'background: {{VALUE}};border-color: {{VALUE}};',
				],
			]
		);
		$widget->add_control(
			'effets_width',
			[
				'label'      => esc_html__('Effects Width', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['px', '%'],
				'condition' => [
					'ep_image_hover_effects_on' => 'yes',
					'hover_effects' => ['3','5','6'],
				],
				'selectors' => [
					'{{WRAPPER}}.bdt-image-hover-effect-wrap::before, {{WRAPPER}}.bdt-image-hover-effect-wrap::after' => 'border-width: {{SIZE}}{{UNIT}};',
				],
			]
		);
		$widget->add_control(
			'effects_duration',
			[
				'label'      => esc_html__('Effects Duration(ms)', 'bdthemes-element-pack'),
				'type'       => Controls_Manager::SLIDER,
				'size_units' => ['ms'],
				'range'      => [
					'ms' => [
						'min' => 100,
						'max' => 5000,
						'step' => 100,
					],
				],
				'default' => [
					'unit' => 'ms',
				],
				'condition' => [
					'ep_image_hover_effects_on' => 'yes',
				],
				'selectors' => [
					'{{WRAPPER}}.bdt-image-hover-effect-wrap::before, {{WRAPPER}}.bdt-image-hover-effect-wrap::after' => 'transition-duration: {{SIZE}}{{UNIT}};',
				],
			]
		);
	}

	public function should_script_enqueue($widget) {
        if ('yes' === $widget->get_settings_for_display('ep_image_hover_effects_on')) {
            wp_enqueue_style('ep-image-hover-effects');
        }
    }

	protected function add_actions() {
		add_action('elementor/element/image/section_style_image/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/image/ep_image_hover_effects_controls/before_section_end', [$this, 'register_controls'], 10, 2);

		// render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue'], 10, 1);
	}
}
