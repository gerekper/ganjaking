<?php

namespace ElementPack\Modules\RippleEffects;

use Elementor\Controls_Manager;
use ElementPack\Base\Element_Pack_Module_Base;

if (!defined('ABSPATH')) exit; // Exit if accessed directly

class Module extends Element_Pack_Module_Base {

	public function __construct() {
		parent::__construct();
		$this->add_actions();
	}

	public function get_name() {
		return 'bdt-ripple-effects';
	}

	public function register_section($element) {

		$element->start_controls_section(
			'ep_ripple_effects',
			[
				'label' => BDTEP_CP . esc_html__('Ripple Effects', 'bdthemes-element-pack') . BDTEP_NC,
				'tab' => Controls_Manager::TAB_ADVANCED,
			]
		);

		$element->end_controls_section();
	}

	public function register_controls($widget, $args) {

		$widget->add_control(
			'ep_ripple_enable',
			[
				'label'        => esc_html__('Enable Ripple Effects', 'bdthemes-element-pack'),
				'type'         => Controls_Manager::SWITCHER,
				'return_value' => 'yes',
				'frontend_available' => true,
				'render_type'        => 'template',
			]
		);
		$widget->add_control(
			'ep_ripple_selector',
			[
				'label'     => esc_html__('Ripple Effects For', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'widgets'    => 'Widgets',
					'buttons' => 'Widgets > Buttons',
					'images' => 'Widgets > Images',
					'both' => 'Widgets > Image/Button',
					'custom' => 'Custom',
				],
				'default'   => 'buttons',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
		$widget->add_control(
            'ep_ripple_custom_selector',
            [
                'label'              => esc_html__('Custom Selector', 'bdthemes-prime-slider'),
                'type'               => Controls_Manager::TEXT,
                'placeholder'            => '#my-header',
                'default'            => '#my-header',
                'description' => esc_html__('Please use ID or Class to select your element/elements. ( Example - #select-id, .select-class)', 'bdthemes-element-pack'),
                'frontend_available' => true,
                'condition'          => [
                    'ep_ripple_enable'      => 'yes',
                    'ep_ripple_selector' => 'custom',
                ],
            ]
        );
		$widget->add_control(
			'ep_ripple_on',
			[
				'label'     => __('On', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'click' => __('Click', 'bdthemes-element-pack'),
					'mouseenter' => __('Mouse Enter', 'bdthemes-element-pack'),
					'mouseleave' => __('Mouse Leave', 'bdthemes-element-pack'),
				],
				'default'   => 'mouseenter',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
		$widget->add_control(
			'ep_ripple_easing',
			[
				'label'     => __('Easing', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SELECT,
				'options'   => [
					'linear' => __('linear', 'bdthemes-element-pack'),
					'ease' => __('ease', 'bdthemes-element-pack'),
					'ease-in' => __('ease-in', 'bdthemes-element-pack'),
					'ease-in-out' => __('ease-in-out', 'bdthemes-element-pack'),
					'ease-out' => __('ease-out', 'bdthemes-element-pack'),
				],
				'default'   => 'linear',
				'frontend_available' => true,
				'render_type'        => 'template',
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
		
		$widget->add_control(
			'ep_ripple_duration',
			[
				'label'     => __('Duration', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type' => 'template',
				'default' => [
					'size' => 0.7,
				],
				'range' => [
					'px' => [
						'max' => 10,
						'min' => 0.1,
						'step' => 0.1,
					],
				],
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
		$widget->add_control(
			'ep_ripple_opacity',
			[
				'label'     => __('Opacity', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::SLIDER,
				'frontend_available' => true,
				'render_type' => 'template',
				'default' => [
					'size' => 0.4,
				],
				'range' => [
					'px' => [
						'max' => 1,
						'min' => 0.1,
						'step' => 0.1,
					],
				],
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
		$widget->add_control(
			'ep_ripple_color',
			[
				'label'     => __('Color', 'bdthemes-element-pack'),
				'type'      => Controls_Manager::COLOR,
				'frontend_available' => true,
				'render_type' => 'template',
				'default' => '#c5c5c5',
				'condition' => [
					'ep_ripple_enable' => 'yes',
				],
			]
		);
	}	

	public function enqueue_scripts() {
        wp_enqueue_script('ep-ripple-effects-vendor', BDTEP_ASSETS_URL . 'vendor/js/ripple.min.js', [], '', true);
    }

	public function should_script_enqueue($widget) {
        if ('yes' === $widget->get_settings_for_display('ep_ripple_enable')) {
			$this->enqueue_scripts();
			wp_enqueue_script('ep-ripple-effects');
			wp_enqueue_style('ep-ripple-effects');
        }
    }

	protected function add_actions() {
		add_action('elementor/element/common/_section_style/after_section_end', [$this, 'register_section']);
		add_action('elementor/element/common/ep_ripple_effects/before_section_end', [$this, 'register_controls'], 10, 2);

		// render scripts
		add_action('elementor/frontend/widget/before_render', [$this, 'should_script_enqueue']);
        add_action('elementor/preview/enqueue_scripts', [$this, 'enqueue_scripts']);
	}
}
