<?php

namespace MasterAddons\Modules;

use \Elementor\Controls_Manager;
use \MasterAddons\Inc\Classes\JLTMA_Extension_Prototype;

// Exit if accessed directly.
if (!defined('ABSPATH')) {
	exit;
};

/**
 * Reveals - Opening effect
 */

class Extension_Floating_Effects extends JLTMA_Extension_Prototype
{

	private static $instance = null;
	public $name = 'Floating Effects';
	public $has_controls = true;


	private function add_controls($element, $args)
	{

		$element_type = $element->get_type();

		$element->add_control(
			'jltma_floating_effects',
			[
				'label' 				=> __('Floating Effects', MELA_TD),
				'type' 					=> Controls_Manager::SWITCHER,
				'default' 				=> '',
				'label_on' 				=> __('Yes', MELA_TD),
				'label_off' 			=> __('No', MELA_TD),
				'return_value' 			=> 'yes',
				'frontend_available' 	=> true
			]
		);


		$element->add_control(
			'jltma_floating_effects_translate_toggle',
			[
				'label'              => __('Translate', MELA_TD),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'jltma_floating_effects' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'jltma_floating_effects_translate_x',
			[
				'label'              => __('Translate X', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_translate_toggle' => 'yes',
					'jltma_floating_effects'                  => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_translate_y',
			[
				'label'              => __('Translate Y', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 5,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -100,
						'max' => 100,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_translate_toggle' => 'yes',
					'jltma_floating_effects'                  => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_translate_duration',
			[
				'label'              => __('Duration', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'size' => 1000,
				],
				'condition'          => [
					'jltma_floating_effects_translate_toggle' => 'yes',
					'jltma_floating_effects'                  => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_translate_delay',
			[
				'label'              => __('Delay', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'jltma_floating_effects_translate_toggle' => 'yes',
					'jltma_floating_effects'                  => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_popover();

		$element->add_control(
			'jltma_floating_effects_rotate_toggle',
			[
				'label'              => __('Rotate', MELA_TD),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'jltma_floating_effects' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'jltma_floating_effects_rotate_x',
			[
				'label'              => __('Rotate X', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 45,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_rotate_toggle' => 'yes',
					'jltma_floating_effects'               => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_rotate_y',
			[
				'label'              => __('Rotate Y', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 45,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_rotate_toggle' => 'yes',
					'jltma_floating_effects'               => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_rotate_z',
			[
				'label'              => __('Rotate Z', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 0,
						'to'   => 45,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min' => -180,
						'max' => 180,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_rotate_toggle' => 'yes',
					'jltma_floating_effects'               => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_rotate_duration',
			[
				'label'              => __('Duration', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'size' => 1000,
				],
				'condition'          => [
					'jltma_floating_effects_rotate_toggle' => 'yes',
					'jltma_floating_effects'               => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_rotate_delay',
			[
				'label'              => __('Delay', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'jltma_floating_effects_rotate_toggle' => 'yes',
					'jltma_floating_effects'               => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_popover();

		$element->add_control(
			'jltma_floating_effects_scale_toggle',
			[
				'label'              => __('Scale', MELA_TD),
				'type'               => Controls_Manager::POPOVER_TOGGLE,
				'return_value'       => 'yes',
				'frontend_available' => true,
				'condition'          => [
					'jltma_floating_effects' => 'yes',
				],
			]
		);

		$element->start_popover();

		$element->add_control(
			'jltma_floating_effects_scale_x',
			[
				'label'              => __('Scale X', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.2,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_scale_toggle' => 'yes',
					'jltma_floating_effects'              => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_scale_y',
			[
				'label'              => __('Scale Y', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'default'            => [
					'sizes' => [
						'from' => 1,
						'to'   => 1.2,
					],
					'unit'  => 'px',
				],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5,
						'step' => .1,
					],
				],
				'labels'             => [
					__('From', MELA_TD),
					__('To', MELA_TD),
				],
				'scales'             => 1,
				'handles'            => 'range',
				'condition'          => [
					'jltma_floating_effects_scale_toggle' => 'yes',
					'jltma_floating_effects'              => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_scale_duration',
			[
				'label'              => __('Duration', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 10000,
						'step' => 100,
					],
				],
				'default'            => [
					'size' => 1000,
				],
				'condition'          => [
					'jltma_floating_effects_scale_toggle' => 'yes',
					'jltma_floating_effects'              => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'jltma_floating_effects_scale_delay',
			[
				'label'              => __('Delay', MELA_TD),
				'type'               => Controls_Manager::SLIDER,
				'size_units'         => ['px'],
				'range'              => [
					'px' => [
						'min'  => 0,
						'max'  => 5000,
						'step' => 100,
					],
				],
				'condition'          => [
					'jltma_floating_effects_scale_toggle' => 'yes',
					'jltma_floating_effects'              => 'yes',
				],
				'render_type'        => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_popover();
	}

	public function before_render(\Elementor\Element_Base $element)
	{

		$settings = $element->get_settings();

		if (isset($settings['jltma_floating_effects']) && $settings['jltma_floating_effects'] == 'yes') {
			$this->jltma_add_floating_scripts();
		}
	}


	public function jltma_add_floating_scripts()
	{
		wp_enqueue_script('jltma-floating-effects', MELA_PLUGIN_URL . '/assets/vendor/floating-effects/floating-effects.js', array('ma-el-anime-lib', 'jquery'), MELA_VERSION);
	}

	protected function add_actions()
	{

		// Activate controls for widgets
		add_action('elementor/element/common/jltma_section_floating_effects_advanced/before_section_end', function ($element, $args) {
			$this->add_controls($element, $args);
		}, 10, 2);

		// add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'before_render'],10);
		add_action('elementor/frontend/element/before_render', [$this, 'before_render'], 10, 1);
		add_action('elementor/frontend/column/before_render', [$this, 'before_render'], 10, 1);
		add_action('elementor/frontend/section/before_render', [$this, 'before_render'], 10, 1);
		add_action('elementor/frontend/widget/before_render', [$this, 'before_render'], 10, 1);


		add_action('elementor/preview/enqueue_scripts', [$this, 'jltma_add_floating_scripts']);
	}


	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self;
		}
		return self::$instance;
	}
}

Extension_Floating_Effects::get_instance();
